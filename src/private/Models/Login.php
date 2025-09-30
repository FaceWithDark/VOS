<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require __DIR__ . '/../Configurations/Database.php';


function getUserData(
    string $token
): array | bool {
    $httpAuthorisationType  = $token;
    $httpAcceptType         = 'application/json';
    $httpContentType        = 'application/json';
    $userDataUrl            = 'https://osu.ppy.sh/api/v2/me/taiko';

    if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
        $httpHeaderRequest = [
            "Authorization: Bearer {$httpAuthorisationType}",
            "Accept: $httpAcceptType",
            "Content-Type: $httpContentType",
        ];

        # CURL session will be handled manually through curl_setopt()
        $userCurlHandle = curl_init(url: null);

        curl_setopt(
            handle: $userCurlHandle,
            option: CURLOPT_URL,
            value: $userDataUrl
        );
        curl_setopt(
            handle: $userCurlHandle,
            option: CURLOPT_HTTPHEADER,
            value: $httpHeaderRequest
        );
        curl_setopt(
            handle: $userCurlHandle,
            option: CURLOPT_HEADER,
            value: 0
        );
        curl_setopt(
            handle: $userCurlHandle,
            option: CURLOPT_RETURNTRANSFER,
            value: 1
        );

        $userCurlResponse = curl_exec(handle: $userCurlHandle);

        if (curl_errno(handle: $userCurlHandle)) {
            // An error occurred during the API call
            error_log(curl_error(handle: $userCurlHandle));
            curl_close(handle: $userCurlHandle);
            return false;
        } else {
            // API call succeeded and user token is retrieved
            $userUsableData = json_decode(
                json: $userCurlResponse,
                associative: true,
                depth: 512,
                flags: 0
            );
            curl_close(handle: $userCurlHandle);

            return $userUsableData;
        }
    } else {
        $httpHeaderRequest = array(
            "Authorization: Bearer {$httpAuthorisationType}",
            "Accept: $httpAcceptType",
            "Content-Type: $httpContentType",
        );

        # CURL session will be handled manually through curl_setopt()
        $userCurlHandle = curl_init(url: null);

        curl_setopt(
            handle: $userCurlHandle,
            option: CURLOPT_URL,
            value: $userDataUrl
        );
        curl_setopt(
            handle: $userCurlHandle,
            option: CURLOPT_HTTPHEADER,
            value: $httpHeaderRequest
        );
        curl_setopt(
            handle: $userCurlHandle,
            option: CURLOPT_HEADER,
            value: 0
        );
        curl_setopt(
            handle: $userCurlHandle,
            option: CURLOPT_RETURNTRANSFER,
            value: 1
        );

        $userCurlResponse = curl_exec(handle: $userCurlHandle);

        if (curl_errno(handle: $userCurlHandle)) {
            // An error occurred during the API call
            error_log(curl_error(handle: $userCurlHandle));
            curl_close(handle: $userCurlHandle);
            return false;
        } else {
            // API call succeeded and user token is retrieved
            $userUsableData = json_decode(
                json: $userCurlResponse,
                associative: true,
                depth: 512,
                flags: 0
            );
            curl_close(handle: $userCurlHandle);

            return $userUsableData;
        }
    }
}


function checkUserData(
    int $id
): bool {
    // IDE cannot recognise PDO object at runtime somehow
    global $votDatabaseHandle;

    $successCheckLogMessage = sprintf(
        "User data check existed for user ID [%d]",
        $id
    );
    $unsuccessCheckLogMessage = sprintf(
        "User data check not existed for user ID [%d]",
        $id
    );

    $checkQuery = "
        SELECT
            userId
        FROM
            VotUser
        WHERE
            userId = :userId;
    ";

    $checkStatement = $votDatabaseHandle->prepare($checkQuery);
    $checkStatement->bindParam(':userId', $id, PDO::PARAM_INT);

    if ($checkStatement->execute()) {
        // Checking trick: https://www.php.net/manual/en/pdostatement.fetchcolumn.php#100522
        $existUserId = $checkStatement->fetchColumn(column: 0);

        if (!$existUserId) {
            error_log(
                message: $unsuccessCheckLogMessage,
                message_type: 0
            );
            return false;
        } else {
            error_log(
                message: $successCheckLogMessage,
                message_type: 0
            );
            return true;
        }
    } else {
        error_log(
            message: $unsuccessCheckLogMessage,
            message_type: 0
        );
        return false;
    }
}


// Create
function createUserData(
    int $user_id,
    string $name,
    string $flag,
    string $image,
    string $url,
    int $rank,
    string $time_zone,
    string $role_id = 'USR',        // All user have user-level access by default
    string $tournament_id = 'NONE'  // All user belong to none tournament by default
): bool {
    // IDE cannot recognise PDO object at runtime somehow
    global $votDatabaseHandle;

    $successInsertLogMessage = sprintf(
        "Insert successfully for user ID [%d] with [%s] role.",
        $user_id,
        $role_id
    );
    $unsuccessInsertLogMessage = sprintf(
        "Insert unsuccessfully for user ID [%d] with [%s] role.",
        $user_id,
        $role_id
    );

    // User data (except their role) will be stored to a simple table
    $userInsertQuery = "
        INSERT INTO VotUser
            (
                userId,
                tournamentId,
                userName,
                userFlag,
                userImage,
                userUrl,
                userRank,
                userTimeZone
            )
        VALUES
            (
                :userId,
                :tournamentId,
                :userName,
                :userFlag,
                :userImage,
                :userUrl,
                :userRank,
                :userTimeZone
            );
    ";

    $userInsertStatement = $votDatabaseHandle->prepare($userInsertQuery);
    $userInsertStatement->bindParam(':userId',          $user_id,           PDO::PARAM_INT);
    $userInsertStatement->bindParam(':tournamentId',    $tournament_id,     PDO::PARAM_STR);
    $userInsertStatement->bindParam(':userName',        $name,              PDO::PARAM_STR);
    $userInsertStatement->bindParam(':userFlag',        $flag,              PDO::PARAM_STR);
    $userInsertStatement->bindParam(':userImage',       $image,             PDO::PARAM_STR);
    $userInsertStatement->bindParam(':userUrl',         $url,               PDO::PARAM_STR);
    $userInsertStatement->bindParam(':userRank',        $rank,              PDO::PARAM_INT);
    $userInsertStatement->bindParam(':userTimeZone',    $time_zone,         PDO::PARAM_STR);

    // Then, we store some of it (include user role) to a complex table for look-up later
    $generalUserInsertQuery = "
        INSERT INTO VotGeneralRole
            (
                userId,
                roleId
            )
        VALUES
            (
                :userId,
                :roleId
            );
    ";

    $generalUserInsertStatement = $votDatabaseHandle->prepare($generalUserInsertQuery);
    $generalUserInsertStatement->bindParam(':userId',   $user_id,   PDO::PARAM_INT);
    $generalUserInsertStatement->bindParam(':roleId',   $role_id,   PDO::PARAM_STR);

    // Start a transaction (in SQL term means running two or more queries at the
    // same time with conditions)
    $votDatabaseHandle->beginTransaction();

    try {
        // Make sure that the 1st query execute successfully first
        if ($userInsertStatement->execute()) {
            // Then successfully execute 2nd query
            if ($generalUserInsertStatement->execute()) {
                // Commit both queries execution, which means apply it directly
                // and no need to buffer it anymore
                $votDatabaseHandle->commit();
                error_log(
                    message: $successInsertLogMessage,
                    message_type: 0
                );
                return true;
            } else {
                // Rollback to the 1st query, which means delete the 2nd query
                // out from the buffer and unbuffered the 1st query only
                $votDatabaseHandle->rollBack();
                error_log(
                    message: $unsuccessInsertLogMessage,
                    message_type: 0
                );
                return false;
            }
        } else {
            // Rollback to none query, which means delete both the queries out
            // from the buffer and execute nothing
            $votDatabaseHandle->rollBack();
            error_log(
                message: $unsuccessInsertLogMessage,
                message_type: 0
            );
            return false;
        }
    } catch (PDOException $exception) {
        // Kills the page and show the error for debugging (most likely syntax error)
        die("Insert error!! Reason: " . $exception->getMessage());
    }
}


// Read
function readUserData(
    int $id
): array {
    // IDE cannot recognise PDO object at runtime somehow
    global $votDatabaseHandle;

    switch ($id) {
        /* Website owner is also an osu! player */
        case 19817503:
            $adminSuccessReadLogMessage = sprintf(
                "Read successfully for user ID [%d] with [ADMIN] role.",
                $id
            );
            $adminUnsuccessReadLogMessage = sprintf(
                "Read unsuccessfully for user ID [%d] with [ADMIN] role.",
                $id
            );

            // Only read user data that have admin role
            $adminReadQuery = "
                SELECT
                    vu.userName,
                    vr.roleName,
                    vu.userFlag,
                    vu.userImage,
                    vu.userUrl,
                    vu.userRank,
                    vu.userTimeZone
                FROM
                    VotGeneralRole vgr
                JOIN
                    VotUser vu ON vgr.userId = vu.userId
                JOIN
                    VotRole vr ON vgr.roleId = vr.roleId
                WHERE
                    vgr.userId = :userId AND vr.roleName = 'Admin'
                ORDER BY
                    vgr.roleId ASC;
            ";

            $adminReadStatement = $votDatabaseHandle->prepare($adminReadQuery);
            $adminReadStatement->bindParam(':userId', $id, PDO::PARAM_INT);

            if ($adminReadStatement->execute()) {
                error_log(
                    message: $adminSuccessReadLogMessage,
                    message_type: 0
                );
                $adminViewData = $adminReadStatement->fetch(
                    mode: PDO::FETCH_ASSOC,
                    cursorOrientation: PDO::FETCH_ORI_NEXT,
                    cursorOffset: 0
                );
                return $adminViewData;
            } else {
                error_log(
                    message: $adminUnsuccessReadLogMessage,
                    message_type: 0
                );
                return [];
            }
            break;

        /* Host for each tournament is also an osu! player */
        case 9623142:
        case 16039831:
            $hostSuccessReadLogMessage = sprintf(
                "Read successfully for user ID [%d] with [HOST] role.",
                $id
            );
            $hostUnsuccessReadLogMessage = sprintf(
                "Read unsuccessfully for user ID [%d] with [HOST] role.",
                $id
            );

            // Only read user data that have host role
            $hostReadQuery = "
                SELECT
                    vu.userName,
                    vr.roleName,
                    vu.userFlag,
                    vu.userImage,
                    vu.userUrl,
                    vu.userRank,
                    vu.userTimeZone
                FROM
                    VotTournamentRole vgr
                JOIN
                    VotUser vu ON vgr.userId = vu.userId
                JOIN
                    VotRole vr ON vgr.roleId = vr.roleId
                WHERE
                    vgr.userId = :userId AND vr.roleName = 'Host'
                ORDER BY
                    vgr.roleId ASC;
            ";

            $hostReadStatement = $votDatabaseHandle->prepare($hostReadQuery);
            $hostReadStatement->bindParam(':userId', $id, PDO::PARAM_INT);

            if ($hostReadStatement->execute()) {
                error_log(
                    message: $hostSuccessReadLogMessage,
                    message_type: 0
                );
                $hostViewData = $hostReadStatement->fetch(
                    mode: PDO::FETCH_ASSOC,
                    cursorOrientation: PDO::FETCH_ORI_NEXT,
                    cursorOffset: 0
                );
                return $hostViewData;
            } else {
                error_log(
                    message: $hostUnsuccessReadLogMessage,
                    message_type: 0
                );
                return [];
            }
            break;

        default:
            $userSuccessReadLogMessage = sprintf(
                "Read successfully for user ID [%d] with [USER] role.",
                $id
            );
            $userUnsuccessReadLogMessage = sprintf(
                "Read unsuccessfully for user ID [%d] with [USER] role.",
                $id
            );

            // Only read user data that have user role
            $userReadQuery = "
                SELECT
                    vu.userName,
                    vr.roleName,
                    vu.userFlag,
                    vu.userImage,
                    vu.userUrl,
                    vu.userRank,
                    vu.userTimeZone
                FROM
                    VotGeneralRole vgr
                JOIN
                    VotUser vu ON vgr.userId = vu.userId
                JOIN
                    VotRole vr ON vgr.roleId = vr.roleId
                WHERE
                    vgr.userId = :userId AND vr.roleName = 'User'
                ORDER BY
                    vgr.roleId ASC;
            ";

            $userReadStatement = $votDatabaseHandle->prepare($userReadQuery);
            $userReadStatement->bindParam(':userId', $id, PDO::PARAM_INT);

            if ($userReadStatement->execute()) {
                error_log(
                    message: $userSuccessReadLogMessage,
                    message_type: 0
                );
                $userViewData = $userReadStatement->fetch(
                    mode: PDO::FETCH_ASSOC,
                    cursorOrientation: PDO::FETCH_ORI_NEXT,
                    cursorOffset: 0
                );
                return $userViewData;
            } else {
                error_log(
                    message: $userUnsuccessReadLogMessage,
                    message_type: 0
                );
                return [];
            }
            break;
    }
}

// Update
// Delete
