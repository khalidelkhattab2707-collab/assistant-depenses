## ADDED Requirements

### Requirement: Receipt detail page
The system SHALL display the full details of a single receipt including its source text, status, and extracted expenses.

#### Scenario: View receipt with expenses
- **WHEN** an authenticated user navigates to `/recu/{recu}`
- **THEN** the system displays the receipt status as a colored badge
- **THEN** the system displays the full receipt source text in a pre-formatted block
- **THEN** the system displays a table of extracted expenses with columns: Libellé, Qté, Prix unit., Catégorie
- **THEN** each expense shows the category as a formatted label from the enum
- **THEN** each price is formatted with 2 decimal places and "MAD" suffix
- **THEN** a "← Retour" button links back to the receipt list

#### Scenario: View receipt while extraction is pending
- **WHEN** the receipt status is "En attente"
- **THEN** the system displays an info message: "Extraction en cours… Rafraîchis la page dans quelques secondes."

#### Scenario: View receipt with failed extraction
- **WHEN** the receipt status is "Échoué"
- **THEN** the system displays an info message: "L'extraction a échoué. Essaie de soumettre à nouveau."

#### Scenario: Authorization - cannot view another user's receipt
- **WHEN** an authenticated user tries to navigate to a receipt belonging to another user
- **THEN** the system returns a 403 Forbidden response
