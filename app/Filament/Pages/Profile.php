<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

class Profile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static string $view = 'filament.pages.profile';
    protected static ?string $title = 'Mi perfil';
    protected static ?string $navigationLabel = 'Mi perfil';
    protected static ?string $slug = 'perfil';

    public ?array $data = [];

    public function mount(): void
    {
        $user = auth()->user();
        $this->form->fill([
            'name' => $user->name,
            'apellido' => $user->apellido,
            'fecha_nacimiento' => $user->fecha_nacimiento,
            'telefono' => $user->telefono,
            'email' => $user->email,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información de perfil')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre')
                            ->required(),
                        TextInput::make('apellido')
                            ->label('Apellido')
                            ->maxLength(45),
                        DatePicker::make('fecha_nacimiento')
                            ->label('Fecha de Nacimiento')
                            ->format('Y-m-d')
                            ->maxDate(now()),
                        TextInput::make('telefono')
                            ->label('Teléfono')
                            ->tel()
                            ->maxLength(15),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true),
                    ]),
                Section::make('Actualizar contraseña')
                    ->schema([
                        TextInput::make('current_password')
                            ->label('Contraseña actual')
                            ->password()
                            ->rules(['required_with:new_password']),
                        TextInput::make('new_password')
                            ->label('Nueva contraseña')
                            ->password()
                            ->rules(['confirmed']),
                        TextInput::make('new_password_confirmation')
                            ->label('Confirmar nueva contraseña')
                            ->password(),
                    ]),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        $user = auth()->user();

        // Actualizar información básica
        $user->name = $data['name'];
        $user->apellido = $data['apellido'];
        $user->fecha_nacimiento = $data['fecha_nacimiento'];
        $user->telefono = $data['telefono'];
        $user->email = $data['email'];

        // Actualizar contraseña si se proporciona
        if (!empty($data['current_password']) && !empty($data['new_password'])) {
            if (!Hash::check($data['current_password'], $user->password)) {
                Notification::make()
                    ->danger()
                    ->title('La contraseña actual es incorrecta')
                    ->send();
                return;
            }

            $user->password = Hash::make($data['new_password']);
        }

        $user->save();

        Notification::make()
            ->success()
            ->title('Perfil actualizado correctamente')
            ->send();
    }
}
