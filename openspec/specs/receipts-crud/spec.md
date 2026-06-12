## Purpose

Founir les opérations CRUD backend pour les reçus : création, liste paginée, consultation détaillée, suppression avec cascade.

## Requirements

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

### Requirement: Authenticated user can list their receipts
A user SHALL see paginated list of their own receipts, each showing status label and depense count.

#### Scenario: Paginated receipt list
- **WHEN** an authenticated user GETs `/recu`
- **THEN** they see up to 20 receipts per page with status label and depense count

#### Scenario: User cannot see other users' receipts
- **WHEN** a user accesses the list
- **THEN** only their own receipts are shown

### Requirement: Authenticated user can view a single receipt
A user SHALL view a receipt's source text, status, and associated expenses.

#### Scenario: View own receipt
- **WHEN** an authenticated user GETs `/recu/{recu}`
- **THEN** they see the receipt source text, status, and depenses list

#### Scenario: Cannot view another user's receipt
- **WHEN** a user GETs `/recu/{recu}` belonging to another user
- **THEN** a 403 response is returned

### Requirement: Authenticated user can delete a receipt
A user SHALL delete their own receipt. The deletion SHALL cascade to associated expenses.

#### Scenario: Delete own receipt
- **WHEN** an authenticated user DELETEs `/recu/{recu}`
- **THEN** the receipt and its expenses are deleted, and user is redirected with a success message

#### Scenario: Cannot delete another user's receipt
- **WHEN** a user DELETEs `/recu/{recu}` belonging to another user
- **THEN** a 403 response is returned
