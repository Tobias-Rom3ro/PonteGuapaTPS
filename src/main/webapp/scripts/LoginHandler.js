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
        const formData = new URLSearchParams();
        formData.append("email", email);
        formData.append("password", password);

        fetch('/ponteguapa/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                email: email,
                password: password
            })
        })
            .then(async response => {
                const text = await response.text();

                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        window.location.href = data.redirectUrl;
                    } else {
                        alert(data.message || 'Error al iniciar sesión');
                    }
                } catch (e) {
                    console.error("Respuesta no es JSON válido:", text);
                    alert("Error inesperado del servidor");
                }
            })
            .catch(error => {
                console.error('Error en la solicitud:', error);
                alert('Error al conectar con el servidor');
            });
    });

});