# Changelog

All notable changes to this project will be documented in this file.

## 0.3.0 - 2026-06-24

### Changed

- Simplified `qrPayment()` logging to one log per call — success logs `tx` only, failures log `tx` and `message`
- Removed verbose logs that dumped request bodies, tokens, and raw API responses
- Removed logging from `getUserToken()`; payment outcomes are logged only in `qrPayment()`
- `qrPayment()` now treats API responses with `err !== 200` as failures and logs them
- Reorganized `MMQRService` methods to match the API flow: QR payment → access token → user token → encryption → helpers
- Added section comments and docblocks to `MMQRService`

### Added

- `paymentFailed()` internal helper for unified error logging and response shaping
- Unit test assertions for payment success and failure logging

### Security

- Tokens and sensitive response bodies are no longer written to logs

## 0.2.0 - 2026-06-22

### Added

- `MMQRService` with AYA Pay QR payment API client
- `getAccessToken()` and `getUserToken()` OAuth flow
- `encrypt()`, `decrypt()`, and `encryptDecrypt()` for callback payloads
- AYA Pay UAT/production config via environment variables
- Service registered as singleton in `MMQRServiceProvider`
- Unit tests for service and HTTP integration

## 0.1.0 - 2026-06-22

### Added

- Initial Laravel package skeleton
- `MMQRServiceProvider` with config merge and publish support
- PHPUnit test harness with Orchestra Testbench
- GitHub Actions CI workflow
