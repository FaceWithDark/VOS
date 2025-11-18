<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);


if (
    !isset($_COOKIE['vot_access_id']) &&
    !isset($_COOKIE['vot_access_token'])
) {
    $adminSettingPath = explode(
        separator: '/',
        string: trim(
            string: $_SERVER['REQUEST_URI'],
            characters: '/'
        )
    )[1];

    error_log(
        message: sprintf(
            "Suspicious attempt to access [%s] setting page detected!!",
            strtoupper(string: $adminSettingPath)
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
    require __DIR__ . '/../../Views/Setting/AdminSettingView.php';
}

/* 
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Load the selection button on first page load or direct access
    $panelSelectionTemplate =
        <<<EOL
            <label for="roleCategory">
                <input type="radio" name="roleCategory" id="general" placeholder="General Role" />
                General Role
            </label>

            <label for="roleCategory">
                <input type="radio" name="roleCategory" id="tournament" placeholder="Tournament Role" />
                Tournament Role
            </label>
            EOL;

    // It would be much more nasty if I tried to output this using the traditional mixed HTML & PHP codes
    echo $panelSelectionTemplate;
} else {
    $inputUserId = (int)$_POST['userId'];
    $inputUserRole = strtoupper(
        string: preg_replace(
            pattern: '/[^a-zA-Z]/',
            replacement: '',
            subject: $_POST['userRole']
        )
    );

    $votRoleDatabase = $GLOBALS['votDatabaseHandle'];

    if (!isset($_POST['userTournament'])) {
        // General role category case
        $displayUnsuccessCheckLogMessage = sprintf(
            "***:SYSTEM LOG: USER DATA CHECK NOT EXISTED FOR USER ID [%d] WITH [%s] ROLE.***",
            $inputUserId,
            $inputUserRole
        );
        $displayUnsuccessTemplate =
            <<<EOL
                <strong>$displayUnsuccessCheckLogMessage</strong>
                <br />
                <strong>PROCESSING...</strong>
                <br />
                <br />
                EOL;

        $displaySuccessCheckLogMessage = sprintf(
            "***:SYSTEM LOG: USER DATA CHECK EXISTED FOR USER ID [%d] WITH [%s] ROLE.***",
            $inputUserId,
            $inputUserRole
        );
        $displaySuccessTemplate =
            <<<EOL
                <strong>$displaySuccessCheckLogMessage</strong>
                <br />
                <a href='/setting/admin'>Wanna do again?</a>
                EOL;

        if (
            // Non integer input found
            ($inputUserId === 0) &&
            // Incorrect string input found
            ($inputUserRole === '')
        ) {
            echo "<a href='/setting/admin'>Gotchu bitch, try again but in the correct way please.</a>";
        } else {
            if (
                !checkGeneralRoleCategory(
                    id: $inputUserId,
                    role: $inputUserRole,
                    database_handle: $votRoleDatabase
                )
            ) {
                // It would be much more nasty if I tried to output this using the traditional mixed HTML & PHP codes
                echo $displayUnsuccessTemplate;

                if (
                    !createGeneralRoleCategory(
                        id: $inputUserId,
                        role: $inputUserRole,
                        database_handle: $votRoleDatabase
                    )
                ) {
                    echo "<a href='/setting/admin'>Error detected, please try again!</a>";
                } else {
                    // It would be much more nasty if I tried to output this using the traditional mixed HTML & PHP codes
                    echo $displaySuccessTemplate;
                }
            } else {
                // It would be much more nasty if I tried to output this using the traditional mixed HTML & PHP codes
                echo $displaySuccessTemplate;
            }
        }
    } else {
        // Tournament role category case
        $inputUserTournament = strtoupper(
            string: preg_replace(
                pattern: '/[^a-zA-Z0-9]/',
                replacement: '',
                subject: $_POST['userTournament']
            )
        );

        $displayUnsuccessCheckLogMessage = sprintf(
            "***:SYSTEM LOG: USER DATA CHECK NOT EXISTED FOR USER ID [%d] WITH [%s] ROLE IN [%s] TOURNAMENT.***",
            $inputUserId,
            $inputUserRole,
            $inputUserTournament
        );
        $displayUnsuccessTemplate =
            <<<EOL
                <strong>$displayUnsuccessCheckLogMessage</strong>
                <br />
                <strong>PROCESSING...</strong>
                <br />
                <br />
                EOL;

        $displaySuccessCheckLogMessage = sprintf(
            "***:SYSTEM LOG: USER DATA CHECK EXISTED FOR USER ID [%d] WITH [%s] ROLE IN [%s] TOURNAMENT.***",
            $inputUserId,
            $inputUserRole,
            $inputUserTournament
        );
        $displaySuccessTemplate =
            <<<EOL
                <strong>$displaySuccessCheckLogMessage</strong>
                <br />
                <a href='/setting/admin'>Wanna do again?</a>
                EOL;

        if (
            // Non integer input found
            ($inputUserId === 0) &&
            // Incorrect string input found
            ($inputUserRole === '') &&
            ($inputUserTournament === '')
        ) {
            echo "<a href='/setting/admin'>Gotchu bitch, try again but in the correct way please.</a>";
        } else {
            if (
                !checkTournamentRoleCategory(
                    id: $inputUserId,
                    role: $inputUserRole,
                    tournament: $inputUserTournament,
                    database_handle: $votRoleDatabase
                )
            ) {
                // It would be much more nasty if I tried to output this using the traditional mixed HTML & PHP codes
                echo $displayUnsuccessTemplate;

                if (
                    !createTournamentRoleCategory(
                        id: $inputUserId,
                        role: $inputUserRole,
                        tournament: $inputUserTournament,
                        database_handle: $votRoleDatabase
                    )
                ) {
                    echo "<a href='/setting/admin'>Error detected, please try again!</a>";
                } else {
                    // It would be much more nasty if I tried to output this using the traditional mixed HTML & PHP codes
                    echo $displaySuccessTemplate;
                }
            } else {
                // It would be much more nasty if I tried to output this using the traditional mixed HTML & PHP codes
                echo $displaySuccessTemplate;
            }
        }
    }
};
*/
