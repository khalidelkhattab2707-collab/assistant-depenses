## Why

Actuellement, `payload_ia` ne stocke que les données structurées validées — le JSON brut retourné par l'IA est perdu. Impossible de debugger ou auditer la réponse exacte du modèle. De plus, les dépenses sont créées une par une dans une boucle, ce qui génère N requêtes INSERT au lieu d'une seule.

## What Changes

- `payload_ia` stocke désormais la réponse complète : `{ "structured": {...}, "raw_text": "..." }` en cas de succès
- Création des dépenses en une seule requête `insert()` au lieu d'une boucle de `create()`
- Aucune breaking change sur le format existant des vues ou API (le champ `payload_ia` reste un `array`)

## Capabilities

### New Capabilities
- *(none)*

### Modified Capabilities
- `ai-extraction`: Le payload sauvegardé inclut le texte brut de la réponse IA + les données structurées. La création des dépenses utilise un insert groupé.

## Impact

- `app/Services/ExtractionService.php` — modifier `payload_ia` pour inclure `raw_text`, remplacer la boucle par `insert()`
- `tests/Feature/ExtractionTest.php` — ajouter assertion sur `payload_ia.raw_text`
