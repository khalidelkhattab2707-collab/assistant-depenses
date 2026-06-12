## MODIFIED Requirements

### Requirement: Authenticated user can create a receipt
An authenticated user SHALL submit a receipt's raw text. The system SHALL persist the receipt with status `en_attente` and dispatch an async extraction job.

#### Scenario: Successful receipt creation
- **WHEN** an authenticated user POSTs to `/recu` with valid `texte_brut`
- **THEN** the receipt is created with `statut` = `en_attente` and a success flash message is returned
- **AND** the `ExtraireDepensesDuRecu` job is dispatched

#### Scenario: Unauthenticated user cannot create a receipt
- **WHEN** an unauthenticated user POSTs to `/recu`
- **THEN** they are redirected to the login page

#### Scenario: Validation fails with empty text
- **WHEN** a user submits an empty `texte_brut`
- **THEN** the form is re-displayed with validation errors

#### Scenario: Validation fails with too-short text
- **WHEN** a user submits `text_brut` with fewer than 10 characters
- **THEN** the system rejects with validation error: "Le texte du reçu doit contenir au moins 10 caractères."

#### Scenario: Validation fails with too-long text
- **WHEN** a user submits `text_brut` exceeding 10 000 characters
- **THEN** the system rejects with validation error: "Le texte du reçu ne peut pas dépasser 10 000 caractères."

#### Scenario: Validation rejects invalid currency
- **WHEN** a user submits `devis` with a value other than MAD, EUR, or USD
- **THEN** the system rejects with validation error: "La devise sélectionnée est invalide."

#### Scenario: Validation rejects negative total
- **WHEN** a user submits `total_estime` with a negative value
- **THEN** the system rejects with validation error: "Le total estimé doit être un nombre positif."
