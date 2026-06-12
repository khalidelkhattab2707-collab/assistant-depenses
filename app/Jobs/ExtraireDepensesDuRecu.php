<?php

namespace App\Jobs;

use App\Enums\StatutRecu;
use App\Models\Recu;
use App\Services\ExtractionService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\JsonSchema\JsonSchemaTypeFactory;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Laravel\Ai\Files\LocalImage;
use Throwable;

use function Laravel\Ai\agent;

class ExtraireDepensesDuRecu implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(public readonly Recu $recu) {}

    public function handle(ExtractionService $service): void
    {
        $attachments = [];
        if ($this->recu->image_path) {
            $attachments[] = new LocalImage(Storage::disk('public')->path($this->recu->image_path));
        }

        $response = agent(
            instructions: $this->buildInstructions(),
            schema: function (JsonSchemaTypeFactory $schema) {
                return $this->buildSchema($schema);
            },
        )->prompt(
            prompt: $this->recu->text_brut,
            attachments: $attachments,
        );

        $service->persist($this->recu, $response->structured, $response->text);
    }

    private function buildInstructions(): string
    {
        return "Tu es un assistant comptable. Analyse ce reçu fournisseur et extrais chaque article.\n\nReçu :\n{$this->recu->text_brut}\n\nRéponds UNIQUEMENT avec un JSON valide respectant exactement le schéma fourni.\nCatégories disponibles : alimentaire, boissons, hygiene, entretien, autre.\nDevise par défaut : MAD.";
    }

    private function buildSchema(JsonSchemaTypeFactory $schema): array
    {
        return [
            'articles' => $schema->array()->items(
                $schema->object([
                    'libelle'       => $schema->string()->required(),
                    'quantite'      => $schema->integer()->required(),
                    'prix_unitaire' => $schema->number()->required(),
                    'categorie'     => $schema->string()->required()->enum(['alimentaire', 'boissons', 'hygiene', 'entretien', 'autre']),
                ]),
            )->required(),
            'total_estime' => $schema->number()->required(),
            'devise'        => $schema->string()->required(),
        ];
    }

    public function failed(Throwable $exception): void
    {
        Log::error('Extraction échouée', [
            'recu_id' => $this->recu->id,
            'error' => $exception->getMessage(),
        ]);

        $this->recu->update([
            'status' => StatutRecu::Echoue,
            'payload_ia' => ['error' => $exception->getMessage()],
        ]);
    }
}
