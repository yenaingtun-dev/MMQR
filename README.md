# MMQR Payment

Laravel package for **AYA Pay MMQR** (Myanmar) QR payments.

## Requirements

- PHP 8.2+
- Laravel 11 or 12
- OpenSSL extension

## Installation

```bash
composer require yenaingtun-dev/mmqr
```

The service provider is auto-discovered by Laravel.

## Configuration

Publish the config file:

```bash
php artisan vendor:publish --tag=mmqr-config
```

Set your environment variables in `.env`:

| Variable | Description |
|----------|-------------|
| `MMQR_ENV` | `uat` or `production` (default: `uat`) |
| `MMQR_CURRENCY` | Currency code (default: `MMK`) |
| `MMQR_UAT_PHONE` | Merchant phone (UAT) |
| `MMQR_UAT_PIN` | Merchant PIN (UAT) |
| `MMQR_UAT_SERVICE_CODE_QR` | Service code for QR (UAT) |
| `MMQR_UAT_CONSUMER_KEY` | OAuth consumer key (UAT) |
| `MMQR_UAT_CONSUMER_SECRET` | OAuth consumer secret (UAT) |
| `MMQR_UAT_DECRYPTION_KEY` | Callback decryption key (UAT) |
| `MMQR_PROD_*` | Same keys for production |

URL defaults for UAT and production are pre-filled from AYA Pay endpoints and can be overridden via env if needed.

## Usage

### Request QR payment

```php
use YenaingtunDev\MMQR\MMQRService;

$mmqr = app(MMQRService::class);

$response = $mmqr->qrPayment([
    'amount' => 10000,
    'externalTransactionId' => 'ORD-12345',
]);

if (($response['err'] ?? null) === 200) {
    $qrData = $response['data']['qrdata'];
    $amount = $response['data']['amount'];
}
```

### Decrypt payment callback

```php
$decrypted = $mmqr->decrypt($request->paymentResult);

// HTDC-compatible alias:
$decrypted = $mmqr->encryptDecrypt($request->paymentResult, null, 'decrypt');
```

Callback checksum validation and order updates remain the responsibility of your application.

## Development

```bash
composer install
composer test
```

## License

MIT — see [LICENSE](LICENSE).
