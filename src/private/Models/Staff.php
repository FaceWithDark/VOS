<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require __DIR__ . '/../Configurations/Database.php';


function getStaffData(array $data): null
{
    foreach ($data as $staff_data) {
        $staffId            = $staff_data['staff_id'];
        $staffTournamentId  = $staff_data['staff_tournament_id'];
        $staffName          = $staff_data['staff_name'];
        $staffRole          = $staff_data['staff_role'];
        $staffFlag          = $staff_data['staff_flag'];
        $staffImage         = $staff_data['staff_image'];
        $staffUrl           = $staff_data['staff_url'];
        $staffRank          = $staff_data['staff_rank'];
        $staffTimeZone      = $staff_data['staff_time_zone'];
        $staffDatabase      = $GLOBALS['votDatabaseHandle'];

        if (!checkStaffData(id: $staffId, database_handle: $staffDatabase)) {
            createStaffData(
                id: $staffId,
                name: $staffName,
                role: $staffRole,
                flag: $staffFlag,
                image: $staffImage,
                url: $staffUrl,
                rank: $staffRank,
                time_zone: $staffTimeZone,
                database_handle: $staffDatabase,
                tournament_id: $staffTournamentId
            );
        } else {
            // TODO: UPDATE query here (change the 'view' table only, not the actual table if all data stay the same).
            error_log(message: 'Staff data exist, simply ignoring it...', message_type: 0);
        };
    }
    return null;
}


function checkStaffData(
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

    $successCheckLogMessage    = sprintf("Check successfully for staff ID: %d", $id);
    $unsuccessCheckLogMessage  = sprintf("Check unsuccessfully for staff ID: %d", $id);

    if ($checkStatement->execute()) {
        error_log(message: $successCheckLogMessage, message_type: 0);
        $checkAllStaffData = $checkStatement->fetchColumn(
            column: 0
        );
        return $checkAllStaffData;
    } else {
        error_log(message: $unsuccessCheckLogMessage, message_type: 0);
        return false;
    }
}


// Create
function createStaffData(
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

    $successInsertLogMessage    = sprintf("Insert successfully for staff ID: %d", $id);
    $unsuccessInsertLogMessage  = sprintf("Insert unsuccessfully for staff ID: %d", $id);

    if ($insertStatement->execute()) {
        error_log(message: $successInsertLogMessage, message_type: 0);
        $totalInsertLogMessage = sprintf(
            "Total staff data successfully inserted: %d",
            $insertStatement->rowCount()
        );
        return $totalInsertLogMessage;
    } else {
        error_log(message: $unsuccessInsertLogMessage, message_type: 0);
        return false;
    }
}


// Read
function readStaffData(
    string $role,
    string $name,
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
            AND vt.tournamentId = :tournamentId
        ORDER BY
            vu.userRole ASC;
    ";

    if ($role !== 'DEFAULT') {
        // Edge case not needed, perform the reading logic as usual
        $readStatement = $database_handle->prepare($readQuery);
        $readStatement->bindParam(':userRole',      $role,  PDO::PARAM_STR);
        $readStatement->bindParam(':tournamentId',  $name,  PDO::PARAM_STR);

        $successReadLogMessage = sprintf(
            "Read successfully for all staff data with %s role from %s",
            $role,
            strtoupper(string: $name)
        );
        $unsuccessReadLogMessage = sprintf(
            "Read unsuccessfully for all staff data with %s role from %s",
            $role,
            strtoupper(string: $name)
        );

        if ($readStatement->execute()) {
            error_log(message: $successReadLogMessage, message_type: 0);
            $readAllStaffData = $readStatement->fetchAll(mode: PDO::FETCH_ASSOC);
            return $readAllStaffData;
        } else {
            error_log(message: $unsuccessReadLogMessage, message_type: 0);
            return false;
        }
    } else {
        // bindParam() 2nd parameter is a 'lvalue' type so I can't pass the string straight away
        $staffRoleEdgeCaseFirstByPass       = 'HOST';
        $staffRoleEdgeCaseSecondByPass      = 'MAPPOOLER';
        $staffRoleEdgeCaseThirdByPass       = 'GFX/VFX';
        $staffRoleEdgeCaseForthByPass       = 'MAPPER';
        $staffRoleEdgeCaseFifthByPass       = 'PLAYTESTER';
        $staffRoleEdgeCaseSixthByPass       = 'REFEREE';
        $staffRoleEdgeCaseSeventhByPass     = 'STREAMER';
        $staffRoleEdgeCaseEighthByPass      = 'COMMENTATOR';
        $staffRoleEdgeCaseNinethByPass      = 'STATISTICIAN';

        /*
        Because filter staff role by default is basically fetching all staff
        roles within the database of a specific tournament, so I'll just being a
        bit hacky here by reading each individual staff role data straight away.
        Take a look at the 'StaffController.php' file at line 51 to see why doing
        so is beneficial
        */

        $newReadQuery = str_replace(
            " = :userRole",
            " IN (:userRoleFirstId, :userRoleSecondId, :userRoleThirdId, :userRoleForthId, :userRoleFifthId, :userRoleSixthId, :userRoleSeventhId, :userRoleEightId, :userRoleNinethId)",
            $readQuery
        );

        $newReadStatement = $database_handle->prepare($newReadQuery);
        $newReadStatement->bindParam(':userRoleFirstId',    $staffRoleEdgeCaseFirstByPass,      PDO::PARAM_STR);
        $newReadStatement->bindParam(':userRoleSecondId',   $staffRoleEdgeCaseSecondByPass,     PDO::PARAM_STR);
        $newReadStatement->bindParam(':userRoleThirdId',    $staffRoleEdgeCaseThirdByPass,      PDO::PARAM_STR);
        $newReadStatement->bindParam(':userRoleForthId',    $staffRoleEdgeCaseForthByPass,      PDO::PARAM_STR);
        $newReadStatement->bindParam(':userRoleFifthId',    $staffRoleEdgeCaseFifthByPass,      PDO::PARAM_STR);
        $newReadStatement->bindParam(':userRoleSixthId',    $staffRoleEdgeCaseSixthByPass,      PDO::PARAM_STR);
        $newReadStatement->bindParam(':userRoleSeventhId',  $staffRoleEdgeCaseSeventhByPass,    PDO::PARAM_STR);
        $newReadStatement->bindParam(':userRoleEightId',    $staffRoleEdgeCaseEighthByPass,     PDO::PARAM_STR);
        $newReadStatement->bindParam(':userRoleNinethId',   $staffRoleEdgeCaseNinethByPass,     PDO::PARAM_STR);
        $newReadStatement->bindParam(':tournamentId',       $name,                              PDO::PARAM_STR);

        $newSuccessReadLogMessage = sprintf(
            "Read successfully for all staff data from all role within VOT4",
            strtoupper(string: $name)
        );
        $newUnsuccessReadLogMessage = sprintf(
            "Read unsuccessfully for all staff data from all role within VOT4",
            strtoupper(string: $name)
        );

        if ($newReadStatement->execute()) {
            error_log(message: $newSuccessReadLogMessage, message_type: 0);
            $readAllStaffData = $newReadStatement->fetchAll(mode: PDO::FETCH_ASSOC);
            return $readAllStaffData;
        } else {
            error_log(message: $newUnsuccessReadLogMessage, message_type: 0);
            return false;
        }
    }
}

// Update
// Delete
