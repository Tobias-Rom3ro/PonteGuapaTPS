<?php
// app/Filament/Resources/VentaResource/Pages/ListVentas.php

namespace App\Filament\Resources\VentaResource\Pages;

use App\Filament\Resources\VentaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListVentas extends ListRecords
{
    protected static string $resource = VentaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Registrar Venta'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'todas' => Tab::make('Todas')
                ->badge(fn () => $this->getModel()::count()),
            'hoy' => Tab::make('Hoy')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('fecha_venta', today()))
                ->badge(fn () => $this->getModel()::whereDate('fecha_venta', today())->count()),
            'esta_semana' => Tab::make('Esta Semana')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereBetween('fecha_venta', [now()->startOfWeek(), now()->endOfWeek()]))
                ->badge(fn () => $this->getModel()::whereBetween('fecha_venta', [now()->startOfWeek(), now()->endOfWeek()])->count()),
            'este_mes' => Tab::make('Este Mes')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereMonth('fecha_venta', now()->month))
                ->badge(fn () => $this->getModel()::whereMonth('fecha_venta', now()->month)->count()),
            'servicios' => Tab::make('Servicios')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('tipo', 'servicio'))
                ->badge(fn () => $this->getModel()::where('tipo', 'servicio')->count()),
            'productos' => Tab::make('Productos')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('tipo', 'producto'))
                ->badge(fn () => $this->getModel()::where('tipo', 'producto')->count()),
        ];
    }
}

