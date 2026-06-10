# AGENTS.md — Assistant Dépenses Laravel

> **Guide officiel pour agents IA** (OpenCode, Claude Code, Cursor, Copilot, etc.)
> Ce fichier fait autorité sur toute décision d'architecture, de workflow et de qualité dans ce projet.
> Aucun agent ne doit générer de code sans avoir lu et appliqué ce document.

---

## Table des matières

1. [Vue d'ensemble du projet](#1-vue-densemble-du-projet)
2. [Stack technique](#2-stack-technique)
3. [Arborescence recommandée](#3-arborescence-recommandée)
4. [Entités & Modèles](#4-entités--modèles)
5. [Enums PHP](#5-enums-php)
6. [Architecture obligatoire](#6-architecture-obligatoire)
7. [Workflow IA — Extraction structurée](#7-workflow-ia--extraction-structurée)
8. [Queue & Jobs](#8-queue--jobs)
9. [Sécurité & Autorisation](#9-sécurité--autorisation)
10. [Performance & N+1](#10-performance--n1)
11. [Tests Pest](#11-tests-pest)
12. [Workflow OpenSpec](#12-workflow-openspec)
13. [Conventions Git & Commits](#13-conventions-git--commits)
14. [Qualité de code](#14-qualité-de-code)
15. [Checklists](#15-checklists)
16. [Interdictions absolues](#16-interdictions-absolues)

---

## 1. Vue d'ensemble du projet

**Assistant Dépenses** est une application Laravel permettant à un commerçant de :

- Coller le texte brut d'un reçu fournisseur (souvent en darija, mal formaté)
- Déclencher une extraction IA asynchrone via le SDK officiel `laravel/ai`
- Obtenir une liste structurée d'articles — libellé, quantité, prix, catégorie
- Suivre l'état du traitement en temps réel (En attente / Traité / Échoué)
- Consulter et filtrer l'historique de ses dépenses par catégorie

> **Principe directeur :** La saisie manuelle est le travail pénible que l'IA absorbe. Le structured output garanti remplace le `json_decode` fragile. La queue empêche toute page bloquée.

---

## 2. Stack technique

| Couche | Technologie | Version |
|---|---|---|
| Framework | Laravel | 12.x |
| Langage | PHP | 8.4+ |
| Base de données | MySQL | 8.x |
| Frontend | Blade + TailwindCSS | — |
| Auth | Laravel Breeze | — |
| Queue | Laravel Queue (database driver) | — |
| SDK IA | `laravel/ai` (officiel) | latest |
| Fournisseur IA | Groq API | — |
| Tests | Pest | 3.x |
| Debug | Laravel Debugbar | — |
| Specs | OpenSpec | — |

---

## 3. Arborescence recommandée

```
assistant-depenses/
│
├── AGENTS.md                          ← CE FICHIER (premier commit)
├── specs/                             ← Géré par OpenSpec, commité
│   ├── authentication/
│   │   ├── proposal.md
│   │   ├── spec.md
│   │   └── tasks.md
│   ├── receipts/
│   │   ├── proposal.md
│   │   ├── spec.md
│   │   └── tasks.md
│   ├── expenses/
│   │   ├── proposal.md
│   │   ├── spec.md
│   │   └── tasks.md
│   └── ai-extraction/
│       ├── proposal.md
│       ├── spec.md
│       └── tasks.md
│
├── app/
│   ├── Console/
│   ├── Enums/
│   │   ├── RecuStatus.php             ← Enum PHP natif
│   │   └── DepenseCategory.php        ← Enum PHP natif
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── RecuController.php     ← Mince, délègue aux Services
│   │   │   └── DepenseController.php
│   │   └── Requests/
│   │       └── StoreRecuRequest.php   ← Validation avant tout appel IA
│   ├── Jobs/
│   │   └── ExtraireDepensesDuRecu.php ← Seul endroit où l'IA est appelée
│   ├── Models/
│   │   ├── User.php
│   │   ├── Recu.php                   ← hasMany Depense, casts enum
│   │   └── Depense.php                ← belongsTo Recu, casts enum
│   ├── Policies/
│   │   ├── RecuPolicy.php
│   │   └── DepensePolicy.php
│   └── Services/
│       ├── RecuService.php            ← Logique métier reçu
│       └── ExtractionService.php      ← Orchestration appel IA
│
├── database/
│   ├── migrations/
│   │   ├── xxxx_create_recus_table.php
│   │   └── xxxx_create_depenses_table.php
│   └── seeders/
│
├── resources/
│   └── views/
│       ├── recus/
│       │   ├── index.blade.php
│       │   ├── show.blade.php
│       │   └── create.blade.php
│       └── depenses/
│           └── index.blade.php
│
├── routes/
│   └── web.php
│
└── tests/
    └── Feature/
        ├── RecuSubmissionTest.php
        └── ExtractionTest.php         ← Utilise Fake laravel/ai
```

---

## 4. Entités & Modèles

### 4.1 User

Géré intégralement par Laravel Breeze. Ne pas modifier la migration Breeze.

**Relations :**
```php
// app/Models/User.php
public function recus(): HasMany
{
    return $this->hasMany(Recu::class);
}
```

---

### 4.2 Recu

**Migration obligatoire :**
```php
Schema::create('recus', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->text('texte_brut');
    $table->string('statut')->default('pending');      // cast enum RecuStatus
    $table->json('payload_ia')->nullable();            // cast array
    $table->decimal('total_estime', 10, 2)->nullable();
    $table->string('devise')->default('MAD');
    $table->timestamps();
});
```

**Modèle obligatoire :**
```php
// app/Models/Recu.php
protected $fillable = ['user_id', 'texte_brut', 'statut', 'payload_ia', 'total_estime', 'devise'];

protected $casts = [
    'statut'     => RecuStatus::class,   // Enum cast
    'payload_ia' => 'array',             // JSON cast
];

public function depenses(): HasMany
{
    return $this->hasMany(Depense::class);
}

public function user(): BelongsTo
{
    return $this->belongsTo(User::class);
}
```

---

### 4.3 Depense

**Migration obligatoire :**
```php
Schema::create('depenses', function (Blueprint $table) {
    $table->id();
    $table->foreignId('recu_id')->constrained()->cascadeOnDelete();
    $table->string('libelle');
    $table->integer('quantite');
    $table->decimal('prix_unitaire', 10, 2);
    $table->string('categorie');                       // cast enum DepenseCategory
    $table->timestamps();
});
```

**Modèle obligatoire :**
```php
// app/Models/Depense.php
protected $fillable = ['recu_id', 'libelle', 'quantite', 'prix_unitaire', 'categorie'];

protected $casts = [
    'categorie' => DepenseCategory::class,  // Enum cast
];

public function recu(): BelongsTo
{
    return $this->belongsTo(Recu::class);
}
```

---

## 5. Enums PHP

Les enums sont des types — pas des chaînes magiques. Tout statut et toute catégorie transite par un enum PHP natif (8.1+).

```php
// app/Enums/RecuStatus.php
namespace App\Enums;

enum RecuStatus: string
{
    case Pending   = 'pending';
    case Processed = 'processed';
    case Failed    = 'failed';

    public function label(): string
    {
        return match($this) {
            self::Pending   => 'En attente',
            self::Processed => 'Traité',
            self::Failed    => 'Échoué',
        };
    }
}
```

```php
// app/Enums/DepenseCategory.php
namespace App\Enums;

enum DepenseCategory: string
{
    case Alimentaire = 'alimentaire';
    case Boissons    = 'boissons';
    case Hygiene     = 'hygiene';
    case Entretien   = 'entretien';
    case Autre       = 'autre';

    public function label(): string
    {
        return match($this) {
            self::Alimentaire => 'Alimentaire',
            self::Boissons    => 'Boissons',
            self::Hygiene     => 'Hygiène',
            self::Entretien   => 'Entretien',
            self::Autre       => 'Autre',
        };
    }
}
```

> **Règle :** Un agent ne doit jamais écrire `'pending'`, `'processed'`, `'failed'`, `'alimentaire'`, etc. en dur dans le code. Il utilise toujours l'enum : `RecuStatus::Pending`, `DepenseCategory::Boissons`.

---

## 6. Architecture obligatoire

### 6.1 Responsabilités strictes

| Couche | Rôle | Ce qu'elle NE fait PAS |
|---|---|---|
| **Controller** | Reçoit la requête HTTP, délègue, retourne la vue | Logique métier, SQL, appel IA |
| **Form Request** | Valide les données entrantes | Appel IA, logique métier |
| **Service** | Logique métier pure | Requêtes HTTP directes, vues |
| **Job** | Traitement asynchrone IA | Logique métier complexe |
| **Model** | Données, relations, casts | Logique métier, appels HTTP |
| **View (Blade)** | Affichage uniquement | SQL, logique PHP, appels IA |

### 6.2 Exemple de flux correct

```
HTTP POST /recus
    └─> StoreRecuRequest::authorize() + rules()  ← validation
    └─> RecuController::store()                  ← mince
        └─> RecuService::create()                ← logique métier
            └─> Recu::create([...])              ← persistance
            └─> ExtraireDepensesDuRecu::dispatch($recu)  ← queue
    └─> redirect() avec message flash
```

### 6.3 Controllers fins — limite stricte

- **Maximum 150 lignes** par contrôleur
- **Maximum 10 lignes** par méthode de contrôleur
- Si une méthode dépasse 10 lignes → extraire dans un Service

```php
// CORRECT
class RecuController extends Controller
{
    public function store(StoreRecuRequest $request, RecuService $service)
    {
        $recu = $service->create(auth()->user(), $request->validated());
        return redirect()->route('recus.index')
                         ->with('success', 'Reçu en cours de traitement.');
    }
}

// INTERDIT
class RecuController extends Controller
{
    public function store(Request $request)
    {
        // 50 lignes de logique métier ici ← VIOLATION
    }
}
```

---

## 7. Workflow IA — Extraction structurée

### 7.1 Contrat JSON obligatoire

Tout appel IA **doit** retourner exactement ce schéma. Aucune déviation n'est acceptée.

```json
{
  "articles": [
    {
      "libellé": "string",
      "quantité": "integer",
      "prix_unitaire": "number",
      "catégorie": "alimentaire | boissons | hygiène | entretien | autre"
    }
  ],
  "total_estimé": "number",
  "devise": "MAD"
}
```

### 7.2 Règles d'utilisation du SDK `laravel/ai`

```php
// OBLIGATOIRE — via SDK officiel avec Structured Output
use Illuminate\Support\Facades\AI;

$response = AI::structured(
    prompt: $this->buildPrompt($recu->texte_brut),
    schema: $this->getJsonSchema(),
);

// INTERDIT — appels HTTP directs
$response = Http::post('https://api.groq.com/...', [...]);

// INTERDIT — json_decode sauvage
$data = json_decode($rawResponse, true);  // ← JAMAIS SANS VALIDATION
```

### 7.3 Prompt recommandé

```php
private function buildPrompt(string $texteBrut): string
{
    return <<<PROMPT
    Tu es un assistant comptable. Analyse ce reçu fournisseur et extrais chaque article.
    
    Reçu :
    {$texteBrut}
    
    Réponds UNIQUEMENT avec un JSON valide respectant exactement le schéma fourni.
    Catégories disponibles : alimentaire, boissons, hygiene, entretien, autre.
    Devise par défaut : MAD.
    PROMPT;
}
```

### 7.4 Validation obligatoire de la réponse IA

Avant toute persistance, valider la structure retournée avec `Validator::make()` :

```php
$validator = Validator::make($response, [
    'articles'              => 'required|array|min:1',
    'articles.*.libellé'    => 'required|string',
    'articles.*.quantité'   => 'required|integer|min:1',
    'articles.*.prix_unitaire' => 'required|numeric|min:0',
    'articles.*.catégorie'  => ['required', Rule::in(['alimentaire', 'boissons', 'hygiène', 'entretien', 'autre'])],
    'total_estimé'          => 'required|numeric',
    'devise'                => 'required|string',
]);

if ($validator->fails()) {
    throw new \RuntimeException('Réponse IA hors schéma : ' . $validator->errors()->first());
}
```

---

## 8. Queue & Jobs

### 8.1 Règle absolue

> **L'extraction IA ne s'exécute JAMAIS dans une requête HTTP synchrone.**
> Elle est TOUJOURS dispatchée dans un Job et traitée par un worker.

### 8.2 Structure du Job

```php
// app/Jobs/ExtraireDepensesDuRecu.php

namespace App\Jobs;

use App\Models\Recu;
use App\Enums\RecuStatus;
use App\Services\ExtractionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class ExtraireDepensesDuRecu implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(public readonly Recu $recu) {}

    public function handle(ExtractionService $service): void
    {
        $service->extraire($this->recu);
    }

    public function failed(Throwable $exception): void
    {
        $this->recu->update(['statut' => RecuStatus::Failed]);
    }
}
```

### 8.3 Dispatch depuis le Service

```php
// app/Services/RecuService.php
public function create(User $user, array $data): Recu
{
    $recu = $user->recus()->create([
        'texte_brut' => $data['texte_brut'],
        'statut'     => RecuStatus::Pending,
    ]);

    ExtraireDepensesDuRecu::dispatch($recu);

    return $recu;
}
```

### 8.4 Lancer le worker

```bash
php artisan queue:work --tries=3 --backoff=60
```

---

## 9. Sécurité & Autorisation

### 9.1 Policies obligatoires

Chaque action sur un Recu ou une Depense passe par une Policy.

```php
// app/Policies/RecuPolicy.php
public function view(User $user, Recu $recu): bool
{
    return $user->id === $recu->user_id;
}

public function delete(User $user, Recu $recu): bool
{
    return $user->id === $recu->user_id;
}
```

### 9.2 Autorisation dans les Controllers

```php
// OBLIGATOIRE
public function show(Recu $recu)
{
    $this->authorize('view', $recu);
    // ...
}

// INTERDIT
public function show(Recu $recu)
{
    if ($recu->user_id !== auth()->id()) abort(403); // ← logique dans contrôleur
}
```

### 9.3 Règles de sécurité

- Protection CSRF active sur toutes les routes POST/PUT/DELETE (Blade `@csrf`)
- Validation serveur **toujours** via Form Request — jamais côté client uniquement
- Routes authentifiées protégées par middleware `auth`
- Pas de données sensibles dans les logs

---

## 10. Performance & N+1

### 10.1 Eager Loading obligatoire

```php
// CORRECT — eager loading
$recus = auth()->user()->recus()->with('depenses')->paginate(20);

// INTERDIT — N+1
$recus = auth()->user()->recus()->get();
// puis en Blade : $recu->depenses->count()  ← requête par ligne
```

### 10.2 Vérification Debugbar

Avant chaque commit touchant une liste ou un index :
1. Activer Laravel Debugbar
2. Charger la page
3. Vérifier le nombre de requêtes SQL dans l'onglet "Queries"
4. **Zéro requête dans les boucles Blade**

### 10.3 Pagination obligatoire

Toute liste paginée avec `->paginate(20)`. Jamais `->get()` sur une relation non bornée.

---

## 11. Tests Pest

### 11.1 Règle fondamentale

> **Aucun test ne doit faire un vrai appel à l'API Groq.**
> Utiliser exclusivement le Fake du SDK `laravel/ai`.

### 11.2 Structure d'un test d'extraction

```php
// tests/Feature/ExtractionTest.php

use App\Jobs\ExtraireDepensesDuRecu;
use App\Models\Recu;
use App\Models\User;
use App\Enums\RecuStatus;
use Illuminate\Support\Facades\Queue;

it('dispatche le job d\'extraction à la soumission d\'un reçu', function () {
    Queue::fake();
    $user = User::factory()->create();

    $this->actingAs($user)
         ->post('/recus', ['texte_brut' => 'Coca x2 12dh, Pain x5 2.5dh'])
         ->assertRedirect('/recus');

    Queue::assertPushed(ExtraireDepensesDuRecu::class);
});

it('extrait les dépenses avec le fake IA et les persiste', function () {
    // Fake du SDK laravel/ai — configuration selon la doc officielle
    AI::fake([
        'articles' => [
            ['libellé' => 'Coca', 'quantité' => 2, 'prix_unitaire' => 12.0, 'catégorie' => 'boissons'],
            ['libellé' => 'Pain', 'quantité' => 5, 'prix_unitaire' => 2.5,  'catégorie' => 'alimentaire'],
        ],
        'total_estimé' => 36.5,
        'devise'       => 'MAD',
    ]);

    $recu = Recu::factory()->create(['texte_brut' => 'Coca x2, Pain x5']);

    (new \App\Services\ExtractionService())->extraire($recu);

    expect($recu->fresh()->statut)->toBe(RecuStatus::Processed)
        ->and($recu->depenses)->toHaveCount(2);
});
```

### 11.3 Couverture minimale attendue

| Feature | Tests requis |
|---|---|
| Authentification | Inscription, connexion, déconnexion |
| Soumission reçu | Validation Form Request, dispatch Job |
| Extraction IA | Structured output, persistance, statut |
| Erreur IA | Statut Failed en cas d'exception |
| Autorisation | Un user ne voit pas les reçus d'un autre |
| Suppression | Suppression en cascade reçu + dépenses |

---

## 12. Workflow OpenSpec

### 12.1 Règle absolue

> **Aucun développement ne commence sans spec approuvée dans `specs/`.**
> Un agent IA ne génère pas de code tant que la spec n'est pas validée.

### 12.2 Étapes obligatoires par feature

```
1. PROPOSAL  → specs/<feature>/proposal.md
               Contexte, problème, solution envisagée, décisions

2. SPEC      → specs/<feature>/spec.md
               Comportements attendus, contrats d'interface, cas limites

3. TASKS     → specs/<feature>/tasks.md
               Liste ordonnée des tâches atomiques, estimations

4. VALIDATION → Revue humaine ou agent lead
                Aucun build sans approbation explicite

5. BUILD     → Implémentation feature par feature, commit par commit
```

### 12.3 Template `proposal.md`

```markdown
# Proposal — [Nom de la feature]

## Contexte
Pourquoi cette feature existe-t-elle ? Quel problème résout-elle ?

## Solution proposée
Description technique de l'approche retenue.

## Décisions d'architecture
- Décision 1 : [choix] parce que [raison]
- Décision 2 : ...

## Cas limites identifiés
- Que se passe-t-il si le texte du reçu est vide ?
- Que se passe-t-il si l'API Groq est injoignable ?

## Hors scope
Ce que cette feature ne couvre pas.
```

### 12.4 Template `spec.md`

```markdown
# Spec — [Nom de la feature]

## Comportements attendus

### Scénario nominal
Given / When / Then

### Scénarios alternatifs
Given / When / Then

### Cas d'erreur
Given / When / Then

## Contrats d'interface
- Endpoint : POST /recus
- Payload : { texte_brut: string }
- Réponse : 302 redirect avec flash message

## Contraintes non fonctionnelles
- Temps de réponse HTTP : < 200ms (la queue absorbe le reste)
```

### 12.5 Template `tasks.md`

```markdown
# Tasks — [Nom de la feature]

## Checklist d'implémentation

- [ ] Migration `recus` table
- [ ] Enum `RecuStatus`
- [ ] Modèle `Recu` avec casts et relations
- [ ] Form Request `StoreRecuRequest`
- [ ] Service `RecuService::create()`
- [ ] Job `ExtraireDepensesDuRecu`
- [ ] Service `ExtractionService::extraire()`
- [ ] Controller `RecuController` (mince)
- [ ] Policy `RecuPolicy`
- [ ] Vues Blade
- [ ] Tests Pest
- [ ] Vérification N+1 Debugbar
```

---

## 13. Conventions Git & Commits

### 13.1 Format obligatoire

```
<type>(<scope>): <description courte en anglais ou français>

[Corps optionnel — contexte, décisions, usage AI]

[Footer optionnel — références issues]
```

### 13.2 Types acceptés

| Type | Usage |
|---|---|
| `feat` | Nouvelle fonctionnalité |
| `fix` | Correction de bug |
| `docs` | Documentation, specs |
| `test` | Ajout ou modification de tests |
| `refactor` | Refactoring sans changement comportemental |
| `chore` | Tâches techniques (config, deps) |

### 13.3 Exemples de commits corrects

```bash
# Premier commit obligatoire
git commit -m "chore(init): add AGENTS.md — project agent guide"

# Spec
git commit -m "docs(spec): add receipt extraction proposal and spec"

# Feature avec mention AI
git commit -m "feat(ai): add ExtraireDepensesDuRecu job — generated with AI assistance"

# Fix avec contexte
git commit -m "fix(expenses): resolve DepenseCategory enum casting issue"

# Test
git commit -m "test(extraction): add Pest feature test using laravel/ai fake"
```

### 13.4 Règles

- Un commit = une unité logique (pas de "wip: everything")
- Toujours mentionner l'usage IA dans le message si du code a été généré par un agent
- Ne jamais commiter `AGENTS.md` modifié sans en informer l'équipe

---

## 14. Qualité de code

### 14.1 Principes applicables

| Principe | Application concrète dans ce projet |
|---|---|
| **SRP** | Une classe = une responsabilité. `ExtractionService` gère l'IA. `RecuService` gère la logique métier reçu. |
| **OCP** | Le fournisseur IA est interchangeable via config — pas de `if ($provider === 'groq')` en dur |
| **DRY** | Les labels des enums centralisés dans `label()`. Pas de duplication de règles de validation |
| **KISS** | Pas de pattern complexe inutile. Si un Service suffit, pas de Repository |
| **PSR-12** | Formatage strict, nommage cohérent |

### 14.2 Limites à respecter

- Contrôleur : max 150 lignes, méthodes max 10 lignes
- Service : méthodes max 30 lignes
- Job `handle()` : délègue au Service, max 5 lignes
- Pas de méthode avec plus de 3 paramètres (utiliser un DTO ou array typé)

---

## 15. Checklists

### 15.1 ✅ Checklist avant chaque commit

```
□ Le code respecte PSR-12 (php-cs-fixer ou équivalent)
□ Aucune logique métier dans les vues ou contrôleurs
□ Aucun json_decode non validé
□ Aucune requête SQL dans les boucles Blade
□ Les nouveaux modèles ont leurs casts déclarés
□ Les nouvelles routes sont protégées par auth si nécessaire
□ Un test Pest couvre le comportement ajouté ou modifié
□ Le message de commit mentionne l'usage AI si applicable
□ Aucun secret / clé API dans le code committé
```

### 15.2 ✅ Checklist avant Pull Request

```
□ La spec correspondante est dans specs/ et approuvée
□ Tous les tests Pest passent (php artisan test)
□ Aucun N+1 détecté via Debugbar sur les pages de liste
□ Les migrations ont des foreign keys et cascades correctes
□ Les Policies couvrent toutes les actions sensibles
□ La feature est entièrement derrière auth middleware
□ Le Job utilise ShouldQueue et sérialise le modèle
□ Le statut Failed est géré dans la méthode failed() du Job
□ Le structured output est validé avant persistance
□ La pagination est en place sur toutes les listes
□ Aucune variable d'environnement hardcodée
□ Le CHANGELOG ou la PR description résume les décisions techniques
```

### 15.3 ✅ Checklist validation DWWM

```
□ US1 — Inscription / Connexion / Déconnexion fonctionnelles (Breeze)
□ US2 — Liste des reçus avec statut formaté et compteur de dépenses
□ US3 — Soumission non bloquante, message flash immédiat
□ US4 — Détail reçu : texte source + statut + liste dépenses structurées
□ US5 — Suppression reçu avec cascade sur les dépenses
□ US6 — Structured output garanti, validé, persisté ligne par ligne
□ US7 — Statut Failed visible en cas d'erreur IA
□ US8 — Liste dépenses filtrable par catégorie avec labels formatés
□ Architecture MVC respectée et justifiable à l'oral
□ Queue et Job documentés et démontrables
□ Enums PHP utilisés systématiquement
□ Eager loading vérifié, zéro N+1
□ AGENTS.md présent dès le premier commit
□ Dossier specs/ commité et structuré
□ Tests Pest présents, tournant sans appel Groq réel
□ Aucune page blanche en cas d'erreur IA
```

---

## 16. Interdictions absolues

Ces pratiques entraînent un rejet immédiat du code :

| Interdit | Raison | Alternative |
|---|---|---|
| `json_decode($response)` sans validation | Crash silencieux si l'IA répond hors schéma | `Validator::make()` + Structured Output SDK |
| Logique métier dans un Controller | Violation SRP, non testable isolément | Extraire dans un Service |
| Appel IA synchrone dans une requête HTTP | Page bloquée, mauvaise UX | Job dispatché dans la Queue |
| Appel HTTP direct à Groq (`Http::post(...)`) | Contourne le SDK officiel et la config | `AI::structured(...)` via `laravel/ai` |
| Strings magiques pour statuts/catégories | Bugs silencieux, non refactorisables | Enums PHP avec cast Eloquent |
| Champs JSON non castés en `array` | Accès objet cassé, sérialisation imprévisible | `'payload_ia' => 'array'` dans `$casts` |
| Requêtes SQL dans les boucles Blade | N+1, performances dégradées | Eager loading avec `with()` |
| Code généré sans spec approuvée | Travail non aligné, refactoring coûteux | Workflow OpenSpec obligatoire |
| Clés API dans le code source | Fuite de secrets | Variables d'environnement `.env` |
| Tests dépendant de l'API Groq réelle | Tests lents, fragiles, coûteux | `AI::fake()` du SDK |
| Contrôleur > 150 lignes | Difficile à lire, tester, maintenir | Extraire Services et Form Requests |

---

> **Ce fichier est vivant.** Toute modification doit être commitée avec le type `docs(agents):` et justifiée.
> Dernière mise à jour : Juin 2026