<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Ponte Guapa</title>
    <link rel="stylesheet" href="{{ asset('styles/register.css') }}">
    <link rel="icon" href="{{ asset('resources/icons/icon.png') }}" type="image/png">
</head>

<body>
<div class="background-container">
    <img src="{{ asset('resources/images/background.jpeg') }}" alt="Fondo" class="background-image"/>
</div>
<main class="login-container">
    <div class="login-card">
        <div class="login-content">
            <div class="login-header">
                <img src="{{ asset('resources/icons/icon.png') }}" alt="Logo Ponte Guapa" class="logo" />
                <h1>¡Crea tu cuenta!</h1>
                <p class="subtitle">Regístrate para disfrutar de nuestros servicios</p>
            </div>
            <form class="login-form" method="POST" action="{{ route('register') }}">
                @csrf

                <div class="form-group">
                    <label for="name">Nombre</label>
                    <input name="name" type="text" id="name" placeholder="Escribe tu nombre completo" class="form-input" value="{{ old('name') }}" required autofocus>
                    @error('name')
                    <span class="invalid-feedback" role="alert" style="color: red; font-size: 12px; margin-top: 5px; display: block;">
                        {{ $message }}
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input name="email" type="email" id="email" placeholder="Tu dirección de email" class="form-input" value="{{ old('email') }}" required>
                    @error('email')
                    <span class="invalid-feedback" role="alert" style="color: red; font-size: 12px; margin-top: 5px; display: block;">
                        {{ $message }}
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <div class="password-container">
                        <input name="password" id="password" type="password" placeholder="Crea una contraseña" class="form-input" required>
                        <img class="eye-icon" src="{{ asset('resources/icons/icon-eye.png') }}" alt="Ver">
                    </div>
                    @error('password')
                    <span class="invalid-feedback" role="alert" style="color: red; font-size: 12px; margin-top: 5px; display: block;">
                        {{ $message }}
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password-confirm">Confirmar contraseña</label>
                    <div class="password-container">
                        <input name="password_confirmation" id="password-confirm" type="password" placeholder="Confirma tu contraseña" class="form-input" required>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="login-button">Registrarse</button>
                </div>

                <div class="separator">
                    <span>O</span>
                </div>

                <button type="button" class="google-button">
                    <img src="{{ asset('resources/icons/icon-google.svg') }}" alt="Google" width="18" height="18">
                    Regístrate con Google
                </button>
            </form>


            <div class="account-options">
                <p>¿Ya tienes una cuenta? <a href="{{ route('login') }}" class="admin-link">Inicia sesión</a></p>
            </div>
        </div>
    </div>
</main>
<script src="{{ asset('js/PasswordVisibility.js') }}"></script>
</body>
