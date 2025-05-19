<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require __DIR__ . '/../Configurations/Database.php';
require __DIR__ . '/../Configurations/Environment.php';
require __DIR__ . '/../Models/Token.php';

function getUserData(string $access_token): array | bool
{
    // TODO: fetch from the database only if user data stored before. Otherwise, insert new user data in
    $osuUserUrl             = 'https://osu.ppy.sh/api/v2/me/taiko';
    $osuUserAccessToken     = $access_token;

    $httpAcceptType         = 'application/json';
    $httpContentType        = 'application/json';

    if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
        $httpHeaderRequest = [
            "Authorization: Bearer {$osuUserAccessToken}",
            "Accept: $httpAcceptType",
            "Content-Type: $httpContentType",
        ];
    } else {
        $httpHeaderRequest = array(
            "Authorization: Bearer {$osuUserAccessToken}",
            "Accept: $httpAcceptType",
            "Content-Type: $httpContentType",
        );
    }

    # CURL session will be handled manually through curl_setopt()
    $userDataCurlHandle = curl_init(url: null);

    curl_setopt(handle: $userDataCurlHandle, option: CURLOPT_URL, value: $osuUserUrl);
    curl_setopt(handle: $userDataCurlHandle, option: CURLOPT_HTTPHEADER, value: $httpHeaderRequest);
    curl_setopt(handle: $userDataCurlHandle, option: CURLOPT_HEADER, value: 0);
    curl_setopt(handle: $userDataCurlHandle, option: CURLOPT_RETURNTRANSFER, value: 1);

    $userDataCurlResponse = curl_exec(handle: $userDataCurlHandle);

    if (curl_errno(handle: $userDataCurlHandle)) {
        error_log(curl_error(handle: $userDataCurlHandle));
        curl_close(handle: $userDataCurlHandle);
        return false; // An error occurred during the API call

    } else {
        $userDataCurlDecodedResponse = json_decode(
            json: $userDataCurlResponse,
            associative: true,
            depth: 512,
            flags: 0
        );

        curl_close(handle: $userDataCurlHandle);

        $userId         = $userDataCurlDecodedResponse['id'];
        $userImage      = $userDataCurlDecodedResponse['avatar_url'];
        $userUrl        = "https://osu.ppy.sh/users/$userId";
        $userFlag       = $userDataCurlDecodedResponse['country_code'];
        $userName       = $userDataCurlDecodedResponse['username'];
        $userRank       = $userDataCurlDecodedResponse['statistics']['global_rank'];
        $userTimeZone   = getUserTimeZone()['location']['timezone'];
        //$userTimeZone   = 'a'; // Use this for testing as I don't have unlimited API call for the user timezone

        createUserData(
            id: $userId,
            image: $userImage,
            url: $userUrl,
            flag: $userFlag,
            name: $userName,
            rank: $userRank,
            time_zone: $userTimeZone,
            // Variable scoping in PHP is a bit weird somehow: https://www.php.net/manual/en/language.variables.scope.php
            database_handle: $GLOBALS['votDatabaseHandle']
        );

        return $userDataCurlDecodedResponse;
    }
}

function getUserTimeZone(): array | bool
{
    $osuUserIp              = getUserIp()['ip'];
    $osuUserTimeZoneApiKey  = getenv(name: 'IPIFY_API_KEY', local_only: true) ?: getenv(name: 'IPIFY_API_KEY');
    $osuUserTimeZoneUrl     = "https://geo.ipify.org/api/v2/country?apiKey=$osuUserTimeZoneApiKey&ipAddress=$osuUserIp";

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
};

function getUserIp(): array | bool
{
    $osuUserIpUrl       = 'https://api.ipify.org?format=json';

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

    curl_setopt(handle: $userIpCurlHandle, option: CURLOPT_URL, value: $osuUserIpUrl);
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
};
