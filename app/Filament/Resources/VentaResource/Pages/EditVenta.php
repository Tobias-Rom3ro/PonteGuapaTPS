<?php

namespace App\Filament\Resources\VentaResource\Pages;

use App\Filament\Resources\VentaResource;
use App\Models\Producto;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditVenta extends EditRecord
{
    protected static string $resource = VentaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->visible(fn () => auth()->user()->hasRole('administrador')),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Solo permitir editar ventas de productos
        if ($record->tipo !== 'producto') {
            $this->halt();
            $this->notify('danger', 'Solo se pueden editar ventas de productos');
            return $record;
        }

        // Manejar cambios en el stock si cambió la cantidad
        if (isset($data['cantidad']) && $data['cantidad'] != $record->cantidad) {
            $producto = Producto::find($record->producto_id);
            if ($producto) {
                $diferencia = $data['cantidad'] - $record->cantidad;

                // Verificar stock disponible
                if ($diferencia > 0 && $producto->stock < $diferencia) {
                    $this->halt();
                    $this->notify('danger', "Stock insuficiente. Disponible: {$producto->stock}");
                    return $record;
                }

                // Ajustar stock
                $producto->decrement('stock', $diferencia);
            }
        }

        $record->update($data);
        return $record;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
