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
    $applicationSecret
        =  getenv(name: 'APPLICATION_SECRET', local_only: true)
        ?: getenv(name: 'APPLICATION_SECRET');
    $applicationGrantType
        =  getenv(name: 'APPLICATION_GRANT_TYPE', local_only: true)
        ?: getenv(name: 'APPLICATION_GRANT_TYPE');
    $applicationRedirectUrl
        =  getenv(name: 'APPLICATION_REDIRECT_URL', local_only: true)
        ?: getenv(name: 'APPLICATION_REDIRECT_URL');

    if (
        // *** [CANCEL] BUTTON HANDLING ***
        isset($_GET['error']) &&
        $_GET['error'] === 'access_denied'
    ) {
        exit(header(
            header: 'Location: /home',
            replace: true,
            response_code: 302
        ));
    } elseif (
        // *** [AUTHORISE] BUTTON HANDLING ***
        isset($_GET['code']) &&
        preg_match(
            pattern: '/^[a-z0-9]+$/i',
            subject: $_GET['code']
        )
    ) {
        $applicationAuthorisationCode = $_GET['code'];

        $userAccessTokenUrl     = 'https://osu.ppy.sh/oauth/token';
        $httpAcceptType         = 'application/json';
        $httpContentType        = 'application/x-www-form-urlencoded';

        if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
            $payloadData = [
                'client_id'         => $applicationId,
                'client_secret'     => $applicationSecret,
                'code'              => $applicationAuthorisationCode,
                'grant_type'        => $applicationGrantType,
                'redirect_uri'      => $applicationRedirectUrl
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

            curl_setopt(
                handle: $userAccessTokenCurlHandle,
                option: CURLOPT_URL,
                value: $userAccessTokenUrl
            );
            curl_setopt(
                handle: $userAccessTokenCurlHandle,
                option: CURLOPT_POST,
                value: 1
            );
            curl_setopt(
                handle: $userAccessTokenCurlHandle,
                option: CURLOPT_HTTPHEADER,
                value: $httpHeaderRequest
            );
            curl_setopt(
                handle: $userAccessTokenCurlHandle,
                option: CURLOPT_POSTFIELDS,
                value: $urlEncodedPayloadData
            );
            curl_setopt(
                handle: $userAccessTokenCurlHandle,
                option: CURLOPT_HEADER,
                value: 0
            );
            curl_setopt(
                handle: $userAccessTokenCurlHandle,
                option: CURLOPT_RETURNTRANSFER,
                value: 1
            );

            $userAccessTokenCurlResponse = curl_exec(handle: $userAccessTokenCurlHandle);

            if (curl_errno(handle: $userAccessTokenCurlHandle)) {
                // An error occurred during the API call
                error_log(curl_error(handle: $userAccessTokenCurlHandle));
                curl_close(handle: $userAccessTokenCurlHandle);
            } else {
                // API call succeeded and user token is retrieved
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
                } else {
                    // Ensure that user won't have to re-authorise multiple times if
                    // idling for a long time
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
                }
            }
        } else {
            $payloadData = array(
                'client_id'         => $applicationId,
                'client_secret'     => $applicationSecret,
                'code'              => $applicationAuthorisationCode,
                'grant_type'        => $applicationGrantType,
                'redirect_uri'      => $applicationRedirectUrl
            );
            $httpHeaderRequest = array(
                "Accept: $httpAcceptType",
                "Content-Type: $httpContentType",
            );

            $urlEncodedPayloadData = http_build_query(
                data: $payloadData,
                numeric_prefix: '',
                arg_separator: null,
                encoding_type: PHP_QUERY_RFC1738
            );

            # CURL session will be handled manually through curl_setopt()
            $userAccessTokenCurlHandle = curl_init(url: null);

            curl_setopt(
                handle: $userAccessTokenCurlHandle,
                option: CURLOPT_URL,
                value: $userAccessTokenUrl
            );
            curl_setopt(
                handle: $userAccessTokenCurlHandle,
                option: CURLOPT_POST,
                value: 1
            );
            curl_setopt(
                handle: $userAccessTokenCurlHandle,
                option: CURLOPT_HTTPHEADER,
                value: $httpHeaderRequest
            );
            curl_setopt(
                handle: $userAccessTokenCurlHandle,
                option: CURLOPT_POSTFIELDS,
                value: $urlEncodedPayloadData
            );
            curl_setopt(
                handle: $userAccessTokenCurlHandle,
                option: CURLOPT_HEADER,
                value: 0
            );
            curl_setopt(
                handle: $userAccessTokenCurlHandle,
                option: CURLOPT_RETURNTRANSFER,
                value: 1
            );

            $userAccessTokenCurlResponse = curl_exec(handle: $userAccessTokenCurlHandle);

            if (curl_errno(handle: $userAccessTokenCurlHandle)) {
                // An error occurred during the API call
                error_log(curl_error(handle: $userAccessTokenCurlHandle));
                curl_close(handle: $userAccessTokenCurlHandle);
            } else {
                // API call succeeded and user token is retrieved
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
                } else {
                    // Ensure that user won't have to re-authorise multiple times if
                    // idling for a long time
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
                        header: 'Location: /',
                        replace: true,
                        response_code: 302
                    ));
                }
            }
        }
    } else {
        // *** BAD INJECTIONS HANDLING ***
        http_response_code(401);
    }
}
