<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);


if (
    !isset($_COOKIE['vot_access_id']) &&
    !isset($_COOKIE['vot_access_token'])
) {
    $generalSettingPath = trim(
        string: parse_url(
            url: $_SERVER['REQUEST_URI'],
            component: PHP_URL_PATH
        ),
        characters: '/'
    );

    error_log(
        message: sprintf(
            "Suspicious attempt to access [%s] setting page detected!!",
            strtoupper(string: $generalSettingPath)
        ),
        message_type: 0
    );

    exit(header(
        header: 'Location: /home',
        replace: true,
        response_code: 302
    ));
} else {
    require __DIR__ . '/../../Controllers/NavigationBarController.php';

    $userAuthoriseId    = $_SESSION['id'];
    $userViewData       = readUserData(id: $userAuthoriseId);
    $userAuthoriseRole  = htmlspecialchars($userViewData['roleName']);

    switch ($userAuthoriseRole) {
        case 'Admin':
            // Admin-level access can change all settings
            require __DIR__ . '/../../Views/Setting/AdminSelectionView.php';
            break;

        case 'Host':
            // Host-level access can change tournament settings
            require __DIR__ . '/../../Views/Setting/TournamentSelectionView.php';
            break;

        case 'User':
            // User-level access can change basic settings
            require __DIR__ . '/../../Views/Setting/UserSelectionView.php';

        default:
            // Invalid access level name for some reason...
            // TODO: proper error handling
            $generalSettingInformationTemplate =
                <<<EOL
                <header>
                    <h1>Setting</h1>
                </header>

                <div class="vot-center-button-container">
                    <a href="/home">Don't try to be naughty kid, I know what you trying to do.</a>
                </div>
                EOL;

            // It would be much more nasty if i tried to output this using the
            // traditional mixed HTML & PHP codes
            echo $generalSettingInformationTemplate;

            break;
    }
}
