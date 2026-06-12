## Why

L'application n'a actuellement aucun outil pour détecter les requêtes N+1 dans les vues Blade. Sans Debugbar, les problèmes de performance passent inaperçus jusqu'à ce que le volume de données augmente et que les pages deviennent lentes. Les specs AGENTS.md exigent explicitement "zéro requête N+1" et une vérification Debugbar avant chaque commit.

## What Changes

- Installer `barryvdh/laravel-debugbar` via Composer
- Charger chaque page listant des données et vérifier le nombre de requêtes SQL
- Ajouter l'eager loading (`->with()`) là où des N+1 sont détectés
- Documenter le processus de vérification

## Capabilities

### New Capabilities
- `performance-monitoring`: Détection et vérification des N+1 via Debugbar

### Modified Capabilities
- *(none)*

## Impact

- `composer.json` — nouvelle dépendance dev
- `app/Http/Controllers/RecuController.php` — ajouter `->with('depenses')` dans index/show
- `app/Http/Controllers/DepenseController.php` — déjà ok, à vérifier
