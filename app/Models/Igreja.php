<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Igreja extends Model
{
    protected $fillable = [
        'paroquia_id',
        'nome',
        'bairro',
        'endereco',
    ];

    public function paroquia(): BelongsTo
    {
        return $this->belongsTo(Paroquia::class);
    }

    public function horarioMissas(): HasMany
    {
        return $this->hasMany(HorarioMissa::class);
    }
}
