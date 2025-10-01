<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);


if (
    !isset($_COOKIE['vot_access_id']) &&
    !isset($_COOKIE['vot_access_token'])
) {
    echo '<a href="/home">Are you for real bro? You are not even login yet...</a></p>';
} else {
    // Run the session first before any output buffers
    session_start(
        options: [
            'name'              => 'vot_access_id',
            'cookie_lifetime'   => 86400,
            'cookie_httponly'   => 1
        ]
    );
    $_SESSION = [];
    session_destroy();

    setcookie(
        name: session_name(),
        value: '',
        expires_or_options: 1,
        path: '/',
        secure: false,
        httponly: false
    );

    $userAuthoriseToken = $_COOKIE['vot_access_token'];
    unset($userAuthoriseToken);

    setcookie(
        name: 'vot_access_token',
        value: '',
        expires_or_options: 1,
        path: '/',
        secure: false,
        httponly: false
    );

    // Show the page again after actions have been done
    require __DIR__ . '/../Views/Home/LogoutView.php';
}
