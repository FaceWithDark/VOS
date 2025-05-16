<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require __DIR__ . '/../Controllers/Controller.php';
require __DIR__ . '/../Models/NavigationBar.php';

function getUserAuthoriseCode(): null
{
    $applicationId              = getenv(name: 'APPLICATION_ID', local_only: true)              ?: getenv(name: 'APPLICATION_ID');
    $applicationRedirectUrl     = getenv(name: 'APPLICATION_REDIRECT_URL', local_only: true)    ?: getenv(name: 'APPLICATION_REDIRECT_URL');
    $applicationResponseType    = getenv(name: 'APPLICATION_RESPONSE_TYPE', local_only: true)   ?: getenv(name: 'APPLICATION_RESPONSE_TYPE');
    $applicationScope           = getenv(name: 'APPLICATION_SCOPE', local_only: true)           ?: getenv(name: 'APPLICATION_SCOPE');
    $applicationState           = getenv(name: 'APPLICATION_STATE', local_only: true)           ?: getenv(name: 'APPLICATION_STATE');

    $applicationAuthoriseUrl = "https://osu.ppy.sh/oauth/authorize?client_id=$applicationId&redirect_uri=$applicationRedirectUrl&response_type=$applicationResponseType&scope=$applicationScope&state=$applicationState";

    exit(header(
        header: "Location: $applicationAuthoriseUrl",
        replace: true,
        response_code: 302
    ));
    return null;
}

function getUserAccessToken(string $temporary_code): bool | null
{
    $userAccessTokenUrl             = "https://osu.ppy.sh/oauth/token";

    $applicationId                  = getenv(name: 'APPLICATION_ID', local_only: true)              ?: getenv(name: 'APPLICATION_ID');
    $applicationSecret              = getenv(name: 'APPLICATION_SECRET', local_only: true)          ?: getenv(name: 'APPLICATION_SECRET');
    $applicationAuthorisationCode   = $temporary_code;
    $applicationGrantType           = getenv(name: 'APPLICATION_GRANT_TYPE', local_only: true)      ?: getenv(name: 'APPLICATION_GRANT_TYPE');
    $applicationRedirectUrl         = getenv(name: 'APPLICATION_REDIRECT_URL', local_only: true)    ?: getenv(name: 'APPLICATION_REDIRECT_URL');

    $httpAcceptType                 = 'application/json';
    $httpContentType                = 'application/x-www-form-urlencoded';

    if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
        // Prepare payload data for token exchange
        $payloadData = [
            'client_id'     => $applicationId,
            'client_secret' => $applicationSecret,
            'code'          => $applicationAuthorisationCode,
            'grant_type'    => $applicationGrantType,
            'redirect_uri'  => $applicationRedirectUrl
        ];
        $httpHeaderRequest = [
            "Accept: $httpAcceptType",
            "Content-Type: $httpContentType",
        ];
    } else {
        $payloadData = array(
            'client_id'     => $applicationId,
            'client_secret' => $applicationSecret,
            'code'          => $applicationAuthorisationCode,
            'grant_type'    => $applicationGrantType,
            'redirect_uri'  => $applicationRedirectUrl
        );
        $httpHeaderRequest = array(
            "Accept: $httpAcceptType",
            "Content-Type: $httpContentType",
        );
    }

    $urlencodedPayloadData = http_build_query(
        data: $payloadData,
        numeric_prefix: '',
        arg_separator: null,
        encoding_type: PHP_QUERY_RFC1738
    );

    # CURL session will be handled manually through curl_setopt()
    $curlHandle = curl_init(url: null);

    curl_setopt(handle: $curlHandle, option: CURLOPT_URL, value: $userAccessTokenUrl);
    curl_setopt(handle: $curlHandle, option: CURLOPT_POST, value: 1);
    curl_setopt(handle: $curlHandle, option: CURLOPT_HTTPHEADER, value: $httpHeaderRequest);
    curl_setopt(handle: $curlHandle, option: CURLOPT_POSTFIELDS, value: $urlencodedPayloadData);
    curl_setopt(handle: $curlHandle, option: CURLOPT_HEADER, value: 0);
    curl_setopt(handle: $curlHandle, option: CURLOPT_RETURNTRANSFER, value: 1);

    $curlResponse = curl_exec(handle: $curlHandle);

    if (curl_errno(handle: $curlHandle)) {
        error_log(curl_error(handle: $curlHandle));
        curl_close(handle: $curlHandle);
        return false; // An error occurred during the API call

    } else {
        $curlDecodedResponse = json_decode(
            json: $curlResponse,
            associative: true,
            depth: 512,
            flags: 0
        );

        $userAccessToken    = $curlDecodedResponse['access_token'];
        $userRefreshToken   = $curlDecodedResponse['refresh_token'];
        $userExpireToken    = time() + 86400;

        if (empty($userAccessToken)) {
            // Ensure that user won't have to re-authorise multiple times if idling for a long time
            setcookie(
                name: 'vot_access_token',
                value: $userRefreshToken,
                expires_or_options: $userExpireToken,
                path: '/',
                domain: '',
                secure: false,
                httponly: true
            );
            exit(header(
                header: 'Location: /token',
                replace: true,
                response_code: 302
            ));
            return null;
        } else {
            // Just use the freshly created one until expired time
            setcookie(
                name: 'vot_access_token',
                value: $userAccessToken,
                expires_or_options: $userExpireToken,
                path: '/',
                domain: '',
                secure: false,
                httponly: true
            );
            exit(header(
                header: 'Location: /token',
                replace: true,
                response_code: 302
            ));
            return null;
        }
    }
}

/*
function getUserData(string $access_token): array | bool
{
    $osuUserUrl                 = "https://osu.ppy.sh/api/v2/me/taiko";
    $osuUserAccessToken         = $_COOKIE['vot_access_token'];

    $httpAcceptType                 = 'application/json';
    $httpContentType                = 'application/json';

    if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
        $httpHeaderRequest = [
            "Accept: $httpAcceptType",
            "Content-Type: $httpContentType",
        ];
    } else {
        $httpHeaderRequest = array(
            "Accept: $httpAcceptType",
            "Content-Type: $httpContentType",
        );
    }

    # CURL session will be handled manually through curl_setopt()
    $curlHandle = curl_init(url: null);

    curl_setopt(handle: $curlHandle, option: CURLOPT_URL, value: $userAccessTokenUrl);
    curl_setopt(handle: $curlHandle, option: CURLOPT_HTTPHEADER, value: $httpHeaderRequest);
    curl_setopt(handle: $curlHandle, option: CURLOPT_HEADER, value: 0);
    curl_setopt(handle: $curlHandle, option: CURLOPT_RETURNTRANSFER, value: 1);

    $curlResponse = curl_exec(handle: $curlHandle);

    if (curl_errno(handle: $curlHandle)) {
        error_log(curl_error(handle: $curlHandle));
        curl_close(handle: $curlHandle);
        return false; // An error occurred during the API call

    } else {
        $curlDecodedResponse = json_decode(
            json: $curlResponse,
            associative: true,
            depth: 512,
            flags: 0
        );
}

// Exchange the authorisation code for an access token
$userData = getUserData(token: $osuUserAccessToken, url: $osuUserUrl);

if ($userData !== false) {
    // echo ('User data received');
    $userId         = $userData->id;
    $userImage      = $userData->avatar_url;
    $userUrl        = $userData->user_url; // TODO: no built-in API for this. Create one.
    $userFlag       = $userData->country_code;
    $userName       = $userData->username;
    $userRank       = $userData->statistics->global_rank;
    $userTimeZone   = $userData->$user_timezone; // TODO: no built-in API for this. Create one.

    createUserData(
        id: $userId,
        image: $userImage,
        url: $userUrl,
        flag: $userFlag,
        name: $userName,
        rank: $userRank,
        time_zone: $userTimeZone,
        database_connection: $userData # TODO: move database connection code into a general include-able file (maybe, `Controller.php`)
    );
} else {
    die();
}
*/
