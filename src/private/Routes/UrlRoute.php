<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);


$httpRedirectRequest = parse_url(
    url: $_SERVER['REQUEST_URI'],
    component: PHP_URL_PATH
);

switch ($httpRedirectRequest) {
    case '/':
    case '/home':
        require __DIR__ . '/../Controllers/Home/HomeController.php';
        break;

    case '/login':
        require __DIR__ . '/../Controllers/Home/LoginController.php';
        break;

    case '/logout':
        require __DIR__ . '/../Controllers/Home/LogoutController.php';
        break;

    case '/archive':
        require __DIR__ . '/../Controllers/NavigationBarController.php';
        require __DIR__ . '/../Views/Archive/ArchiveTournamentView.php';
        break;

    case '/archive/vot':
        require __DIR__ . '/../Controllers/NavigationBarController.php';
        require __DIR__ . '/../Views/Archive/ArchiveVotView.php';
        break;

    case '/staff':
        require __DIR__ . '/../Controllers/NavigationBarController.php';
        require __DIR__ . '/../Views/Staff/StaffTournamentView.php';
        break;

    case '/staff/vot':
        require __DIR__ . '/../Controllers/NavigationBarController.php';
        require __DIR__ . '/../Views/Staff/StaffVotView.php';
        break;

    case '/song':
        require __DIR__ . '/../Controllers/NavigationBarController.php';
        require __DIR__ . '/../Views/Song/SongTournamentView.php';
        break;

    case '/song/vot':
        require __DIR__ . '/../Controllers/NavigationBarController.php';
        require __DIR__ . '/../Controllers/SongController.php';
        break;

    case '/setting':
        require __DIR__ . '/../Controllers/Setting/GeneralSettingController.php';
        break;

    case '/setting/admin':
        require __DIR__ . '/../Controllers/Setting/AdminSettingController.php';
        break;

    case '/setting/tournament':
        require __DIR__ . '/../Controllers/Setting/TournamentSettingController.php';
        break;

    case '/setting/user':
        require __DIR__ . '/../Controllers/Setting/UserSettingController.php';
        break;

    case '/vot4':
        require __DIR__ . '/../Controllers/NavigationBarController.php';
        require __DIR__ . '/../Views/Tournament/Vot4TournamentView.php';
        break;

    case '/vot5':
        require __DIR__ . '/../Controllers/NavigationBarController.php';
        require __DIR__ . '/../Views/Tournament/Vot5TournamentView.php';
        break;

    case '/vot4/mappool':
    case '/vot5/mappool':
        require __DIR__ . '/../Controllers/NavigationBarController.php';
        require __DIR__ . '/../Controllers/MappoolController.php';
        break;

    case '/vot4/staff':
    case '/vot5/staff':
        require __DIR__ . '/../Controllers/NavigationBarController.php';
        require __DIR__ . '/../Controllers/StaffController.php';
        break;

    case '/authorise':
        require __DIR__ . '/../Controllers/AuthoriseController.php';
        break;

    case '/callback':
        require __DIR__ . '/../Controllers/CallbackController.php';
        break;

    case '/entry':
        require __DIR__ . '/../Controllers/EntryController.php';
        break;

    default:
        // Non-configured URL paths will default direct to [HOME] page
        exit(header(
            header: 'Location: /home',
            replace: true,
            response_code: 302
        ));
        break;
}
