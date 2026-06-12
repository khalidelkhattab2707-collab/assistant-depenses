## Why

Le SDK `laravel/ai` est déjà installé en dépendance Composer et déjà présent dans `vendor/`, mais la configuration publiée (`config/ai.php`) n'est pas utilisée correctement : le fournisseur par défaut est codé en dur à `'openai'` au lieu de lire `AI_PROVIDER` depuis `.env`, et l'entrée Groq ne définit pas de modèle. Sans ces corrections, l'application ne peut pas utiliser Groq via `AI::structured()`.

## What Changes

- Rendre le fournisseur par défaut de `config/ai.php` dynamique via `env('AI_PROVIDER', 'openai')`
- Ajouter la configuration du modèle Groq dans `config/ai.php` (lecture de `GROQ_MODEL` depuis `.env`)
- Vérifier que le SDK fonctionne avec Groq en lançant les tests existants

## Capabilities

### New Capabilities
- (aucune — le SDK est déjà installé, il s'agit de configuration uniquement)

### Modified Capabilities
- `ai-extraction`: La configuration du provider Groq (modèle par défaut) change — les appels `AI::structured()` utiliseront désormais Groq comme fournisseur par défaut

## Impact

- `config/ai.php` — 2 lignes modifiées
- `.env` — déjà configuré, aucune modification
- Les tests existants mockent `AI::structured()` via `Ai::shouldReceive()`, donc aucun impact
