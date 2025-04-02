document.addEventListener('DOMContentLoaded', function() {
    // Filtrado de ejercicios por categoría
    const categoryButtons = document.querySelectorAll('.category-filter button');
    const exerciseCards = document.querySelectorAll('.exercise-card');

    categoryButtons.forEach(button => {
        button.addEventListener('click', function() {
            const category = this.getAttribute('data-category');

            // Actualizar botones activos
            categoryButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

            // Filtrar ejercicios
            exerciseCards.forEach(card => {
                if (category === 'all' || card.getAttribute('data-category') === category) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    // Funcionalidad para los botones "Comenzar" de los ejercicios
    const startButtons = document.querySelectorAll('.start-exercise');

    startButtons.forEach(button => {
        button.addEventListener('click', function() {
            const exerciseTitle = this.closest('.exercise-card').querySelector('h3').textContent;
            alert(`Iniciando ejercicio: ${exerciseTitle}`);
            // Aquí se implementaría la lógica para iniciar el ejercicio
        });
    });

    // Funcionalidad para los botones de herramientas de práctica
    const toolButtons = document.querySelectorAll('.tool-card .btn-secondary');

    toolButtons.forEach(button => {
        button.addEventListener('click', function() {
            const toolName = this.closest('.tool-card').querySelector('h3').textContent;
            alert(`Abriendo herramienta: ${toolName}`);
            // Aquí se implementaría la lógica para abrir la herramienta correspondiente
        });
    });
});


