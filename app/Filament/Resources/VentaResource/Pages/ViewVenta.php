<?php

namespace App\Filament\Resources\VentaResource\Pages;

use App\Filament\Resources\VentaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewVenta extends ViewRecord
{
    protected static string $resource = VentaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn ($record) => $record->tipo === 'producto'),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Información de la Venta')
                    ->schema([
                        Infolists\Components\TextEntry::make('tipo')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'servicio' => 'success',
                                'producto' => 'info',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'servicio' => 'Servicio',
                                'producto' => 'Producto',
                            })
                            ->label('Tipo'),
                        Infolists\Components\TextEntry::make('cliente.name')
                            ->label('Cliente'),
                        Infolists\Components\TextEntry::make('vendedor.name')
                            ->label('Vendedor'),
                        Infolists\Components\TextEntry::make('detalle_item')
                            ->label('Detalle'),
                        Infolists\Components\TextEntry::make('cantidad')
                            ->label('Cantidad'),
                        Infolists\Components\TextEntry::make('precio_unitario')
                            ->money('COP')
                            ->label('Precio Unitario'),
                        Infolists\Components\TextEntry::make('total')
                            ->money('COP')
                            ->label('Total'),
                        Infolists\Components\TextEntry::make('fecha_venta')
                            ->dateTime('d/m/Y H:i')
                            ->label('Fecha de Venta'),
                        Infolists\Components\TextEntry::make('notas')
                            ->label('Notas')
                            ->placeholder('Sin notas'),
                    ])
                    ->columns(2),
            ]);
    }
}
