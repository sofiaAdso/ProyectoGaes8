document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    lucide.createIcons();

    // Dropdown functionality
    const dropdownTriggers = document.querySelectorAll('.dropdown-trigger');

    dropdownTriggers.forEach(trigger => {
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            // Here you would toggle the visibility of the dropdown menu
            console.log('Dropdown clicked:', this.textContent);
        });
    });

    // Play button functionality
    const playButtons = document.querySelectorAll('.play-button');

    playButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Here you would implement the video play functionality
            console.log('Play button clicked');
        });
    });

    // User button functionality
    const userButton = document.querySelector('.user-button');

    userButton.addEventListener('click', function() {
        // Here you would implement the user menu functionality
        console.log('User button clicked');
    });
});

