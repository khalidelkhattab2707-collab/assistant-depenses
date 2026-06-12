## Context

Le Job `ExtraireDepensesDuRecu` délègue entièrement à `ExtractionService::extraire()` qui fait tout : appel IA, validation, persistance. Le schéma JSON est défini en dur dans le service.

## Goals / Non-Goals

**Goals:**
- `ExtraireDepensesDuRecu` a ses propres méthodes `buildPrompt()` et `getJsonSchema()`
- `handle()` appelle `AI::structured()` et passe le résultat à `ExtractionService` pour persistance
- `ExtractionService` accepte un tableau `$data` (déjà extrait) et gère uniquement validation + création

**Non-Goals:**
- Changement du comportement métier
- Changement des tests (ils testent le service directement, pas le job)

## Decisions

| Decision | Choice | Rationale |
|---|---|---|
| Méthodes sur le Job | `private` | Pas besoin d'accès externe, le contrat est encapsulé |
| ExtractionService | Nouvelle méthode `persist(Recu, array, string)` | Reçoit les données déjà validées + le raw_text pour `payload_ia` |
| Prompt | Construit dans `buildPrompt()` | Inclut `$this->recu->text_brut` et `$this->recu->image_path` |

## Risks / Trade-offs

- [Duplication du schéma] → Le schéma existe maintenant dans `getJsonSchema()` du Job au lieu du service, pas de duplication réelle
- [Test du service] → Les tests existants mockent `AI::structured()` — ils testent `ExtractionService` directement et restent valides
