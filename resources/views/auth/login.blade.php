<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Ponte Guapa</title>
    <link rel="stylesheet" href="{{ asset('styles/login.css') }}">
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
                <h1>¡Bienvenida!</h1>
                <p class="subtitle">Inicia sesión para continuar</p>
            </div>
            <form class="login-form" method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label for="email">Usuario</label>
                    <input name="email" type="email" id="email" placeholder="Email o número de celular" class="form-input" value="{{ old('email') }}" required autofocus>
                    @error('email')
                    <span class="invalid-feedback" role="alert" style="color: red; font-size: 12px; margin-top: 5px; display: block;">
                        {{ $message }}
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <div class="password-container">
                        <input name="password" id="password" type="password" placeholder="Ingresar contraseña" class="form-input" required>
                        <img class="eye-icon" src="{{ asset('resources/icons/icon-eye.png') }}" alt="Ver">
                    </div>
                    @error('password')
                    <span class="invalid-feedback" role="alert" style="color: red; font-size: 12px; margin-top: 5px; display: block;">
                        {{ $message }}
                    </span>
                    @enderror
                </div>

                <div class="remember-reset">
                    <div class="remember-me">
                        <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label for="remember" class="checkbox-label">Recuérdame</label>
                    </div>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-password">¿Olvidaste la contraseña?</a>
                    @endif
                </div>

                <div class="form-actions">
                    <button type="submit" class="login-button">Iniciar sesión</button>
                </div>

                <div class="separator">
                    <span>O</span>
                </div>

                <button type="button" class="google-button">
                    <img src="{{ asset('resources/icons/icon-google.svg') }}" alt="Google" width="18" height="18">
                    Inicia sesión con Google
                </button>
            </form>

            <div class="account-options">
                <p>¿No tienes una cuenta? <a href="{{ route('register') }}" class="admin-link">Registrate aquí</a></p>
            </div>
        </div>
    </div>
</main>

