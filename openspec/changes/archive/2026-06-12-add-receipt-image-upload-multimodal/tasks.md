## 1. Migration & Model

- [x] 1.1 Create migration to add `image_path` (nullable string) to `recus` table
- [x] 1.2 Add `image_path` to `Recu` model `$fillable`
- [x] 1.3 Run `php artisan storage:link` to create public symlink

## 2. Validation & Upload

- [x] 2.1 Add `image` validation rule to `StoreRecuRequest` (jpg/png/webp, max:10240)
- [x] 2.2 Update `RecuService::create()` to handle image upload via `$data['image']`
- [x] 2.3 Update `RecuController::show()` to pass image URL to view

## 3. Extraction

- [x] 3.1 Update `ExtractionService::extraire()` to pass image path to the multimodal AI via `AI::structured()` with `images` parameter

## 4. Views

- [x] 4.1 Add file upload input to `recus/create.blade.php` (with `enctype="multipart/form-data"`)
- [x] 4.2 Display uploaded image in `recus/show.blade.php` when `$recu->image_path` is not null

## 5. Verify

- [x] 5.1 Run `php artisan test` to ensure all tests pass
- [x] 5.2 Run migration to confirm `image_path` column is created
