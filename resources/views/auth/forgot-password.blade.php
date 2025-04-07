<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - Ponte Guapa</title>
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
                <h1>¿Olvidaste tu contraseña?</h1>
                <p class="subtitle">Ingresa tu correo electrónico para restablecerla</p>
            </div>
            @if (session('status'))
                <div style="background-color: #d1e7dd; color: #0f5132; padding: 12px; border-radius: 4px; margin-bottom: 20px; text-align: center;">
                    {{ session('status') }}
                </div>
            @endif

            <form class="login-form" method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="form-group">
                    <label for="email">Correo electrónico</label>
                    <input name="email" type="email" id="email" placeholder="Ingresa tu correo electrónico" class="form-input" value="{{ old('email') }}" required autofocus>
                    @error('email')
                    <span class="invalid-feedback" role="alert" style="color: red; font-size: 12px; margin-top: 5px; display: block;">
                        {{ $message }}
                    </span>
                    @enderror
                </div>

                <div class="form-actions">
                    <button type="submit" class="login-button">Enviar enlace</button>
                </div>
            </form>

            <div class="account-options">
                <p>¿Recordaste tu contraseña? <a href="{{ route('login') }}" class="admin-link">Volver al inicio de sesión</a></p>
            </div>
        </div>
    </div>
