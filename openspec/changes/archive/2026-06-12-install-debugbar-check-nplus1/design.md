## Context

L'application utilise Laravel 13 avec MySQL. Les contrôleurs existants ont déjà quelques `->with()` mais `RecuController::show()` ne charge pas les dépenses en eager, ce qui produit une requête N+1 dans la vue `recus/show.blade.php`.

Debugbar (`barryvdh/laravel-debugbar`) est un package de dev qui ajoute une barre d'outils en bas de chaque page avec le nombre de requêtes SQL, les temps d'exécution, etc.

## Goals / Non-Goals

**Goals:**
- Installer Debugbar en dépendance dev (`--dev`)
- Ajouter `->with('depenses')` dans `RecuController::show()`
- Vérifier visuellement que toutes les pages listent zéro requête N+1
- Documenter le process de vérification

**Non-Goals:**
- Optimisation des requêtes existantes (hors N+1)
- Cache ou indexation avancée
- Tests automatisés de performance

## Decisions

| Decision | Choice | Rationale |
|---|---|---|
| Package | `barryvdh/laravel-debugbar` | Standard Laravel, intégration Blade native, aucun changement de code nécessaire |
| Installation | `composer require --dev` | Dépendance de développement uniquement, jamais en production |
| Correction N+1 | `->with('depenses')` sur `show()` | Solution la plus simple, pas de changement architectural |

## Risks / Trade-offs

- [Debugbar actif en local] → Installé en `--dev`, jamais chargé en production
- [Oubli de futurs N+1] → Documenter le process de vérification Debugbar dans AGENTS.md ou specs
