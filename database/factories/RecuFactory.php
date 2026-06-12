<?php

namespace Database\Factories;

use App\Enums\StatutRecu;
use App\Models\Recu;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RecuFactory extends Factory
{
    protected $model = Recu::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'text_brut' => fake()->sentence(),
            'status' => StatutRecu::EnAttente,
            'devis' => 'MAD',
        ];
    }
}
