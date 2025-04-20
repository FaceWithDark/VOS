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
    } else {
        navigationBarTriggerLocation.classList.remove("collapsed");
        navigationBarTriggerLocation.classList.add("expanded");

        const navigationBarTitleLayout = document.createElement("i");
        navigationBarTitleLayout.className = "bx bxs-navigation";
        navigationBarTitleLayout.id = "expandable-title";
        navigationBarTitleLayout.innerHTML = "<strong>VOT</strong>";

        navigationBarTitleLocation.appendChild(navigationBarTitleLayout);

        // TODO: replace "Login" --> "Logout" only when user authenticated
        // (check based on user's cookie token)
        const navigationBarItemValue = [
            "Login",
            "Home",
            "Archive",
            "Staff",
            "Song",
        ];

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
        console.error(
            "Required ID to trigger navigation bar feature not found!",
        );
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
