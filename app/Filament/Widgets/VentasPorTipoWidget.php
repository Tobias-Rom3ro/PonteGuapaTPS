<?php

namespace App\Filament\Widgets;

use App\Models\Venta;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class VentasPorTipoWidget extends ChartWidget
{
    protected static ?string $heading = 'Ventas por Tipo (Este Mes)';
    protected static ?int $sort = 6;
    protected int | string | array $columnSpan = 1;

    public ?string $filter = 'mes';

    protected function getData(): array
    {
        $periodo = match ($this->filter) {
            'hoy' => [Carbon::today(), Carbon::today()->endOfDay()],
            'semana' => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            'mes' => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
            default => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
        };

        $ventasServicios = Venta::where('tipo', 'servicio')
            ->whereBetween('fecha_venta', $periodo)
            ->sum('total');

        $ventasProductos = Venta::where('tipo', 'producto')
            ->whereBetween('fecha_venta', $periodo)
            ->sum('total');

        return [
            'datasets' => [
                [
                    'data' => [$ventasServicios, $ventasProductos],
                    'backgroundColor' => ['#10B981', '#3B82F6'],
                    'borderWidth' => 0,
                ],
            ],
            'labels' => ['Servicios', 'Productos'],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getFilters(): ?array
    {
        return [
            'hoy' => 'Hoy',
            'semana' => 'Esta Semana',
            'mes' => 'Este Mes',
        ];
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return context.label + ": $" + context.parsed.toLocaleString() + " (" + percentage + "%)";
                        }',
                    ],
                ],
            ],
        ];
    }

    // Solo mostrar a administradores y empleados
    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['administrador', 'empleado']);
    }
}
