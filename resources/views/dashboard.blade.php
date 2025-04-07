<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Ponte Guapa</title>
    <link rel="stylesheet" href="{{ asset('styles/dashboard.css') }}">
    <link rel="icon" href="{{ asset('resources/icons/icon.png') }}" type="image/png">
</head>
<body>
<div class="dashboard-container">
    <div class="header">
        <div class="header-logo">
            <img src="{{ asset('resources/icons/icon.png') }}" alt="Logo Ponte Guapa" class="logo" />
            <h1 class="header-title">Ponte Guapa - Admin Dashboard</h1>
        </div>
        <div class="user-info">
            <span class="user-name">{{ Auth::user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="logout-btn" id="logoutBtn">Cerrar sesión</button>
            </form>
        </div>
    </div>
    <div class="welcome-card">
        <h2 class="welcome-title">¡Bienvenido al panel administrativo!</h2>
        <p class="welcome-text">
            Empieza a explorar las diferentes secciones para administrar usuarios,
            productos, citas y más.
        </p>
    </div>

    <div class="dashboard-content">
        <div class="card">
            <h3 class="card-title">Usuarios</h3>
            <div class="card-content">
                <p>Administra los usuarios de la plataforma.</p>
            </div>
        </div>

        <div class="card">
            <h3 class="card-title">Productos</h3>
            <div class="card-content">
                <p>Gestiona el inventario de productos.</p>
            </div>
        </div>

        <div class="card">
            <h3 class="card-title">Citas</h3>
            <div class="card-content">
                <p>Visualiza y administra las citas pendientes.</p>
            </div>
        </div>

        <div class="card">
            <h3 class="card-title">Estadísticas</h3>
            <div class="card-content">
                <p>Revisa las estadísticas de ventas y usuarios.</p>
            </div>
        </div>
    </div>
    <div class="footer">
        <p>&copy; 2023 Ponte Guapa. Casi todos los derechos reservados.</p>
    </div>
</div>

