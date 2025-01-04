// Select the button and sidebar elements.
let button = document.querySelector('#click-button');
let sideBar = document.querySelector('nav');

// Toggle sidebar visibility on button click.
button.onclick = function () {
    sideBar.classList.toggle('active-button');
};

// Initialize theme icons and body element when DOM content is loaded.
document.addEventListener('DOMContentLoaded', (event) => {
    let darkModeIcon = document.getElementById('dark-mode');
    let lightModeIcon = document.getElementById('light-mode');
    let bodyElement = document.body;

    // Function to save the theme preference to localStorage
    function saveThemePreference(theme) {
        localStorage.setItem('themePreference', theme);
    }

    // Function to load the theme preference from localStorage
    function loadThemePreference() {
        return localStorage.getItem('themePreference');
    }

    // Define functions to toggle between dark and light mode.
    function toggleDarkMode() {
        darkModeIcon.style.display = 'flex';
        lightModeIcon.style.display = 'none';
        bodyElement.classList.add('dark-theme');
        saveThemePreference('dark');
    }

    function toggleLightMode() {
        lightModeIcon.style.display = 'flex';
        darkModeIcon.style.display = 'none';
        bodyElement.classList.remove('dark-theme');
        saveThemePreference('light');
    }

    // Apply the saved theme preference or default to light mode.
    let themePreference = loadThemePreference();
    if (themePreference === 'dark') {
        toggleDarkMode();
    } else {
        toggleLightMode();
    }

    // Add click event listeners to theme icons to switch modes.
    lightModeIcon.addEventListener('click', toggleDarkMode);
    darkModeIcon.addEventListener('click', toggleLightMode);
});
