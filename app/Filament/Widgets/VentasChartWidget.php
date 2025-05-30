<?php

namespace App\Filament\Widgets;

use App\Models\Venta;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class VentasChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Ventas por Día (Últimos 30 días)';
    protected static ?int $sort = 2;
    protected static ?string $pollingInterval = '60s';

    protected function getData(): array
    {
        $fechas = collect();
        $ventas = collect();

        // Obtener los últimos 30 días
        for ($i = 29; $i >= 0; $i--) {
            $fecha = Carbon::now()->subDays($i);
            $fechas->push($fecha->format('d/m'));

            $ventasDia = Venta::whereDate('fecha_venta', $fecha->toDateString())
                ->sum('total');
            $ventas->push($ventasDia);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Ventas ($)',
                    'data' => $ventas->toArray(),
                    'borderColor' => '#10B981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $fechas->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => 'function(value) { return "$" + value.toLocaleString(); }',
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
