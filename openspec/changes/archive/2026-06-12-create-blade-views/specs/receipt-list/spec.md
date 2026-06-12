## ADDED Requirements

### Requirement: Receipt list page
The system SHALL display a paginated list of the authenticated user's receipts with their status and expense count.

#### Scenario: View receipt list
- **WHEN** an authenticated user navigates to `/recu`
- **THEN** the system displays a table with columns: Date, Preview, Status, Articles, Actions
- **THEN** each row shows the receipt creation date formatted as `DD/MM/YYYY HH:MM`
- **THEN** each row shows a truncated preview (50 chars) of the receipt text
- **THEN** each row shows a colored status badge: green for "Traité", red for "Échoué", yellow for "En attente"
- **THEN** each row shows the count of extracted expenses
- **THEN** each row has a "Voir" button linking to the receipt detail page
- **THEN** each row has a "Supprimer" button with confirmation dialog
- **THEN** receipts are ordered by creation date (newest first)
- **THEN** results are paginated (20 per page) with Bootstrap pagination links
- **THEN** a "Nouveau reçu" button links to the create page
- **THEN** if the list is empty, a message "Aucun reçu pour l'instant" is displayed

#### Scenario: Authorization - user sees only own receipts
- **WHEN** an authenticated user views the receipt list
- **THEN** only receipts belonging to that user are displayed
