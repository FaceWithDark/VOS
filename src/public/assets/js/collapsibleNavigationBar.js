/**
 * Logic to set navigation bar expanding/collapsing state.
 * @param {HTMLElement} tagLocation - Location of the HTML tag where navigation bar state can be set.
 * @param {String} toggleState - Receive pre-defined state conditions allowing expandable/collapsible navigation bar.
 * @returns {Boolean} - Indicates matched keyword to enable corresponding state.
 */
function navigationBarToggleState(tagLocation, toggleState) {
    // I don't get why JS wanted to syntactically handling multi-case this way but it works, somehow
    switch (toggleState) {
        case "expandable":
        case "expanded":
        case "expand":
            tagLocation.classList.remove("collapsed");
            tagLocation.classList.add("expanded");
            break;

        case "collapsible":
        case "collapsed":
        case "collapse":
            tagLocation.classList.remove("expanded");
            tagLocation.classList.add("collapsed");
            break;

        // Non-matching keywords
        default:
            console.warn("Unknown toggle condition:", toggleState);
            break;
    }
    return true;
}

/**
 * Create new element(s) when the navigation bar is in expandable state.
 * @param {HTMLElement} activateLocation - Location of the attribute where new element(s) can be assigned to.
 * @returns {HTMLElement} - Indicates new element(s) is/are created.
 */
function activateExpandableNavigationBar(activateLocation) {
    const setVotNavigationTitle = document.createElement("i");
    setVotNavigationTitle.className = "bx bxs-navigation";
    setVotNavigationTitle.id = "navigation-title";
    setVotNavigationTitle.innerHTML = "<strong>VOT</strong>";

    activateLocation.appendChild(setVotNavigationTitle);

    // Create and append <p> tags within each <a> tag
    const menuItems = ["Login", "Home", "Archive", "Staff", "Song", "Logout"];
    const listItems = activateLocation.querySelectorAll("ul li");

    listItems.forEach(function (li, index) {
        const menuItem = document.createElement("p");
        menuItem.textContent = menuItems[index];
        const anchor = li.querySelector("a");
        anchor.appendChild(menuItem);
    });

    return setVotNavigationTitle;
}

/**
 * Remove assigned element(s) when the navigation bar is in collapsible state.
 * @param {HTMLElement} activateLocation - Location of assigned element(s) for removal.
 * @return {undefined} - Indicates assigned element(s) is/are removed.
 */
function activateCollapsibleNavigationBar(activateLocation) {
    activateLocation.remove();

    return undefined;
}

/**
 * Toggle navigation bar expanding/collapsing feature based on set conditions.
 * @param {HTMLElement} tagLocation - Location of the HTML tag where navigation bar state can access in.
 * @param {HTMLElement} appendLocation - Location of the attribute where navigation bar state can be applied to.
 * @return {Boolean} - Indicates correct state applied to the navigation bar.
 */
function applyCollapsibleNavigationBar(tagLocation, appendLocation) {
    const getVotNavigationTitle = document.getElementById("navigation-title");

    // Remove the <p> elements within each <a> tag
    const listItems = appendLocation.querySelectorAll("ul li");

    listItems.forEach(function (li) {
        const anchor = li.querySelector("a");
        const menuItem = anchor.querySelector("p");
        if (menuItem) {
            activateCollapsibleNavigationBar(menuItem);
            navigationBarToggleState(tagLocation, "collapsed");

            return true;
        }
    });

    if (!getVotNavigationTitle) {
        activateExpandableNavigationBar(appendLocation);
        navigationBarToggleState(tagLocation, "expanded");

        return true;
    } else {
        activateCollapsibleNavigationBar(getVotNavigationTitle);
        navigationBarToggleState(tagLocation, "collapsed");

        return true;
    }
}

/**
 * Activate navigation bar expanding/collapsing feature.
 * @return {Boolean} - Indicates that collapisble navigation bar feature is working.
 */
function toggleCollapsibleNavigationBar() {
    // TODO: Dynamically add <p> tags: Login, Home, Archive, Staff, Song, Log out
    const navigationBarClickIcon = document.getElementById("collapsible-icon");
    const navigationBarTagLocation = document.querySelector("nav");
    const navigationBarTopAppendLocation = document.querySelector(
        //".top-navigation-section",
        ".middle-navigation-second-section",
    );

    // Navigation bar will stay in non expanding form by default
    navigationBarToggleState(navigationBarTagLocation, "collapsed");

    navigationBarClickIcon.addEventListener(
        "click",
        function () {
            applyCollapsibleNavigationBar(
                navigationBarTagLocation,
                navigationBarTopAppendLocation,
            );
        },
    );

    return true;
}

document.addEventListener("DOMContentLoaded", toggleCollapsibleNavigationBar);
