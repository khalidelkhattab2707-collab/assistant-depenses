## Why

Le `StoreRecuRequest` actuel valide seulement que `text_brut` est une chaîne requise, sans contrainte de longueur ni validation du champ `devis`. Un reçu vide ou trop court n'a pas de sens pour l'extraction IA, et la devise devrait être restreinte à des valeurs connues.

## What Changes

- Ajouter une contrainte `min:10` sur `text_brut` pour éviter les soumissions vides ou trop courtes
- Ajouter une contrainte `max:10000` sur `text_brut` pour limiter la taille
- Restreindre `devis` aux valeurs autorisées via `Rule::in(['MAD', 'EUR', 'USD'])`
- Ajouter `min:0` sur `total_estime`
- Ajouter des messages de validation personnalisés en français

## Capabilities

### New Capabilities
- *(aucune — ce changement modifie un fichier existant)*

### Modified Capabilities
- `receipts-crud`: Renforcer la validation du formulaire de création de reçu

## Impact

- `app/Http/Requests/StoreRecuRequest.php` — mise à jour des règles de validation
- Le contrôleur `RecuController::store()` et le service `RecuService::create()` ne changent pas
- Les tests existants doivent continuer à passer
