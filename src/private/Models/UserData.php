<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require __DIR__ . '/../Configurations/Database.php';


function getUserData(array $data): null
{
    foreach ($data as $user_data) {
        $userId             = $user_data['osu_user_id'];
        $userName           = $user_data['osu_user_name'];
        $userFlag           = $user_data['osu_user_flag'];
        $userImage          = $user_data['osu_user_image'];
        $userUrl            = $user_data['osu_user_url'];
        $userRank           = $user_data['osu_user_rank'];
        $userTimeZone       = $user_data['osu_user_time_zone'];
        $userDatabase       = $GLOBALS['votDatabaseHandle'];

        if (!checkUserData(id: $userId, database_handle: $userDatabase)) {
            createUserData(
                id: $userId,
                name: $userName,
                flag: $userFlag,
                image: $userImage,
                url: $userUrl,
                rank: $userRank,
                time_zone: $userTimeZone,
                database_handle: $userDatabase,
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
    string $flag,
    string $image,
    string $url,
    int $rank,
    string $time_zone,
    object $database_handle
    // There is a default data for 'tournamentId' column, DO NOT DELETE IT!!
): string | bool {
    $insertQuery = "
        INSERT INTO
            VotUser (
                userId,
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
                :userName,
                :userFlag,
                :userImage,
                :userUrl,
                :userRank,
                :userTimeZone
            );
    ";

    $insertStatement = $database_handle->prepare($insertQuery);
    $insertStatement->bindParam(':userId',          $id,                PDO::PARAM_INT);
    $insertStatement->bindParam(':userName',        $name,              PDO::PARAM_STR);
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
    int $id,
    object $database_handle
): array | bool {
    switch ($id) {
        /* Website owner is also an osu! player */
        case 19817503:
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

            $adminReadStatement = $database_handle->prepare($adminReadQuery);
            $adminReadStatement->bindParam(':userId', $id, PDO::PARAM_INT);

            $adminSuccessReadLogMessage     = sprintf("Read successfully for admin role with user ID: %d", $id);
            $adminUnsuccessReadLogMessage   = sprintf("Read unsuccessfully for admin role with user ID: %d", $id);

            if ($adminReadStatement->execute()) {
                error_log(message: $adminSuccessReadLogMessage, message_type: 0);
                $adminViewData = $adminReadStatement->fetch(
                    mode: PDO::FETCH_ASSOC,
                    cursorOrientation: PDO::FETCH_ORI_NEXT,
                    cursorOffset: 0
                );
                return $adminViewData;
            } else {
                error_log(message: $adminUnsuccessReadLogMessage, message_type: 0);
                return false;
            }
            break;

        /* Host for each tournament is also an osu! player */
        case 9623142 || 16039831:
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

            $hostReadStatement = $database_handle->prepare($hostReadQuery);
            $hostReadStatement->bindParam(':userId', $id, PDO::PARAM_INT);

            $hostSuccessReadLogMessage     = sprintf("Read successfully for host role with user ID: %d", $id);
            $hostUnsuccessReadLogMessage   = sprintf("Read unsuccessfully for host role with user ID: %d", $id);

            if ($hostReadStatement->execute()) {
                error_log(message: $hostSuccessReadLogMessage, message_type: 0);
                $hostViewData = $hostReadStatement->fetch(
                    mode: PDO::FETCH_ASSOC,
                    cursorOrientation: PDO::FETCH_ORI_NEXT,
                    cursorOffset: 0
                );
                return $hostViewData;
            } else {
                error_log(message: $hostUnsuccessReadLogMessage, message_type: 0);
                return false;
            }
            break;

        default:
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

            $userReadStatement = $database_handle->prepare($userReadQuery);
            $userReadStatement->bindParam(':userId', $id, PDO::PARAM_INT);

            $userSuccessReadLogMessage     = sprintf("Read successfully for user role with user ID: %d", $id);
            $userUnsuccessReadLogMessage   = sprintf("Read unsuccessfully for user role with user ID: %d", $id);

            if ($userReadStatement->execute()) {
                error_log(message: $userSuccessReadLogMessage, message_type: 0);
                $userViewData = $userReadStatement->fetch(
                    mode: PDO::FETCH_ASSOC,
                    cursorOrientation: PDO::FETCH_ORI_NEXT,
                    cursorOffset: 0
                );
                return $userViewData;
            } else {
                error_log(message: $userUnsuccessReadLogMessage, message_type: 0);
                return false;
            }
            break;
    }
}

// Update
// Delete
