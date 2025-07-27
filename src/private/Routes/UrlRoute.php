<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

// Helper function wrapped in its own file
require __DIR__ . '/../Configurations/TimeZone.php';

// Controller function wrapped in its own file
require __DIR__ . '/../Controllers/UserDataController.php';
require __DIR__ . '/../Controllers/LogOutController.php';
require __DIR__ . '/../Controllers/MappoolController.php';
require __DIR__ . '/../Controllers/StaffController.php';
require __DIR__ . '/../Controllers/SongController.php';

$httpRedirectRequest = parse_url(url: $_SERVER['REQUEST_URI'], component: PHP_URL_PATH);

switch ($httpRedirectRequest) {
    case '/':
    case '/home':
        if (!isset($_COOKIE['vot_access_token'])) {
            require __DIR__ . '/../Views/NavigationBar/UnauthorsiedNavigationBarView.php';
        } else {
            require __DIR__ . '/../Views/NavigationBar/AuthorisedNavigationBarView.php';
        }
        require __DIR__ . '/../Views/HomeView.php';
        break;

    case '/authorise':
        getUserAuthoriseCode();
        break;

    case '/callback':
        # TODO: filter manual `code` parameter injection
        $userAuthoriseCode = $_GET['code'];

        if (!isset($userAuthoriseCode)) {
            http_response_code(401);
            break;
        } else {
            getUserAccessToken(temporary_code: $userAuthoriseCode);
            break;
        }

    case '/archive':
        if (!isset($_COOKIE['vot_access_token'])) {
            require __DIR__ . '/../Views/NavigationBar/UnauthorsiedNavigationBarView.php';
        } else {
            require __DIR__ . '/../Views/NavigationBar/AuthorisedNavigationBarView.php';
        }
        require __DIR__ . '/../Views/Archive/ArchiveTournamentView.php';
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

    case '/vot4':
        if (!isset($_COOKIE['vot_access_token'])) {
            require __DIR__ . '/../Views/NavigationBar/UnauthorsiedNavigationBarView.php';
            require __DIR__ . '/../Views/Tournament/Vot4TournamentView.php';
        } else {
            require __DIR__ . '/../Views/NavigationBar/AuthorisedNavigationBarView.php';
            require __DIR__ . '/../Views/Tournament/Vot4TournamentView.php';
        }
        break;

    case '/vot4/mappool':
        if (!isset($_COOKIE['vot_access_token'])) {
            // No need to fetch new beatmap data (if any), read beatmap data
            // straight away within the include 'View' file
            require __DIR__ . '/../Views/NavigationBar/UnauthorsiedNavigationBarView.php';
            require __DIR__ . '/../Views/Tournament/Vot4MappoolView.php';
        } else {
            // In need of fetching new beatmap data (if any) using the below
            // data fetching method
            require __DIR__ . '/../Views/NavigationBar/AuthorisedNavigationBarView.php';

            if (!isset($_GET['round'])) {
                // Do nothing, show the page only
                require __DIR__ . '/../Views/Tournament/Vot4MappoolView.php';
            } else {
                // Perform the MVC, after button get clicked
                require __DIR__ . '/../Views/Tournament/Vot4MappoolView.php';
                $vot4TournamentName = explode(
                    separator: '/',
                    string: trim(
                        string: $_SERVER['REQUEST_URI'],
                        characters: '/'
                    ),
                    limit: PHP_INT_MAX
                )[0];
                $vot4TournamentRound = $_GET['round'];
                getTournamentMappool(
                    name: $vot4TournamentName,
                    round: $vot4TournamentRound
                );
            }
        }
        break;

    case '/vtc':
        // TODO: deny access for now. I will implement this if other issues fixed.
        http_response_code(403);
        break;

    case '/staff':
        if (!isset($_COOKIE['vot_access_token'])) {
            require __DIR__ . '/../Views/NavigationBar/UnauthorsiedNavigationBarView.php';
        } else {
            require __DIR__ . '/../Views/NavigationBar/AuthorisedNavigationBarView.php';
        }
        require __DIR__ . '/../Views/Staff/StaffTournamentView.php';
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

    case '/vot4/staff':
        $vot4TournamentDetail = explode(
            separator: '/',
            string: trim(
                string: $_SERVER['REQUEST_URI'],
                characters: '/'
            ),
            limit: PHP_INT_MAX
        );

        // echo '<pre>' . print_r($vot4TournamentDetail, true) . '</pre>';
        $vot4TournamentName = $vot4TournamentDetail[0];

        if (!isset($_COOKIE['vot_access_token'])) {
            // Start 1st output buffer (HTML outputs in this case). Delete this buffer if no valid GET parameter value found
            ob_start(
                callback: null,
                chunk_size: 0,
                flags: PHP_OUTPUT_HANDLER_STDFLAGS
            );

            require __DIR__ . '/../Views/NavigationBar/UnauthorsiedNavigationBarView.php';
            require __DIR__ . '/../Views/Tournament/Vot4StaffView.php';
        } else {
            // Start 1st output buffer (HTML outputs in this case). Delete this buffer if no valid GET parameter value found
            ob_start(
                callback: null,
                chunk_size: 0,
                flags: PHP_OUTPUT_HANDLER_STDFLAGS
            );

            require __DIR__ . '/../Views/NavigationBar/AuthorisedNavigationBarView.php';
            require __DIR__ . '/../Views/Tournament/Vot4StaffView.php';

            if (isset($_GET['role'])) {
                $vot4StaffRole = $_GET['role'];

                switch ($vot4StaffRole) {
                    case 'default':
                        getTournamentStaff(tournament_name: $vot4TournamentName);
                        break;

                    default:
                        while (ob_get_level() > 1) {
                            ob_end_clean(); // Delete all output buffers (HTML outputs in this case)
                        }
                        http_response_code(404);
                        ob_end_flush(); // End output buffers delete process
                        break;
                }
            }
        }
        break;

    case '/song':
        if (!isset($_COOKIE['vot_access_token'])) {
            require __DIR__ . '/../Views/NavigationBar/UnauthorsiedNavigationBarView.php';
        } else {
            require __DIR__ . '/../Views/NavigationBar/AuthorisedNavigationBarView.php';
        }
        require __DIR__ . '/../Views/Song/SongTournamentView.php';
        break;

    case '/song/vot':
        if (!isset($_COOKIE['vot_access_token'])) {
            // Start 1st output buffer (HTML outputs in this case). Delete this buffer if no valid GET parameter value found
            ob_start(
                callback: null,
                chunk_size: 0,
                flags: PHP_OUTPUT_HANDLER_STDFLAGS
            );

            require __DIR__ . '/../Views/NavigationBar/UnauthorsiedNavigationBarView.php';
            require __DIR__ . '/../Views/Song/SongVotView.php';
        } else {
            // Start 1st output buffer (HTML outputs in this case). Delete this buffer if no valid GET parameter value found
            ob_start(
                callback: null,
                chunk_size: 0,
                flags: PHP_OUTPUT_HANDLER_STDFLAGS
            );

            require __DIR__ . '/../Views/NavigationBar/AuthorisedNavigationBarView.php';
            require __DIR__ . '/../Views/Song/SongVotView.php';

            if (isset($_GET['tournament'])) {
                $votCustomSongLocation = $_GET['tournament'];

                switch ($votCustomSongLocation) {
                    case 'vot4':
                        getTournamentCustomSong(tournament_name: $votCustomSongLocation);
                        break;

                    default:
                        while (ob_get_level() > 1) {
                            ob_end_clean(); // Delete all output buffers (HTML outputs in this case)
                        }
                        http_response_code(404);
                        ob_end_flush(); // End output buffers delete process
                        break;
                }
            }
        }
        break;

    case '/entry':
        # TODO: block non-admin access to this page properly through privilege levels
        require __DIR__ . '/../Views/EntryView.php';
        break;

    case '/logout':
        $userAccessCookie = $_COOKIE['vot_access_token'];

        if (!isset($userAccessCookie)) {
            # TODO: proper HTTP handling page
            exit(header(
                header: 'Location: /home',
                replace: true,
                response_code: 302
            ));
        } else {
            getUserLogOut(cookie: $userAccessCookie);
            require __DIR__ . '/../Views/LogOutView.php';
            break;
        }

        # User is not allowed to see the navigation bar by itself as a page
    case '/nav':
    case '/navbar':
    case '/navigation':
    case '/navigationbar':
        # TODO: block non-admin access to this page properly through privilege levels
        http_response_code(403);
        break;

    default:
        http_response_code(404);
        break;
}
