<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require __DIR__ . '/InternetProtocol.php';
require __DIR__ . '/Environment.php';

function getUserTimeZone(): array | bool
{
    $osuUserTimeZone        = getUserIp()['timezone'];
    $osuUserTimeZoneUrl     = "https://api.opentimezone.com/timezone/$osuUserTimeZone";
    $httpAcceptType         = 'application/json';
    $httpContentType        = 'application/json';

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
    $userTimeZoneCurlHandle = curl_init(url: null);

    curl_setopt(handle: $userTimeZoneCurlHandle, option: CURLOPT_URL, value: $osuUserTimeZoneUrl);
    curl_setopt(handle: $userTimeZoneCurlHandle, option: CURLOPT_HTTPHEADER, value: $httpHeaderRequest);
    curl_setopt(handle: $userTimeZoneCurlHandle, option: CURLOPT_HEADER, value: 0);
    curl_setopt(handle: $userTimeZoneCurlHandle, option: CURLOPT_RETURNTRANSFER, value: 1);

    $userTimeZoneCurlResponse = curl_exec(handle: $userTimeZoneCurlHandle);

    if (curl_errno(handle: $userTimeZoneCurlHandle)) {
        error_log(curl_error(handle: $userTimeZoneCurlHandle));
        curl_close(handle: $userTimeZoneCurlHandle);
        return false; // An error occurred during the API call

    } else {
        $userTimeZoneCurlDecodedResponse = json_decode(
            json: $userTimeZoneCurlResponse,
            associative: true,
            depth: 512,
            flags: 0
        );

        curl_close(handle: $userTimeZoneCurlHandle);
        return $userTimeZoneCurlDecodedResponse;
    }
}
