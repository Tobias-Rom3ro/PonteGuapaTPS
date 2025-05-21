<?php

namespace App\Filament\Resources\CitaResource\Pages;

use App\Filament\Resources\CitaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Form;

class EditCita extends EditRecord
{
    protected static string $resource = CitaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function form(Form $form): Form
    {
        return $this->resource::form($form);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Si no es staff, asegurarse de que no pueda cambiar el estado
        if (!auth()->user()->hasAnyRole(['administrador', 'empleado'])) {
            unset($data['estado']);
        }

        $record->update($data);
        return $record;
    }

    protected function authorizeAccess(): void
    {
        // Verificar si el usuario puede editar esta cita específica
        $record = $this->getRecord();
        
        abort_unless(
            auth()->user()->hasAnyRole(['administrador', 'empleado']) || 
            auth()->id() === $record->user_id,
            403
        );
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
