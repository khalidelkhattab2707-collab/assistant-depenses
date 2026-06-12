## Why

The receipts CRUD exists but violates the AGENTS.md architecture: controllers contain business logic, the service layer is missing, policies are absent, AI extraction is not triggered from the controller, and there is no test coverage. We need to refactor the existing code to match the mandated architecture before adding new features.

## What Changes

- Extract business logic from `RecuController` into `RecuService`
- Create `ExtractionService` and have the job delegate to it
- Create `RecuPolicy` and `DepensePolicy` for authorization
- Dispatch `ExtraireDepensesDuRecu` job from `RecuService::create()`
- Add proper AI response validation in the extraction flow
- Add pagination to receipts and expenses lists
- Add Pest feature tests for submission and extraction
- Create `specs/` OpenSpec documentation for the foundation

## Capabilities

### New Capabilities
- `receipts-crud`: Create, list, show, and delete receipts with proper service layer, policies, job dispatch, and pagination
- `ai-extraction`: Async AI extraction from receipt text using structured output via `laravel/ai` SDK with validated response handling
- `expenses-listing`: List expenses filtered by category with pagination and formatted labels

### Modified Capabilities
None — no existing specs to modify.

## Impact

- `app/Http/Controllers/RecuController.php`: Refactor to delegate to `RecuService`
- `app/Http/Controllers/DepenseController.php`: Add pagination
- `app/Jobs/ExtraireDepensesDuRecu.php`: Delegate to `ExtractionService`
- `app/Models/Recu.php`, `app/Models/Depense.php`: Ensure proper casts
- New files: `app/Services/RecuService.php`, `app/Services/ExtractionService.php`, `app/Policies/RecuPolicy.php`, `app/Policies/DepensePolicy.php`
- New tests: `tests/Feature/RecuSubmissionTest.php`, `tests/Feature/ExtractionTest.php`
- New specs: `openspec/specs/receipts-crud/`, `openspec/specs/ai-extraction/`, `openspec/specs/expenses-listing/`
