<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require __DIR__ . '/../Configurations/Database.php';

function getMappoolData(array $mappool_data): null
{
    foreach ($mappool_data as $beatmap_data) {
        $beatmapId                  = $beatmap_data['beatmap_id'];
        $roundId                    = $beatmap_data['beatmap_round_id'];
        $tournamentId               = $beatmap_data['beatmap_tournament_id'];
        $beatmapType                = $beatmap_data['beatmap_type'];
        $beatmapImage               = $beatmap_data['beatmap_image'];
        $beatmapUrl                 = $beatmap_data['beatmap_url'];
        $beatmapName                = $beatmap_data['beatmap_name'];
        $beatmapDifficultyName      = $beatmap_data['beatmap_difficulty_name'];
        $beatmapFeatureArtist       = $beatmap_data['beatmap_fa'];
        $beatmapMapper              = $beatmap_data['beatmap_mapper'];
        $beatmapMapperUrl           = $beatmap_data['beatmap_mapper_url'];
        $beatmapDifficulty          = $beatmap_data['beatmap_difficulty'];
        $beatmapLength              = $beatmap_data['beatmap_length'];
        $beatmapOverallSpeed        = $beatmap_data['beatmap_bpm'];
        $beatmapOverallDifficulty   = $beatmap_data['beatmap_od'];
        $beatmapOverallHealth       = $beatmap_data['beatmap_hp'];
        $beatmapPassCount           = $beatmap_data['beatmap_pass_count'];
        // Variable scoping in PHP is a bit weird somehow: https://www.php.net/manual/en/language.variables.scope.php
        $mappoolDatabase            = $GLOBALS['votDatabaseHandle'];

        if (!checkMappoolData(beatmap_id: $beatmapId, database_handle: $mappoolDatabase)) {
            createMappoolData(
                beatmap_id: $beatmapId,
                round_id: $roundId,
                tournament_id: $tournamentId,
                type: $beatmapType,
                image: $beatmapImage,
                url: $beatmapUrl,
                name: $beatmapName,
                difficulty_name: $beatmapDifficultyName,
                feature_artist: $beatmapFeatureArtist,
                mapper: $beatmapMapper,
                mapper_url: $beatmapMapperUrl,
                difficulty: $beatmapDifficulty,
                length: $beatmapLength,
                overall_speed: $beatmapOverallSpeed,
                overall_difficulty: $beatmapOverallDifficulty,
                overall_health: $beatmapOverallHealth,
                pass_count: $beatmapPassCount,
                database_handle: $mappoolDatabase
            );
        } else {
            // TODO: UPDATE query here (change the 'view' table only, not the actual table if all data stay the same).
            error_log(message: 'Mappool data exist, simply ignoring it...', message_type: 0);
        };
    }
    return null;
}

function checkMappoolData(int $beatmap_id, object $database_handle): int | bool
{
    $checkQuery = "
        SELECT COUNT(beatmapId)
        FROM VotTournamentBeatmap
        WHERE beatmapId = :beatmapId;
    ";

    $checkStatement = $database_handle->prepare($checkQuery);

    // I can't type hint this due to `var` parameter has an address (&) assigned to it, which syntactically
    // not valid for a PHP code. Therefore, to let this method works, I have to make this one as an
    // exceptional for letting my code to be strictly-typed.
    $checkStatement->bindParam(':beatmapId',   $beatmap_id,    PDO::PARAM_INT);

    $successCheckLogMessage    = sprintf("Check successfully for user ID: %d", $beatmap_id);
    $unsuccessCheckLogMessage  = sprintf("Check unsuccessfully for user ID: %d", $beatmap_id);

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
function createMappoolData(
    int $beatmap_id,
    string $round_id,
    string $tournament_id,
    string $type,
    string $image,
    string $url,
    string $name,
    string $difficulty_name,
    string $feature_artist,
    string $mapper,
    string $mapper_url,
    float $difficulty,
    int $length,
    float $overall_speed,
    float $overall_difficulty,
    float $overall_health,
    int $pass_count,
    object $database_handle
): string | bool {
    $insertQuery = "
        INSERT INTO VotTournamentBeatmap (
            beatmapId,
            roundId,
            tournamentId,
            beatmapType,
            beatmapImage,
            beatmapUrl,
            beatmapName,
            beatmapDifficultyName,
            beatmapFeatureArtist,
            beatmapMapper,
            beatmapMapperUrl,
            beatmapDifficulty,
            beatmapLength,
            beatmapOverallSpeed,
            beatmapOverallDifficulty,
            beatmapOverallHealth,
            beatmapPassCount
        )
        VALUES (
            :beatmapId,
            :roundId,
            :tournamentId,
            :beatmapType,
            :beatmapImage,
            :beatmapUrl,
            :beatmapName,
            :beatmapDifficultyName,
            :beatmapFeatureArtist,
            :beatmapMapper,
            :beatmapMapperUrl,
            :beatmapDifficulty,
            :beatmapLength,
            :beatmapOverallSpeed,
            :beatmapOverallDifficulty,
            :beatmapOverallHealth,
            :beatmapPassCount
        );
    ";

    $insertStatement = $database_handle->prepare($insertQuery);

    // I can't type hint this due to `var` parameter has an address (&) assigned to it, which syntactically
    // not valid for a PHP code. Therefore, to let this method works, I have to make this one as an
    // exceptional for letting my code to be strictly-typed.
    //
    // Surprisingly, PDO method doesn't have a proper way to handle float data type. Reference: https://stackoverflow.com/a/1335191
    $insertStatement->bindParam(':beatmapId',                   $beatmap_id,            PDO::PARAM_INT);
    $insertStatement->bindParam(':roundId',                     $round_id,              PDO::PARAM_STR);
    $insertStatement->bindParam(':tournamentId',                $tournament_id,         PDO::PARAM_STR);
    $insertStatement->bindParam(':beatmapType',                 $type,                  PDO::PARAM_STR);
    $insertStatement->bindParam(':beatmapImage',                $image,                 PDO::PARAM_STR);
    $insertStatement->bindParam(':beatmapUrl',                  $url,                   PDO::PARAM_STR);
    $insertStatement->bindParam(':beatmapName',                 $name,                  PDO::PARAM_STR);
    $insertStatement->bindParam(':beatmapDifficultyName',       $difficulty_name,       PDO::PARAM_STR);
    $insertStatement->bindParam(':beatmapFeatureArtist',        $feature_artist,        PDO::PARAM_STR);
    $insertStatement->bindParam(':beatmapMapper',               $mapper,                PDO::PARAM_STR);
    $insertStatement->bindParam(':beatmapMapperUrl',            $mapper_url,            PDO::PARAM_STR);
    $insertStatement->bindParam(':beatmapDifficulty',           $difficulty,            PDO::PARAM_STR); // No proper way to handle float data type in PDO somehow
    $insertStatement->bindParam(':beatmapLength',               $length,                PDO::PARAM_INT);
    $insertStatement->bindParam(':beatmapOverallSpeed',         $overall_speed,         PDO::PARAM_STR); // No proper way to handle float data type in PDO somehow
    $insertStatement->bindParam(':beatmapOverallDifficulty',    $overall_difficulty,    PDO::PARAM_STR); // No proper way to handle float data type in PDO somehow
    $insertStatement->bindParam(':beatmapOverallHealth',        $overall_health,        PDO::PARAM_STR); // No proper way to handle float data type in PDO somehow
    $insertStatement->bindParam(':beatmapPassCount',            $pass_count,            PDO::PARAM_INT);

    $successInsertLogMessage    = sprintf("Insert successfully for user ID: %d", $beatmap_id);
    $unsuccessInsertLogMessage  = sprintf("Insert unsuccessfully for user ID: %d", $beatmap_id);

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
function readMappoolData(
    // int $beatmap_id,
    string $round_name,
    string $tournament_name,
    object $database_handle
): array | bool {
    $readQuery = "
        SELECT
            vt.tournamentName,
            vr.roundName,
            vb.beatmapType,
            vb.beatmapImage,
            vb.beatmapUrl,
            vb.beatmapName,
            vb.beatmapDifficultyName,
            vb.beatmapFeatureArtist,
            vb.beatmapMapper,
            vb.beatmapMapperUrl,
            vb.beatmapDifficulty,
            vb.beatmapLength,
            vb.beatmapOverallSpeed,
            vb.beatmapOverallDifficulty,
            vb.beatmapOverallHealth,
            vb.beatmapPassCount
        FROM
            VotTournamentBeatmap vb
        JOIN
            VotTournamentRound vr ON vb.roundId = vr.roundId
        JOIN
            VotTournamentType vt ON vr.tournamentId = vt.tournamentId
        WHERE
            vr.roundId = :roundId
            AND vt.tournamentId = :tournamentId
        ORDER BY
            (CASE vb.beatmapType
                WHEN 'NM1' THEN 1
                WHEN 'NM2' THEN 2
                WHEN 'NM3' THEN 3
                WHEN 'NM4' THEN 4
                WHEN 'NM5' THEN 5
                WHEN 'NM6' THEN 6

                WHEN 'HD1' THEN 7
                WHEN 'HD2' THEN 8

                WHEN 'HR1' THEN 9
                WHEN 'HR2' THEN 10

                WHEN 'DT1' THEN 11
                WHEN 'DT2' THEN 12

                WHEN 'FM1' THEN 13
                WHEN 'FM2' THEN 14
                WHEN 'FM3' THEN 15

                WHEN 'EZ1' THEN 16

                WHEN 'HDHR1' THEN 17

                WHEN 'TB' THEN 18

                ELSE 19
            END) ASC;
    ";

    $readStatement = $database_handle->prepare($readQuery);

    // I can't type hint this due to `var` parameter has an address (&) assigned to it, which syntactically
    // not valid for a PHP code. Therefore, to let this method works, I have to make this one as an
    // exceptional for letting my code to be strictly-typed.
    $readStatement->bindParam(':roundId',       $round_name,        PDO::PARAM_INT);
    $readStatement->bindParam(':tournamentId',  $tournament_name,   PDO::PARAM_INT);

    // $successReadLogMessage    = sprintf("Read successfully for user ID: %d", $beatmap_id);
    // $unsuccessReadLogMessage  = sprintf("Read unsuccessfully for user ID: %d", $beatmap_id);

    if ($readStatement->execute()) {
        // error_log(message: $successReadLogMessage, message_type: 0);
        $readAllMappoolData = $readStatement->fetchAll(mode: PDO::FETCH_ASSOC);
        return $readAllMappoolData;
    } else {
        // error_log(message: $unsuccessReadLogMessage, message_type: 0);
        return false;
    }
}

// Update
// Delete
