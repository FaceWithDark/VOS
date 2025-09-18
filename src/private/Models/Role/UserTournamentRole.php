<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require __DIR__ . '/../../Configurations/Database.php';


function checkTournamentRoleCategory(
    int $id,
    string $role,
    string $tournament,
    object $database_handle,
): bool {
    $successCheckLogMessage = sprintf(
        "User data check existed for user ID [%d] with [%s] role in [%s] tournament.",
        $id,
        $role,
        $tournament
    );
    $unsuccessCheckLogMessage = sprintf(
        "User data check not existed for user ID [%d] with [%s] role in [%s] tournament.",
        $id,
        $role,
        $tournament
    );

    $checkQuery = "
        SELECT
            userId,
            roleId,
            tournamentId
        FROM
            VotTournamentRole
        WHERE
            userId = :userId AND roleId = :roleId AND tournamentId = :tournamentId;
    ";

    $checkStatement = $database_handle->prepare($checkQuery);
    $checkStatement->bindParam(':userId',         $id,            PDO::PARAM_INT);
    $checkStatement->bindParam(':roleId',         $role,          PDO::PARAM_STR);
    $checkStatement->bindParam(':tournamentId',   $tournament,    PDO::PARAM_STR);

    if ($checkStatement->execute()) {
        // Checking trick: https://www.php.net/manual/en/pdostatement.fetchcolumn.php#100522
        $existUserRole = $checkStatement->fetchColumn(column: 0);

        if (!$existUserRole) {
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
function createTournamentRoleCategory(
    int $id,
    string $role,
    string $tournament,
    object $database_handle
): bool {
    $successInsertLogMessage = sprintf(
        "Insert successfully for user ID [%d] with [%s] role in [%s] tournament.",
        $id,
        $role,
        $tournament
    );
    $unsuccessInsertLogMessage = sprintf(
        "Insert unsuccessfully for user ID [%d] with [%s] role in [%s] tournament.",
        $id,
        $role,
        $tournament
    );

    $insertQuery = "
                INSERT INTO VotTournamentRole
                    (
                        userId,
                        roleId,
                        tournamentId
                    )
                VALUES
                    (
                        :userId,
                        :roleId,
                        :tournamentId
                    );
            ";

    $insertStatement = $database_handle->prepare($insertQuery);
    $insertStatement->bindParam(':userId',        $id,            PDO::PARAM_INT);
    $insertStatement->bindParam(':roleId',        $role,          PDO::PARAM_STR);
    $insertStatement->bindParam(':tournamentId',  $tournament,    PDO::PARAM_STR);

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
