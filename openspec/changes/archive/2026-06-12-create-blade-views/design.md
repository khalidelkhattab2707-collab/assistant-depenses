## Context

L'application dispose de l'infrastructure backend complète : modèles `Recu` et `Depense` avec leurs relations, enums `StatutRecu` et `CategorieDepense`, policies, services, jobs et contrôleurs. Les vues Blade sont partiellement implémentées avec Bootstrap 5 (layout `layouts.app`), mais la vue `depenses/index.blade.php` est un placeholder vide. Plusieurs bugs existent dans les vues existantes (noms d'attributs incorrects).

## Goals / Non-Goals

**Goals:**
- Avoir des vues fonctionnelles pour les 4 pages principales : liste des reçus, création d'un reçu, détail d'un reçu, liste des dépenses
- Corriger les bugs dans les vues existantes (attributs `text_brut` vs `texte_brut`, `status` vs `statut`)
- Utiliser Bootstrap 5 (framework déjà en place dans `layouts.app`) pour toutes les vues métier
- Ajouter la pagination Bootstrap sur les listes
- Ajouter les liens vers Reçus et Dépenses dans la navigation `layouts.app`

**Non-Goals:**
- Migration vers TailwindCSS pour les vues métier
- Refonte du layout Breeze (auth, profil)
- Ajout de nouvelles fonctionnalités backend
- Modification des contrôleurs, services ou modèles

## Decisions

| Décision | Alternative | Raison |
|---|---|---|
| Bootstrap 5 pour les vues métier | TailwindCSS | Le layout `layouts.app` utilise déjà Bootstrap 5. Harmoniser avec le choix existant. |
| Pagination Bootstrap (`$recus->links()`) | Pagination manuelle | Laravel fournit une pagination Bootstrap native. Simple et cohérent. |
| Layout `layouts.app` pour toutes les vues métier | Layout Breeze Tailwind | Les vues recus/depenses héritent déjà de `layouts.app`. Pas de raison de changer. |
| Badge de statut avec classes Bootstrap | Classes personnalisées | `bg-success`, `bg-warning`, `bg-danger` sont standard Bootstrap. |
| Filtre catégorie via query string `?categorie=` | Filtre AJAX / Livewire | Approche simple, sans dépendance JS supplémentaire. Rechargement de page acceptable. |

## Structure des vues

```
resources/views/
├── layouts/
│   └── app.blade.php         ← Layout Bootstrap avec nav (Reçus, Dépenses)
├── recus/
│   ├── index.blade.php       ← Liste paginée (bugs à corriger)
│   ├── create.blade.php      ← Formulaire (OK)
│   └── show.blade.php       ← Détail reçu + dépenses (OK)
└── depenses/
    └── index.blade.php       ← Liste filtrable (À implémenter)
```

## Données injectées par les contrôleurs

| Vue | Contrôleur | Données |
|---|---|---|
| `recus.index` | `RecuController::index()` | `$recus` — pagination 20, eager loading `depenses` |
| `recus.create` | `RecuController::create()` | — |
| `recus.show` | `RecuController::show($recu)` | `$recu` — avec `depenses` chargées (eager loading dans le contrôleur) |
| `depenses.index` | `DepenseController::index($request)` | `$depenses` paginées 20, `$categories` (liste des enum cases) |

## Bugs à corriger dans les vues existantes

| Fichier | Ligne | Code incorrect | Correction |
|---|---|---|---|
| `recus/index.blade.php` | 18 | `$recu->texte_brut` | `$recu->text_brut` |
| `recus/index.blade.php` | 20 | `match($recu->statut)` | `match($recu->status)` |

## Pagination

Les deux listes (`recus.index`, `depenses.index`) utilisent `->paginate(20)`. L'affichage de la pagination utilise `{{ $recus->links() }}` et `{{ $depenses->links() }}` qui génèrent du HTML compatible Bootstrap 5 par défaut avec `Illuminate\Pagination\Paginator::useBootstrapFive()`.

## Navigation

Le layout `layouts/app.blade.php` inclut déjà les liens Reçus et Dépenses dans la navbar. Vérifier que les routes correspondent.

## Risks / Trade-offs

| Risque | Mitigation |
|---|---|
| Pagination Bootstrap non stylée si `useBootstrapFive()` non appelé | Ajouter `Paginator::useBootstrapFive()` dans `AppServiceProvider::boot()` |
| Formulaire create soumet `text_brut` mais le modèle s'attend à `texte_brut` | Vérifier que le StoreRecuRequest et le RecuService utilisent `text_brut` — c'est déjà le cas |
| Les enums `StatutRecu::cases()` pourraient ne pas correspondre aux valeurs attendues | Utiliser l'enum directement dans les vues pour les labels et les couleurs |
