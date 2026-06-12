## Context

The `ExtraireDepensesDuRecu` job already has a `failed()` method that sets `status = StatutRecu::Echoue`. The `recus` table has a `payload_ia` JSON column that is only populated on successful extraction. On failure, it stays null — the error is silent.

## Goals / Non-Goals

**Goals:**
- Store the exception message in `payload_ia` when the job fails (as `{ "error": "..." }`)
- Log the exception with receipt ID context via `Log::error()`
- Update the existing test to assert the error is stored

**Non-Goals:**
- No UI change (the receipt show view already renders `payload_ia` content via existing view logic)
- No retry logic changes (already `$tries = 3` with `$backoff = 60`)

## Decisions

| Decision | Choice | Rationale |
|---|---|---|
| Storage location | `payload_ia` JSON column | Already exists on `recus` table, cast as `array`, nullable — zero schema changes |
| Error structure | `['error' => $exception->getMessage()]` | Simple, merges with any future metadata. The field is `array` cast so `json_encode` is automatic |
| Logging | `Log::error()` with receipt ID + message | Standard Laravel practice, integrates with Telescope for queue debugging |

## Risks / Trade-offs

- [Persistence race] → The job is serialized with `SerializesModels`, so `$this->recu` is a fresh model instance — update is safe
- [Payload overwrite] → Failure payload overwrites any partial data in `payload_ia` — acceptable, a failed extraction has no valid output
