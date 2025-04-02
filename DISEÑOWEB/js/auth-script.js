document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');

    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            // Aquí normalmente harías una llamada a tu API para autenticar al usuario
            // Por ahora, simularemos la autenticación con datos de ejemplo
            if (email === 'admin@example.com' && password === 'admin123') {
                alert('Inicio de sesión exitoso como administrador');
                window.location.href = 'admin-panel.html';
            } else if (email === 'user@example.com' && password === 'user123') {
                alert('Inicio de sesión exitoso como usuario');
                window.location.href = 'index.html';
            } else {
                alert('Credenciales incorrectas');
            }
        });
    }

    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm-password').value;
            const isAdmin = document.getElementById('is-admin').checked;

            if (password !== confirmPassword) {
                alert('Las contraseñas no coinciden');
                return;
            }

            // Aquí normalmente harías una llamada a tu API para registrar al usuario
            // Por ahora, simularemos el registro
            const userType = isAdmin ? 'administrador' : 'usuario';
            alert(`Registro exitoso como ${userType}. Nombre: ${name}, Email: ${email}`);

            // Redirigir a la página de inicio de sesión o directamente al dashboard
            window.location.href = 'login.html';
        });
    }
});

