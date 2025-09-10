<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);
?>

<header>
    <h1>Setting</h1>
</header>

<section class="setting-page">
    <?php
    $votUserId          = $_SESSION['id'];
    $votUserDatabase    = $GLOBALS['votDatabaseHandle'];

    $votUserData        = readUserData(
        id: $votUserId,
        database_handle: $votUserDatabase
    );

    $votUserRole = htmlspecialchars($votUserData['roleName']);

    switch ($votUserRole) {
        case 'Admin':
            // Admin-level access can change all settings
            $generalSettingTemplate =
                <<<EOL
                <div class="vot-center-button-container">
                    <a href="/setting/admin">Admin Setting</a>
                    <a href="/setting/tournament">Tournament Setting</a>
                    <a href="/setting/user">User Setting</a>
                </div>
                EOL;

            // It would be much more nasty if I tried to output this using the traditional mixed HTML & PHP codes
            echo $generalSettingTemplate;
            break;

        case 'Host':
            // Host-level access can change tournament settings
            $generalSettingTemplate =
                <<<EOL
                <div class="vot-center-button-container">
                    <a href="/setting/tournament">Tournament Setting</a>
                    <a href="/setting/user">User Setting</a>
                </div>
                EOL;

            // It would be much more nasty if I tried to output this using the traditional mixed HTML & PHP codes
            echo $generalSettingTemplate;
            break;

        case 'User':
            // User-level access can change basic settings
            $generalSettingTemplate =
                <<<EOL
                <div class="vot-center-button-container">
                    <a href="/setting/user">User setting</a>
                </div>
                EOL;

            // It would be much more nasty if i tried to output this using the traditional mixed html & php codes
            echo $generalSettingTemplate;

        default:
            // Invalid access level name for some reason...
            $generalSettingTemplate =
                <<<EOL
                <div class="vot-center-button-container">
                    <a href="/home">Don't try to be naughty kid, I know what you trying to do.</a>
                </div>
                EOL;

            // It would be much more nasty if i tried to output this using the traditional mixed html & php codes
            echo $generalSettingTemplate;

            break;
    }
    ?>
</section>
