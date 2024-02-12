// Create custom click button function after clicked on the 'menu' icon.
let button = document.querySelector('#click-button');
// Create custom navigation bar function after clicked on the 'menu' icon.
let sideBar = document.querySelector('nav');

// Run a custom fuction when the customized click button is clicked.
button.onclick = function () {
    // Toggling a custom class for the customized navigation bar called early on.
    sideBar.classList.toggle('active-button');
};