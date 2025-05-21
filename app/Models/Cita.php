<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cita extends Model
{
    protected $fillable = [
        'user_id',
        'servicio_id',
        'fecha_hora',
        'estado',
        'notas',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function servicio(): BelongsTo
    {
        return $this->belongsTo(Servicio::class);
    }
}
