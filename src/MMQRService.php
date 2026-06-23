<?php

namespace YenaingtunDev\MMQR;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MMQRService
{
    public function __construct(
        protected array $config
    ) {}

    public function qrPayment(array $data): array
    {
        $envConfig = $this->configForEnvironment();

        $body = [
            'amount' => $data['amount'],
            'externalTransactionId' => $data['externalTransactionId'],
            'MMQR' => true,
            'currency' => $this->config['currency'],
            'serviceCode' => $envConfig['service_code_qr'],
        ];

        $accessToken = $this->getAccessToken()['access_token'] ?? null;
        if (! $accessToken) {
            return [
                'err' => 500,
                'message' => 'Access token not found in response.',
            ];
        }

        $userTokenResponse = $this->getUserToken();
        if (is_string($userTokenResponse)) {
            return [
                'err' => 500,
                'message' => 'Failed to get user token: '.$userTokenResponse,
            ];
        }

        if (isset($userTokenResponse['err'])) {
            return $userTokenResponse;
        }

        $userToken = $userTokenResponse['token']['token'] ?? null;
        if (! $userToken) {
            return [
                'err' => 500,
                'message' => 'User token not found in response.',
            ];
        }

        Log::info('AYA QR Request Body', ['body' => $body]);
        Log::info('AYA QR User Token', ['user_token' => $userToken]);
        Log::info('AYA QR Access Token', ['access_token' => $accessToken]);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept-Language' => 'en',
            'Authorization' => 'Bearer '.$userToken,
            'Token' => 'Bearer '.$accessToken,
        ])->asJson()->post($envConfig['qr_url'], $body);

        Log::info('AYA QR Response', ['response' => $response->body()]);

        return $response->json() ?? [
            'err' => 500,
            'message' => 'Invalid response from AYA Pay.',
        ];
    }

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

        Log::error('Error occurred while requesting MMQR user token: '.$response->body());

        return $response->body();
    }

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
