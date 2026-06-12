## 1. Enhance Job & Test

- [x] 1.1 Add `Log::error()` with receipt ID and exception message to `ExtraireDepensesDuRecu::failed()`
- [x] 1.2 Store exception message in `payload_ia` inside `ExtraireDepensesDuRecu::failed()`
- [x] 1.3 Update test to assert error message is persisted in `payload_ia`

## 2. Sync & Verify

- [x] 2.1 Sync delta specs to main specs
- [x] 2.2 Run `php artisan test` to ensure all tests pass
