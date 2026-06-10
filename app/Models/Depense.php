<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Depense extends Model
{
    protected $fillable=['recu_id','libelle','quantite','prix_unitaire','categorie'];
    public function recu():belongsTo
    {
        return $this->belongsTo(Recu::class);
    }
    
}
