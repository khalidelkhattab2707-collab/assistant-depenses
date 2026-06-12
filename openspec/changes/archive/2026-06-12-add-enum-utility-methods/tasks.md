## 1. Add Utility Methods to Enums

- [x] 1.1 Add `color(): string` method to `StatutRecu` (EnAttente → warning, Traite → success, Echoue → danger)
- [x] 1.2 Add `values(): array` static method to `StatutRecu`
- [x] 1.3 Add `values(): array` static method to `CategorieDepense`

## 2. Refactor Views to Use color() Method

- [x] 2.1 Replace `@php match(...)` block in `recus/index.blade.php` with `$recu->status->color()`
- [x] 2.2 Replace `@php match(...)` block in `recus/show.blade.php` with `$recu->status->color()`

## 3. Refactor DepenseController to Use values()

- [x] 3.1 Replace manual `array_map(fn($case) => $case->value, CategorieDepense::cases())` with `CategorieDepense::values()` in `DepenseController::index()`

## 4. Verify

- [x] 4.1 Run `php artisan test` to ensure all tests pass
