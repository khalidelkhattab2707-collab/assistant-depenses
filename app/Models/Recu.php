<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use app\Enums\StatutRecu;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recu extends Model
{
    protected $fillable=['user_id','text_brut','status','devis','payload_ia','total_estime'];
    protected $casts =[ 
      'status' => StatutRecu::class,
      'payload_ia' => 'array',
      'total_estime' => 'decimal:2' 
      ];
    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);

    }
    public function depenses():HasMany
    {
        return $this->hasMany(Depense::class);
    }

}
