<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require __DIR__ . '/../Configurations/Database.php';
require __DIR__ . '/../Configurations/Environment.php';

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

function getUserAccessToken(string $temporary_code): null | bool
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

    $urlEncodedPayloadData = http_build_query(
        data: $payloadData,
        numeric_prefix: '',
        arg_separator: null,
        encoding_type: PHP_QUERY_RFC1738
    );

    # CURL session will be handled manually through curl_setopt()
    $userTokenCurlHandle = curl_init(url: null);

    curl_setopt(handle: $userTokenCurlHandle, option: CURLOPT_URL, value: $userAccessTokenUrl);
    curl_setopt(handle: $userTokenCurlHandle, option: CURLOPT_POST, value: 1);
    curl_setopt(handle: $userTokenCurlHandle, option: CURLOPT_HTTPHEADER, value: $httpHeaderRequest);
    curl_setopt(handle: $userTokenCurlHandle, option: CURLOPT_POSTFIELDS, value: $urlEncodedPayloadData);
    curl_setopt(handle: $userTokenCurlHandle, option: CURLOPT_HEADER, value: 0);
    curl_setopt(handle: $userTokenCurlHandle, option: CURLOPT_RETURNTRANSFER, value: 1);

    $userTokenCurlResponse = curl_exec(handle: $userTokenCurlHandle);

    if (curl_errno(handle: $userTokenCurlHandle)) {
        error_log(curl_error(handle: $userTokenCurlHandle));
        curl_close(handle: $userTokenCurlHandle);
        return false; // An error occurred during the API call

    } else {
        $userTokenCurlDecodedResponse = json_decode(
            json: $userTokenCurlResponse,
            associative: true,
            depth: 512,
            flags: 0
        );

        $userAccessToken    = $userTokenCurlDecodedResponse['access_token'];
        $userRefreshToken   = $userTokenCurlDecodedResponse['refresh_token'];
        $userExpireToken    = time() + 86400;

        if (!empty($userAccessToken)) {
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
                header: 'Location: /home',
                replace: true,
                response_code: 302
            ));
            return null;
        } else {
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
                header: 'Location: /home',
                replace: true,
                response_code: 302
            ));
            return null;
        }
    }
}
