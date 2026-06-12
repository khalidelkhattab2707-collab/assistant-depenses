## Context

The current receipts CRUD was implemented without the mandated service layer. `RecuController` contains inline business logic instead of delegating to `RecuService`. The `ExtraireDepensesDuRecu` job performs AI extraction inline without an `ExtractionService`. Policies are absent despite authorization calls. No tests cover the core flows.

The existing code uses French naming conventions (`StatutRecu`, `CategorieDepense`, `text_brut`, `devis`) — we keep these to avoid a breaking rename migration.

## Goals / Non-Goals

**Goals:**
- Extract receipt creation logic into `RecuService::create()`
- Create `ExtractionService::extraire()` for AI extraction orchestration
- Delegate job `handle()` to `ExtractionService`
- Create `RecuPolicy` and `DepensePolicy` with proper `view` and `delete` gates
- Dispatch `ExtraireDepensesDuRecu` job from `RecuService::create()`
- Validate AI structured output with `Validator::make()` before persisting
- Add `->paginate(20)` to `RecuController::index()` and `DepenseController::index()`
- Write Pest feature tests using `Queue::fake()` and `AI::fake()`

**Non-Goals:**
- No column renames (keep `text_brut`, `status`, `devis` as-is)
- No new migrations or schema changes
- No UI redesign
- No English enum rename

## Decisions

1. **Keep existing French naming** — The codebase already uses `StatutRecu`, `CategorieDepense`, `text_brut`, `status`, `devis`. Renaming would require a migration and break running code. AGENTS.md names are illustrative.

2. **RecuService receives `User` and validated data** — `RecuService::create(User $user, array $data)` creates the recu and dispatches the job. The controller stays thin.

3. **ExtractionService owns the AI call** — The job receives the `Recu` model and delegates to `ExtractionService::extraire()`. This makes the extraction logic independently testable.

4. **AI response validated before persistence** — `Validator::make()` checks the structured output against the schema (articles array, required fields, valid categories) before storing. Invalid responses throw `RuntimeException` caught by the job's `failed()`.

5. **Policies in `app/Policies/`** — Each policy checks `$user->id === $recu->user_id`. Laravel auto-discovers policies when the model follows naming convention (`Recu` → `RecuPolicy`).

6. **Paginate with 20 per page** — Both list endpoints use `->paginate(20)` with eager loading (`->with('depenses')` for receipts, `->with('recu')` for expenses).

7. **Route name fix** — `RecuController::destroy()` redirects to `route('recus.index')` instead of the broken `route('recu.index')`.

## Risks / Trade-offs

- **Risk: Existing inline logic in job `handle()` already works** → Mitigation: Wrap in ExtractionService without changing behavior; validate before persisting.
- **Risk: Policy auto-discovery fails** → Mitigation: Register policies explicitly in `AppServiceProvider` if needed.
- **Risk: `AI::fake()` signature differs from actual SDK** → Mitigation: Check the laravel/ai fake contract before writing tests; adjust as needed.
- **Trade-off: Keeping French names means divergence from AGENTS.md** → Acceptable because the spec says names are examples, and French is the project language.
