## Purpose

Fournir la liste paginée des dépenses de l'utilisateur avec filtrage par catégorie et labels formatés.

## Requirements

### Requirement: Authenticated user can list their expenses
A user SHALL see a paginated list of expenses from their own receipts, filterable by category.

#### Scenario: Paginated expense list
- **WHEN** an authenticated user GETs `/depenses`
- **THEN** they see up to 20 expenses per page with category label, libelle, quantite, and prix_unitaire

#### Scenario: Filter expenses by category
- **WHEN** a user GETs `/depenses?categorie=boissons`
- **THEN** only expenses with `categorie` matching `boissons` are shown

#### Scenario: Invalid category filter is ignored
- **WHEN** a user GETs `/depenses?categorie=invalid`
- **THEN** all expenses are shown (filter is ignored)

### Requirement: Category labels are formatted
Each expense SHALL display a human-readable category label instead of the raw enum value.

#### Scenario: Category label displayed
- **WHEN** an expense with `categorie` = `boissons` is rendered
- **THEN** the label "Boissons" is displayed

### Requirement: User cannot see other users' expenses
A user SHALL only see expenses linked to their own receipts.

#### Scenario: Scope to own receipts
- **WHEN** a user lists expenses
- **THEN** only expenses belonging to their receipts are returned
