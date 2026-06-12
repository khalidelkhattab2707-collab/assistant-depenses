## Context

Les routes des reçus sont déclarées manuellement (7 lignes dans `web.php`). La méthode `RecuController::update()` n'existe pas malgré la route PUT. Le `DepenseController` n'a qu'une méthode `index()`, empêchant toute manipulation individuelle des dépenses. Les routes `Route::resource()` simplifient la maintenance et suivent les conventions Laravel.

## Goals / Non-Goals

**Goals:**
- Remplacer les routes manuelles par `Route::resource('recus', RecuController::class)`
- Ajouter `RecuController::update()` (manquant)
- Ajouter 6 méthodes CRUD à `DepenseController` : `create`, `store`, `show`, `edit`, `update`, `destroy`
- Ajouter `Route::resource('depenses', DepenseController::class)`
- Créer les vues manquantes : `recus.edit`, `depenses.create`, `depenses.edit`, `depenses.show`
- Compléter `DepensePolicy` avec les gates `create`, `update`, `delete`

**Non-Goals:**
- Modification du modèle `Depense` ou ajout de colonnes
- Modification des enums existants
- Ajout de nouvelles fonctionnalités métier

## Decisions

| Décision | Raison |
|---|---|
| `Route::resource()` avec names par défaut | Les names actuels (`recus.index`, etc.) correspondent déjà à la convention Laravel |
| Dépenses CRUD protégé par `auth` middleware via le group | Cohérent avec le reste des routes |
| `DepensePolicy` avec `create` retourne `true` | Tout utilisateur connecté peut créer une dépense |
| `update` et `delete` vérifient `$user->id === $depense->recu->user_id` | Même logique que `view()` — propriété du reçu parent |
| Vue `recus/edit.blade.php` similaire à `recus/create.blade.php` | Formulaire pré-rempli avec les données existantes (statut modifiable) |
| Vues dépenses minimalistes (create, edit, show) | Pas de logique complexe — formulaire simple, détail basique |

## Routes générées

### Recus (Route::resource)
| Verb | URI | Action | Route Name |
|---|---|---|---|
| GET | /recu | index | recus.index |
| GET | /recu/create | create | recus.create |
| POST | /recu | store | recus.store |
| GET | /recu/{recu} | show | recus.show |
| GET | /recu/{recu}/edit | edit | recus.edit |
| PUT/PATCH | /recu/{recu} | update | recus.update |
| DELETE | /recu/{recu} | destroy | recus.destroy |

### Dépenses (Route::resource)
| Verb | URI | Action | Route Name |
|---|---|---|---|
| GET | /depenses | index | depenses.index |
| GET | /depenses/create | create | depenses.create |
| POST | /depenses | store | depenses.store |
| GET | /depenses/{depense} | show | depenses.show |
| GET | /depenses/{depense}/edit | edit | depenses.edit |
| PUT/PATCH | /depenses/{depense} | update | depenses.update |
| DELETE | /depenses/{depense} | destroy | depenses.destroy |

## Risks / Trade-offs

| Risque | Mitigation |
|---|---|
| `Route::resource()` change les URI (`/recu` reste identique) | Vérifier les URI générées : `php artisan route:list` |
| La vue `recus/edit.blade.php` n'existe pas — erreur 500 si accès | Créer la vue avant de déployer les routes |
| `DELETE /recu/{recu}` existe déjà — pas de breaking change | Les tests existants couvrent la suppression |
