## Purpose

Définir le comportement de l'extraction IA asynchrone : dispatch en queue, structured output via `laravel/ai`, validation du schéma, et gestion des erreurs.

## Requirements

### Requirement: AI extraction runs asynchronously via queue
When a receipt is created, the `ExtraireDepensesDuRecu` job SHALL be dispatched to the queue. The job SHALL attempt up to 3 times with a 60-second backoff.

#### Scenario: Job is dispatched after receipt creation
- **WHEN** a receipt is created via `RecuService::create()`
- **THEN** the `ExtraireDepensesDuRecu` job is dispatched with the receipt

### Requirement: AI extraction uses structured output
The extraction SHALL use the `laravel/ai` SDK's `AI::structured()` method with a JSON schema. The SDK SHALL be configured to use Groq as the default provider via `AI_PROVIDER` env variable and `GROQ_MODEL` env variable for the model name. The Job `ExtraireDepensesDuRecu` SHALL define `buildPrompt()` and `getJsonSchema()` methods. The response SHALL be validated with `Validator::make()` before persistence.

#### Scenario: SDK uses configured Groq provider
- **WHEN** `AI::structured()` is called
- **THEN** the SDK uses the provider and model specified by `AI_PROVIDER` and `GROQ_MODEL` in `.env`

#### Scenario: Falls back to OpenAI
- **WHEN** `AI_PROVIDER` is not set
- **THEN** the SDK defaults to OpenAI as provider

#### Scenario: Job builds prompt and schema
- **WHEN** the `ExtraireDepensesDuRecu` job runs
- **THEN** it builds the prompt via `buildPrompt()` and the schema via `getJsonSchema()`
- **AND** calls `AI::structured()` directly from `handle()`

#### Scenario: Valid AI response creates depenses
- **WHEN** the AI returns valid structured data with articles
- **THEN** the receipt statut is set to `traite`, `Depense` records are created, and `total_estime` / `payload_ia` are updated

#### Scenario: Valid AI response with image creates depenses
- **WHEN** the AI receives both text and an image
- **THEN** the extraction processes the image content using multimodal vision
- **AND** the receipt statut is set to `traite`, `Depense` records are created

#### Scenario: Raw text is saved alongside structured data
- **WHEN** the AI returns a valid structured response with both `structured` and `text` properties
- **THEN** `payload_ia` contains both `structured` (validated data) and `raw_text` (original JSON string)
- **AND** depenses are created in a single bulk insert query

#### Scenario: Invalid AI response sets status to failed
- **WHEN** the AI returns data that fails schema validation
- **THEN** a `RuntimeException` is thrown and the job's `failed()` method sets receipt statut to `echoue`

#### Scenario: Job failure stores error message and logs exception
- **WHEN** the job throws an exception after exhausting retries
- **THEN** the receipt statut is set to `echoue`
- **AND** the exception message is stored in `payload_ia` as `{ "error": "<message>" }`
- **AND** the exception is logged via `Log::error()` with receipt ID context

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

#### Scenario: Depenses are created via bulk insert on success
- **WHEN** validation passes
- **THEN** all depenses are created with a single `insert()` call
