## Context

Actuellement `ExtractionService::extraire()` utilise `$response->structured` pour `payload_ia` mais ignore `$response->text` (le JSON brut). Les dépenses sont créées en loop N× `create()`.

## Goals / Non-Goals

**Goals:**
- `payload_ia` contient `{ structured: [...], raw_text: "..." }` sur extraction réussie
- Les dépenses sont créées avec `Depense::insert()` en un seul query
- Les tests vérifient la présence de `raw_text` dans `payload_ia`

**Non-Goals:**
- Migration de base de données (pas de nouveau champ, `payload_ia` reste un JSON)
- Changement de l'interface utilisateur
- Changement du format d'échec (`payload_ia.error` reste inchangé)

## Decisions

| Decision | Choice | Rationale |
|---|---|---|
| Stockage raw | Dans `payload_ia.raw_text` | Pas de nouvelle colonne, rétrocompatible, le cast `array` gère tout |
| Insert groupé | `Depense::insert()` | Une seule requête SQL au lieu de N, pas de `created_at`/`updated_at` automatiques → on les définit manuellement |
| Changement payload | `structured` + `raw_text` | Permet de distinguer les données validées du texte brut original |

## Risks / Trade-offs

- [Insert sans timestamps] → Les timestamps sont définis manuellement dans le tableau pour `created_at` et `updated_at`
- [Payload plus volumineux] → Acceptable, `raw_text` est une string JSON qui reste modérée pour un reçu
