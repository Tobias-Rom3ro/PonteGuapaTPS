<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Servicio extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo_servicio_id',
        'nombre',
        'descripcion',
        'precio',
        'es_combo',
        'servicios_incluidos',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'es_combo' => 'boolean',
        'servicios_incluidos' => 'array',
    ];

    public function tipoServicio(): BelongsTo
    {
        return $this->belongsTo(TipoServicio::class);
    }

    // Relación para obtener los servicios incluidos en el combo
    public function serviciosDelCombo()
    {
        if (!$this->es_combo || !$this->servicios_incluidos) {
            return collect();
        }

        return self::whereIn('id', $this->servicios_incluidos)->get();
    }

    // Método para obtener el precio total de los servicios individuales del combo
    public function getPrecioTotalServiciosAttribute()
    {
        if (!$this->es_combo) {
            return $this->precio;
        }

        return $this->serviciosDelCombo()->sum('precio');
    }

    // Método para obtener el descuento del combo
    public function getDescuentoComboAttribute()
    {
        if (!$this->es_combo) {
            return 0;
        }

        $precioTotal = $this->precio_total_servicios;
        return $precioTotal > 0 ? $precioTotal - $this->precio : 0;
    }

    // Scope para obtener solo servicios individuales (no combos)
    public function scopeIndividuales($query)
    {
        return $query->where('es_combo', false);
    }

    // Scope para obtener solo combos
    public function scopeCombos($query)
    {
        return $query->where('es_combo', true);
    }
}
