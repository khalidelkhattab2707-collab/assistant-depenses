## 1. Refactor Job

- [x] 1.1 Add `private function buildPrompt(): string` to `ExtraireDepensesDuRecu` using `$this->recu->text_brut`
- [x] 1.2 Add `private function getJsonSchema(): array` to `ExtraireDepensesDuRecu` with the full JSON schema
- [x] 1.3 Update `handle()` to call `AI::structured()` directly with `buildPrompt()` and `getJsonSchema()`, then pass result to service

## 2. Refactor Service

- [x] 2.1 Add `public function persist(Recu $recu, array $data, string $rawText): void` to `ExtractionService` with validation + bulk insert + recu update
- [x] 2.2 Keep old `extraire()` method but have it delegate to new methods for backward compat

## 3. Verify

- [x] 3.1 Run `php artisan test` to ensure all tests pass
