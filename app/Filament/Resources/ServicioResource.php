<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServicioResource\Pages;
use App\Models\Servicio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Placeholder;

class ServicioResource extends Resource
{
    protected static ?string $model = Servicio::class;

    protected static ?string $modelLabel = 'Servicio';

    protected static ?string $pluralModelLabel = 'Servicios';

    protected static ?string $navigationIcon = 'heroicon-o-scissors';

    protected static ?string $navigationGroup = 'Servicios';

    protected static ?int $navigationSort = 2;

    // Solo mostrar en navegación si es administrador
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->check() && auth()->user()->hasRole('administrador');
    }

    // Solo permitir acceso a administradores
    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->hasRole('administrador');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información Básica')
                    ->schema([
                        Forms\Components\Select::make('tipo_servicio_id')
                            ->relationship('tipoServicio', 'nombre')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Tipo de Servicio'),
                        Forms\Components\TextInput::make('nombre')
                            ->required()
                            ->maxLength(45)
                            ->label('Nombre'),
                        Forms\Components\TextInput::make('descripcion')
                            ->maxLength(45)
                            ->label('Descripción'),
                        Forms\Components\TextInput::make('precio')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->label('Precio'),
                    ]),

                Section::make('Configuración de Combo')
                    ->schema([
                        Toggle::make('es_combo')
                            ->label('¿Es un combo?')
                            ->helperText('Activa esta opción para crear un combo de servicios')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if (!$state) {
                                    $set('servicios_incluidos', []);
                                }
                            }),

                        CheckboxList::make('servicios_incluidos')
                            ->label('Servicios incluidos en el combo')
                            ->options(function () {
                                return Servicio::where('es_combo', false)
                                    ->pluck('nombre', 'id')
                                    ->toArray();
                            })
                            ->columns(2)
                            ->visible(fn (callable $get) => $get('es_combo'))
                            ->required(fn (callable $get) => $get('es_combo'))
                            ->helperText('Selecciona los servicios que se incluyen en este combo'),

                        Placeholder::make('precio_total_info')
                            ->label('Información del Combo')
                            ->content(function (callable $get) {
                                $serviciosIds = $get('servicios_incluidos') ?? [];
                                if (empty($serviciosIds) || !$get('es_combo')) {
                                    return 'Selecciona servicios para ver el precio total';
                                }

                                $servicios = Servicio::whereIn('id', $serviciosIds)->get();
                                $precioTotal = $servicios->sum('precio');
                                $precioCombo = (float)($get('precio') ?? 0);
                                $descuento = $precioTotal - $precioCombo;

                                $serviciosNombres = $servicios->pluck('nombre')->join(', ');

                                return "Servicios: {$serviciosNombres}\n" .
                                    "Precio total individual: $" . number_format($precioTotal, 0, ',', '.') . "\n" .
                                    "Precio del combo: $" . number_format($precioCombo, 0, ',', '.') . "\n" .
                                    "Descuento: $" . number_format($descuento, 0, ',', '.');
                            })
                            ->visible(fn (callable $get) => $get('es_combo') && !empty($get('servicios_incluidos')))
                    ])
                    ->collapsible()
                    ->collapsed(fn (callable $get) => !$get('es_combo'))
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('es_combo')
                    ->boolean()
                    ->label('Combo')
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-minus'),
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
                    ->label('Servicios del Combo')
                    ->getStateUsing(function (Servicio $record) {
                        if (!$record->es_combo) {
                            return '-';
                        }

                        $servicios = $record->serviciosDelCombo();
                        return $servicios->pluck('nombre')->join(', ');
                    })
                    ->wrap()
                    ->visible(fn () => request()->has('combo') || request()->get('combo') === 'true'),
                Tables\Columns\TextColumn::make('descuento')
                    ->label('Descuento')
                    ->money('COP')
                    ->getStateUsing(function (Servicio $record) {
                        return $record->es_combo ? $record->descuento_combo : 0;
                    })
                    ->visible(fn () => request()->has('combo') || request()->get('combo') === 'true'),
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
                Tables\Filters\SelectFilter::make('tipo_servicio_id')
                    ->relationship('tipoServicio', 'nombre')
                    ->label('Tipo de Servicio'),
                Tables\Filters\TernaryFilter::make('es_combo')
                    ->label('Tipo')
                    ->placeholder('Todos')
                    ->trueLabel('Solo combos')
                    ->falseLabel('Solo servicios individuales'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListServicios::route('/'),
            'create' => Pages\CreateServicio::route('/create'),
            'edit' => Pages\EditServicio::route('/{record}/edit'),
        ];
    }
}
