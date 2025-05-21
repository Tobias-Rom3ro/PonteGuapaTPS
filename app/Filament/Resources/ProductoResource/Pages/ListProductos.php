<?php

namespace App\Filament\Resources\ProductoResource\Pages;

use App\Filament\Resources\ProductoResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\IconPosition;

class ListProductos extends ListRecords
{
    protected static string $resource = ProductoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make()
                ->icon('heroicon-o-plus')
                ->iconPosition(IconPosition::After),
        ];
    }

    public function getDefaultTableRecordsPerPageSelectOption(): int
    {
        return 12;
    }

    public function getViewData(): array
    {
        return [
            'records' => $this->getRecords(),
        ];
    }

    /**
     * Devuelve la colección de registros para la vista personalizada.
     */
    protected function getRecords()
    {
        // Usa la query definida en el recurso para obtener los registros
        $query = static::getResource()::getEloquentQuery();
        return $query->get();
    }

    protected function getTableView(): string
    {
        return 'filament.resources.producto-resource.pages.producto-grid';
    }
}
