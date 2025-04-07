<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña - Ponte Guapa</title>
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
                <h1>Restablece tu contraseña</h1>
                <p class="subtitle">Crea una nueva contraseña segura</p>
            </div>
            <form class="login-form" method="POST" action="{{ route('password.store') }}">
                @csrf

                <!-- Password Reset Token -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div class="form-group">
                    <label for="email">Correo electrónico</label>
                    <input name="email" type="email" id="email" class="form-input" value="{{ old('email', $request->email) }}" required readonly>
                    @error('email')
                    <span class="invalid-feedback" role="alert" style="color: red; font-size: 12px; margin-top: 5px; display: block;">
                        {{ $message }}
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Nueva contraseña</label>
                    <div class="password-container">
                        <input name="password" id="password" type="password" placeholder="Ingresa tu nueva contraseña" class="form-input" required>
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
                        <input name="password_confirmation" id="password-confirm" type="password" placeholder="Confirma tu nueva contraseña" class="form-input" required>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="login-button">Restablecer contraseña</button>
                </div>
            </form>
        </div>
    </div>
