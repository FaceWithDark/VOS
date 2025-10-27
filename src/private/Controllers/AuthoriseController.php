<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require __DIR__ . '/../Configurations/Environment.php';


if (
    isset($_COOKIE['vot_access_id']) &&
    isset($_COOKIE['vot_access_token'])
) {
    echo '<a href="/home">Seriously? Are not you already logged in bro??</a>';
} else {
    $applicationId
        =  getenv(name: 'APPLICATION_ID', local_only: true)
        ?: getenv(name: 'APPLICATION_ID');
    $applicationRedirectUrl
        =  getenv(name: 'APPLICATION_REDIRECT_URL', local_only: true)
        ?: getenv(name: 'APPLICATION_REDIRECT_URL');
    $applicationResponseType
        =  getenv(name: 'APPLICATION_RESPONSE_TYPE', local_only: true)
        ?: getenv(name: 'APPLICATION_RESPONSE_TYPE');
    $applicationScope
        =  getenv(name: 'APPLICATION_SCOPE', local_only: true)
        ?: getenv(name: 'APPLICATION_SCOPE');
    $applicationState
        =  getenv(name: 'APPLICATION_STATE', local_only: true)
        ?: getenv(name: 'APPLICATION_STATE');

    $applicationUrl = "https://osu.ppy.sh/oauth/authorize?client_id=$applicationId&redirect_uri=$applicationRedirectUrl&response_type=$applicationResponseType&scope=$applicationScope&state=$applicationState";

    exit(header(
        header: "Location: $applicationUrl",
        replace: true,
        response_code: 302
    ));
}
