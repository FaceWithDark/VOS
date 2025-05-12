<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require __DIR__ . '/../Models/NavigationBar.php';

function redirectUserAuthorisationPage()
{
    $userAuthorisationUrl = getUserAuthorisationUrl();
    exit(header(
        header: "Location: $userAuthorisationUrl",
        replace: true,
        response_code: 302
    ));
}

function redirectUserCallbackPage()
{
    $userAuthorisationCode = $_GET['code'];

    if (!isset($userAuthorisationCode)) {
        http_response_code(401);
    } else {
        getUserAccessToken(temporary_code: $userAuthorisationCode);
    }
}
