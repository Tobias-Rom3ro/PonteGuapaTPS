<?php

namespace App\Filament\Widgets;

use App\Models\Venta;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class TopProductosWidget extends BaseWidget
{
    protected static ?string $heading = 'Productos Más Vendidos (Este Mes)';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Venta::query()
                    ->where('tipo', 'producto')
                    ->whereMonth('fecha_venta', now()->month)
                    ->whereYear('fecha_venta', now()->year)
                    ->selectRaw('
                        producto_id,
                        SUM(cantidad) as total_cantidad,
                        SUM(total) as total_ventas,
                        COUNT(*) as numero_ventas
                    ')
                    ->groupBy('producto_id')
                    ->orderByDesc('total_ventas')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('producto.nombre')
                    ->label('Producto')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_cantidad')
                    ->label('Cantidad Vendida')
                    ->alignCenter()
                    ->sortable(),

                Tables\Columns\TextColumn::make('numero_ventas')
                    ->label('N° Ventas')
                    ->alignCenter()
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_ventas')
                    ->label('Total Generado')
                    ->money('COP')
                    ->sortable()
                    ->weight('bold')
                    ->color('success'),

                Tables\Columns\TextColumn::make('producto.stock')
                    ->label('Stock Actual')
                    ->alignCenter()
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state <= 5 => 'danger',
                        $state <= 10 => 'warning',
                        default => 'success',
                    }),
            ])
            ->defaultSort('total_ventas', 'desc')
            ->paginated(false);
    }

    // Solo mostrar a administradores y empleados
    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['administrador', 'empleado']);
    }

    public function getColumnSpan(): int | string | array
    {
        return 1;
    }
}
