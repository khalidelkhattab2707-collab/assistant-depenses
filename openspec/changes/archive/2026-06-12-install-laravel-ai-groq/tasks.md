## 1. Configure default provider

- [x] 1.1 Change `config/ai.php` default from `'openai'` to `env('AI_PROVIDER', 'openai')`

## 2. Configure Groq model

- [x] 2.1 Add `models.text.default` and `models.text.cheapest` entries to Groq provider config in `config/ai.php`, reading from `GROQ_MODEL` env

## 3. Verify

- [x] 3.1 Run `php artisan test` to ensure all tests pass
