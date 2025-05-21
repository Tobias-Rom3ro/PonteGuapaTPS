<?php

namespace App\Filament\Pages;

use App\Models\Cita;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;

class MisCitas extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static string $view = 'filament.pages.mis-citas';
    protected static ?string $title = 'Mis Citas';
    protected static ?string $navigationLabel = 'Mis Citas';
    protected static ?string $navigationGroup = 'Mis Citas';
    protected static ?int $navigationSort = 1;

    // Mostrar para todos los usuarios autenticados
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->check();
    }

    public function table(Table $table): Table
    {
        // Si es administrador o empleado, mostrar todas las citas
        // Si es usuario normal, mostrar solo sus citas
        $query = Cita::query();
        if (!auth()->user()->hasAnyRole(['administrador', 'empleado'])) {
            $query->where('user_id', auth()->id());
        }

        return $table
            ->query($query)
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->sortable()
                    ->searchable()
                    ->label('Cliente'),
                Tables\Columns\TextColumn::make('servicio.nombre')
                    ->sortable()
                    ->searchable()
                    ->label('Servicio'),
                Tables\Columns\TextColumn::make('fecha_hora')
                    ->dateTime()
                    ->sortable()
                    ->label('Fecha y Hora'),
                Tables\Columns\TextColumn::make('estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pendiente' => 'warning',
                        'confirmada' => 'success',
                        'cancelada' => 'danger',
                        'completada' => 'info',
                        default => 'gray',
                    })
                    ->sortable()
                    ->label('Estado'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'confirmada' => 'Confirmada',
                        'cancelada' => 'Cancelada',
                        'completada' => 'Completada',
                    ])
                    ->label('Estado'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
} 