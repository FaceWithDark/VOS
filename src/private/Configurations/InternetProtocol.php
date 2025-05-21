<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

function getUserIp(): array | bool
{
    $userIpUrl       = 'https://api.ipify.org?format=json';

    $httpAcceptType     = 'application/json';
    $httpContentType    = 'application/json';

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
    $userIpCurlHandle = curl_init(url: null);

    curl_setopt(handle: $userIpCurlHandle, option: CURLOPT_URL, value: $userIpUrl);
    curl_setopt(handle: $userIpCurlHandle, option: CURLOPT_HTTPHEADER, value: $httpHeaderRequest);
    curl_setopt(handle: $userIpCurlHandle, option: CURLOPT_HEADER, value: 0);
    curl_setopt(handle: $userIpCurlHandle, option: CURLOPT_RETURNTRANSFER, value: 1);

    $userIpCurlResponse = curl_exec(handle: $userIpCurlHandle);

    if (curl_errno(handle: $userIpCurlHandle)) {
        error_log(curl_error(handle: $userIpCurlHandle));
        curl_close(handle: $userIpCurlHandle);
        return false; // An error occurred during the API call

    } else {
        $userIpCurlDecodedResponse = json_decode(
            json: $userIpCurlResponse,
            associative: true,
            depth: 512,
            flags: 0
        );

        curl_close(handle: $userIpCurlHandle);
        return $userIpCurlDecodedResponse;
    }
}
