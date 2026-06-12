<?php

use App\Enums\StatutRecu;
use App\Models\Depense;
use App\Models\Recu;
use App\Models\User;
use App\Services\ExtractionService;
use Laravel\Ai\Ai;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->recu = Recu::factory()->create([
        'user_id' => $this->user->id,
        'text_brut' => 'Coca x2 24dh, Pain x5 12.5dh',
        'status' => StatutRecu::EnAttente,
    ]);
});

it('extracts depenses from valid AI response', function () {
    Ai::fakeAgent(\Laravel\Ai\StructuredAnonymousAgent::class, [
        [
            'articles' => [
                ['libelle' => 'Coca', 'quantite' => 2, 'prix_unitaire' => 12.0, 'categorie' => 'boissons'],
                ['libelle' => 'Pain', 'quantite' => 5, 'prix_unitaire' => 2.5, 'categorie' => 'alimentaire'],
            ],
            'total_estime' => 36.5,
            'devise' => 'MAD',
        ],
    ]);

    $service = app(ExtractionService::class);
    $service->extraire($this->recu);

    $this->recu->refresh();

    expect($this->recu->status)->toBe(StatutRecu::Traite)
        ->and($this->recu->depenses)->toHaveCount(2)
        ->and($this->recu->depenses[0]->libelle)->toBe('Coca')
        ->and($this->recu->depenses[0]->categorie->value)->toBe('boissons')
        ->and($this->recu->depenses[1]->libelle)->toBe('Pain')
        ->and($this->recu->depenses[1]->categorie->value)->toBe('alimentaire')
        ->and((float) $this->recu->total_estime)->toBe(36.5)
        ->and($this->recu->payload_ia)->toHaveKey('structured')
        ->and($this->recu->payload_ia)->toHaveKey('raw_text');
});

it('throws exception on invalid AI response', function () {
    Ai::fakeAgent(\Laravel\Ai\StructuredAnonymousAgent::class, [
        ['articles' => [], 'total_estime' => 0, 'devise' => 'MAD'],
    ]);

    $service = app(ExtractionService::class);

    $this->expectException(\RuntimeException::class);

    $service->extraire($this->recu);
});

it('marks receipt as failed when job fails', function () {
    $job = new \App\Jobs\ExtraireDepensesDuRecu($this->recu);
    $job->failed(new \Exception('API error'));

    $fresh = $this->recu->fresh();
    expect($fresh->status->value)->toBe('echoue')
        ->and($fresh->payload_ia)->toHaveKey('error')
        ->and($fresh->payload_ia['error'])->toBe('API error');
});
