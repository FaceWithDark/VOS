<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require __DIR__ . '/../Configurations/Database.php';
require __DIR__ . '/../Configurations/Environment.php';


function getOsuUserAuthoriseCode(): null
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


function getOsuUserAccessToken(
    string $code
): bool | null {
    $applicationId                  = getenv(name: 'APPLICATION_ID', local_only: true)              ?: getenv(name: 'APPLICATION_ID');
    $applicationSecret              = getenv(name: 'APPLICATION_SECRET', local_only: true)          ?: getenv(name: 'APPLICATION_SECRET');
    $applicationAuthorisationCode   = $code;
    $applicationGrantType           = getenv(name: 'APPLICATION_GRANT_TYPE', local_only: true)      ?: getenv(name: 'APPLICATION_GRANT_TYPE');
    $applicationRedirectUrl         = getenv(name: 'APPLICATION_REDIRECT_URL', local_only: true)    ?: getenv(name: 'APPLICATION_REDIRECT_URL');

    $userAccessTokenUrl             = "https://osu.ppy.sh/oauth/token";
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

        $urlEncodedPayloadData = http_build_query(
            data: $payloadData,
            numeric_prefix: '',
            arg_separator: null,
            encoding_type: PHP_QUERY_RFC1738
        );

        # CURL session will be handled manually through curl_setopt()
        $userAccessTokenCurlHandle = curl_init(url: null);

        curl_setopt(handle: $userAccessTokenCurlHandle, option: CURLOPT_URL, value: $userAccessTokenUrl);
        curl_setopt(handle: $userAccessTokenCurlHandle, option: CURLOPT_POST, value: 1);
        curl_setopt(handle: $userAccessTokenCurlHandle, option: CURLOPT_HTTPHEADER, value: $httpHeaderRequest);
        curl_setopt(handle: $userAccessTokenCurlHandle, option: CURLOPT_POSTFIELDS, value: $urlEncodedPayloadData);
        curl_setopt(handle: $userAccessTokenCurlHandle, option: CURLOPT_HEADER, value: 0);
        curl_setopt(handle: $userAccessTokenCurlHandle, option: CURLOPT_RETURNTRANSFER, value: 1);

        $userAccessTokenCurlResponse = curl_exec(handle: $userAccessTokenCurlHandle);

        if (curl_errno(handle: $userAccessTokenCurlHandle)) {
            error_log(curl_error(handle: $userAccessTokenCurlHandle));
            curl_close(handle: $userAccessTokenCurlHandle);
            return false; // An error occurred during the API call

        } else {
            $userAccessTokenReadableData = json_decode(
                json: $userAccessTokenCurlResponse,
                associative: true,
                depth: 512,
                flags: 0
            );

            curl_close(handle: $userAccessTokenCurlHandle);

            $userAccessToken    = $userAccessTokenReadableData['access_token'];
            $userRefreshToken   = $userAccessTokenReadableData['refresh_token'];
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
                    header: 'Location: /login',
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
                    header: 'Location: /login',
                    replace: true,
                    response_code: 302
                ));
                return null;
            }
        }
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
    $userAccessTokenCurlHandle = curl_init(url: null);

    curl_setopt(handle: $userAccessTokenCurlHandle, option: CURLOPT_URL, value: $userAccessTokenUrl);
    curl_setopt(handle: $userAccessTokenCurlHandle, option: CURLOPT_POST, value: 1);
    curl_setopt(handle: $userAccessTokenCurlHandle, option: CURLOPT_HTTPHEADER, value: $httpHeaderRequest);
    curl_setopt(handle: $userAccessTokenCurlHandle, option: CURLOPT_POSTFIELDS, value: $urlEncodedPayloadData);
    curl_setopt(handle: $userAccessTokenCurlHandle, option: CURLOPT_HEADER, value: 0);
    curl_setopt(handle: $userAccessTokenCurlHandle, option: CURLOPT_RETURNTRANSFER, value: 1);

    $userAccessTokenCurlResponse = curl_exec(handle: $userAccessTokenCurlHandle);

    if (curl_errno(handle: $userAccessTokenCurlHandle)) {
        error_log(curl_error(handle: $userAccessTokenCurlHandle));
        curl_close(handle: $userAccessTokenCurlHandle);
        return false; // An error occurred during the API call

    } else {
        $userAccessTokenReadableData = json_decode(
            json: $userAccessTokenCurlResponse,
            associative: true,
            depth: 512,
            flags: 0
        );

        curl_close(handle: $userAccessTokenCurlHandle);

        $userAccessToken    = $userAccessTokenReadableData['access_token'];
        $userRefreshToken   = $userAccessTokenReadableData['refresh_token'];
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


function getOsuUser(
    $token
): array {
    $allOsuUserData         = [];
    $osuUserData            = getOsuUserData(token: $token);

    $osuUserId              = $osuUserData['id'];
    $osuUserTournamentId    = 'NONE'; // Everyone belong to none tournament by default
    $osuUserName            = $osuUserData['username'];
    $osuUserRole            = 'User'; // Everyone have user-level access by default
    $osuUserFlag            = $osuUserData['country_code'];
    $osuUserImage           = $osuUserData['avatar_url'];
    $osuUserUrl             = "https://osu.ppy.sh/users/{$osuUserData['id']}";
    $osuUserRank            = $osuUserData['statistics']['global_rank'];
    $osuUserTimeZone        = getUserTimeZone()['baseOffset'];

    $allOsuUserData[]       = [
        'osu_user_id'               => $osuUserId,
        'osu_user_tournament_id'    => $osuUserTournamentId,
        'osu_user_name'             => $osuUserName,
        'osu_user_role'             => $osuUserRole,
        'osu_user_flag'             => $osuUserFlag,
        'osu_user_image'            => $osuUserImage,
        'osu_user_url'              => $osuUserUrl,
        'osu_user_rank'             => $osuUserRank,
        'osu_user_time_zone'        => $osuUserTimeZone
    ];

    getUserData(data: $allOsuUserData);

    return $allOsuUserData;
}


function getOsuUserData(
    string $token
): array | bool {
    $httpAuthorisationType  = $token;
    $httpAcceptType         = 'application/json';
    $httpContentType        = 'application/json';
    $osuUserUrl             = 'https://osu.ppy.sh/api/v2/me/taiko';

    if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
        $httpHeaderRequest = [
            "Authorization: Bearer {$httpAuthorisationType}",
            "Accept: $httpAcceptType",
            "Content-Type: $httpContentType",
        ];

        # CURL session will be handled manually through curl_setopt()
        $osuUserCurlHandle = curl_init(url: null);

        curl_setopt(handle: $osuUserCurlHandle, option: CURLOPT_URL, value: $osuUserUrl);
        curl_setopt(handle: $osuUserCurlHandle, option: CURLOPT_HTTPHEADER, value: $httpHeaderRequest);
        curl_setopt(handle: $osuUserCurlHandle, option: CURLOPT_HEADER, value: 0);
        curl_setopt(handle: $osuUserCurlHandle, option: CURLOPT_RETURNTRANSFER, value: 1);

        $osuUserCurlResponse = curl_exec(handle: $osuUserCurlHandle);

        if (curl_errno(handle: $osuUserCurlHandle)) {
            error_log(curl_error(handle: $osuUserCurlHandle));
            curl_close(handle: $osuUserCurlHandle);
            return false; // An error occurred during the API call

        } else {
            $osuUserReadableData = json_decode(
                json: $osuUserCurlResponse,
                associative: true,
                depth: 512,
                flags: 0
            );

            curl_close(handle: $osuUserCurlHandle);
            return $osuUserReadableData;
        }
    } else {
        $httpHeaderRequest = array(
            "Authorization: Bearer {$httpAuthorisationType}",
            "Accept: $httpAcceptType",
            "Content-Type: $httpContentType",
        );

        # CURL session will be handled manually through curl_setopt()
        $osuUserCurlHandle = curl_init(url: null);

        curl_setopt(handle: $osuUserCurlHandle, option: CURLOPT_URL, value: $osuUserUrl);
        curl_setopt(handle: $osuUserCurlHandle, option: CURLOPT_HTTPHEADER, value: $httpHeaderRequest);
        curl_setopt(handle: $osuUserCurlHandle, option: CURLOPT_HEADER, value: 0);
        curl_setopt(handle: $osuUserCurlHandle, option: CURLOPT_RETURNTRANSFER, value: 1);

        $osuUserCurlResponse = curl_exec(handle: $osuUserCurlHandle);

        if (curl_errno(handle: $osuUserCurlHandle)) {
            error_log(curl_error(handle: $osuUserCurlHandle));
            curl_close(handle: $osuUserCurlHandle);
            return false; // An error occurred during the API call

        } else {
            $osuUserReadableData = json_decode(
                json: $osuUserCurlResponse,
                associative: true,
                depth: 512,
                flags: 0
            );

            curl_close(handle: $osuUserCurlHandle);
            return $osuUserReadableData;
        }
    }
}
