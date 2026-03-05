<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HorarioMissa extends Model
{
    protected $table = 'horario_missas';

    protected $fillable = [
        'igreja_id',
        'dia_semana',
        'horario',
    ];

    public function igreja(): BelongsTo
    {
        return $this->belongsTo(Igreja::class);
    }
}
