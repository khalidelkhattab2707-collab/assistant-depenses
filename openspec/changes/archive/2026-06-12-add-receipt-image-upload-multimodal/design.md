## Context

Actuellement `recus` n'a pas de colonne pour stocker une image. Le formulaire de création n'accepte que du texte. Le service d'extraction envoie uniquement `text_brut` dans le prompt.

## Goals / Non-Goals

**Goals:**
- Ajouter `image_path` (string, nullable) à la table `recus`
- Upload d'image (jpg/png/webp, max 10 Mo) vers `storage/app/public/receipts/`
- Afficher l'image dans la vue détail du reçu
- Envoyer l'image comme contexte multimodal dans le prompt IA
- Aucune régression sur le flux existant (texte seul reste supporté)

**Non-Goals:**
- Redimensionnement ou optimisation d'image
- Support de plusieurs images par reçu
- OCR pur (l'IA multimodale s'en charge)

## Decisions

| Decision | Choice | Rationale |
|---|---|---|
| Stockage | `storage/app/public/receipts/` | Simple, pas de service externe. Lien symbolique `php artisan storage:link` |
| Validation | 10 Mo max, jpg/png/webp | Standards web, taille raisonnable pour une photo de reçu |
| SDK IA | `AI::structured()` avec paramètre `images` | Le SDK `laravel/ai` supporte le multimodal via le paramètre `images` (tableau de chemins ou URLs) |
| Image optionnelle | `nullable` | L'utilisateur peut toujours coller du texte seul |

## Risks / Trade-offs

- [Taille d'image] → Limite à 10 Mo, le SDK convertit en base64 si nécessaire
- [Modèle non multimodal] → Si le fournisseur IA ne supporte pas les images, le job `failed()` capture l'erreur normalement
- [Stockage public] → Les images sont dans `public/` accessibles via URL ; acceptable car l'utilisateur est propriétaire de ses données et les routes sont authentifiées
