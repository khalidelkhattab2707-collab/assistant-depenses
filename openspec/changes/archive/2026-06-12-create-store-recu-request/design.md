## Context

Le `StoreRecuRequest` actuel valide `text_brut` comme une simple chaîne requise. Aucune protection contre les soumissions vides, trop longues, ou les devises invalides. La validation est le seul point d'entrée — elle doit être robuste avant tout appel IA.

## Goals / Non-Goals

**Goals:**
- `text_brut` : min 10 caractères, max 10 000
- `devis` : restreint à MAD, EUR, USD (via `Rule::in`)
- `total_estime` : min 0
- Messages d'erreur personnalisés en français

**Non-Goals:**
- Modification du contrôleur, service, ou modèle
- Ajout de nouvelles colonnes ou champs
- Validation côté client JavaScript

## Decisions

| Décision | Raison |
|---|---|
| `Rule::in()` pour `devis` | Évite les devises invalides. Extensible : ajouter une devise = une ligne |
| `min:10` sur `text_brut` | Un reçu vide ou de 2 caractères n'a pas de sens pour l'extraction IA |
| `max:10000` sur `text_brut` | Limite la taille des payloads envoyés à l'API Groq |
| Messages en français | L'interface utilisateur est en français. Cohérence avec le reste de l'app |
| `$validator->stopOnFirstFailure()` non utilisé | L'utilisateur doit voir toutes les erreurs d'un coup |

## Risks / Trade-offs

| Risque | Mitigation |
|---|---|
| Un reçu valide de moins de 10 caractères serait rejeté | Cas très improbable (un reçu a toujours au moins un article). Ajustable si besoin |
| `max:10000` pourrait être trop court pour un très long reçu | 10k chars = ~2000 mots. Large marge pour un reçu d'épicerie |
