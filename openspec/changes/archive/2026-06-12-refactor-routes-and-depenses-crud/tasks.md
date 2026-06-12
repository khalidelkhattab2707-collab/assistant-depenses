## 1. RecuController & Routes

- [x] 1.1 Add missing `update()` method to `RecuController` with authorization and redirect
- [x] 1.2 Replace manual recu routes in `web.php` with `Route::resource('recus', RecuController::class)`
- [x] 1.3 Create `recus/edit.blade.php` view (pre-filled form with status, devis, total_estime)

## 2. DepenseController CRUD

- [x] 2.1 Add `create()` and `store()` methods to `DepenseController` (form + validation + persist)
- [x] 2.2 Add `show()` method to `DepenseController` (detail view with authorization)
- [x] 2.3 Add `edit()` and `update()` methods to `DepenseController` (form + validation + update)
- [x] 2.4 Add `destroy()` method to `DepenseController` (delete with authorization + cascade)
- [x] 2.5 Add `Route::resource('depenses', DepenseController::class)` to `web.php`

## 3. DepensePolicy

- [x] 3.1 Add `create()`, `update()`, `delete()` methods to `DepensePolicy`

## 4. Depenses Views

- [x] 4.1 Create `depenses/create.blade.php` (form with libelle, quantite, prix_unitaire, categorie select)
- [x] 4.2 Create `depenses/edit.blade.php` (pre-filled edit form)
- [x] 4.3 Create `depenses/show.blade.php` (detail view)

## 5. Verify

- [x] 5.1 Run `php artisan test` to ensure all tests pass
- [x] 5.2 Run `php artisan route:list` to verify all resource routes are registered
