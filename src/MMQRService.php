<?php

namespace YenaingtunDev\MMQR;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MMQRService
{
    public function __construct(
        protected array $config
    ) {}

    // -------------------------------------------------------------------------
    // QR payment
    // -------------------------------------------------------------------------

    /**
     * Request a MMQR payment QR code from AYA Pay.
     *
     * Flow: access token → user token → QR request.
     */
    public function qrPayment(array $data): array
    {
        $tx = $data['externalTransactionId'];
        $envConfig = $this->configForEnvironment();

        $body = [
            'amount' => $data['amount'],
            'externalTransactionId' => $tx,
            'MMQR' => true,
            'currency' => $this->config['currency'],
            'serviceCode' => $envConfig['service_code_qr'],
        ];

        // 1. Access token (client credentials)
        $accessToken = $this->getAccessToken()['access_token'] ?? null;
        if (! $accessToken) {
            return $this->paymentFailed($tx, 'Access token not found in response.');
        }

        // 2. User token (merchant login)
        $userTokenResponse = $this->getUserToken();
        if (is_string($userTokenResponse)) {
            return $this->paymentFailed($tx, 'Failed to get user token: '.$userTokenResponse);
        }

        if (isset($userTokenResponse['err'])) {
            return $this->paymentFailed(
                $tx,
                $userTokenResponse['message'] ?? 'User token request failed.',
                $userTokenResponse
            );
        }

        $userToken = $userTokenResponse['token']['token'] ?? null;
        if (! $userToken) {
            return $this->paymentFailed($tx, 'User token not found in response.');
        }

        // 3. QR payment request
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept-Language' => 'en',
            'Authorization' => 'Bearer '.$userToken,
            'Token' => 'Bearer '.$accessToken,
        ])->asJson()->post($envConfig['qr_url'], $body);

        $result = $response->json();
        if ($result === null) {
            return $this->paymentFailed($tx, 'Invalid response from AYA Pay.');
        }

        if (($result['err'] ?? null) !== 200) {
            return $this->paymentFailed(
                $tx,
                $result['message'] ?? 'Payment request failed.',
                $result
            );
        }

        Log::info('MMQR payment ok', ['tx' => $tx]);

        return $result;
    }

    // -------------------------------------------------------------------------
    // Authentication (access token → user token)
    // -------------------------------------------------------------------------

    /**
     * OAuth client-credentials token. Required before user login and QR requests.
     */
    public function getAccessToken(): array
    {
        $envConfig = $this->configForEnvironment();

        $response = Http::withHeaders([
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Authorization' => 'Basic '.$this->base64Credentials(),
        ])->asForm()->post($envConfig['access_token_url'], [
            'grant_type' => 'client_credentials',
        ]);

        return $response->json() ?? [];
    }

    /**
     * Merchant login token. Uses the access token in the Token header.
     */
    public function getUserToken(): array|string
    {
        $envConfig = $this->configForEnvironment();

        $body = [
            'phone' => $envConfig['phone'],
            'password' => $envConfig['pin'],
        ];

        if ($body['password'] === null || $body['password'] === '') {
            return [
                'err' => 500,
                'message' => 'Password is required',
            ];
        }

        if ($body['phone'] === null || $body['phone'] === '') {
            return [
                'err' => 500,
                'message' => 'Phone is required',
            ];
        }

        $accessToken = $this->getAccessToken()['access_token'] ?? null;
        if (! $accessToken) {
            return [
                'err' => 500,
                'message' => 'Access token not found in response.',
            ];
        }

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Token' => 'Bearer '.$accessToken,
        ])->post($envConfig['user_token_url'], $body);

        if ($response->successful()) {
            return $response->json();
        }

        return $response->body();
    }

    // -------------------------------------------------------------------------
    // Encryption (callbacks / webhooks)
    // -------------------------------------------------------------------------

    public function encrypt(string $data, ?string $key = null): string
    {
        $cipher = 'AES-256-ECB';
        $key = $key ?? $this->configForEnvironment()['decryption_key'];
        $cipherRaw = openssl_encrypt($data, $cipher, $key, OPENSSL_RAW_DATA);

        return base64_encode($cipherRaw);
    }

    public function decrypt(string $data, ?string $key = null): string|false
    {
        $cipher = 'AES-256-ECB';
        $key = $key ?? $this->configForEnvironment()['decryption_key'];
        $cipherRaw = base64_decode($data);

        return openssl_decrypt($cipherRaw, $cipher, $key, OPENSSL_RAW_DATA);
    }

    public function encryptDecrypt(string $data, ?string $key = null, string $action = 'encrypt'): string|false
    {
        if ($action === 'encrypt') {
            return $this->encrypt($data, $key);
        }

        if ($action === 'decrypt') {
            return $this->decrypt($data, $key);
        }

        return false;
    }

    // -------------------------------------------------------------------------
    // Internal helpers
    // -------------------------------------------------------------------------

    protected function paymentFailed(string $tx, string $message, ?array $response = null): array
    {
        Log::error('MMQR payment failed', ['tx' => $tx, 'message' => $message]);

        return $response ?? ['err' => 500, 'message' => $message];
    }

    protected function configForEnvironment(): array
    {
        $environment = $this->config['environment'] ?? 'uat';

        return $this->config[$environment] ?? $this->config['uat'];
    }

    protected function base64Credentials(): string
    {
        $envConfig = $this->configForEnvironment();

        return base64_encode($envConfig['consumer_key'].':'.$envConfig['consumer_secret']);
    }
}
