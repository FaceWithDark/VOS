<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require __DIR__ . '/../Configurations/Database.php';


function getBeatmapData(array $data): null
{
    foreach ($data as $beatmap_data) {
        $beatmapId                  = $beatmap_data['beatmap_id'];
        $roundId                    = $beatmap_data['beatmap_round_id'];
        $tournamentId               = $beatmap_data['beatmap_tournament_id'];
        $beatmapType                = $beatmap_data['beatmap_type'];
        $beatmapImage               = $beatmap_data['beatmap_image'];
        $beatmapUrl                 = $beatmap_data['beatmap_url'];
        $beatmapName                = $beatmap_data['beatmap_name'];
        $beatmapDifficultyName      = $beatmap_data['beatmap_difficulty_name'];
        $beatmapFeatureArtist       = $beatmap_data['beatmap_feature_artist'];
        $beatmapMapper              = $beatmap_data['beatmap_mapper'];
        $beatmapMapperUrl           = $beatmap_data['beatmap_mapper_url'];
        $beatmapDifficulty          = $beatmap_data['beatmap_difficulty'];
        $beatmapLength              = $beatmap_data['beatmap_length'];
        $beatmapOverallSpeed        = $beatmap_data['beatmap_overall_speed'];
        $beatmapOverallDifficulty   = $beatmap_data['beatmap_overall_difficulty'];
        $beatmapOverallHealth       = $beatmap_data['beatmap_overall_health'];
        $beatmapPassCount           = $beatmap_data['beatmap_pass_count'];
        $beatmapDatabase            = $GLOBALS['votDatabaseHandle'];

        if (!checkBeatmapData(id: $beatmapId, database_handle: $beatmapDatabase)) {
            createBeatmapData(
                beatmap_id: $beatmapId,
                round_id: $roundId,
                tournament_id: $tournamentId,
                type: $beatmapType,
                image: $beatmapImage,
                url: $beatmapUrl,
                name: $beatmapName,
                diff_name: $beatmapDifficultyName,
                fa: $beatmapFeatureArtist,
                mapper: $beatmapMapper,
                mapper_url: $beatmapMapperUrl,
                diff: $beatmapDifficulty,
                length: $beatmapLength,
                bpm: $beatmapOverallSpeed,
                od: $beatmapOverallDifficulty,
                hp: $beatmapOverallHealth,
                pass_count: $beatmapPassCount,
                database_handle: $beatmapDatabase
            );
        } else {
            // TODO: UPDATE query here (change the 'view' table only, not the actual table if all data stay the same).
            error_log(message: 'Beatmap data exist, simply ignoring it...', message_type: 0);
        };
    }
    return null;
}


function checkBeatmapData(
    int $id,
    object $database_handle
): int | bool {
    $checkQuery = "
        SELECT
            COUNT(beatmapId)
        FROM
            VotBeatmap
        WHERE
            beatmapId = :beatmapId;
    ";

    $checkStatement = $database_handle->prepare($checkQuery);
    $checkStatement->bindParam(':beatmapId', $id, PDO::PARAM_INT);

    $successCheckLogMessage    = sprintf("Check successfully for beatmap ID: %d", $id);
    $unsuccessCheckLogMessage  = sprintf("Check unsuccessfully for beatmap ID: %d", $id);

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
function createBeatmapData(
    int $beatmap_id,
    string $round_id,
    string $tournament_id,
    string $type,
    string $image,
    string $url,
    string $name,
    string $diff_name,
    string $fa,
    string $mapper,
    string $mapper_url,
    float $diff,
    int $length,
    float $bpm,
    float $od,
    float $hp,
    int $pass_count,
    object $database_handle
): string | bool {
    $insertQuery = "
        INSERT INTO
            VotBeatmap (
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
        VALUES
            (
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
    // Surprisingly, PDO method doesn't have a proper way to handle float data
    // type. Reference: https://stackoverflow.com/a/1335191
    $insertStatement->bindParam(':beatmapId',                   $beatmap_id,        PDO::PARAM_INT);
    $insertStatement->bindParam(':roundId',                     $round_id,          PDO::PARAM_STR);
    $insertStatement->bindParam(':tournamentId',                $tournament_id,     PDO::PARAM_STR);
    $insertStatement->bindParam(':beatmapType',                 $type,              PDO::PARAM_STR);
    $insertStatement->bindParam(':beatmapImage',                $image,             PDO::PARAM_STR);
    $insertStatement->bindParam(':beatmapUrl',                  $url,               PDO::PARAM_STR);
    $insertStatement->bindParam(':beatmapName',                 $name,              PDO::PARAM_STR);
    $insertStatement->bindParam(':beatmapDifficultyName',       $diff_name,         PDO::PARAM_STR);
    $insertStatement->bindParam(':beatmapFeatureArtist',        $fa,                PDO::PARAM_STR);
    $insertStatement->bindParam(':beatmapMapper',               $mapper,            PDO::PARAM_STR);
    $insertStatement->bindParam(':beatmapMapperUrl',            $mapper_url,        PDO::PARAM_STR);
    $insertStatement->bindParam(':beatmapDifficulty',           $diff,              PDO::PARAM_STR);
    $insertStatement->bindParam(':beatmapLength',               $length,            PDO::PARAM_INT);
    $insertStatement->bindParam(':beatmapOverallSpeed',         $bpm,               PDO::PARAM_STR);
    $insertStatement->bindParam(':beatmapOverallDifficulty',    $od,                PDO::PARAM_STR);
    $insertStatement->bindParam(':beatmapOverallHealth',        $hp,                PDO::PARAM_STR);
    $insertStatement->bindParam(':beatmapPassCount',            $pass_count,        PDO::PARAM_INT);

    $successInsertLogMessage    = sprintf("Insert successfully for beatmap ID: %d", $beatmap_id);
    $unsuccessInsertLogMessage  = sprintf("Insert unsuccessfully for beatmap ID: %d", $beatmap_id);

    if ($insertStatement->execute()) {
        error_log(message: $successInsertLogMessage, message_type: 0);
        $totalInsertLogMessage = sprintf(
            "Total beatmap data successfully inserted: %d",
            $insertStatement->rowCount()
        );
        return $totalInsertLogMessage;
    } else {
        error_log(message: $unsuccessInsertLogMessage, message_type: 0);
        return false;
    }
}


// Read
function readBeatmapData(
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
            VotBeatmap vb
        JOIN
            VotRound vr ON vb.roundId = vr.roundId
        JOIN
            VotTournament vt ON vb.tournamentId = vt.tournamentId
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
                WHEN 'NC1' THEN 11
                WHEN 'DT2' THEN 12
                WHEN 'NC2' THEN 12

                WHEN 'FM1' THEN 13
                WHEN 'FM2' THEN 14
                WHEN 'FM3' THEN 15

                WHEN 'EZ' THEN 16

                WHEN 'HDHR' THEN 17

                WHEN 'FL' THEN 18

                WHEN 'TB' THEN 19

                ELSE 19
            END) ASC;
    ";

    $readStatement = $database_handle->prepare($readQuery);
    if ($round_name !== 'ASTR') {
        // Edge case not needed, perform the reading logic as usual
        $readStatement->bindParam(':roundId',       $round_name,        PDO::PARAM_STR);
        $readStatement->bindParam(':tournamentId',  $tournament_name,   PDO::PARAM_STR);
    } else {
        // bindParam() 2nd parameter is a 'lvalue' type so I can't pass the string straight away
        $allStarEdgeCaseBypass = 'GF';
        /*
        Because All Star mappool is basically the same as Grand
        Final mappool, so I'll just being a bit hacky here by
        reading Grand Final mappool data straight away. Take a
        look at the 'MappoolController.php' file at line 2207
        to see why doing so is beneficial
        */
        $readStatement->bindParam(':roundId',       $allStarEdgeCaseBypass,     PDO::PARAM_STR);
        $readStatement->bindParam(':tournamentId',  $tournament_name,           PDO::PARAM_STR);
    }

    $successReadLogMessage = sprintf(
        "Read successfully for all mappool data from %s round within %s",
        $round_name,
        strtoupper(string: $tournament_name)
    );
    $unsuccessReadLogMessage = sprintf(
        "Read unsuccessfully for all mappool data from %s round within %s",
        $round_name,
        strtoupper(string: $tournament_name)
    );

    if ($readStatement->execute()) {
        error_log(message: $successReadLogMessage, message_type: 0);
        $readAllMappoolData = $readStatement->fetchAll(mode: PDO::FETCH_ASSOC);
        return $readAllMappoolData;
    } else {
        error_log(message: $unsuccessReadLogMessage, message_type: 0);
        return false;
    }
}

// Update
// Delete
