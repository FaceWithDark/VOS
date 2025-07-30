<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require __DIR__ . '/../Configurations/Database.php';


function getUserData(array $data): null
{
    foreach ($data as $user_data) {
        $userId         = $user_data['osu_user_id'];
        $userName       = $user_data['osu_user_name'];
        $userRole       = $user_data['osu_user_role'];
        $userFlag       = $user_data['osu_user_flag'];
        $userImage      = $user_data['osu_user_image'];
        $userUrl        = $user_data['osu_user_url'];
        $userRank       = $user_data['osu_user_rank'];
        $userTimeZone   = $user_data['osu_user_time_zone'];
        $userDatabase   = $GLOBALS['votDatabaseHandle'];

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
        } else {
            // TODO: UPDATE query here (change the 'view' table only, not the actual table if all data stay the same).
            error_log(message: 'User data exist, simply ignoring it...', message_type: 0);
        };
    }
    return null;
}


function checkUserData(
    int $id,
    object $database_handle
): int | bool {
    $checkQuery = "
        SELECT
            COUNT(userId)
        FROM
            VotUser
        WHERE
            userId = :userId;
    ";

    $checkStatement = $database_handle->prepare($checkQuery);
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
function createUserData(
    int $id,
    string $name,
    string $role,
    string $flag,
    string $image,
    string $url,
    int $rank,
    string $time_zone,
    object $database_handle,
    // Default params need to be be declared after all optional params.
    // Reference: https://www.php.net/manual/en/functions.arguments.php#functions.arguments.default
    string $tournament_id = 'NONE' // Everyone belong to none tournament by default
): string | bool {
    $insertQuery = "
        INSERT INTO
            VotUser (
                userId,
                tournamentId,
                userName,
                userRole,
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
                :userRole,
                :userFlag,
                :userImage,
                :userUrl,
                :userRank,
                :userTimeZone
            );
    ";

    $insertStatement = $database_handle->prepare($insertQuery);
    $insertStatement->bindParam(':userId',          $id,                PDO::PARAM_INT);
    $insertStatement->bindParam(':tournamentId',    $tournament_id,     PDO::PARAM_STR);
    $insertStatement->bindParam(':userName',        $name,              PDO::PARAM_STR);
    $insertStatement->bindParam(':userRole',        $role,              PDO::PARAM_STR);
    $insertStatement->bindParam(':userFlag',        $flag,              PDO::PARAM_STR);
    $insertStatement->bindParam(':userImage',       $image,             PDO::PARAM_STR);
    $insertStatement->bindParam(':userUrl',         $url,               PDO::PARAM_STR);
    $insertStatement->bindParam(':userRank',        $rank,              PDO::PARAM_INT);
    $insertStatement->bindParam(':userTimeZone',    $time_zone,         PDO::PARAM_STR);

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
function readUserData(
    object $database_handle
): array | bool {
    $readQuery = "
        SELECT
            vu.userName,
            vu.userRole,
            vu.userFlag,
            vu.userImage,
            vu.userUrl,
            vu.userRank,
            vu.userTimeZone
        FROM
            VotUser vu
        JOIN
            VotTournament vt ON vu.tournamentId = vt.tournamentId
        WHERE
            vu.userRole = :userRole
        ORDER BY
            vu.userRole ASC;
    ";

    $readStatement = $database_handle->prepare($readQuery);
    $userRoleFilter = 'User'; // There's no other way I can do to filter query result so yeah
    $readStatement->bindParam(':userRole', $userRoleFilter, PDO::PARAM_STR);

    $successReadLogMessage = sprintf("Read successfully for all authorised user data");
    $unsuccessReadLogMessage = sprintf("Read unsuccessfully for all authorised user data");

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
