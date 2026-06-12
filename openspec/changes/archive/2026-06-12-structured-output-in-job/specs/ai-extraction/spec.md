## MODIFIED Requirements

### Requirement: AI extraction uses structured output
The extraction SHALL use the `laravel/ai` SDK's `AI::structured()` method with a JSON schema. The Job `ExtraireDepensesDuRecu` SHALL define `buildPrompt()` and `getJsonSchema()` methods. The response SHALL be validated with `Validator::make()` before persistence.

#### Scenario: Job builds prompt and schema
- **WHEN** the `ExtraireDepensesDuRecu` job runs
- **THEN** it builds the prompt via `buildPrompt()` and the schema via `getJsonSchema()`
- **AND** calls `AI::structured()` directly from `handle()`
