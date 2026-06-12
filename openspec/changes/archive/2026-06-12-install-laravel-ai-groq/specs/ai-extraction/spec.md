## MODIFIED Requirements

### Requirement: AI extraction uses structured output
The extraction SHALL use the `laravel/ai` SDK's `AI::structured()` method with a JSON schema. The SDK SHALL be configured to use Groq as the default provider via `AI_PROVIDER` env variable and `GROQ_MODEL` env variable for the model name.

#### Scenario: SDK uses configured Groq provider
- **WHEN** `AI::structured()` is called
- **THEN** the SDK uses the provider and model specified by `AI_PROVIDER` and `GROQ_MODEL` in `.env`

#### Scenario: Falls back to OpenAI
- **WHEN** `AI_PROVIDER` is not set
- **THEN** the SDK defaults to OpenAI as provider
