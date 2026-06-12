## ADDED Requirements

### Requirement: User can upload a receipt image
The system SHALL allow users to upload an image file (jpg, png, webp) of a receipt when creating a new receipt. The image SHALL be stored at `storage/app/public/receipts/` and the path persisted in `recus.image_path`.

#### Scenario: Successful image upload
- **WHEN** a user submits a receipt with a valid image file (jpg/png/webp, < 10 Mo)
- **THEN** the image is stored and `recus.image_path` contains the relative path
- **AND** the extraction job is dispatched with the image available

#### Scenario: Image upload with oversized file
- **WHEN** a user submits an image larger than 10 Mo
- **THEN** the form is rejected with a validation error

#### Scenario: Image upload with invalid format
- **WHEN** a user submits a file that is not jpg/png/webp
- **THEN** the form is rejected with a validation error

#### Scenario: Receipt created without image
- **WHEN** a user submits only text without an image
- **THEN** the receipt is created normally with `image_path` set to null

### Requirement: User can view the uploaded receipt image
The system SHALL display the uploaded receipt image on the receipt detail page.

#### Scenario: Image visible on show page
- **WHEN** a user views a receipt that has an uploaded image
- **THEN** the image is displayed in the detail view
