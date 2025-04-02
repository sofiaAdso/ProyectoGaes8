document.addEventListener('DOMContentLoaded', function() {
    // Filtrado de lecciones por nivel
    const levelButtons = document.querySelectorAll('.level-filter button');
    const lessonCards = document.querySelectorAll('.lesson-card');

    levelButtons.forEach(button => {
        button.addEventListener('click', function() {
            const level = this.getAttribute('data-level');

            // Actualizar botones activos
            levelButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

            // Filtrar lecciones
            lessonCards.forEach(card => {
                if (level === 'all' || card.getAttribute('data-level') === level) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    // Funcionalidad para los botones "Comenzar"
    const startButtons = document.querySelectorAll('.lesson-card .btn-primary');

    startButtons.forEach(button => {
        button.addEventListener('click', function() {
            const lessonTitle = this.closest('.lesson-card').querySelector('h3').textContent;
            alert(`Iniciando lección: ${lessonTitle}`);
            // Aquí se implementaría la lógica para iniciar la lección
        });
    });
});

