<?php

namespace App\Models;

use App\Enums\CategorieDepense;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Depense extends Model
{
    use HasFactory;
    protected $fillable = ['recu_id', 'libelle', 'quantite', 'prix_unitaire', 'categorie'];

    protected $casts = [
        'categorie' => CategorieDepense::class,
    ];

    public function recu(): BelongsTo
    {
        return $this->belongsTo(Recu::class);
    }
}
