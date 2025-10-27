<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require __DIR__ . '/../Models/Login.php';


if (
    !isset($_COOKIE['vot_access_id']) &&
    !isset($_COOKIE['vot_access_token'])
) {
    // Deny everyone access to entry file ('logout' scenario) even the website owner
    // TODO: Find a way to retrieve the IP address of the person that trying to access the file
    http_response_code(403);
} else {
    $userAuthoriseToken     = $_COOKIE['vot_access_token'];
    $userAuthoriseData      = getUserData(token: $userAuthoriseToken);

    if ($userAuthoriseData['id'] !== 19817503) {
        // Deny everyone access to entry file ('login' scenario) expect the website owner
        // TODO: Find a way to retrieve the IP address of the person that trying to access the file
        http_response_code(403);
    } else {
        // Only me (the website owner) can access entry file
        require __DIR__ . '/../Views/EntryView.php';
    }
}
