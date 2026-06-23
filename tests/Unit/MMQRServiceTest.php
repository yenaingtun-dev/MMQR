<?php

namespace YenaingtunDev\MMQR\Tests\Unit;

use Illuminate\Support\Facades\Http;
use YenaingtunDev\MMQR\MMQRService;
use YenaingtunDev\MMQR\Tests\TestCase;

class MMQRServiceTest extends TestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('mmqr', [
            'environment' => 'uat',
            'currency' => 'MMK',
            'uat' => [
                'access_token_url' => 'https://example.test/token',
                'user_token_url' => 'https://example.test/login',
                'qr_url' => 'https://example.test/qr',
                'phone' => '09123456789',
                'pin' => '123456',
                'service_code_qr' => 'test-service-qr',
                'consumer_key' => 'test-key',
                'consumer_secret' => 'test-secret',
                'decryption_key' => 'nSlzVqOX9aZIkThhHy1brbevlaSFyaoJ',
            ],
            'production' => [],
        ]);
    }

    public function test_encrypt_and_decrypt_round_trip(): void
    {
        $service = app(MMQRService::class);
        $payload = '{"transactionStatus":"SUCCESS"}';

        $encrypted = $service->encrypt($payload);
        $decrypted = $service->decrypt($encrypted);

        $this->assertSame($payload, $decrypted);
    }

    public function test_encrypt_decrypt_alias_matches_encrypt_and_decrypt(): void
    {
        $service = app(MMQRService::class);
        $payload = 'test-payload';

        $encrypted = $service->encryptDecrypt($payload, null, 'encrypt');
        $decrypted = $service->encryptDecrypt($encrypted, null, 'decrypt');

        $this->assertSame($payload, $decrypted);
    }

    public function test_get_access_token(): void
    {
        Http::fake([
            'https://example.test/token' => Http::response([
                'access_token' => 'test-access-token',
                'token_type' => 'Bearer',
            ]),
        ]);

        $response = app(MMQRService::class)->getAccessToken();

        $this->assertSame('test-access-token', $response['access_token']);
    }

    public function test_qr_payment_returns_qr_data(): void
    {
        Http::fake([
            'https://example.test/token' => Http::response([
                'access_token' => 'test-access-token',
            ]),
            'https://example.test/login' => Http::response([
                'token' => ['token' => 'test-user-token'],
            ]),
            'https://example.test/qr' => Http::response([
                'err' => 200,
                'data' => [
                    'qrdata' => '000201010212...',
                    'amount' => 1000,
                ],
            ]),
        ]);

        $response = app(MMQRService::class)->qrPayment([
            'amount' => 1000,
            'externalTransactionId' => 'ORD-001',
        ]);

        $this->assertSame(200, $response['err']);
        $this->assertSame('000201010212...', $response['data']['qrdata']);
    }

    public function test_qr_payment_returns_error_when_user_token_missing(): void
    {
        Http::fake([
            'https://example.test/token' => Http::response([
                'access_token' => 'test-access-token',
            ]),
            'https://example.test/login' => Http::response([
                'token' => [],
            ]),
        ]);

        $response = app(MMQRService::class)->qrPayment([
            'amount' => 1000,
            'externalTransactionId' => 'ORD-001',
        ]);

        $this->assertSame(500, $response['err']);
        $this->assertSame('User token not found in response.', $response['message']);
    }
}
