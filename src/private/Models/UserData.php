<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require __DIR__ . '/../Configurations/Database.php';
require __DIR__ . '/../Configurations/TimeZone.php';

function getUserData(string $access_token): array | bool
{
    $osuUserUrl             = 'https://osu.ppy.sh/api/v2/me/taiko';

    $httpAuthorisationType  = $access_token;
    $httpAcceptType         = 'application/json';
    $httpContentType        = 'application/json';

    if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
        $httpHeaderRequest = [
            "Authorization: Bearer {$httpAuthorisationType}",
            "Accept: $httpAcceptType",
            "Content-Type: $httpContentType",
        ];
    } else {
        $httpHeaderRequest = array(
            "Authorization: Bearer {$httpAuthorisationType}",
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

        // Variable scoping in PHP is a bit weird somehow: https://www.php.net/manual/en/language.variables.scope.php
        if (!checkUserData(id: $userId, database_handle: $GLOBALS['votDatabaseHandle'])) {
            createUserData(
                id: $userId,
                image: $userImage,
                url: $userUrl,
                flag: $userFlag,
                name: $userName,
                rank: $userRank,
                time_zone: $userTimeZone,
                database_handle: $GLOBALS['votDatabaseHandle']
            );
            // TODO: Need to create user role as well (Maybe within this function or somewhere else).
        } else {
            // TODO: UPDATE query here (change the 'view' table only, not the actual table if all data stay the same).
            error_log(message: 'User data exist, simply ignoring it...', message_type: 0);
        };
    }
    return $userDataCurlDecodedResponse;
}

function checkUserData(int $id, object $database_handle): int | bool
{
    $checkQuery = "
        SELECT COUNT(userId)
        FROM VotUser
        WHERE userId = :userId;
    ";

    $checkStatement = $database_handle->prepare($checkQuery);

    // I can't type hint this due to `var` parameter has an address (&) assigned to it, which syntactically
    // not valid for a PHP code. Therefore, to let this method works, I have to make this one as an
    // exceptional for letting my code to be strictly-typed.
    $checkStatement->bindParam(':userId', $id, PDO::PARAM_INT);

    $successCheckLogMessage    = sprintf("Check successfully for user ID: %d", $id);
    $unsuccessCheckLogMessage  = sprintf("Check unsuccessfully for user ID: %d", $id);

    if ($checkStatement->execute()) {
        error_log(message: $successCheckLogMessage, message_type: 0);
        $checkAllUserData = $checkStatement->fetchColumn(
            column: 0
        );
        return $checkAllUserData;
    } else {
        error_log(message: $unsuccessCheckLogMessage, message_type: 0);
        return false;
    }
}

// Create
function createUserData(int $id, string $image, string $url, string $flag, string $name, int $rank, string $time_zone, object $database_handle): string | bool
{
    $insertQuery = "
        INSERT INTO VotUser (userId, userImage, userUrl, userFlag, userName, userRank, userTimeZone)
        VALUES (:userId, :userImage, :userUrl, :userFlag, :userName, :userRank, :userTimeZone);
    ";

    $insertStatement = $database_handle->prepare($insertQuery);

    // I can't type hint this due to `var` parameter has an address (&) assigned to it, which syntactically
    // not valid for a PHP code. Therefore, to let this method works, I have to make this one as an
    // exceptional for letting my code to be strictly-typed.
    $insertStatement->bindParam(':userId',           $id,           PDO::PARAM_INT);
    $insertStatement->bindParam(':userImage',        $image,        PDO::PARAM_STR);
    $insertStatement->bindParam(':userUrl',          $url,          PDO::PARAM_STR);
    $insertStatement->bindParam(':userFlag',         $flag,         PDO::PARAM_STR);
    $insertStatement->bindParam(':userName',         $name,         PDO::PARAM_STR);
    $insertStatement->bindParam(':userRank',         $rank,         PDO::PARAM_INT);
    $insertStatement->bindParam(':userTimeZone',     $time_zone,    PDO::PARAM_STR);

    $successInsertLogMessage    = sprintf("Insert successfully for user ID: %d", $id);
    $unsuccessInsertLogMessage  = sprintf("Insert unsuccessfully for user ID: %d", $id);

    if ($insertStatement->execute()) {
        error_log(message: $successInsertLogMessage, message_type: 0);
        $totalInsertLogMessage = sprintf(
            "Total user data successfully inserted: %d",
            $insertStatement->rowCount()
        );
        return $totalInsertLogMessage;
    } else {
        error_log(message: $unsuccessInsertLogMessage, message_type: 0);
        return false;
    }
}

// Read
function readUserData(int $id, object $database_handle): array | bool
{
    $readQuery = "
        SELECT r.roleName, u.userImage, u.userUrl, u.userFlag, u.userName, u.userRank, u.userTimeZone
        FROM VotUser u
        LEFT JOIN VotUserRole r ON u.userId = r.userId
        WHERE r.userId = :userId
        ORDER BY r.roleName ASC;
    ";

    $readStatement = $database_handle->prepare($readQuery);

    // I can't type hint this due to `var` parameter has an address (&) assigned to it, which syntactically
    // not valid for a PHP code. Therefore, to let this method works, I have to make this one as an
    // exceptional for letting my code to be strictly-typed.
    $readStatement->bindParam(':userId', $id, PDO::PARAM_INT);

    $successReadLogMessage    = sprintf("Read successfully for user ID: %d", $id);
    $unsuccessReadLogMessage  = sprintf("Read unsuccessfully for user ID: %d", $id);

    if ($readStatement->execute()) {
        error_log(message: $successReadLogMessage, message_type: 0);
        $readAllUserData = $readStatement->fetch(
            mode: PDO::FETCH_ASSOC,
            cursorOrientation: PDO::FETCH_ORI_NEXT,
            cursorOffset: 0
        );
        return $readAllUserData;
    } else {
        error_log(message: $unsuccessReadLogMessage, message_type: 0);
        return false;
    }
}

// Update
// Delete
