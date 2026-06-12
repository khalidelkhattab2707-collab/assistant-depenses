## Why

When the extraction job fails, the `failed()` method only sets the receipt status to `echoue` without capturing why. The user sees "Échoué" with no explanation, making debugging impossible and eroding trust in the AI extraction feature.

## What Changes

- Enhance `ExtraireDepensesDuRecu::failed()` to store the exception message in `recu.payload_ia`
- Log the exception with context (receipt ID, error message) for admin debugging
- Update the existing test to verify error details are persisted

## Capabilities

### New Capabilities
- *(none)*

### Modified Capabilities
- `ai-extraction`: Enhance the job failure handling — the `failed()` method SHALL persist the error message in `payload_ia` and log the exception

## Impact

- `app/Jobs/ExtraireDepensesDuRecu.php` — modify `failed()` method
- `tests/Feature/ExtractionTest.php` — update failed scenario assertion
