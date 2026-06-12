## ADDED Requirements

### Requirement: Authenticated user can create a depense
An authenticated user SHALL create a new expense linked to a receipt, providing libelle, quantite, prix_unitaire, and categorie.

#### Scenario: Successful depense creation
- **WHEN** an authenticated user POSTs to `/depenses` with valid data
- **THEN** the depense is created and user is redirected with a success message

#### Scenario: Unauthenticated user cannot create a depense
- **WHEN** an unauthenticated user POSTs to `/depenses`
- **THEN** they are redirected to the login page

### Requirement: Authenticated user can view a single depense
An authenticated user SHALL view the details of a single expense.

#### Scenario: View own depense
- **WHEN** an authenticated user GETs `/depenses/{depense}`
- **THEN** they see the depense details (libelle, quantite, prix_unitaire, categorie)

#### Scenario: Cannot view another user's depense
- **WHEN** a user GETs `/depenses/{depense}` belonging to another user
- **THEN** a 403 response is returned

### Requirement: Authenticated user can edit a depense
An authenticated user SHALL update an existing expense's libelle, quantite, prix_unitaire, or categorie.

#### Scenario: Edit own depense
- **WHEN** an authenticated user GETs `/depenses/{depense}/edit`
- **THEN** they see a pre-filled form with the depense data
- **WHEN** they submit the form with updated data via PUT
- **THEN** the depense is updated and user is redirected with a success message

#### Scenario: Cannot edit another user's depense
- **WHEN** a user PUTs `/depenses/{depense}` belonging to another user
- **THEN** a 403 response is returned

### Requirement: Authenticated user can delete a depense
An authenticated user SHALL delete an existing expense.

#### Scenario: Delete own depense
- **WHEN** an authenticated user DELETEs `/depenses/{depense}`
- **THEN** the depense is deleted and user is redirected with a success message

#### Scenario: Cannot delete another user's depense
- **WHEN** a user DELETEs `/depenses/{depense}` belonging to another user
- **THEN** a 403 response is returned
