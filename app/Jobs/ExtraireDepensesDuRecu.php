<?php
namespace App\Jobs;
use App\Models\Recu;
use App\Enums\StatutRecu;
use Laravel\AI\Facades\AI;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ExtraireDepensesDuRecu implements ShouldQueue {
    use Queueable;

    public int $tries   = 3;
    public int $timeout = 60;

    public function __construct(public Recu $recu) {}

    public function handle(): void
    {
        $response = AI::structured(
            prompt: "Tu es un assistant comptable. Extrais tous les articles de ce reçu fournisseur.\nRécu :\n\n" . $this->recu->texte_brut,
            schema: [
                'type'       => 'object',
                'properties' => [
                    'articles' => [
                        'type'  => 'array',
                        'items' => [
                            'type'       => 'object',
                            'properties' => [
                                'libelle'       => ['type' => 'string'],
                                'quantite'      => ['type' => 'integer'],
                                'prix_unitaire' => ['type' => 'number'],
                                'categorie'     => [
                                    'type' => 'string',
                                    'enum' => ['alimentaire','boissons','hygiene','entretien','autre']
                                ],
                            ],
                            'required' => ['libelle','quantite','prix_unitaire','categorie']
                        ]
                    ],
                    'total_estime' => ['type' => 'number'],
                    'devise'        => ['type' => 'string'],
                ],
                'required' => ['articles','total_estime','devise']
            ]
        );

        $this->recu->update(['payload_brut' => $response]);

        foreach ($response['articles'] as $article) {
            $this->recu->depenses()->create([
                'libelle'       => $article['libelle'],
                'quantite'      => $article['quantite'],
                'prix_unitaire' => $article['prix_unitaire'],
                'categorie'     => $article['categorie'],
            ]);
        }

        $this->recu->update(['statut' => StatutRecu::Traite]);
    }

    public function failed(\Throwable $e): void {
        $this->recu->update(['statut' => StatutRecu::Echoue]);
    }
}