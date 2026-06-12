## ADDED Requirements

### Requirement: AI extraction runs asynchronously via queue
When a receipt is created, the `ExtraireDepensesDuRecu` job SHALL be dispatched to the queue. The job SHALL attempt up to 3 times with a 60-second backoff.

#### Scenario: Job is dispatched after receipt creation
- **WHEN** a receipt is created via `RecuService::create()`
- **THEN** the `ExtraireDepensesDuRecu` job is dispatched with the receipt

### Requirement: AI extraction uses structured output
The extraction SHALL use the `laravel/ai` SDK's `AI::structured()` method with a JSON schema. The response SHALL be validated with `Validator::make()` before persistence.

#### Scenario: Valid AI response creates depenses
- **WHEN** the AI returns valid structured data with articles
- **THEN** the receipt statut is set to `traite`, `Depense` records are created, and `total_estime` / `payload_ia` are updated

#### Scenario: Invalid AI response sets status to failed
- **WHEN** the AI returns data that fails schema validation
- **THEN** a `RuntimeException` is thrown and the job's `failed()` method sets receipt statut to `echoue`

#### Scenario: Job failure sets status to failed
- **WHEN** the job throws an exception after exhausting retries
- **THEN** the receipt statut is set to `echoue`

### Requirement: AI response validation enforces schema
The system SHALL validate the AI response against a strict schema before persisting any data.

#### Scenario: Schema validation passes
- **WHEN** the AI response contains `articles` array with required fields (`libellé`, `quantité`, `prix_unitaire`, `catégorie`) and valid `total_estimé` / `devise`
- **THEN** the data is persisted

#### Scenario: Schema validation fails on missing field
- **WHEN** the AI response is missing `articles` or a required article field
- **THEN** a `RuntimeException` is thrown

#### Scenario: Schema validation fails on invalid category
- **WHEN** an article has `catégorie` not in the allowed list
- **THEN** a `RuntimeException` is thrown
