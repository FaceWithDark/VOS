<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

$httpRedirectRequest = $_SERVER['REQUEST_URI'];

switch ($httpRedirectRequest) {
    case '/':
    case '/home':
        require __DIR__ . '/../private/Views/HomeView.php';
        break;

    case '/entry':
        require __DIR__ . '/../private/Views/EntryView.php';
        break;

    default:
        http_response_code(404);
}
