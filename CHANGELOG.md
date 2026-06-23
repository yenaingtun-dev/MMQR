# Changelog

All notable changes to this project will be documented in this file.

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
