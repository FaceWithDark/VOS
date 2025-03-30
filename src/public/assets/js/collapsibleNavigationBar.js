/**
 * Toggle navigation bar expanding/collapsing state.
 * @param {HTMLElement} navigationBarToggleClass - Indicates <nav> tag for toggling mentioned feature.
 * @param {String} navigationBarToggleCondition - Indicates whether the navigation bar should be expanded or collapsed.
 * @returns {Boolean} - Confirms correct keyword to enable/disable the mentioned feature.
 */
function navigationBarToggleState(
    navigationBarToggleClass,
    navigationBarToggleCondition,
) {
    switch (navigationBarToggleCondition) {
        case "enable":
            navigationBarToggleClass.classList.remove("collapsed");
            navigationBarToggleClass.classList.add("expanded");
            break;

        case "disable":
            navigationBarToggleClass.classList.remove("expanded");
            navigationBarToggleClass.classList.add("collapsed");
            break;

        // Default case
        default:
            console.warn(
                "Unknown toggle condition:",
                navigationBarToggleCondition,
            );
            break;
    }
    return true;
}

/**
 * Create required elements when navigation bar is in expanding state.
 * @param {HTMLElement} navigationBarAppendLocation - Indicates <nav> itself or classes location for appending required elements.
 * @return {HTMLElement} - Created elements.
 */
function navigationBarExpendedElement(navigationBarAppendLocation) {
    const votNavigationElement = document.createElement("i");
    votNavigationElement.className = "bx bxs-navigation";
    votNavigationElement.id = "navigation-title";
    votNavigationElement.innerHTML = "<strong>VOT</strong>";

    navigationBarAppendLocation.appendChild(votNavigationElement);

    return votNavigationElement;
}

/**
 * Remove created elements when navigation bar is in collapsing state.
 * @param {HTMLElement} votNavigationElement - Indicates created elements for removal.
 * @return {undefined} - Confirms created elements have been removed successfully.
 */
function navigationBarCollapsedElement(votNavigationElement) {
    votNavigationElement.remove();
    return undefined;
}

/**
 * Main logic for enabling/disabling collapsible navigation bar feature.
 * @param {HTMLElement} navigationBarTagLocation - <nav> tag location for activate toggling/disabling conditions.
 * @param {HTMLElement} navigationBarAppendLocation - Indicates <nav> itself or classes location for appending required elements.
 * @return {Boolean} - Indicates that the navigation bar state has been successfully applied.
 */
function setNavigationBarCollapsible(
    navigationBarTagLocation,
    navigationBarAppendLocation,
) {
    const votNavigation = document.getElementById("navigation-title");

    if (!votNavigation) {
        navigationBarExpendedElement(navigationBarAppendLocation);
        navigationBarToggleState(navigationBarTagLocation, "enable");
    } else {
        navigationBarCollapsedElement(votNavigation);
        navigationBarToggleState(navigationBarTagLocation, "disable");
    }

    return true;
}

/**
 * Retrive enabling/disabling collapsible logic
 * @return {Boolean} - Confirms navigation bar enabling/disabling feature is working.
 */
function getNavigationBarCollapsible() {
    const collapsibleClickIcon = document.getElementById("collapsible-icon");
    const navigationBarTagLocation = document.querySelector("nav");
    const navigationBarAppendLocation = document.querySelector(
        ".top-navigation-section",
    );

    collapsibleClickIcon.addEventListener(
        "click",
        function () {
            setNavigationBarCollapsible(
                navigationBarTagLocation,
                navigationBarAppendLocation,
            );
        },
    );

    return true;
}

document.addEventListener("DOMContentLoaded", getNavigationBarCollapsible);
