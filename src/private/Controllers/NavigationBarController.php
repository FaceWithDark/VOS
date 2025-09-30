<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require __DIR__ . '/../Models/Login.php';


if (
    !isset($_COOKIE['vot_access_id']) &&
    !isset($_COOKIE['vot_access_token'])
) {
    require __DIR__ . '/../Views/NavigationBar/UnauthoriseView.php';
} else {
    // Run the buffer capture first before any output buffers
    ob_start(
        callback: null,
        chunk_size: 0,
        flags: PHP_OUTPUT_HANDLER_STDFLAGS
    );

    // Run the session first before any output buffers
    session_start(
        options: [
            'name' => 'vot_access_id',
            'cookie_lifetime' => 86400,
            'cookie_httponly' => 1
        ]
    );

    require __DIR__ . '/../Views/NavigationBar/AuthoriseView.php';
    $userCurrentInformationTemplate = ob_get_clean();

    $userAuthoriseId        = $_SESSION['id'];
    $userViewData           = readUserData(id: $userAuthoriseId);

    $userAuthoriseName      = htmlspecialchars($userViewData['userName']);
    $userAuthoriseImage     = htmlspecialchars($userViewData['userImage']);
    $userAuthoriseUrl       = htmlspecialchars($userViewData['userUrl']);

    $userNewInformationTemplate =
        <<<EOL
        <div class="middle-navigation-first-section">
            <a href="$userAuthoriseUrl">
                <img src="$userAuthoriseImage" alt="User Avatar" class="user-image">
                <p>$userAuthoriseName</p>
            </a>
        </div>
        EOL;


    /*                          Final output is like so:
    ============================================================================
    <body>
        <nav>
            <!-- 1st [div] here -->

            <div class="middle-navigation-first-section">
                <a href="$userAuthoriseUrl">
                    <img src="$userAuthoriseImage" alt="User Avatar"
                         class="user-image">
                    <p>$userAuthoriseName</p>
                </a>
            </div>

            <! Other [div] here -->
        </nav>
    </body>
    ============================================================================
    */
    echo preg_replace(
        pattern: '/(<div\s+class="top-navigation-section"[^>]*>.*?<\/div>)/s',
        replacement: '$1' . $userNewInformationTemplate,
        subject: $userCurrentInformationTemplate,
        limit: 1 // Only the 1st matched regex pattern is acceptable
    );
}
