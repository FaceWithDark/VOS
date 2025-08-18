<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

function getUserLogIn(): null
{
    session_start(
        options: [
            'name' => 'vot_access_id',
            'cookie_lifetime' => 86400,
            'cookie_httponly' => 1
        ]
    );
    session_regenerate_id(delete_old_session: true);

    return null;
}
