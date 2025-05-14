<?php

namespace App\Filament\Pages;

use App\Models\TipoServicio;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;

class TiposServiciosList extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static string $view = 'filament.pages.tipos-servicios-list';
    protected static ?string $title = 'Tipos de Servicios';
    protected static ?string $navigationLabel = 'Tipos de Servicios';
    protected static ?string $navigationGroup = 'Servicios';
    protected static ?int $navigationSort = 1;

    // Solo mostrar para empleados y usuarios normales (no administradores)
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->check() && !auth()->user()->hasRole('administrador');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(TipoServicio::query())
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable()
                    ->sortable()
                    ->label('Nombre'),
                Tables\Columns\TextColumn::make('descripcion')
                    ->searchable()
                    ->label('Descripción'),
            ])
            ->filters([
                //
            ]);
    }
}
