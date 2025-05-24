<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require __DIR__ . '/../private/Controllers/UserDataController.php';

$httpRedirectRequest = parse_url(url: $_SERVER['REQUEST_URI'], component: PHP_URL_PATH);

switch ($httpRedirectRequest) {
    case '/':
    case '/home':
        if (!isset($_COOKIE['vot_access_token'])) {
            require __DIR__ . '/../private/Views/NavigationBar/UnauthorsiedNavigationBarView.php';
        } else {
            require __DIR__ . '/../private/Views/NavigationBar/AuthorisedNavigationBarView.php';
        }
        require __DIR__ . '/../private/Views/HomeView.php';
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
            require __DIR__ . '/../private/Views/NavigationBar/UnauthorsiedNavigationBarView.php';
        } else {
            require __DIR__ . '/../private/Views/NavigationBar/AuthorisedNavigationBarView.php';
        }
        require __DIR__ . '/../private/Views/Archive/ArchiveTournamentView.php';
        break;

    case '/vot';
        ob_start(callback: null);

        if (!isset($_COOKIE['vot_access_token'])) {
            require __DIR__ . '/../private/Views/NavigationBar/UnauthorsiedNavigationBarView.php';
        } else {
            require __DIR__ . '/../private/Views/NavigationBar/AuthorisedNavigationBarView.php';
        }
        require __DIR__ . '/../private/Views/Archive/ArchiveVotView.php';

        if (!empty($_GET['name'])) {
            $tournamentName = $_GET['name'];

            switch ($tournamentName) {
                case 'vot1':
                    ob_clean();

                    if (version_compare(PHP_VERSION, '4.2.0', '>=')) {
                        // https://www.php.net/manual/en/function.ob-end-flush.php#42979
                        while (ob_get_level() > 0) {
                            exit(header(
                                header: 'Location: /vot1',
                                replace: true,
                                response_code: 302
                            ));
                            ob_end_flush();
                            break;
                        }
                    } else {
                        // https://www.php.net/manual/en/function.ob-end-flush.php#refsect1-function.ob-end-flush-examples
                        exit(header(
                            header: 'Location: /vot1',
                            replace: true,
                            response_code: 302
                        ));
                        while (@ob_end_flush());
                        break;
                    }

                case 'vot2':
                    ob_clean();

                    if (version_compare(PHP_VERSION, '4.2.0', '>=')) {
                        // https://www.php.net/manual/en/function.ob-end-flush.php#42979
                        while (ob_get_level() > 0) {
                            exit(header(
                                header: 'Location: /vot2',
                                replace: true,
                                response_code: 302
                            ));
                            ob_end_flush();
                            break;
                        }
                    } else {
                        // https://www.php.net/manual/en/function.ob-end-flush.php#refsect1-function.ob-end-flush-examples
                        exit(header(
                            header: 'Location: /vot2',
                            replace: true,
                            response_code: 302
                        ));
                        while (@ob_end_flush());
                        break;
                    }

                case 'vot3':
                    ob_clean();

                    if (version_compare(PHP_VERSION, '4.2.0', '>=')) {
                        // https://www.php.net/manual/en/function.ob-end-flush.php#42979
                        while (ob_get_level() > 0) {
                            exit(header(
                                header: 'Location: /vot3',
                                replace: true,
                                response_code: 302
                            ));
                            ob_end_flush();
                            break;
                        }
                    } else {
                        // https://www.php.net/manual/en/function.ob-end-flush.php#refsect1-function.ob-end-flush-examples
                        exit(header(
                            header: 'Location: /vot3',
                            replace: true,
                            response_code: 302
                        ));
                        while (@ob_end_flush());
                        break;
                    }

                case 'vot4':
                    ob_clean();

                    if (version_compare(PHP_VERSION, '4.2.0', '>=')) {
                        // https://www.php.net/manual/en/function.ob-end-flush.php#42979
                        while (ob_get_level() > 0) {
                            exit(header(
                                header: 'Location: /vot4',
                                replace: true,
                                response_code: 302
                            ));
                            ob_end_flush();
                            break;
                        }
                    } else {
                        // https://www.php.net/manual/en/function.ob-end-flush.php#refsect1-function.ob-end-flush-examples
                        exit(header(
                            header: 'Location: /vot4',
                            replace: true,
                            response_code: 302
                        ));
                        while (@ob_end_flush());
                        break;
                    }

                default:
                    ob_clean();

                    if (version_compare(PHP_VERSION, '4.2.0', '>=')) {
                        // https://www.php.net/manual/en/function.ob-end-flush.php#42979
                        while (ob_get_level() > 0) {
                            http_response_code(404);
                            ob_end_flush();
                        }
                    } else {
                        // https://www.php.net/manual/en/function.ob-end-flush.php#refsect1-function.ob-end-flush-examples
                        http_response_code(404);
                        while (@ob_end_flush());
                    }
            }
        } else {
            // TODO: This is just a trick, not real solution. I can't think of one yet but better to put a TODO here.
            error_log(message: 'GET data not found! Ignoring for now...', message_type: 0);
        }

        break;

    case '/vot1':
        if (!isset($_COOKIE['vot_access_token'])) {
            require __DIR__ . '/../private/Views/NavigationBar/UnauthorsiedNavigationBarView.php';
        } else {
            require __DIR__ . '/../private/Views/NavigationBar/AuthorisedNavigationBarView.php';
        }
        require __DIR__ . '/../private/Views/Vot/Vot1View.php';
        break;

    case '/vot2':
        if (!isset($_COOKIE['vot_access_token'])) {
            require __DIR__ . '/../private/Views/NavigationBar/UnauthorsiedNavigationBarView.php';
        } else {
            require __DIR__ . '/../private/Views/NavigationBar/AuthorisedNavigationBarView.php';
        }
        require __DIR__ . '/../private/Views/Vot/Vot2View.php';
        break;

    case '/vot3':
        if (!isset($_COOKIE['vot_access_token'])) {
            require __DIR__ . '/../private/Views/NavigationBar/UnauthorsiedNavigationBarView.php';
        } else {
            require __DIR__ . '/../private/Views/NavigationBar/AuthorisedNavigationBarView.php';
        }
        require __DIR__ . '/../private/Views/Vot/Vot3View.php';
        break;

    case '/vot4':
        if (!isset($_COOKIE['vot_access_token'])) {
            require __DIR__ . '/../private/Views/NavigationBar/UnauthorsiedNavigationBarView.php';
        } else {
            require __DIR__ . '/../private/Views/NavigationBar/AuthorisedNavigationBarView.php';
        }
        require __DIR__ . '/../private/Views/Vot/Vot4View.php';
        break;

    case '/vtc':
        // TODO: Deny access for now. I will implement this if other issues fixed.
        http_response_code(403);
        break;

    case '/staff':
        if (!isset($_COOKIE['vot_access_token'])) {
            require __DIR__ . '/../private/Views/NavigationBar/UnauthorsiedNavigationBarView.php';
        } else {
            require __DIR__ . '/../private/Views/NavigationBar/AuthorisedNavigationBarView.php';
        }
        require __DIR__ . '/../private/Views/Staff/StaffTournamentView.php';
        break;

    case '/song':
        if (!isset($_COOKIE['vot_access_token'])) {
            require __DIR__ . '/../private/Views/NavigationBar/UnauthorsiedNavigationBarView.php';
        } else {
            require __DIR__ . '/../private/Views/NavigationBar/AuthorisedNavigationBarView.php';
        }
        require __DIR__ . '/../private/Views/Song/SongTournamentView.php';
        break;

    case '/entry':
        # TODO: block non-admin access to this page properly through privilege levels
        require __DIR__ . '/../private/Views/EntryView.php';
        break;

    case '/logout':
        // TODO: Leave it like this for now as I will slowly fixing through each pages.
        exit(header(
            header: 'Location: /home',
            replace: true,
            response_code: 302
        ));
        break;

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
