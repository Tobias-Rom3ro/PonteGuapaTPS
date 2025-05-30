<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Venta extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo',
        'user_id',
        'vendedor_id',
        'cita_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
        'total',
        'notas',
        'fecha_venta',
    ];

    protected $casts = [
        'precio_unitario' => 'decimal:2',
        'total' => 'decimal:2',
        'cantidad' => 'integer',
        'fecha_venta' => 'datetime',
    ];

    // Relaciones
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function vendedor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendedor_id');
    }

    public function cita(): BelongsTo
    {
        return $this->belongsTo(Cita::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    // Scopes
    public function scopeServicios($query)
    {
        return $query->where('tipo', 'servicio');
    }

    public function scopeProductos($query)
    {
        return $query->where('tipo', 'producto');
    }

    public function scopeEntreFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha_venta', [$fechaInicio, $fechaFin]);
    }

    public function scopeDelVendedor($query, $vendedorId)
    {
        return $query->where('vendedor_id', $vendedorId);
    }

    // Métodos estáticos para crear ventas
    public static function crearVentaServicio(Cita $cita, $vendedorId = null)
    {
        $vendedorId = $vendedorId ?: auth()->id();
        $servicio = $cita->servicio;

        return self::create([
            'tipo' => 'servicio',
            'user_id' => $cita->user_id,
            'vendedor_id' => $vendedorId,
            'cita_id' => $cita->id,
            'cantidad' => 1,
            'precio_unitario' => $servicio->precio,
            'total' => $servicio->precio,
            'fecha_venta' => now(),
        ]);
    }

    public static function crearVentaProducto($clienteId, $productoId, $cantidad, $vendedorId = null, $notas = null)
    {
        $vendedorId = $vendedorId ?: auth()->id();
        $producto = Producto::findOrFail($productoId);

        return self::create([
            'tipo' => 'producto',
            'user_id' => $clienteId,
            'vendedor_id' => $vendedorId,
            'producto_id' => $productoId,
            'cantidad' => $cantidad,
            'precio_unitario' => $producto->precio,
            'total' => $producto->precio * $cantidad,
            'notas' => $notas,
            'fecha_venta' => now(),
        ]);
    }

    // Accessor para obtener el nombre del item vendido
    public function getNombreItemAttribute()
    {
        if ($this->tipo === 'servicio') {
            return $this->cita?->servicio?->nombre ?? 'Servicio eliminado';
        }

        return $this->producto?->nombre ?? 'Producto eliminado';
    }

    // Accessor para obtener información completa del item
    public function getDetalleItemAttribute()
    {
        if ($this->tipo === 'servicio') {
            $servicio = $this->cita?->servicio;
            return $servicio ? "Servicio: {$servicio->nombre}" : 'Servicio eliminado';
        }

        $producto = $this->producto;
        return $producto ? "Producto: {$producto->nombre} (x{$this->cantidad})" : 'Producto eliminado';
    }
}
