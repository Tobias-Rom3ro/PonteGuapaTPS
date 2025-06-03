<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\MisCitasWidget;
use App\Filament\Widgets\VentasChartWidget;
use App\Filament\Widgets\TopProductosWidget;
use App\Filament\Widgets\VentasRecientesWidget;
use App\Filament\Widgets\VentasPorTipoWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getTitle(): string
    {
        return '¡Bienvenida!';
    }
    public function getWidgets(): array
    {
        return [
            MisCitasWidget::class,
            VentasChartWidget::class,
            VentasPorTipoWidget::class,
            VentasRecientesWidget::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return [
            'md' => 2,
            'xl' => 3,
        ];
    }
}
