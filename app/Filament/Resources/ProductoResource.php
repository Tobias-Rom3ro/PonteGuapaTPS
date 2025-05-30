<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductoResource\Pages;
use App\Models\Producto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ProductoResource extends Resource
{
    protected static ?string $model = Producto::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Gestión de Tienda';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextArea::make('descripcion')
                    ->required()
                    ->maxLength(1000),

                Forms\Components\TextInput::make('precio')
                    ->required()
                    ->numeric()
                    ->prefix('$')
                    ->minValue(0)
                    ->step(0.01),

                Forms\Components\TextInput::make('stock')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->default(0),

                Forms\Components\FileUpload::make('imagen_id')
                    ->label('Imagen del producto')
                    ->image()
                    ->required()
                    ->imageEditor()
                    ->disk('cloudinary')
                    ->directory('productos')
                    ->maxSize(5120)
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\ImageColumn::make('imagen_id')
                    ->label('Imagen')
                    ->circular()
                    ->size(60)
                    ->getStateUsing(fn ($record) => $record->getImagenUrl()),

                Tables\Columns\TextColumn::make('precio')
                    ->money('COP')
                    ->sortable(),

                Tables\Columns\TextColumn::make('stock')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (Producto $record) {
                        if ($record->imagen_id) {
                            Cloudinary::admin()->deleteAssets([$record->imagen_id], ['resource_type' => 'image']);
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($records) {
                            foreach ($records as $record) {
                                if ($record->imagen_id) {
                                    Cloudinary::destroy($record->imagen_id);
                                }
                            }
                        }),
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
            'index' => Pages\ListProductos::route('/'),
            'create' => Pages\CreateProducto::route('/create'),
            'edit' => Pages\EditProducto::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function canViewAny(): bool
    {
        $user = Auth::user();
        return $user && $user->can('view productos');
    }

    public static function canCreate(): bool
    {
        $user = Auth::user();
        return $user && $user->can('manage productos');
    }

    public static function canEdit(Model $record): bool
    {
        $user = Auth::user();
        return $user && $user->can('manage productos');
    }

    public static function canDelete(Model $record): bool
    {
        $user = Auth::user();
        return $user && $user->can('manage productos');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['nombre', 'precio'];
    }

    public static function getNavigationSort(): ?int
    {
        return 2; // Posición en el menú
    }

    public static function getModelLabel(): string
    {
        return __('Producto');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Productos');
    }

}
