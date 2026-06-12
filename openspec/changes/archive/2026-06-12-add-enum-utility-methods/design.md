## Context

Les enums PHP `StatutRecu` et `CategorieDepense` sont utilisés dans toute l'application (modèles, vues, contrôleurs). La méthode `label()` existe déjà. Cependant, la couleur Bootstrap pour les badges de statut est dupliquée dans deux vues Blade (`index.blade.php` et `show.blade.php`) via des blocs `@php match(...)`. De même, la liste des valeurs pour la validation est construite manuellement dans `DepenseController::index()`.

## Goals / Non-Goals

**Goals:**
- Ajouter `color(): string` à `StatutRecu` (ex: `EnAttente → warning`, `Traite → success`, `Echoue → danger`)
- Ajouter `values(): array` aux deux enums (retourne les valeurs scalaires)
- Remplacer les blocs `@php match(...)` dans les vues par `$recu->status->color()`
- Simplifier `DepenseController::index()` avec `Rule::in(CategorieDepense::values())`

**Non-Goals:**
- Modifier les noms des cas ou valeurs des enums
- Ajouter ou supprimer des cas d'enum
- Modifier les modèles Eloquent ou les migrations

## Decisions

| Décision | Alternative | Raison |
|---|---|---|
| `color()` sur `StatutRecu` seulement | `color()` sur les deux enums | `CategorieDepense` n'a pas de couleur spécifique — badge gris `bg-secondary` suffit |
| `values()` comme méthode statique | `values()` comme méthode d'instance | Usage typique : `Rule::in(CategorieDepense::values())` — pas besoin d'instance |
| Couleurs Bootstrap : `EnAttente → warning`, `Traite → success`, `Echoue → danger` | Autre mapping | Correspond aux conventions UX : jaune=attente, vert=réussi, rouge=échec |

## Détail des méthodes

### StatutRecu
```php
public function color(): string {
    return match($this) {
        self::EnAttente => 'warning',
        self::Traite    => 'success',
        self::Echoue    => 'danger',
    };
}

public static function values(): array {
    return array_column(self::cases(), 'value');
}
```

### CategorieDepense
```php
public static function values(): array {
    return array_column(self::cases(), 'value');
}
```

## Risks / Trade-offs

| Risque | Mitigation |
|---|---|
| `array_column()` PHP < 8.3 ne fonctionne pas avec les enum cases | PHP 8.4+ requis par le projet. Compatible. |
| Une vue oublié utilise encore `match` en dur | Audit des vues Blade après la modification |
