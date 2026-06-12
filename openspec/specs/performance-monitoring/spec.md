# Performance Monitoring — Spec

## Purpose

Détecter et éliminer les requêtes N+1 dans toutes les vues Blade via Laravel Debugbar, garantissant que les pages de liste et de détail chargent les relations avec eager loading.

## Requirements

### Requirement: N+1 queries are detected and eliminated
The system SHALL use Laravel Debugbar to detect N+1 queries on all list and detail pages. Every Blade loop that accesses a relationship SHALL have eager loading via `->with()` in the corresponding controller.

#### Scenario: Debugbar is installed as dev dependency
- **WHEN** `composer install --dev` is run
- **THEN** Debugbar is available and shows SQL query count in the toolbar

#### Scenario: Recu show page has no N+1
- **WHEN** a user views a receipt detail page
- **THEN** `$recu->depenses` is loaded with a single query via `->with('depenses')`
- **AND** Debugbar shows constant query count regardless of number of depenses

#### Scenario: All index pages have no N+1
- **WHEN** a user views any paginated list page
- **THEN** Debugbar shows zero additional queries inside Blade loops
