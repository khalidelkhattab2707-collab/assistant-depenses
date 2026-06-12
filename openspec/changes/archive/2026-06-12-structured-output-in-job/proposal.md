## Why

Actuellement, le schéma JSON et le prompt de l'extraction IA sont définis dans `ExtractionService`, tandis que le Job `ExtraireDepensesDuRecu` ne fait que déléguer. L'architecture recommandée par `AGENTS.md` veut que le Job porte la responsabilité du structured output (prompt + schéma), pour rendre le contrat IA explicite au niveau du point d'entrée du traitement asynchrone.

## What Changes

- Ajouter `buildPrompt()` et `getJsonSchema()` comme méthodes privées sur `ExtraireDepensesDuRecu`
- `handle()` appelle `AI::structured()` directement avec ces méthodes, puis passe le résultat à `ExtractionService` pour la persistance
- `ExtractionService` ne garde que la logique de validation et persistance (plus d'appel IA direct)
- Aucune breaking change sur le comportement visible

## Capabilities

### New Capabilities
- *(none)*

### Modified Capabilities
- `ai-extraction`: Le Job `ExtraireDepensesDuRecu` porte le prompt et le schéma JSON. `ExtractionService` ne gère plus que la persistance.

## Impact

- `app/Jobs/ExtraireDepensesDuRecu.php` — ajouter `buildPrompt()`, `getJsonSchema()`, modifier `handle()` pour appeler `AI::structured()` directement
- `app/Services/ExtractionService.php` — accepter les données déjà extraites plutôt que d'appeler l'IA
