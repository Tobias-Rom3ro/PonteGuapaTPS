<?php

namespace App\Filament\Resources\VentaResource\Pages;

use App\Filament\Resources\VentaResource;
use App\Models\Producto;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateVenta extends CreateRecord
{
    protected static string $resource = VentaResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // Verificar stock del producto
        if ($data['tipo'] === 'producto') {
            $producto = Producto::find($data['producto_id']);
            if ($producto && $producto->stock < $data['cantidad']) {
                $this->halt();
                $this->notify('danger', "Stock insuficiente. Disponible: {$producto->stock}");
                return new \App\Models\Venta();
            }

            // Reducir stock
            if ($producto) {
                $producto->decrement('stock', $data['cantidad']);
            }
        }

        return static::getModel()::create($data);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
