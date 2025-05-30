<?php

namespace App\Filament\Widgets;

use App\Models\Venta;
use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class VentasPorVendedorWidget extends ChartWidget
{
    protected static ?string $heading = 'Ventas por Vendedor (Este Mes)';
    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $ventasPorVendedor = Venta::whereMonth('fecha_venta', now()->month)
            ->whereYear('fecha_venta', now()->year)
            ->selectRaw('vendedor_id, SUM(total) as total_ventas')
            ->groupBy('vendedor_id')
            ->with('vendedor')
            ->get();

        $labels = [];
        $data = [];
        $backgroundColors = [
            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
            '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF'
        ];

        foreach ($ventasPorVendedor as $index => $venta) {
            $labels[] = $venta->vendedor->name ?? 'Sin vendedor';
            $data[] = $venta->total_ventas;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Ventas ($)',
                    'data' => $data,
                    'backgroundColor' => array_slice($backgroundColors, 0, count($data)),
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'right',
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) { return context.label + ": $" + context.parsed.toLocaleString(); }',
                    ],
                ],
            ],
        ];
    }

    // Solo mostrar a administradores
    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->hasRole('administrador');
    }
}
