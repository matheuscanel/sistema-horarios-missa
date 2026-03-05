<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Paroquia extends Model
{
    protected $fillable = [
        'nome',
        'status',
    ];

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function igrejas(): HasMany
    {
        return $this->hasMany(Igreja::class);
    }
}
