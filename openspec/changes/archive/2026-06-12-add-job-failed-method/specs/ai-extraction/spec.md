## MODIFIED Requirements

### Requirement: Job failure sets status to failed

#### Scenario: Job failure stores error message and logs exception
- **WHEN** the job throws an exception after exhausting retries
- **THEN** the receipt statut is set to `echoue`
- **AND** the exception message is stored in `payload_ia` as `{ "error": "<message>" }`
- **AND** the exception is logged via `Log::error()` with receipt ID context
