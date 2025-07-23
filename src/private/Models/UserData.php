<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require __DIR__ . '/../Configurations/Database.php';

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
        $userName       = $userDataCurlDecodedResponse['username'];
        $userRole       = 'User'; // TODO: I'll do it properly. Leave TODO here for now
        $userFlag       = $userDataCurlDecodedResponse['country_code'];
        $userImage      = $userDataCurlDecodedResponse['avatar_url'];
        $userUrl        = "https://osu.ppy.sh/users/$userId";
        $userRank       = $userDataCurlDecodedResponse['statistics']['global_rank'];
        $userTimeZone   = getUserTimeZone()['baseOffset'];
        $userDatabase   = $GLOBALS['votDatabaseHandle'];

        // Variable scoping in PHP is a bit weird somehow: https://www.php.net/manual/en/language.variables.scope.php
        if (!checkUserData(id: $userId, database_handle: $userDatabase)) {
            createUserData(
                id: $userId,
                name: $userName,
                role: $userRole,
                flag: $userFlag,
                image: $userImage,
                url: $userUrl,
                rank: $userRank,
                time_zone: $userTimeZone,
                database_handle: $userDatabase
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
    $checkStatement->bindParam(':userId',   $id,    PDO::PARAM_INT);

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
function createUserData(int $id, string $name, string $role, string $flag, string $image, string $url, int $rank, string $time_zone, object $database_handle): string | bool
{
    $insertQuery = "
        INSERT INTO VotUser (userId, userName, userRole, userFlag, userImage, userUrl, userRank, userTimeZone)
        VALUES (:userId, :userName, :userRole, :userFlag, :userImage, :userUrl, :userRank, :userTimeZone);
    ";

    $insertStatement = $database_handle->prepare($insertQuery);

    // I can't type hint this due to `var` parameter has an address (&) assigned to it, which syntactically
    // not valid for a PHP code. Therefore, to let this method works, I have to make this one as an
    // exceptional for letting my code to be strictly-typed.
    $insertStatement->bindParam(':userId',          $id,            PDO::PARAM_INT);
    $insertStatement->bindParam(':userName',        $name,          PDO::PARAM_STR);
    $insertStatement->bindParam(':userRole',        $role,          PDO::PARAM_STR);
    $insertStatement->bindParam(':userFlag',        $flag,          PDO::PARAM_STR);
    $insertStatement->bindParam(':userImage',       $image,         PDO::PARAM_STR);
    $insertStatement->bindParam(':userUrl',         $url,           PDO::PARAM_STR);
    $insertStatement->bindParam(':userRank',        $rank,          PDO::PARAM_INT);
    $insertStatement->bindParam(':userTimeZone',    $time_zone,     PDO::PARAM_STR);

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
        SELECT userName, userRole, userFlag, userImage, userUrl, userRank, userTimeZone
        FROM VotUser
        WHERE userId = :userId
        ORDER BY userRole ASC;
    ";

    $readStatement = $database_handle->prepare($readQuery);

    // I can't type hint this due to `var` parameter has an address (&) assigned to it, which syntactically
    // not valid for a PHP code. Therefore, to let this method works, I have to make this one as an
    // exceptional for letting my code to be strictly-typed.
    $readStatement->bindParam(':userId',    $id,    PDO::PARAM_INT);

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
