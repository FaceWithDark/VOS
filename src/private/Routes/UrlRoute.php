<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

// Helper function wrapped in its own file
require __DIR__ . '/../Configurations/TimeZone.php';

// Controller function wrapped in its own file
require __DIR__ . '/../Controllers/UserDataController.php';
require __DIR__ . '/../Controllers/LogOutController.php';
require __DIR__ . '/../Controllers/LogInController.php';
require __DIR__ . '/../Controllers/SongController.php';

$httpRedirectRequest = parse_url(url: $_SERVER['REQUEST_URI'], component: PHP_URL_PATH);

switch ($httpRedirectRequest) {
    case '/':
    case '/home':
        if (!isset($_COOKIE['vot_access_token'])) {
            require __DIR__ . '/../Views/NavigationBar/UnauthorsiedNavigationBarView.php';
            require __DIR__ . '/../Views/Home/HomeView.php';
        } else {
            require __DIR__ . '/../Views/NavigationBar/AuthorisedNavigationBarView.php';
            require __DIR__ . '/../Views/Home/HomeView.php';
        }
        break;

    case '/authorise':
        getOsuUserAuthoriseCode();
        break;

    case '/callback':
        if ((isset($_GET['error'])) && ($_GET['error'] === 'access_denied')) {
            // 'Cancel' button clicked
            exit(header(
                header: 'Location: /home',
                replace: true,
                response_code: 302
            ));
            break;
        } elseif ((isset($_GET['code'])) && (is_string(value: $_GET['code']))) {
            // 'Authorise' button clicked
            // TODO: regex filter. Only alphanumeric value passed to prevent
            // path injection
            $osuUserAuthoriseCode = $_GET['code'];
            getOsuUserAccessToken(code: $osuUserAuthoriseCode);
            break;
        } else {
            // Path injection detected
            http_response_code(401);
            break;
        }

    case '/archive':
        if (!isset($_COOKIE['vot_access_token'])) {
            require __DIR__ . '/../Views/NavigationBar/UnauthorsiedNavigationBarView.php';
            require __DIR__ . '/../Views/Archive/ArchiveTournamentView.php';
        } else {
            require __DIR__ . '/../Views/NavigationBar/AuthorisedNavigationBarView.php';
            require __DIR__ . '/../Views/Archive/ArchiveTournamentView.php';
        }
        break;

    case '/archive/vot':
        if (!isset($_COOKIE['vot_access_token'])) {
            require __DIR__ . '/../Views/NavigationBar/UnauthorsiedNavigationBarView.php';
            require __DIR__ . '/../Views/Archive/ArchiveVotView.php';
        } else {
            require __DIR__ . '/../Views/NavigationBar/AuthorisedNavigationBarView.php';
            require __DIR__ . '/../Views/Archive/ArchiveVotView.php';
        }
        break;

    case '/staff':
        if (!isset($_COOKIE['vot_access_token'])) {
            require __DIR__ . '/../Views/NavigationBar/UnauthorsiedNavigationBarView.php';
            require __DIR__ . '/../Views/Staff/StaffTournamentView.php';
        } else {
            require __DIR__ . '/../Views/NavigationBar/AuthorisedNavigationBarView.php';
            require __DIR__ . '/../Views/Staff/StaffTournamentView.php';
        }
        break;

    case '/staff/vot':
        if (!isset($_COOKIE['vot_access_token'])) {
            require __DIR__ . '/../Views/NavigationBar/UnauthorsiedNavigationBarView.php';
            require __DIR__ . '/../Views/Staff/StaffVotView.php';
        } else {
            require __DIR__ . '/../Views/NavigationBar/AuthorisedNavigationBarView.php';
            require __DIR__ . '/../Views/Staff/StaffVotView.php';
        }
        break;

    case '/song':
        if (!isset($_COOKIE['vot_access_token'])) {
            require __DIR__ . '/../Views/NavigationBar/UnauthorsiedNavigationBarView.php';
            require __DIR__ . '/../Views/Song/SongTournamentView.php';
        } else {
            require __DIR__ . '/../Views/NavigationBar/AuthorisedNavigationBarView.php';
            require __DIR__ . '/../Views/Song/SongTournamentView.php';
        }
        break;

    case '/song/vot':
        if (!isset($_COOKIE['vot_access_token'])) {
            // No need to fetch new custom song data (if any), read custom song
            // data straight away within the include 'View' file
            require __DIR__ . '/../Views/NavigationBar/UnauthorsiedNavigationBarView.php';
            require __DIR__ . '/../Views/Song/SongVotView.php';
        } else {
            // In need of fetching new custom song data (if any) using the below
            // data fetching method
            require __DIR__ . '/../Views/NavigationBar/AuthorisedNavigationBarView.php';

            if (!isset($_GET['tournament'])) {
                // Do nothing, show the page only
                require __DIR__ . '/../Views/Song/SongVotView.php';
            } else {
                // Perform the MVC, after button get clicked
                require __DIR__ . '/../Views/Song/SongVotView.php';
                $votTournamentName = $_GET['tournament'];
                getTournamentCustomSong(name: $votTournamentName);
            }
        }
        break;

    case '/setting':
        if (!isset($_COOKIE['vot_access_token'])) {
            // No direct access to this page without access token ('logout' scenario)
            // TODO: Find a way to retrieve the IP address of the person that trying to access the page
            error_log(
                message: "Access denied to [SETTING] page due to no access token found from user!!!",
                message_type: 0
            );
            exit(header(
                header: 'Location: /home',
                replace: true,
                response_code: 302
            ));
            break;
        } else {
            require __DIR__ . '/../Views/NavigationBar/AuthorisedNavigationBarView.php';
            require __DIR__ . '/../Views/Setting/GeneralSettingView.php';
            break;
        }

    case '/setting/admin':
        if (!isset($_COOKIE['vot_access_token'])) {
            // No direct access to this page without access token ('logout' scenario)
            // TODO: Find a way to retrieve the IP address of the person that trying to access the page
            error_log(
                message: "Access denied to [ADMIN SETTING] page due to no access token found from user!!!",
                message_type: 0
            );
            exit(header(
                header: 'Location: /home',
                replace: true,
                response_code: 302
            ));
            break;
        } else {
            $osuUserAccessToken = $_COOKIE['vot_access_token'];
            $osuUserData = getOsuUserData(token: $osuUserAccessToken);

            if ($osuUserData['id'] !== 19817503) {
                // No direct access to this page without admin permission ('login' scenario)
                // TODO: Find a way to retrieve the IP address of the person that trying to access the page
                error_log(
                    message: "Access denied to [ADMIN SETTING] page due to no admin permission!!!",
                    message_type: 0
                );
                exit(header(
                    header: 'Location: /home',
                    replace: true,
                    response_code: 302
                ));
                break;
            } else {
                // Direct access granted for user with admin permission
                require __DIR__ . '/../Views/NavigationBar/AuthorisedNavigationBarView.php';
                require __DIR__ . '/../Views/Setting/AdminSettingView.php';
                break;
            }
        }

    case '/setting/tournament':
        if (!isset($_COOKIE['vot_access_token'])) {
            // No direct access to this page without access token ('logout' scenario)
            // TODO: Find a way to retrieve the IP address of the person that trying to access the page
            error_log(
                message: "Access denied to [TOURNAMENT SETTING] page due to no access token found from user!!!",
                message_type: 0
            );
            exit(header(
                header: 'Location: /home',
                replace: true,
                response_code: 302
            ));
            break;
        } else {
            $osuUserAccessToken = $_COOKIE['vot_access_token'];
            $osuUserData = getOsuUserData(token: $osuUserAccessToken);

            // Website owner can access this role along with user that have proper role to access this page
            if (
                ($osuUserData['id'] !== 19817503)   ||
                ($osuUserData['id'] !== 9623142)    ||
                ($osuUserData['id'] !== 16039831)
            ) {
                // No direct access to this page without tournament host permission ('login' scenario)
                // TODO: Find a way to retrieve the IP address of the person that trying to access the page
                error_log(
                    message: "Access denied to [TOURNAMENT SETTING] page due to no tournament host permission!!!",
                    message_type: 0
                );
                exit(header(
                    header: 'Location: /home',
                    replace: true,
                    response_code: 302
                ));
                break;
            } else {
                // Direct access granted for user with tournament host permission
                require __DIR__ . '/../Views/NavigationBar/AuthorisedNavigationBarView.php';
                require __DIR__ . '/../Views/Setting/TournamentSettingView.php';
                break;
            }
        }

    case '/setting/user':
        if (!isset($_COOKIE['vot_access_token'])) {
            // No direct access to this page without access token ('logout' scenario)
            // TODO: Find a way to retrieve the IP address of the person that trying to access the page
            error_log(
                message: "Access denied to [USER SETTING] page due to no access token found from user!!!",
                message_type: 0
            );
            exit(header(
                header: 'Location: /home',
                replace: true,
                response_code: 302
            ));
            break;
        } else {
            // Direct access granted for authenticated user
            require __DIR__ . '/../Views/NavigationBar/AuthorisedNavigationBarView.php';
            require __DIR__ . '/../Views/Setting/UserSettingView.php';
        }
        break;

    case '/vot4':
        if (!isset($_COOKIE['vot_access_token'])) {
            require __DIR__ . '/../Views/NavigationBar/UnauthorsiedNavigationBarView.php';
            require __DIR__ . '/../Views/Tournament/Vot4TournamentView.php';
        } else {
            require __DIR__ . '/../Views/NavigationBar/AuthorisedNavigationBarView.php';
            require __DIR__ . '/../Views/Tournament/Vot4TournamentView.php';
        }
        break;

    case '/vot5':
        if (!isset($_COOKIE['vot_access_token'])) {
            require __DIR__ . '/../Views/NavigationBar/UnauthorsiedNavigationBarView.php';
            require __DIR__ . '/../Views/Tournament/Vot5TournamentView.php';
        } else {
            require __DIR__ . '/../Views/NavigationBar/AuthorisedNavigationBarView.php';
            require __DIR__ . '/../Views/Tournament/Vot5TournamentView.php';
        }
        break;

    case '/vot4/mappool':
    case '/vot5/mappool':
        if (!isset($_COOKIE['vot_access_token'])) {
            require __DIR__ . '/../Views/NavigationBar/UnauthorsiedNavigationBarView.php';
            require __DIR__ . '/../Controllers/MappoolController.php';
            break;
        } else {
            require __DIR__ . '/../Views/NavigationBar/AuthorisedNavigationBarView.php';
            require __DIR__ . '/../Controllers/MappoolController.php';
            break;
        }

    case '/vot4/staff':
    case '/vot5/staff':
        if (!isset($_COOKIE['vot_access_token'])) {
            require __DIR__ . '/../Views/NavigationBar/UnauthorsiedNavigationBarView.php';
            require __DIR__ . '/../Controllers/StaffController.php';
            break;
        } else {
            require __DIR__ . '/../Views/NavigationBar/AuthorisedNavigationBarView.php';
            require __DIR__ . '/../Controllers/StaffController.php';
            break;
        }

    case '/entry':
        if (!isset($_COOKIE['vot_access_token'])) {
            // Deny everyone access to entry file ('logout' scenario) even the website owner
            // TODO: Find a way to retrieve the IP address of the person that trying to access the file
            error_log(message: "Someone tried to access entry file without permission from website owner!!!", message_type: 0);
            exit(header(
                header: 'Location: /home',
                replace: true,
                response_code: 302
            ));
        } else {
            $osuUserAccessToken = $_COOKIE['vot_access_token'];
            $osuUserData = getOsuUserData(token: $osuUserAccessToken);

            if ($osuUserData['username'] !== 'DeepInDark') {
                // Deny everyone access to entry file ('login' scenario) expect the website owner
                // TODO: Find a way to retrieve the IP address of the person that trying to access the file
                error_log(message: "Someone tried to access entry file without permission from website owner!!!", message_type: 0);
                exit(header(
                    header: 'Location: /home',
                    replace: true,
                    response_code: 302
                ));
            } else {
                // Only me (the website owner) can access entry file
                require __DIR__ . '/../Views/EntryView.php';
            }
        }
        break;

    case '/login':
        if (!isset($_COOKIE['vot_access_token'])) {
            exit(header(
                header: 'Location: /home',
                replace: true,
                response_code: 302
            ));
            break;
        } else {
            getUserLogIn();

            require __DIR__ . '/../Views/Home/LogInView.php';

            $userAccessToken    = $_COOKIE['vot_access_token'];
            $osuUserData        = getOsuUser(token: $userAccessToken);
            $_SESSION['id']     = $osuUserData[0]['osu_user_id']; // Only one user data per unique token used so it is safe to do so

            break;
        }

    case '/logout':
        if (!isset($_COOKIE['vot_access_token'])) {
            exit(header(
                header: 'Location: /home',
                replace: true,
                response_code: 302
            ));
            break;
        } else {
            session_start(
                options: [
                    'name' => 'vot_access_id',
                    'cookie_lifetime' => 86400,
                    'cookie_httponly' => 1,
                    'read_and_close' => true
                ]
            );

            $userAccessId       = $_SESSION['id'];
            $userAccesstoken    = $_COOKIE['vot_access_token'];

            getUserLogOut(
                id: $userAccessId,
                cookie: $userAccesstoken
            );

            require __DIR__ . '/../Views/Home/LogOutView.php';
            break;
        }

    default:
        // Any URL paths that I haven't configured will be sent back to 'Home' page
        exit(header(
            header: 'Location: /home',
            replace: true,
            response_code: 302
        ));
        break;
}
