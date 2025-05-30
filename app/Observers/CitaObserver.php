<?php

namespace App\Observers;

use App\Models\Cita;
use App\Models\Venta;

class CitaObserver
{
    /**
     * Handle the Cita "updated" event.
     */
    public function updated(Cita $cita): void
    {
        // Verificar si el estado cambió a 'completada'
        if ($cita->isDirty('estado') && $cita->estado === 'completada') {
            // Verificar que no exista ya una venta para esta cita
            $ventaExistente = Venta::where('cita_id', $cita->id)->first();

            if (!$ventaExistente) {
                // Crear la venta automáticamente
                Venta::crearVentaServicio($cita);
            }
        }
    }
}
