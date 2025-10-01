<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);


if (
    !isset($_COOKIE['vot_access_id']) &&
    !isset($_COOKIE['vot_access_token'])
) {
    // Simply: "/setting/<role-name>" --> "<role-name>" or "null" (for </setting> only)
    $roleSettingPath = explode(
        separator: '/',
        string: trim(
            string: $_SERVER['REQUEST_URI'],
            characters: '/'
        ),
        limit: PHP_INT_MAX
    )[1] ?? null;

    $insufficientPermissionLogMessage = sprintf(
        "Suspicious attempt to access [%s] setting page detected!!",
        strtoupper(
            string: isset($roleSettingPath)
                ? $roleSettingPath
                : 'SETTING'
        )
    );

    switch ($roleSettingPath) {
        // </setting/admin> URL path handling
        case 'admin':
            // TODO: Find a way to retrieve the IP address of the person that
            // trying to access the page.
            error_log(
                message: $insufficientPermissionLogMessage,
                message_type: 0
            );
            http_response_code(401);
            break;

        // </setting/tournament> URL path handling
        case 'tournament':
            // TODO: Find a way to retrieve the IP address of the person that
            // trying to access the page.
            error_log(
                message: $insufficientPermissionLogMessage,
                message_type: 0
            );
            http_response_code(401);
            break;

        // </setting/user> URL path handling
        case 'user':
            // TODO: Find a way to retrieve the IP address of the person that
            // trying to access the page.
            error_log(
                message: $insufficientPermissionLogMessage,
                message_type: 0
            );
            http_response_code(401);
            break;

        // </setting> URL path handling
        default:
            // TODO: Find a way to retrieve the IP address of the person that
            // trying to access the page.
            error_log(
                message: $insufficientPermissionLogMessage,
                message_type: 0
            );
            http_response_code(401);
            break;
    }
} else {
    require __DIR__ . '/../Controllers/NavigationBarController.php';

    $userAuthoriseId    = $_SESSION['id'];
    $userViewData       = readUserData(id: $userAuthoriseId);
    $userAuthoriseRole  = htmlspecialchars($userViewData['roleName']);

    switch ($userAuthoriseRole) {
        case 'Admin':
            // Admin-level access can change all settings
            $generalSettingInformationTemplate =
                <<<EOL
                <header>
                    <h1>Setting</h1>
                </header>

                <section class="setting-page">
                    <div class="vot-center-button-container">
                        <a href="/setting/admin">Admin Setting</a>
                        <a href="/setting/tournament">Tournament Setting</a>
                        <a href="/setting/user">User Setting</a>
                    </div>
                </section>
                EOL;

            // It would be much more nasty if I tried to output this using the
            // traditional mixed HTML & PHP codes
            echo $generalSettingInformationTemplate;
            break;

        case 'Host':
            // Host-level access can change tournament settings
            $generalSettingInformationTemplate =
                <<<EOL
                <header>
                    <h1>Setting</h1>
                </header>

                <section class="setting-page">
                    <div class="vot-center-button-container">
                        <a href="/setting/tournament">Tournament Setting</a>
                        <a href="/setting/user">User Setting</a>
                    </div>
                </section>
                EOL;

            // It would be much more nasty if I tried to output this using the
            // traditional mixed HTML & PHP codes
            echo $generalSettingInformationTemplate;
            break;

        case 'User':
            // User-level access can change basic settings
            $generalSettingInformationTemplate =
                <<<EOL
                <header>
                    <h1>Setting</h1>
                </header>

                <section class="setting-page">
                    <div class="vot-center-button-container">
                        <a href="/setting/user">User Setting</a>
                    </div>
                </section>
                EOL;

            // It would be much more nasty if i tried to output this using the
            // traditional mixed html & php codes
            echo $generalSettingInformationTemplate;

        default:
            // Invalid access level name for some reason...
            $generalSettingInformationTemplate =
                <<<EOL
                <header>
                    <h1>Setting</h1>
                </header>

                <div class="vot-center-button-container">
                    <a href="/home">Don't try to be naughty kid, I know what you trying to do.</a>
                </div>
                EOL;

            // It would be much more nasty if i tried to output this using the traditional mixed html & php codes
            echo $generalSettingInformationTemplate;

            break;
    }
}
