/**
 * Set theme mode for dynamic theme switch button.
 * @param {String} themeMode - theme change indicator
 * @returns {(Boolean | undefined)} - indicate that the theme has successfully changed or not
 */
function setThemeMode(themeMode) {
    document.body.className = "";

    switch (themeMode) {
        case "light":
            document.body.classList.add("light-theme");
            localStorage.setItem("theme-mode", themeMode);
            break;

        case "system":
            document.body.classList.add("system-theme");
            localStorage.setItem("theme-mode", themeMode);
            break;

        case "dark":
            document.body.classList.add("dark-theme");
            localStorage.setItem("theme-mode", themeMode);
            break;

        // Default case
        default:
            console.warn("Unknown theme:", themeMode);
            break;
    }
}

/**
 * Set light theme mode.
 * @return {Boolean} - indicate that the theme has successfully applied
 */
function lightThemeMode() {
    setThemeMode("light");
    return true;
}

/**
 * Set system theme mode.
 * @return {Boolean} - indicate that the theme has successfully applied
 */
function systemThemeMode() {
    setThemeMode("system");
    return true;
}

/**
 * Set dark theme mode.
 * @return {Boolean} - indicate that the theme has successfully applied
 */
function darkThemeMode() {
    setThemeMode("dark");
    return true;
}

/**
 * Set corresponding theme mode's value to each theme switch buttons.
 * @return {Boolean} - indicate that each theme switch buttons have their own theme mode's value
 */
function setThemeSwitch() {
    const lightButton = document.getElementById("light-theme");
    const darkButton = document.getElementById("dark-theme");
    const systemButton = document.getElementById("system-theme");

    lightButton.addEventListener("click", lightThemeMode);
    systemButton.addEventListener("click", systemThemeMode);
    darkButton.addEventListener("click", darkThemeMode);

    const savedThemeMode = localStorage.getItem("theme");
    if (!savedThemeMode) {
        systemThemeMode();
        return true;
    } else {
        setThemeMode(savedThemeMode);
        return true;
    }
}

document.addEventListener("DOMContentLoaded", setThemeSwitch);
