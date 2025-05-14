<?php

namespace App\Filament\Pages;

use App\Models\Servicio;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;

class ServiciosList extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-scissors';
    protected static string $view = 'filament.pages.servicios-list';
    protected static ?string $title = 'Servicios';
    protected static ?string $navigationLabel = 'Servicios';
    protected static ?string $navigationGroup = 'Servicios';
    protected static ?int $navigationSort = 2;

    // Solo mostrar para empleados y usuarios normales (no administradores)
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->check() && !auth()->user()->hasRole('administrador');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Servicio::query())
            ->columns([
                Tables\Columns\TextColumn::make('tipoServicio.nombre')
                    ->sortable()
                    ->searchable()
                    ->label('Tipo de Servicio'),
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable()
                    ->sortable()
                    ->label('Nombre'),
                Tables\Columns\TextColumn::make('descripcion')
                    ->searchable()
                    ->label('Descripción'),
                Tables\Columns\TextColumn::make('precio')
                    ->money('COP')
                    ->sortable()
                    ->label('Precio'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipo_servicio_id')
                    ->relationship('tipoServicio', 'nombre')
                    ->label('Tipo de Servicio'),
            ]);
    }
}
