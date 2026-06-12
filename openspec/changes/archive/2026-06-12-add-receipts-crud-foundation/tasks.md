## 1. Services Layer

- [x] 1.1 Create `app/Services/RecuService.php` with `create(User $user, array $data): Recu` — creates receipt with `statut = StatutRecu::EnAttente`, dispatches `ExtraireDepensesDuRecu`
- [x] 1.2 Create `app/Services/ExtractionService.php` with `extraire(Recu $recu): void` — calls `AI::structured()`, validates response with `Validator::make()`, persists depenses, updates receipt statut

## 2. Policies

- [x] 2.1 Create `app/Policies/RecuPolicy.php` with `view()` and `delete()` gates checking `$user->id === $recu->user_id`
- [x] 2.2 Create `app/Policies/DepensePolicy.php` with `view()` gate checking ownership via `$depense->recu->user_id`
- [x] 2.3 Register policies in `AppServiceProvider` if auto-discovery doesn't cover them

## 3. Job Refactoring

- [x] 3.1 Refactor `app/Jobs/ExtraireDepensesDuRecu.php` `handle()` to delegate to `ExtractionService::extraire()`
- [x] 3.2 Add `$tries = 3` and `$backoff = 60` properties if missing
- [x] 3.3 Verify `failed()` sets `StatutRecu::Echoue`

## 4. Receipt Controller Refactoring

- [x] 4.1 Refactor `RecuController::store()` to delegate to `RecuService::create()`, return redirect with flash message
- [x] 4.2 Add `->with('depenses')` eager loading and `->paginate(20)` in `RecuController::index()`
- [x] 4.3 Fix `destroy()` redirect to `route('recus.index')` (was `route('recu.index')`)

## 5. Expenses Controller Refactoring

- [x] 5.1 Add `->paginate(20)` with `->with('recu')` in `DepenseController::index()`
- [x] 5.2 Add category filter support (`?categorie=X`) in `DepenseController::index()` with validation against `CategorieDepense` cases

## 6. Tests

- [x] 6.1 Create `tests/Feature/RecuSubmissionTest.php` — test receipt creation, validation, job dispatch, pagination, authorization
- [x] 6.2 Create `tests/Feature/ExtractionTest.php` — test AI extraction with `AI::fake()`, valid/invalid responses, job failure handling
- [x] 6.3 Run full test suite and verify all tests pass
