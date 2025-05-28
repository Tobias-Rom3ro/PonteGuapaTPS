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
                Tables\Columns\IconColumn::make('es_combo')
                    ->boolean()
                    ->label('Combo')
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-minus')
                    ->tooltip(fn (Servicio $record) => $record->es_combo ? 'Combo de servicios' : 'Servicio individual'),
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
                Tables\Columns\TextColumn::make('servicios_combo')
                    ->label('Servicios Incluidos')
                    ->getStateUsing(function (Servicio $record) {
                        if (!$record->es_combo) {
                            return '-';
                        }

                        $servicios = $record->serviciosDelCombo();
                        return $servicios->pluck('nombre')->join(', ');
                    })
                    ->wrap()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('descuento_info')
                    ->label('Ahorro')
                    ->getStateUsing(function (Servicio $record) {
                        if (!$record->es_combo) {
                            return '-';
                        }

                        $descuento = $record->descuento_combo;
                        return $descuento > 0 ? '$' . number_format($descuento, 0, ',', '.') : '-';
                    })
                    ->color(fn (Servicio $record) => $record->es_combo && $record->descuento_combo > 0 ? 'success' : 'gray')
                    ->weight('bold')
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipo_servicio_id')
                    ->relationship('tipoServicio', 'nombre')
                    ->label('Tipo de Servicio'),
                Tables\Filters\TernaryFilter::make('es_combo')
                    ->label('Tipo de Servicio')
                    ->placeholder('Todos los servicios')
                    ->trueLabel('Solo combos')
                    ->falseLabel('Solo servicios individuales'),
            ])
            ->defaultSort('es_combo', 'desc'); // Mostrar combos primero
    }
}
