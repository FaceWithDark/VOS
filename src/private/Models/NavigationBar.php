<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require __DIR__ . '/../Controllers/Controller.php';

function getUserAuthorisationUrl(): string
{
    $applicationId              = getenv(name: 'APPLICATION_ID', local_only: true)              ?: getenv(name: 'APPLICATION_ID');
    $applicationRedirectUrl     = getenv(name: 'APPLICATION_REDIRECT_URL', local_only: true)    ?: getenv(name: 'APPLICATION_REDIRECT_URL');
    $applicationResponseType    = getenv(name: 'APPLICATION_RESPONSE_TYPE', local_only: true)   ?: getenv(name: 'APPLICATION_RESPONSE_TYPE');
    $applicationScope           = getenv(name: 'APPLICATION_SCOPE', local_only: true)           ?: getenv(name: 'APPLICATION_SCOPE');
    $applicationState           = getenv(name: 'APPLICATION_STATE', local_only: true)           ?: getenv(name: 'APPLICATION_STATE');

    $applicationAuthoriseUrl = "https://osu.ppy.sh/oauth/authorize?client_id=$applicationId&redirect_uri=$applicationRedirectUrl&response_type=$applicationResponseType&scope=$applicationScope&state=$applicationState";

    return $applicationAuthoriseUrl;
};

function getUserAccessToken(string $temporary_code): array | bool
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
            return $curlDecodedResponse;
        }
    }
}

/*
// Fetch user data from the Osu! API
function getUserData(string $token, string $url): object | bool
{
    $osuUserUrl                 = "https://osu.ppy.sh/api/v2/me/taiko";
    $osuUserAccessToken         = $_COOKIE['vot_access_token'];

    // Initialize a cURL session
    $curlHandle = curl_init();

    // Set the cURL options
    curl_setopt($curlHandle, CURLOPT_URL, $url);
    curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curlHandle, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer {$token}",
        'Accept: application/json',
    ]);

    // Execute the cURL session and get the response
    $curlResponse = curl_exec($curlHandle);

    // Check for cURL errors
    if (curl_errno($curlHandle)) {
        error_log(curl_error($curlHandle)); // Log the cURL error message
        curl_close($curlHandle);            // Close the cURL session
        return false;               // An error occurred during the API call
    } else {
        // Get the HTTP status code
        $curlHttpCode = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);

        // Close the cURL session
        curl_close($curlHandle);

        if ($curlHttpCode !== 200) {
            // API call did not return a 200 status
            return false;
        } else {
            $curlDecodedResponse = json_decode($curlResponse);
            return $curlDecodedResponse;
        }
    }
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

// C.R.U.D operations
// Create
function createUserData(
    int $id,
    string $image,
    string $url,
    string $flag,
    string $name,
    int $rank,
    float $time_zone,
    object $database_connection
): string | bool {
    $insertQuery = "
        INSERT INTO VotUser (userId, userImage, userUrl, userFlag, userName, userRank, userTimeZone)
        VALUES (:userId, :userImage, :userUrl, :userFlag, :userName, :userRank, :userTimeZone);
    ";

    $insertStatement = $database_connection->prepare($insertQuery);
    $insertStatement->bindParam(param: ':userId',           var: $id,           type: PDO::PARAM_INT);
    $insertStatement->bindParam(param: ':userImage',        var: $image,        type: PDO::PARAM_STR);
    $insertStatement->bindParam(param: ':userUrl',          var: $url,          type: PDO::PARAM_STR);
    $insertStatement->bindParam(param: ':userFlag',         var: $flag,         type: PDO::PARAM_STR);
    $insertStatement->bindParam(param: ':userName',         var: $name,         type: PDO::PARAM_STR);
    $insertStatement->bindParam(param: ':userRank',         var: $rank,         type: PDO::PARAM_INT);
    # Per suggestion: https://www.php.net/manual/en/pdo.constants.php#129168
    $insertStatement->bindValue(param: ':userTimeZone',     var: $time_zone,    type: PDO::PARAM_STR);

    $successInsertLogMessage    = sprintf("Insert successfully for user ID: %d", $id);
    $totalInsertLogMessage      = sprintf("Total user data successfully inserted: %d", $insertStatement->rowCount());
    $unsuccessInsertLogMessage  = sprintf("Insert unsuccessfully for user ID: %d", $id);

    if ($insertStatement->execute()) {
        error_log(message: $successInsertLogMessage, message_type: 0);
        return $totalInsertLogMessage;
    } else {
        error_log(message: $unsuccessInsertLogMessage, message_type: 0);
        return false;
    }
};

// Read
function readUserData(int $id, object $database_connection): string | bool
{
    $readQuery = "
        SELECT r.roleId, u.userImage, u.userUrl, u.userFlag, u.userName, u.userRank, u.userTimeZone
        FROM VotUserRole
        RIGHT JOIN VotUser ON r.roleId = u.userId
        ORDER BY r.roleId;
    ";

    $readStatement = $database_connection->prepare($readQuery);

    $successReadLogMessage    = sprintf("Read successfully for user ID: %d", $id);
    $totalReadLogMessage      = sprintf("Total user data successfully read: %d", $readStatement->rowCount());
    $unsuccessReadLogMessage  = sprintf("Read unsuccessfully for user ID: %d", $id);

    if ($readStatement->execute()) {
        error_log(message: $successReadLogMessage, message_type: 0);
        return $totalReadLogMessage;
    } else {
        error_log(message: $unsuccessReadLogMessage, message_type: 0);
        return false;
    }
};

// Update
// Delete
