document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    lucide.createIcons();

    // Navigation functionality
    const navItems = document.querySelectorAll('.nav-item');
    const sections = document.querySelectorAll('.content-section');

    navItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const targetSection = this.getAttribute('data-section');

            // Update active states
            navItems.forEach(nav => nav.classList.remove('active'));
            this.classList.add('active');

            // Show target section
            sections.forEach(section => {
                section.classList.remove('active');
                if (section.id === targetSection) {
                    section.classList.add('active');
                }
            });
        });
    });

    // Form submissions
    const userForm = document.getElementById('userForm');
    const lessonForm = document.getElementById('lessonForm');

    userForm.addEventListener('submit', function(e) {
        e.preventDefault();
        // Aquí se implementaría la lógica para guardar el usuario
        const formData = new FormData(this);
        console.log('Guardando usuario:', Object.fromEntries(formData));
        hideModal('userModal');
        this.reset();
    });

    lessonForm.addEventListener('submit', function(e) {
        e.preventDefault();
        // Aquí se implementaría la lógica para guardar la lección
        const formData = new FormData(this);
        console.log('Guardando lección:', Object.fromEntries(formData));
        hideModal('lessonModal');
        this.reset();
    });
});

// Modal functions
function showModal(modalId) {
    document.getElementById(modalId).style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function hideModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
    document.body.style.overflow = 'auto';
}

// User management functions
function editUser(userId) {
    console.log('Editando usuario:', userId);
    // Aquí se implementaría la lógica para cargar y editar el usuario
    showModal('userModal');
}

function deleteUser(userId) {
    if (confirm('¿Estás seguro de que deseas eliminar este usuario?')) {
        console.log('Eliminando usuario:', userId);
        // Aquí se implementaría la lógica para eliminar el usuario
    }
}

function showUserReport(userId) {
    console.log('Mostrando reporte del usuario:', userId);
    // Aquí se implementaría la lógica para mostrar el reporte del usuario
}

// Lesson management functions
function editLesson(lessonId) {
    console.log('Editando lección:', lessonId);
    // Aquí se implementaría la lógica para cargar y editar la lección
    showModal('lessonModal');
}

function deleteLesson(lessonId) {
    if (confirm('¿Estás seguro de que deseas eliminar esta lección?')) {
        console.log('Eliminando lección:', lessonId);
        // Aquí se implementaría la lógica para eliminar la lección
    }
}

