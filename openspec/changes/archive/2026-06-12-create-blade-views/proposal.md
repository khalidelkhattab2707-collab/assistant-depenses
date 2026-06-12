## Why

L'application dispose déjà de l'infrastructure backend (modèles, enums, policies, etc.) mais ne possède pas d'interface utilisateur pour interagir avec les reçus et dépenses. Les vues Blade sont nécessaires pour permettre à Si Brahim de soumettre un reçu, voir ses reçus, consulter le détail d'un reçu avec ses dépenses, et filtrer ses dépenses par catégorie.

## What Changes

- Création de la vue `recus/index.blade.php` — liste paginée des reçus avec statut et nombre de dépenses
- Création de la vue `recus/create.blade.php` — formulaire de soumission d'un reçu (texte brut)
- Création de la vue `recus/show.blade.php` — détail d'un reçu : texte source, statut, dépenses associées
- Création de la vue `depenses/index.blade.php` — liste paginée des dépenses avec filtre par catégorie
- Les contrôleurs `RecuController` et `DepenseController` doivent retourner ces vues

## Capabilities

### New Capabilities
- `receipt-list`: Afficher la liste paginée des reçus de l'utilisateur connecté avec leur statut formaté (Pending/Processed/Failed) et le compteur de dépenses
- `receipt-create`: Afficher un formulaire pour coller le texte brut d'un reçu et le soumettre
- `receipt-detail`: Afficher le détail d'un reçu (texte source, statut, dépenses extraites structurées)
- `expense-list`: Afficher la liste paginée de toutes les dépenses de l'utilisateur, filtrable par catégorie avec labels formatés

### Modified Capabilities
- *(none — ce changement introduit les premières vues)*

## Impact

- Contrôleurs : `RecuController` et `DepenseController` doivent injecter les données nécessaires aux vues (eager loading, pagination)
- Vues Blade : 4 nouvelles vues sous `resources/views/recus/` et `resources/views/depenses/`
- Routes : les routes existantes doivent être reliées aux vues
- Layout Breeze : les vues utilisent le layout `layouts.app` fourni par Laravel Breeze
- Navigation : le menu Breeze doit inclure des liens vers les pages reçus et dépenses
