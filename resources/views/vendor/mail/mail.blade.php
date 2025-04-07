@component('mail::message')
    # Restablecer Contraseña - Ponte Guapa

    Hola,

    Has recibido este correo porque recibimos una solicitud de restablecimiento de contraseña para tu cuenta.

    @component('mail::button', ['url' => $actionUrl])
        {{ $actionText }}
    @endcomponent

    Este enlace de restablecimiento de contraseña expirará en {{ config('auth.passwords.'.config('auth.defaults.passwords').'.expire') }} minutos.

    Si no solicitaste un restablecimiento de contraseña, no es necesario realizar ninguna acción.

    Saludos,<br>
    El equipo de {{ config('app.name') }}

    @component('mail::subcopy')
        Si tienes problemas para hacer clic en el botón "{{ $actionText }}", copia y pega la siguiente URL en tu navegador web:
        <span class="break-all">[{{ $displayableActionUrl }}]({{ $actionUrl }})</span>
    @endcomponent
@endcomponent
