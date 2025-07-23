<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require __DIR__ . '/../Configurations/Database.php';

function getStaffData(array $data): null
{
    foreach ($data as $staff_data) {
        $staffId         = $staff_data['staff_id'];
        $staffName       = $staff_data['staff_name'];
        $staffRole       = $staff_data['staff_role'];
        $staffFlag       = $staff_data['staff_flag'];
        $staffImage      = $staff_data['staff_image'];
        $staffUrl        = $staff_data['staff_url'];
        $staffRank       = $staff_data['staff_rank'];
        $staffTimeZone   = $staff_data['staff_time_zone'];
        $staffDatabase   = $GLOBALS['votDatabaseHandle'];

        if (!checkStaffData(staff_id: $staffId, database_handle: $staffDatabase)) {
            createStaffData(
                id: $staffId,
                name: $staffName,
                role: $staffRole,
                flag: $staffFlag,
                image: $staffImage,
                url: $staffUrl,
                rank: $staffRank,
                time_zone: $staffTimeZone,
                database_handle: $staffDatabase
            );
            // TODO: Need to create user role as well (Maybe within this function or somewhere else).
        } else {
            // TODO: UPDATE query here (change the 'view' table only, not the actual table if all data stay the same).
            error_log(message: 'Staff data exist, simply ignoring it...', message_type: 0);
        };
    }
    return null;
}

function checkStaffData(int $staff_id, object $database_handle): int | bool
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
    $checkStatement->bindParam(':userId',   $staff_id,    PDO::PARAM_INT);

    $successCheckLogMessage    = sprintf("Check successfully for staff ID: %d", $staff_id);
    $unsuccessCheckLogMessage  = sprintf("Check unsuccessfully for staff ID: %d", $staff_id);

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
    object $database_handle
): string | bool {
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
function readStaffData(int $staff_id, object $database_handle): array | bool
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
    $readStatement->bindParam(':userId',    $staff_id,    PDO::PARAM_INT);

    $successReadLogMessage    = sprintf("Read successfully for staff ID: %d", $staff_id);
    $unsuccessReadLogMessage  = sprintf("Read unsuccessfully for staff ID: %d", $staff_id);

    if ($readStatement->execute()) {
        error_log(message: $successReadLogMessage, message_type: 0);
        $readAllStaffData = $readStatement->fetchAll(mode: PDO::FETCH_ASSOC);
        return $readAllStaffData;
    } else {
        error_log(message: $unsuccessReadLogMessage, message_type: 0);
        return false;
    }
}

// Update
// Delete
