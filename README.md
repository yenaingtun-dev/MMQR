# MMQR Payment

Laravel package for Malaysia **MMQR** (DuitNow QR) merchant-presented payments.

> **Status:** Early skeleton — QR generation, parsing, and PayNet API integration are planned for future releases.

## Requirements

- PHP 8.2+
- Laravel 11 or 12

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

Available settings (see `config/mmqr.php`):

| Key | Default | Description |
|-----|---------|-------------|
| `merchant_name` | `""` | Merchant display name |
| `merchant_city` | `"MY"` | Merchant city |
| `currency` | `"458"` | ISO 4217 numeric code (MYR) |
| `country_code` | `"MY"` | ISO 3166-1 alpha-2 |
| `mode` | `"static"` | `static` or `dynamic` |

## Development

```bash
composer install
composer test
```

## Roadmap

- EMV TLV encode/decode for DuitNow QR payloads
- CRC-16 validation
- Static and dynamic merchant QR builders
- PayNet API client, webhooks, and signature verification

## License

MIT — see [LICENSE](LICENSE).
