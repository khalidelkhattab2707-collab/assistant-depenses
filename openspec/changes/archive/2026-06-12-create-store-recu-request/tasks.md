## 1. Update StoreRecuRequest Validation Rules

- [x] 1.1 Add `min:10` and `max:10000` constraints to `text_brut` rule
- [x] 1.2 Add `Rule::in(['MAD', 'EUR', 'USD'])` to `devis` rule
- [x] 1.3 Add `min:0` constraint to `total_estime` rule
- [x] 1.4 Add custom French validation messages via `messages()` method

## 2. Verify

- [x] 2.1 Run `php artisan test` to ensure all existing tests pass
- [ ] 2.2 Verify validation errors display correctly in the `recus.create` view
