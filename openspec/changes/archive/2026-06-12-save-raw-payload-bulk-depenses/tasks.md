## 1. Update ExtractionService

- [x] 1.1 Modify `ExtractionService::extraire()` to save `$response->text` as `raw_text` inside `payload_ia`
- [x] 1.2 Replace foreach loop with `Depense::insert()` for bulk creation

## 2. Update Test

- [x] 2.1 Add assertion to verify `payload_ia` contains `structured` and `raw_text` keys

## 3. Verify

- [x] 3.1 Run `php artisan test` to ensure all tests pass
