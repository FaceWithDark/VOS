const toggleButton = document.querySelector('.toggle_button')
const toggleButtonIcon = document.querySelector('.toggle_button i')
const dropDownMenu = document.querySelector('.dropdown_menu')

toggleButton.onclick = function() {
    dropDownMenu.classList.toggle('open')
    const isOpen = dropDownMenu.classList.contains('open')

    toggleButtonIcon.classList = isOpen
        ? 'fa-solid fa-xmark'
        : 'fa-solid fa-bars'
}