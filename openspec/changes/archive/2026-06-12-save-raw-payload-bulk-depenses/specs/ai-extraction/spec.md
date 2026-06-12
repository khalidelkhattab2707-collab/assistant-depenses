## MODIFIED Requirements

### Requirement: AI extraction uses structured output
#### Scenario: Raw text is saved alongside structured data
- **WHEN** the AI returns a valid structured response with both `structured` and `text` properties
- **THEN** `payload_ia` contains both `structured` (validated data) and `raw_text` (original JSON string)
- **AND** depenses are created in a single bulk insert query

### Requirement: AI response validation enforces schema
#### Scenario: Depenses are created via bulk insert on success
- **WHEN** validation passes
- **THEN** all depenses are created with a single `insert()` call
