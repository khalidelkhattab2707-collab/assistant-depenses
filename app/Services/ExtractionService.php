<?php

namespace App\Services;

use App\Enums\StatutRecu;
use App\Models\Depense;
use App\Models\Recu;
use Illuminate\JsonSchema\JsonSchemaTypeFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Laravel\Ai\Files\LocalImage;
use RuntimeException;

use function Laravel\Ai\agent;

class ExtractionService
{
    public function extraire(Recu $recu): void
    {
        $attachments = [];
        if ($recu->image_path) {
            $attachments[] = new LocalImage(Storage::disk('public')->path($recu->image_path));
        }

        $response = agent(
            instructions: "Tu es un assistant comptable. Analyse ce reçu fournisseur et extrais chaque article.\n\nReçu :\n{$recu->text_brut}\n\nRéponds UNIQUEMENT avec un JSON valide respectant exactement le schéma fourni.\nCatégories disponibles : alimentaire, boissons, hygiene, entretien, autre.\nDevise par défaut : MAD.",
            schema: function (JsonSchemaTypeFactory $schema) {
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
            },
        )->prompt(
            prompt: $recu->text_brut,
            attachments: $attachments,
        );

        $this->persist($recu, $response->structured, $response->text);
    }

    public function persist(Recu $recu, array $data, string $rawText): void
    {
        $validator = Validator::make($data, [
            'articles'                         => 'required|array|min:1',
            'articles.*.libelle'               => 'required|string',
            'articles.*.quantite'              => 'required|integer|min:1',
            'articles.*.prix_unitaire'         => 'required|numeric|min:0',
            'articles.*.categorie'             => ['required', 'string', 'in:alimentaire,boissons,hygiene,entretien,autre'],
            'total_estime'                     => 'required|numeric',
            'devise'                            => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new RuntimeException('Réponse IA hors schéma : ' . $validator->errors()->first());
        }

        $now = now();
        $depenses = array_map(fn($article) => [
            'recu_id'       => $recu->id,
            'libelle'       => $article['libelle'],
            'quantite'      => $article['quantite'],
            'prix_unitaire' => $article['prix_unitaire'],
            'categorie'     => $article['categorie'],
            'created_at'    => $now,
            'updated_at'    => $now,
        ], $data['articles']);

        Depense::insert($depenses);

        $recu->update([
            'status'       => StatutRecu::Traite,
            'payload_ia'   => [
                'structured' => $data,
                'raw_text'   => $rawText,
            ],
            'total_estime' => $data['total_estime'],
        ]);
    }
}
