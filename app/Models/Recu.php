<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recu extends Model
{
    protected $fillable=['user_id','text_brut','status','devis','payload_ia','total_estime'];
    public function user():belongsTo
    {
        return $this->belongsTo(User::class);

    }
    public function depenses():hasMany
    {
        return $this->hasMany(Depense::class);
    }

}
