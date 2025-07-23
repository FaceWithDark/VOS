<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

function getUserLogOut(string $cookie): bool
{
    // https://www.php.net/manual/en/function.setcookie.php
    if (!isset($cookie)) {
        http_response_code(401);
        return false;
    } else {
        unset($cookie);

        setcookie(
            name: 'vot_access_token',
            value: '',
            expires_or_options: 1,
            path: '/',
            secure: false,
            httponly: false
        );

        return true;
    }
}
