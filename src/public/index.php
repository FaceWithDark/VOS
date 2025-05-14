<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require __DIR__ . '/../private/Controllers/NavigationBarController.php';

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
        redirectUserAuthorisationPage();
        break;

    case '/callback':
        redirectUserCallbackPage();
        break;

    # TODO: add a timer to auto-redirect to 'Home' page
    case '/token':
        if (!isset($_COOKIE['vot_access_token'])) {
            http_response_code(403);
        } else {
            require __DIR__ . '/../private/Views/NavigationBar/UserAccessTokenView.php';
        }
        break;

    case '/entry':
        require __DIR__ . '/../private/Views/EntryView.php';
        break;

    # User is not allowed to see the navigation bar by itself as a page
    # TODO: block non-admin access to this page properly through privilege levels
    case '/nav':
    case '/navbar':
    case '/navigation':
    case '/navigationbar':
        http_response_code(403);
        break;

    default:
        http_response_code(404);
}
