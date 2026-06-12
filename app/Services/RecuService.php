<?php

namespace App\Services;

use App\Enums\StatutRecu;
use App\Jobs\ExtraireDepensesDuRecu;
use App\Models\Recu;
use App\Models\User;

class RecuService
{
    public function create(User $user, array $data): Recu
    {
        $imagePath = null;
        if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
            $imagePath = $data['image']->store('receipts', 'public');
        }

        $recu = $user->recus()->create([
            'text_brut'  => $data['text_brut'],
            'image_path' => $imagePath,
            'status'     => StatutRecu::EnAttente,
            'devis'      => $data['devis'] ?? 'MAD',
        ]);

        ExtraireDepensesDuRecu::dispatch($recu);

        return $recu;
    }
}
