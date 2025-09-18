<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require __DIR__ . '/../../Configurations/Database.php';


function checkGeneralRoleCategory(
    int $id,
    string $role,
    object $database_handle
): bool {
    $successCheckLogMessage = sprintf(
        "User data check existed for user ID [%d] with [%s] role.",
        $id,
        $role,
    );
    $unsuccessCheckLogMessage = sprintf(
        "User data check not existed for user ID [%d] with [%s] role.",
        $id,
        $role,
    );

    $checkQuery = "
        SELECT
            userId,
            roleId
        FROM
            VotGeneralRole
        WHERE
            userId = :userId AND roleId = :roleId;
    ";

    $checkStatement = $database_handle->prepare($checkQuery);
    $checkStatement->bindParam(':userId',   $id,    PDO::PARAM_INT);
    $checkStatement->bindParam(':roleId',   $role,  PDO::PARAM_STR);

    if ($checkStatement->execute()) {
        // Checking trick: https://www.php.net/manual/en/pdostatement.fetchcolumn.php#100522
        $existRoleCategory = $checkStatement->fetchColumn(column: 0);

        if (!$existRoleCategory) {
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
function createGeneralRoleCategory(
    int $id,
    string $role,
    object $database_handle
): bool {
    $successInsertLogMessage = sprintf(
        "Insert successfully for user ID [%d] with [%s] role.",
        $id,
        $role,
    );
    $unsuccessInsertLogMessage = sprintf(
        "Insert unsuccessfully for user ID [%d] with [%s] role.",
        $id,
        $role,
    );

    $insertQuery = "
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

    $insertStatement = $database_handle->prepare($insertQuery);
    $insertStatement->bindParam(':userId',  $id,    PDO::PARAM_INT);
    $insertStatement->bindParam(':roleId',  $role,  PDO::PARAM_STR);

    try {
        if ($insertStatement->execute()) {
            error_log(
                message: $successInsertLogMessage,
                message_type: 0
            );
            return true;
        } else {
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
// Update
// Delete
