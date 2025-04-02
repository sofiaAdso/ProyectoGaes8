document.addEventListener('DOMContentLoaded', function() {
    // Filtrado de canciones por género y dificultad
    const genreButtons = document.querySelectorAll('.genre-filter button');
    const difficultyButtons = document.querySelectorAll('.difficulty-filter button');
    const songCards = document.querySelectorAll('.song-card');

    function filterSongs() {
        const selectedGenre = document.querySelector('.genre-filter button.active').getAttribute('data-genre');
        const selectedDifficulty = document.querySelector('.difficulty-filter button.active').getAttribute('data-difficulty');

        songCards.forEach(card => {
            const cardGenre = card.getAttribute('data-genre');
            const cardDifficulty = card.getAttribute('data-difficulty');

            if ((selectedGenre === 'all' || cardGenre === selectedGenre) &&
                (selectedDifficulty === 'all' || cardDifficulty === selectedDifficulty)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    genreButtons.forEach(button => {
        button.addEventListener('click', function() {
            genreButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            filterSongs();
        });
    });

    difficultyButtons.forEach(button => {
        button.addEventListener('click', function() {
            difficultyButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            filterSongs();
        });
    });

    // Funcionalidad para los botones "Aprender" de las canciones
    const startButtons = document.querySelectorAll('.start-song');

    startButtons.forEach(button => {
        button.addEventListener('click', function() {
            const songTitle = this.closest('.song-card').querySelector('h3').textContent;
            const songArtist = this.closest('.song-card').querySelector('p').textContent;
            alert(`Iniciando lección para: ${songTitle} - ${songArtist}`);
            // Aquí se implementaría la lógica para iniciar la lección de la canción
        });
    });

    // Inicializar el filtro
    filterSongs();
});

