<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require __DIR__ . '/../Controllers/Controller.php';

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
