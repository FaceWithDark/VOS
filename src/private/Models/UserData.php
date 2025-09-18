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
        $userRoleId         = $user_data['osu_user_role_id'];
        $userTournamentId   = $user_data['osu_user_tournament_id'];
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
                role_id: $userRoleId,
                tournament_id: $userTournamentId
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
): bool {
    $checkQuery = "
        SELECT
            userId
        FROM
            VotUser
        WHERE
            userId = :userId;
    ";

    $checkStatement = $database_handle->prepare($checkQuery);
    $checkStatement->bindParam(':userId', $id, PDO::PARAM_INT);

    $successCheckLogMessage = sprintf(
        "User data check existed for user ID [%d]",
        $id
    );
    $unsuccessCheckLogMessage = sprintf(
        "User data check not existed for user ID [%d]",
        $id
    );

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
    int $id,
    string $name,
    string $flag,
    string $image,
    string $url,
    int $rank,
    string $time_zone,
    object $database_handle,
    string $role_id = 'USR',        // All user have user-level access by default
    string $tournament_id = 'NONE'  // All user belong to none tournament by default
): bool {
    // User data will be stored to a simple table
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

    $userInsertStatement = $database_handle->prepare($userInsertQuery);
    $userInsertStatement->bindParam(':userId',          $id,            PDO::PARAM_INT);
    $userInsertStatement->bindParam(':tournamentId',    $tournament_id, PDO::PARAM_STR);
    $userInsertStatement->bindParam(':userName',        $name,          PDO::PARAM_STR);
    $userInsertStatement->bindParam(':userFlag',        $flag,          PDO::PARAM_STR);
    $userInsertStatement->bindParam(':userImage',       $image,         PDO::PARAM_STR);
    $userInsertStatement->bindParam(':userUrl',         $url,           PDO::PARAM_STR);
    $userInsertStatement->bindParam(':userRank',        $rank,          PDO::PARAM_INT);
    $userInsertStatement->bindParam(':userTimeZone',    $time_zone,     PDO::PARAM_STR);

    // Then, we store some of it to a complex table for look-up later
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

    $generalUserInsertStatement = $database_handle->prepare($generalUserInsertQuery);
    $generalUserInsertStatement->bindParam(':userId',   $id,        PDO::PARAM_INT);
    $generalUserInsertStatement->bindParam(':roleId',   $role_id,   PDO::PARAM_STR);

    $successInsertLogMessage = sprintf(
        "Insert successfully for user ID [%d] with [%s] role.",
        $id,
        $role_id
    );
    $unsuccessInsertLogMessage = sprintf(
        "Insert unsuccessfully for user ID [%d] with [%s] role.",
        $id,
        $role_id
    );

    // Start a transaction (in SQL term means running two or more queries at the
    // same time with conditions)
    $database_handle->beginTransaction();

    try {
        // Make sure that the 1st query execute successfully first
        if ($userInsertStatement->execute()) {
            // Then successfully execute 2nd query
            if ($generalUserInsertStatement->execute()) {
                // Commit both queries execution, which means apply it directly
                // and no need to buffer it anymore
                $database_handle->commit();
                error_log(
                    message: $successInsertLogMessage,
                    message_type: 0
                );
                return true;
            } else {
                // Rollback to the 1st query, which means delete the 2nd query
                // out from the buffer and unbuffered the 1st query only
                $database_handle->rollBack();
                error_log(
                    message: $unsuccessInsertLogMessage,
                    message_type: 0
                );
                return false;
            }
        } else {
            // Rollback to none query, which means delete both the queries out
            // from the buffer and execute nothing
            $database_handle->rollBack();
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

            $adminSuccessReadLogMessage = sprintf(
                "Read successfully for user ID [%d] with [ADMIN] role.",
                $id
            );
            $adminUnsuccessReadLogMessage = sprintf(
                "Read unsuccessfully for user ID [%d] with [ADMIN] role.",
                $id
            );

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
                return false;
            }
            break;

        /* Host for each tournament is also an osu! player */
        case 9623142:
        case 16039831:
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

            $hostSuccessReadLogMessage = sprintf(
                "Read successfully for user ID [%d] with [HOST] role.",
                $id
            );
            $hostUnsuccessReadLogMessage = sprintf(
                "Read unsuccessfully for user ID [%d] with [HOST] role.",
                $id
            );

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

            $userSuccessReadLogMessage = sprintf(
                "Read successfully for user ID [%d] with [USER] role.",
                $id
            );
            $userUnsuccessReadLogMessage = sprintf(
                "Read unsuccessfully for user ID [%d] with [USER] role.",
                $id
            );

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
                return false;
            }
            break;
    }
}

// Update
// Delete
