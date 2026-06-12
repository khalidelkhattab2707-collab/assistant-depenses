## Why

Actuellement, l'utilisateur ne peut coller que du texte brut. Beaucoup de reçus fournisseurs sont sous format papier ou image (photo smartphone). L'utilisateur doit recopier manuellement le texte, ce qui est pénible et source d'erreurs. L'ajout du support image avec un modèle multimodal IA permet d'extraire directement les articles depuis une photo.

## What Changes

- Nouvelle colonne `image_path` nullable sur `recus` (stockage local `public/receipts/`)
- Upload d'image dans le formulaire de création de reçu (format jpg/png/webp, max 10 Mo)
- Affichage de l'image dans la vue détail du reçu
- Envoi de l'image au modèle multimodal via `AI::structured()` avec contexte texte facultatif
- Aucune breaking change — `text_brut` reste obligatoire, l'image est optionnelle

## Capabilities

### New Capabilities
- `receipt-image-upload`: Upload, stockage et affichage d'images de reçus

### Modified Capabilities
- `ai-extraction`: Le prompt d'extraction peut inclure une image en plus du texte brut
- `receipt-create`: Le formulaire de création accepte un fichier image optionnel

## Impact

- `database/migrations/` — nouvelle migration pour ajouter `image_path` à `recus`
- `app/Http/Requests/StoreRecuRequest.php` — ajouter règle de validation pour l'image
- `app/Services/RecuService.php` — gérer l'upload et stocker le chemin
- `app/Services/ExtractionService.php` — passer l'image au prompt multimodal
- `app/Models/Recu.php` — ajouter `image_path` aux `$fillable`
- `resources/views/recus/create.blade.php` — champ file upload
- `resources/views/recus/show.blade.php` — affichage de l'image uploadée
