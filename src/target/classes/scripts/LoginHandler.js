// Script para manejar el proceso de login
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.querySelector('.login-form');

    loginForm.addEventListener('submit', function(event) {
        event.preventDefault();

        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        // Validación básica del lado del cliente
        if (!email || !password) {
            alert('Por favor, completa todos los campos');
            return;
        }

        // Crear datos para enviar al servidor
        const formData = new FormData();
        formData.append('email', email);
        formData.append('password', password);

        // Enviar solicitud de login al servidor
        fetch(window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/')) + '/../login', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Login exitoso, redirigir al usuario
                    window.location.href = data.redirectUrl;
                } else {
                    // Mostrar mensaje de error
                    alert(data.message || 'Error al iniciar sesión');
                }
            })
            .catch(error => {
                console.error('Error en la solicitud:', error);
                alert('Error al conectar con el servidor');
            });
    });
});