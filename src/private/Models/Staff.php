<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require __DIR__ . '/../Configurations/Database.php';


// Read
function readStaffData(
    string $role,
    string $tournament
): array | bool {
    // IDE cannot recognise PDO object at runtime somehow
    global $votDatabaseHandle;

    $readQuery = "
        SELECT
            vu.userName,
            vu.userFlag,
            vu.userImage,
            vu.userUrl,
            vu.userRank,
            vu.userTimeZone
        FROM
            VotTournamentRole vtr
        JOIN
            VotUser vu ON vtr.userId = vu.userId
        WHERE
            vtr.roleId = :roleId
            AND vtr.tournamentId = :tournamentId
        ORDER BY
            vtr.roleId ASC;
    ";

    $successReadLogMessage = sprintf(
        "Read all [%s] staff data successfully from [%s] tournament.",
        $role,
        $tournament
    );
    $unsuccessReadLogMessage = sprintf(
        "Read all [%s] staff data unsuccessfully from [%s] tournament.",
        $role,
        $tournament
    );

    switch ($role) {
        case 'HST':
        case 'MAPLR':
        case 'GNVFX':
        case 'MAPPR':
        case 'PLTST':
        case 'REFRE':
        case 'STRMR':
        case 'CMNTR':
        case 'STACN':
            $readStatement = $votDatabaseHandle->prepare($readQuery);
            $readStatement->bindParam(':roleId',        $role,          PDO::PARAM_STR);
            $readStatement->bindParam(':tournamentId',  $tournament,    PDO::PARAM_STR);

            if ($readStatement->execute()) {
                error_log(message: $successReadLogMessage, message_type: 0);
                $staffViewData = $readStatement->fetchAll(mode: PDO::FETCH_ASSOC);
                return $staffViewData;
            } else {
                error_log(message: $unsuccessReadLogMessage, message_type: 0);
                return false;
            }
            break;

        default:
            error_log(
                message: sprintf(
                    "Someone trying to be sneaky with [%s] role and [%s] tournament input!!",
                    $role,
                    $tournament
                ),
                message_type: 0
            );
            return [];
            break;
    }
}
