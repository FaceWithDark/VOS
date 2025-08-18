<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

function getUserLogOut(
    int $id,
    string $cookie
): null {
    unset($id);
    setcookie(
        name: session_name(),
        value: '',
        expires_or_options: 1,
        path: '/',
        secure: false,
        httponly: false
    );

    unset($cookie);
    setcookie(
        name: 'vot_access_token',
        value: '',
        expires_or_options: 1,
        path: '/',
        secure: false,
        httponly: false
    );

    return null;
}
