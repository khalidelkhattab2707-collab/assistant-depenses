## Why

Les enums `StatutRecu` et `CategorieDepense` ont chacun une méthode `label()` mais aucune méthode utilitaire pour obtenir la couleur Bootstrap associée (badges) ou la liste des valeurs pour les règles de validation et les filtres. Actuellement, la logique de couleur est dupliquée dans les vues Blade (`match`/`@php` dans `index.blade.php` et `show.blade.php`), et la validation des catégories dans `DepenseController::index()` utilise un `array_map` manuel sur les `cases()`.

## What Changes

- Ajouter `color(): string` à `StatutRecu` — retourne `success`, `warning`, `danger` selon le statut
- Ajouter `values(): array` aux deux enums — retourne un tableau des valeurs scalaires pour validation/dropdowns
- Supprimer la logique de couleur en dur dans les vues Blade (`match`/`@php`) — utiliser `$recu->status->color()` à la place

## Capabilities

### New Capabilities
- *(aucune — changement purement technique, pas de nouvelle spec)*

### Modified Capabilities
- *(aucune — les specs existantes ne changent pas de comportement)*

## Impact

- `app/Enums/StatutRecu.php` — ajout de `color()` et `values()`
- `app/Enums/CategorieDepense.php` — ajout de `values()`
- `resources/views/recus/index.blade.php` — remplacer le `match`/`@php` par `$recu->status->color()`
- `resources/views/recus/show.blade.php` — remplacer le `match`/`@php` par `$recu->status->color()`
- `app/Http/Controllers/DepenseController.php` — simplifier la validation de catégorie avec `Rule::in(CategorieDepense::values())`
- `app/Http/Requests/StoreRecuRequest.php` — utiliser `Rule::in(CategorieDepense::values())` si applicable (actuellement devise, pas catégorie)
