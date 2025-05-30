<?php

namespace App\Filament\Widgets;

use App\Models\Cita;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Actions\Action;

class MisCitasWidget extends BaseWidget
{
    protected static ?string $heading = 'Citas Recientes';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 1;

    // Mostrar para todos los usuarios autenticados
    public static function canView(): bool
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
            ->query($query->latest()->limit(10)) // Mostrar solo las 10 más recientes
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->sortable()
                    ->searchable()
                    ->label('Cliente')
                    ->visible(auth()->user()->hasAnyRole(['administrador', 'empleado'])),
                Tables\Columns\TextColumn::make('servicio.nombre')
                    ->sortable()
                    ->searchable()
                    ->label('Servicio'),
                Tables\Columns\TextColumn::make('fecha_hora')
                    ->dateTime('d/m/Y H:i')
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
                Tables\Actions\EditAction::make()
                    ->url(fn (Cita $record): string => route('filament.admin.resources.citas.edit', $record))
                    ->visible(fn (Cita $record): bool =>
                        auth()->user()->hasAnyRole(['administrador', 'empleado']) ||
                        auth()->id() === $record->user_id
                    ),
            ])
            ->headerActions([
                Action::make('agendar_cita')
                    ->label('Agendar Nueva Cita')
                    ->icon('heroicon-o-plus')
                    ->color('primary')
                    ->url(route('filament.admin.resources.citas.create'))
                    ->button(),
                Action::make('ver_todas')
                    ->label('Ver Todas')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->url(route('filament.admin.resources.citas.index'))
                    ->link()
            ])
            ->emptyStateHeading('No hay citas registradas')
            ->emptyStateDescription('Agenda tu primera cita haciendo clic en el botón de arriba.')
            ->emptyStateIcon('heroicon-o-calendar')
            ->paginated(false);
    }

    public function getColumnSpan(): int | string | array
    {
        return 2;
    }
}
