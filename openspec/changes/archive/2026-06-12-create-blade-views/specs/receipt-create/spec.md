## ADDED Requirements

### Requirement: Receipt creation form
The system SHALL display a form where the authenticated user can paste receipt text and submit it for AI extraction.

#### Scenario: Display creation form
- **WHEN** an authenticated user navigates to `/recu/create`
- **THEN** the system displays a form with a textarea labeled "Texte du reçu fournisseur"
- **THEN** the textarea has 12 rows and a placeholder explaining the accepted input
- **THEN** a submit button labeled "Lancer l'extraction IA" is present
- **THEN** an "Annuler" button links back to the receipt list

#### Scenario: Submit receipt text
- **WHEN** the authenticated user submits the form with valid receipt text
- **THEN** the system creates a new receipt with status "En attente"
- **THEN** the system dispatches the extraction job asynchronously
- **THEN** the user is redirected to the receipt list with a success flash message "Reçu en cours de traitement."

#### Scenario: Validation error on empty text
- **WHEN** the authenticated user submits the form with empty text
- **THEN** the system displays a validation error below the textarea
- **THEN** the form is not submitted
