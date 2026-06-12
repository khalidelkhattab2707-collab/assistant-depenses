## MODIFIED Requirements

### Requirement: AI extraction uses structured output
#### Scenario: Valid AI response with image creates depenses
- **WHEN** the AI receives both text and an image
- **THEN** the extraction processes the image content using multimodal vision
- **AND** the receipt statut is set to `traite`, `Depense` records are created
