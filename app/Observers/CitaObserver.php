<?php

namespace App\Observers;

use App\Models\Cita;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;

class CitaObserver
{
    /**
     * Handle the Cita "created" event.
     */
    public function created(Cita $cita): void
    {
        // Notificar a administradores y empleados sobre nueva cita
        $adminUsers = User::role(['administrador', 'empleado'])->get();

        foreach ($adminUsers as $admin) {
            Notification::make()
                ->title('Nueva cita agendada')
                ->body("El cliente {$cita->user->name} ha agendado una cita para {$cita->servicio->nombre}")
                ->icon('heroicon-o-calendar')
                ->color('success')
                ->actions([
                    Action::make('ver')
                        ->label('Ver cita')
                        ->url(route('filament.admin.resources.citas.edit', $cita))
                        ->button(),
                ])
                ->sendToDatabase($admin);
        }

        // Notificar al cliente que creó la cita (confirmación)
        if (auth()->id() === $cita->user_id) {
            Notification::make()
                ->title('Cita agendada correctamente')
                ->body("Tu cita para {$cita->servicio->nombre} ha sido registrada para el {$cita->fecha_hora->format('d/m/Y H:i')}")
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->send();
        }
    }

    /**
     * Handle the Cita "updated" event.
     */
    public function updated(Cita $cita): void
    {
        // Solo notificar si cambió el estado
        if ($cita->wasChanged('estado')) {
            $statusLabels = [
                'pendiente' => 'Pendiente',
                'confirmada' => 'Confirmada',
                'cancelada' => 'Cancelada',
                'completada' => 'Completada',
            ];

            $statusColors = [
                'pendiente' => 'warning',
                'confirmada' => 'success',
                'cancelada' => 'danger',
                'completada' => 'info',
            ];

            // Notificar al cliente sobre el cambio de estado
            Notification::make()
                ->title('Estado de cita actualizado')
                ->body("Tu cita para {$cita->servicio->nombre} ahora está: {$statusLabels[$cita->estado]}")
                ->icon('heroicon-o-bell')
                ->color($statusColors[$cita->estado])
                ->actions([
                    Action::make('ver')
                        ->label('Ver cita')
                        ->url(route('filament.admin.resources.citas.edit', $cita))
                        ->button(),
                ])
                ->sendToDatabase($cita->user);

            // Si es una confirmación, notificar también a otros admins
            if ($cita->estado === 'confirmada') {
                $adminUsers = User::role(['administrador', 'empleado'])
                    ->where('id', '!=', auth()->id())
                    ->get();

                foreach ($adminUsers as $admin) {
                    Notification::make()
                        ->title('Cita confirmada')
                        ->body("La cita de {$cita->user->name} para {$cita->servicio->nombre} ha sido confirmada")
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->sendToDatabase($admin);
                }
            }
        }
    }

    /**
     * Handle the Cita "deleted" event.
     */
    public function deleted(Cita $cita): void
    {
        // Notificar al cliente sobre la cancelación
        Notification::make()
            ->title('Cita cancelada')
            ->body("Tu cita para {$cita->servicio->nombre} programada para el {$cita->fecha_hora->format('d/m/Y H:i')} ha sido cancelada")
            ->icon('heroicon-o-x-circle')
            ->color('danger')
            ->sendToDatabase($cita->user);

        // Notificar a administradores y empleados
        $adminUsers = User::role(['administrador', 'empleado'])->get();

        foreach ($adminUsers as $admin) {
            Notification::make()
                ->title('Cita eliminada')
                ->body("La cita de {$cita->user->name} para {$cita->servicio->nombre} ha sido eliminada")
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->sendToDatabase($admin);
        }
    }
}
