<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VentaResource\Pages;
use App\Models\Venta;
use App\Models\Producto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Database\Eloquent\Builder;

class VentaResource extends Resource
{
    protected static ?string $model = Venta::class;
    protected static ?string $modelLabel = 'Venta';
    protected static ?string $pluralModelLabel = 'Ventas';
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Gestión de ventas';
    protected static ?int $navigationSort = 1;

    // Solo administradores y empleados pueden ver las ventas
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['administrador', 'empleado']);
    }

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['administrador', 'empleado']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información de la Venta')
                    ->schema([
                        Forms\Components\Select::make('tipo')
                            ->options([
                                'producto' => 'Producto',
                            ])
                            ->default('producto')
                            ->disabled()
                            ->dehydrated()
                            ->label('Tipo de Venta'),

                        Forms\Components\Select::make('user_id')
                            ->relationship('cliente', 'name', function (Builder $query) {
                                $query->whereHas('roles', function ($q) {
                                    $q->where('name', 'usuario');
                                });
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Cliente'),

                        Forms\Components\Hidden::make('vendedor_id')
                            ->default(auth()->id()),

                        Forms\Components\Select::make('producto_id')
                            ->relationship('producto', 'nombre')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                if ($state) {
                                    $producto = Producto::find($state);
                                    if ($producto) {
                                        $set('precio_unitario', $producto->precio);
                                        $cantidad = $get('cantidad') ?: 1;
                                        $set('total', $producto->precio * $cantidad);
                                    }
                                }
                            })
                            ->label('Producto'),

                        Forms\Components\TextInput::make('cantidad')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                $precio = $get('precio_unitario');
                                if ($precio && $state) {
                                    $set('total', $precio * $state);
                                }
                            })
                            ->label('Cantidad'),

                        Forms\Components\TextInput::make('precio_unitario')
                            ->numeric()
                            ->prefix('$')
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                $cantidad = $get('cantidad') ?: 1;
                                if ($state) {
                                    $set('total', $state * $cantidad);
                                }
                            })
                            ->label('Precio Unitario'),

                        Forms\Components\TextInput::make('total')
                            ->numeric()
                            ->prefix('$')
                            ->disabled()
                            ->dehydrated()
                            ->label('Total'),

                        Forms\Components\DateTimePicker::make('fecha_venta')
                            ->default(now())
                            ->required()
                            ->label('Fecha de Venta'),

                        Forms\Components\Textarea::make('notas')
                            ->rows(3)
                            ->label('Notas'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'servicio' => 'success',
                        'producto' => 'info',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'servicio' => 'Servicio',
                        'producto' => 'Producto',
                    })
                    ->sortable()
                    ->label('Tipo'),

                Tables\Columns\TextColumn::make('cliente.name')
                    ->sortable()
                    ->searchable()
                    ->label('Cliente'),

                Tables\Columns\TextColumn::make('vendedor.name')
                    ->sortable()
                    ->searchable()
                    ->label('Vendedor'),

                Tables\Columns\TextColumn::make('detalle_item')
                    ->label('Detalle')
                    ->wrap()
                    ->limit(50),

                Tables\Columns\TextColumn::make('cantidad')
                    ->alignCenter()
                    ->sortable()
                    ->label('Cant.'),

                Tables\Columns\TextColumn::make('precio_unitario')
                    ->money('COP')
                    ->sortable()
                    ->label('Precio Unit.'),

                Tables\Columns\TextColumn::make('total')
                    ->money('COP')
                    ->sortable()
                    ->weight('bold')
                    ->color('success')
                    ->label('Total'),

                Tables\Columns\TextColumn::make('fecha_venta')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->label('Fecha'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Registrado'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipo')
                    ->options([
                        'servicio' => 'Servicios',
                        'producto' => 'Productos',
                    ])
                    ->label('Tipo'),

                Tables\Filters\SelectFilter::make('vendedor_id')
                    ->relationship('vendedor', 'name')
                    ->label('Vendedor'),

                Tables\Filters\Filter::make('fecha_venta')
                    ->form([
                        Forms\Components\DatePicker::make('desde')
                            ->label('Desde'),
                        Forms\Components\DatePicker::make('hasta')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['desde'],
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha_venta', '>=', $date),
                            )
                            ->when(
                                $data['hasta'],
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha_venta', '<=', $date),
                            );
                    })
                    ->label('Rango de Fechas'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) => $record->tipo === 'producto'), // Solo productos se pueden editar
                Tables\Actions\DeleteAction::make()
                    ->visible(fn ($record) => $record->tipo === 'producto' && auth()->user()->hasRole('administrador')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()->hasRole('administrador')),
                ]),
            ])
            ->defaultSort('fecha_venta', 'desc')
            ->poll('30s'); // Actualizar cada 30 segundos
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVentas::route('/'),
            'create' => Pages\CreateVenta::route('/create'),
            'view' => Pages\ViewVenta::route('/{record}'),
            'edit' => Pages\EditVenta::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        // Mostrar el número de ventas del día actual
        return static::getModel()::whereDate('fecha_venta', today())->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}
