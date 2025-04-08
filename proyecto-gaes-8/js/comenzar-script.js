document.addEventListener('DOMContentLoaded', function() {
    const profileForm = document.getElementById('profile-form');

    if (profileForm) {
        profileForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const profileData = Object.fromEntries(formData);

            // Aquí normalmente enviarías los datos a un servidor
            console.log('Datos del perfil:', profileData);

            // Simulamos una respuesta exitosa
            alert('Perfil guardado con éxito. ¡Bienvenido a Guitar Master!');

            // Redirigir al usuario a la página de lecciones o al dashboard
            // window.location.href = 'lecciones.html';
        });
    }

    // Inicializar los íconos de Lucide
    lucide.createIcons();
});