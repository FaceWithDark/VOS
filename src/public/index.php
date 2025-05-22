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
