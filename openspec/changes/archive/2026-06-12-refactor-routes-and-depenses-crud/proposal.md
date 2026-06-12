## Why

Les routes des reçus sont définies manuellement dans `web.php` (7 lignes) au lieu d'utiliser un `Route::resource()`, et la méthode `update()` manque dans `RecuController` alors que la route existe. Le `DepenseController` n'a qu'une méthode `index()` sans CRUD complet, ce qui empêche la création, modification ou suppression d'une dépense individuelle.

## What Changes

- Ajouter la méthode `update()` manquante à `RecuController`
- Remplacer les 7 routes manuelles des reçus par `Route::resource('recus', RecuController::class)`
- Ajouter les méthodes CRUD à `DepenseController` : `create`, `store`, `show`, `edit`, `update`, `destroy`
- Ajouter `Route::resource('depenses', DepenseController::class)`
- Ajouter les vues manquantes : `recus/edit.blade.php`, `depenses/create.blade.php`, `depenses/edit.blade.php`, `depenses/show.blade.php`
- Mettre à jour `DepensePolicy` avec les méthodes `create`, `update`, `delete`

## Capabilities

### New Capabilities
- `depenses-crud`: CRUD complet pour les dépenses (création, modification, consultation, suppression)

### Modified Capabilities
- *(aucune — les specs existantes ne changent pas)*

## Impact

- `routes/web.php` — 7 lignes remplacées par `Route::resource()`, ajout de `Route::resource('depenses')`
- `app/Http/Controllers/RecuController.php` — ajout de la méthode `update()`
- `app/Http/Controllers/DepenseController.php` — 6 nouvelles méthodes CRUD
- `app/Policies/DepensePolicy.php` — ajout des gates `create`, `update`, `delete`
- `resources/views/recus/edit.blade.php` — nouvelle vue (inexistante)
- `resources/views/depenses/create.blade.php` — nouvelle vue
- `resources/views/depenses/edit.blade.php` — nouvelle vue
- `resources/views/depenses/show.blade.php` — nouvelle vue
