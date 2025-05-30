<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CitaResource\Pages;
use App\Filament\Resources\CitaResource\RelationManagers;
use App\Models\Cita;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;

class CitaResource extends Resource
{
    protected static ?string $model = Cita::class;

    protected static ?string $modelLabel = 'Cita';

    protected static ?string $pluralModelLabel = 'Citas';

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationGroup = 'Mis Citas';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Agendar Cita';

    // Mostrar para todos los usuarios autenticados
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->check();
    }

    // Permitir acceso a todos los usuarios autenticados
    public static function canAccess(): bool
    {
        return auth()->check();
    }

    public static function canEdit(Model $record): bool
    {
        // Administradores y empleados pueden editar cualquier cita
        if (auth()->user()->hasAnyRole(['administrador', 'empleado'])) {
            return true;
        }

        // Usuarios normales solo pueden editar sus propias citas
        return auth()->id() === $record->user_id;
    }

    public static function canDelete(Model $record): bool
    {
        // Administradores y empleados pueden eliminar cualquier cita
        if (auth()->user()->hasAnyRole(['administrador', 'empleado'])) {
            return true;
        }

        // Usuarios normales solo pueden eliminar sus propias citas
        return auth()->id() === $record->user_id;
    }

    public static function form(Form $form): Form
    {
        $isStaff = auth()->user()->hasAnyRole(['administrador', 'empleado']);

        $schema = [];

        if ($isStaff) {
            // Si es staff, mostrar selector de usuario
            $schema[] = Forms\Components\Select::make('user_id')
                ->label('Cliente')
                ->required()
                ->searchable()
                ->preload()
                ->relationship(
                    'user',
                    'name',
                    fn (Builder $query) => $query->whereHas('roles', function ($q) {
                        $q->where('name', 'usuario');
                    })
                );
        } else {
            // Si es usuario normal, campo oculto con su ID
            $schema[] = Forms\Components\Hidden::make('user_id')
                ->default(auth()->id());
        }

        // Agregar el resto de campos
        $schema = array_merge($schema, [
            Forms\Components\Select::make('servicio_id')
                ->relationship('servicio', 'nombre')
                ->required()
                ->searchable()
                ->preload()
                ->label('Servicio'),
            Forms\Components\DateTimePicker::make('fecha_hora')
                ->required()
                ->label('Fecha y Hora'),
            Forms\Components\Select::make('estado')
                ->options([
                    'pendiente' => 'Pendiente',
                    'confirmada' => 'Confirmada',
                    'cancelada' => 'Cancelada',
                    'completada' => 'Completada',
                ])
                ->required()
                ->default('pendiente')
                ->label('Estado')
                ->visible($isStaff), // Solo el admin y empleados pueden cambiar el estado
            Forms\Components\Textarea::make('notas')
                ->maxLength(65535)
                ->label('Notas'),
        ]);

        return $form->schema($schema);
    }

    public static function table(Table $table): Table
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
                Tables\Columns\SelectColumn::make('estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'confirmada' => 'Confirmada',
                        'cancelada' => 'Cancelada',
                        'completada' => 'Completada',
                    ])
                    ->sortable()
                    ->label('Estado'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Creado el'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Actualizado el'),
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
                    ->modalWidth('lg')
                    ->using(function (Model $record, array $data): Model {
                        if (!auth()->user()->hasAnyRole(['administrador', 'empleado'])) {
                            unset($data['estado']);
                        }
                        $record->update($data);
                        return $record;
                    })
                    ->form(fn (Form $form) => static::form($form))
                    ->visible(fn (Model $record): bool => static::canEdit($record)),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCitas::route('/'),
            'create' => Pages\CreateCita::route('/create'),
            'edit' => Pages\EditCita::route('/{record}/edit'),
        ];
    }

    protected function handleRecordCreation(array $data): Model
    {
        if (!auth()->user()->hasAnyRole(['administrador', 'empleado'])) {
            $data['estado'] = 'pendiente';
        }
        return static::getModel()::create($data);
    }
}
