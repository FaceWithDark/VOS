<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require __DIR__ . '/../private/Controllers/NavigationBarController.php';
require __DIR__ . '/../private/Controllers/TokenController.php';

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

    # TODO: filter manual `code` parameter injection
    case '/callback':
        $userAuthoriseCode = $_GET['code'];

        if (!isset($userAuthoriseCode)) {
            http_response_code(401);
            break;
        } else {
            getUserAccessToken(temporary_code: $userAuthoriseCode);
            break;
        }

    case '/token':
        $userAccessToken = $_COOKIE['vot_access_token'];

        if (!isset($userAccessToken)) {
            http_response_code(401);
            break;
        } else {
            getUserData(access_token: $userAccessToken);
            require __DIR__ . '/../private/Views/NavigationBar/TokenView.php';
            break;
        }

    case '/entry':
        require __DIR__ . '/../private/Views/EntryView.php';
        break;

    # TODO: block non-admin access to this page properly through privilege levels
    # User is not allowed to see the navigation bar by itself as a page
    case '/nav':
    case '/navbar':
    case '/navigation':
    case '/navigationbar':
        http_response_code(403);
        break;

    default:
        http_response_code(404);
        break;
}
