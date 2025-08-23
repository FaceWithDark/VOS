"use strict"; // Don't ask me why I need to do this, JS sucks in all shapes

function collapsibleNavigationBar() {
    const navigationBarTriggerLocation = document.querySelector("nav");
    const navigationBarCurrentState = navigationBarTriggerLocation.classList
        .contains("collapsed");

    const navigationBarTitleLocation = document.querySelector(
        ".top-navigation-section",
    );
    const navigationBarExpandableTitle = document.getElementById(
        "expandable-title",
    );

    const navigationBarItemLocation = document.querySelectorAll(
        ".middle-navigation-second-section ul li a",
    );

    // All new elements added when expand will be deleted when collapse
    if (!navigationBarCurrentState) {
        navigationBarTriggerLocation.classList.remove("expanded");
        navigationBarTriggerLocation.classList.add("collapsed");

        navigationBarExpandableTitle.remove();

        for (
            let itemValueIndex = 0,
            itemValueArray = navigationBarItemLocation.length;
            itemValueIndex < itemValueArray;
            itemValueIndex++
        ) {
            const navigationBarItemValueLocation =
                navigationBarItemLocation[itemValueIndex].querySelector("p");
            navigationBarItemValueLocation.remove();
        }

        // All needed new elements will be added when expand
    } else {
        navigationBarTriggerLocation.classList.remove("collapsed");
        navigationBarTriggerLocation.classList.add("expanded");

        // Append new elements to top section of the navigation bar layout
        const navigationBarTitleLayout = document.createElement("i");
        navigationBarTitleLayout.className = "bx bxs-navigation";
        navigationBarTitleLayout.id = "expandable-title";
        navigationBarTitleLayout.innerHTML = "<strong>VOT</strong>";

        navigationBarTitleLocation.appendChild(navigationBarTitleLayout);

        // Append new elements to middle 2nd section of the navigation bar layout
        const navigationBarItemValue = [
            "Login",
            "Home",
            "Archive",
            "Staff",
            "Song",
        ];

        const navigationBarUserImagePath = document.querySelector(
            ".user-image",
        ).getAttribute("src");

        const unauthenticatedUserImagePath =
            "/assets/img/Authentication Failed.webp";

        if (
            navigationBarUserImagePath !== unauthenticatedUserImagePath
        ) {
            // Authenticated user should be able to logout as usual
            navigationBarItemValue[0] = "Logout";
        } else {
            // Unauthenticated user should be able to login as usual
            navigationBarItemValue[0] = "Login";
        }

        for (
            let itemIndex = 0, itemArray = navigationBarItemLocation.length;
            itemIndex < itemArray;
            itemIndex++
        ) {
            const navigationBarItemLayout = document.createElement("p");
            navigationBarItemLayout.textContent =
                navigationBarItemValue[itemIndex];
            navigationBarItemLocation[itemIndex].appendChild(
                navigationBarItemLayout,
            );
        }
    }
}

function triggerCollapsibleNavigationBar() {
    const navigationBarTriggerButton = document.getElementById(
        "collapsible-icon",
    );

    if (!navigationBarTriggerButton) {
        console.error("Required ID attribute not found!");
    } else {
        navigationBarTriggerButton.addEventListener(
            "click",
            collapsibleNavigationBar,
            false,
        );
    }
}

// Make sure DOM is fully loaded first
document.addEventListener(
    "DOMContentLoaded",
    triggerCollapsibleNavigationBar,
    false,
);
