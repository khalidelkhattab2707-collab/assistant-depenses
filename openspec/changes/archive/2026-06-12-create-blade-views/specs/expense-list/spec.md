## ADDED Requirements

### Requirement: Expense list page
The system SHALL display a paginated list of all expenses from the authenticated user's receipts, filterable by category.

#### Scenario: View all expenses
- **WHEN** an authenticated user navigates to `/depenses`
- **THEN** the system displays a table with columns: Libellé, Quantité, Prix unitaire, Catégorie, Reçu
- **THEN** each expense shows the category as a formatted label from the enum
- **THEN** each price is formatted with 2 decimal places and "MAD" suffix
- **THEN** each row links to the parent receipt detail page
- **THEN** results are paginated (20 per page) with Bootstrap pagination links
- **THEN** if the list is empty, a message "Aucune dépense pour l'instant." is displayed

#### Scenario: Filter expenses by category
- **WHEN** an authenticated user navigates to `/depenses?categorie=boissons`
- **THEN** the system displays only expenses with category "Boissons"
- **THEN** the filter dropdown or nav shows "Boissons" as the active filter
- **THEN** all category options are available for filtering (Alimentaire, Boissons, Hygiène, Entretien, Autre)

#### Scenario: Filter with invalid category
- **WHEN** an authenticated user navigates to `/depenses?categorie=invalide`
- **THEN** the system ignores the invalid filter and displays all expenses

#### Scenario: Authorization - user sees only own expenses
- **WHEN** an authenticated user views the expense list
- **THEN** only expenses from receipts belonging to that user are displayed
