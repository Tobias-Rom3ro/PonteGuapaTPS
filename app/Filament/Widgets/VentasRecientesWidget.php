<?php

namespace App\Filament\Widgets;

use App\Models\Venta;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class VentasRecientesWidget extends BaseWidget
{
    protected static ?string $heading = 'Ventas Recientes';
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Venta::query()
                    ->latest('fecha_venta')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('tipo')
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

                Tables\Columns\TextColumn::make('cliente.name')
                    ->label('Cliente')
                    ->searchable()
                    ->limit(20),

                Tables\Columns\TextColumn::make('detalle_item')
                    ->label('Detalle')
                    ->limit(30)
                    ->wrap(),

                Tables\Columns\TextColumn::make('vendedor.name')
                    ->label('Vendedor')
                    ->limit(15),

                Tables\Columns\TextColumn::make('total')
                    ->money('COP')
                    ->weight('bold')
                    ->color('success')
                    ->label('Total'),

                Tables\Columns\TextColumn::make('fecha_venta')
                    ->dateTime('d/m/Y H:i')
                    ->label('Fecha')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('ver')
                    ->icon('heroicon-m-eye')
                    ->url(fn (Venta $record): string => route('filament.admin.resources.ventas.view', $record))
                    ->openUrlInNewTab(),
            ])
            ->defaultSort('fecha_venta', 'desc')
            ->paginated(false);
    }

    // Solo mostrar a administradores y empleados
    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['administrador', 'empleado']);
    }

    public function getColumnSpan(): int | string | array
    {
        return 2; // ocupa solo una columna
    }
}
