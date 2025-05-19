<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

// C.R.U.D operations
// Create
function createUserData(
    int $id,
    string $image,
    string $url,
    string $flag,
    string $name,
    int $rank,
    string $time_zone,
    object $database_handle
): string | bool {
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
function readUserData(int $id, object $database_handle): string | bool
{
    $readQuery = "
        SELECT r.roleId, u.userImage, u.userUrl, u.userFlag, u.userName, u.userRank, u.userTimeZone
        FROM VotUserRole
        RIGHT JOIN VotUser ON r.roleId = u.userId
        ORDER BY r.roleId;
    ";

    $readStatement = $database_handle->prepare($readQuery);

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
