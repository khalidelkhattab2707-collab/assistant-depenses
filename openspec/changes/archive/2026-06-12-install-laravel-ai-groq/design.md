## Context

Le SDK `laravel/ai` v0.8.1 est installé dans `vendor/` et la configuration a été publiée dans `config/ai.php`. Le `.env` contient déjà `AI_PROVIDER=groq`, `GROQ_API_KEY=...`, et `GROQ_MODEL=llama3-8b-8192`. Cependant, `config/ai.php` ne lit pas ces valeurs : le `default` est codé en dur à `'openai'` et l'entrée `groq` n'a pas de section `models` pour utiliser `GROQ_MODEL`.

## Goals / Non-Goals

**Goals:**
- `config('ai.default')` renvoie la valeur de `AI_PROVIDER` depuis `.env` (fallback `'openai'`)
- `config('ai.providers.groq')` inclut `models.text.default` lu depuis `GROQ_MODEL` (fallback `'llama3-8b-8192'`)
- Tous les tests Pest passent (ils mockent `AI::structured()` donc aucune régression)

**Non-Goals:**
- Changer le SDK de version ou migrer vers un autre fournisseur IA
- Ajouter un nouveau endpoint ou une nouvelle fonctionnalité métier
- Modifier les appels `AI::structured()` existants dans le code

## Decisions

| Décision | Choix | Alternative | Raison |
|---|---|---|---|
| Fournisseur par défaut | `env('AI_PROVIDER', 'openai')` | Laisser `'openai'` en dur | Le `.env` définit `AI_PROVIDER=groq` ; le rendre dynamique permet de changer de provider sans modifier le code |
| Modèle Groq | `env('GROQ_MODEL', 'llama3-8b-8192')` dans `models.text.default` | Ne pas définir de modèle | `GroqProvider::defaultTextModel()` lit `config['models']['text']['default']` ; sans cette config il utilise un fallback invalide |
| Test de régression | Lancer `php artisan test` | Test d'appel réel à Groq | Les tests mockent le SDK, un vrai appel nécessiterait une clé API valide en CI et ralentirait la suite |

## Risks / Trade-offs

- **[Risque faible]** `config/ai.php` est un fichier versionné — si un développeur commit une valeur de fallback inappropriée, elle serait partagée. → La valeur par défaut dans `.env.example` sert de documentation.
- **[Risque nul]** Les tests sont mockés, donc aucun risque de coût API réel ou de timeout.
