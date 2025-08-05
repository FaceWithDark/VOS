<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require __DIR__ . '/../Configurations/Database.php';


function getCustomSongData(array $data): null
{
    foreach ($data as $custom_song_data) {
        $customSongId                   = $custom_song_data['custom_song_id'];
        $customSongRoundId              = $custom_song_data['custom_song_round_id'];
        $customSongTournamentId         = $custom_song_data['custom_song_tournament_id'];
        $customSongType                 = $custom_song_data['custom_song_type'];
        $customSongImage                = $custom_song_data['custom_song_image'];
        $customSongUrl                  = $custom_song_data['custom_song_url'];
        $customSongName                 = $custom_song_data['custom_song_name'];
        $customSongDifficultyName       = $custom_song_data['custom_song_difficulty_name'];
        $customSongFeatureArtist        = $custom_song_data['custom_song_feature_artist'];
        $customSongMapper               = $custom_song_data['custom_song_mapper'];
        $customSongMapperUrl            = $custom_song_data['custom_song_mapper_url'];
        $customSongDifficulty           = $custom_song_data['custom_song_difficulty'];
        $customSongLength               = $custom_song_data['custom_song_length'];
        $customSongOverallSpeed         = $custom_song_data['custom_song_overall_speed'];
        $customSongOverallDifficulty    = $custom_song_data['custom_song_overall_difficulty'];
        $customSongOverallHealth        = $custom_song_data['custom_song_overall_health'];
        $customSongPassCount            = $custom_song_data['custom_song_pass_count'];
        $customSongCustomIndicator      = $custom_song_data['custom_song_indicator'];
        $customSongDatabase             = $GLOBALS['votDatabaseHandle'];

        if (!checkCustomSongData(id: $customSongId, database_handle: $customSongDatabase)) {
            createCustomSongData(
                id: $customSongId,
                round_id: $customSongRoundId,
                tournament_id: $customSongTournamentId,
                type: $customSongType,
                image: $customSongImage,
                url: $customSongUrl,
                name: $customSongName,
                diff_name: $customSongDifficultyName,
                fa: $customSongFeatureArtist,
                mapper: $customSongMapper,
                mapper_url: $customSongMapperUrl,
                diff: $customSongDifficulty,
                length: $customSongLength,
                bpm: $customSongOverallSpeed,
                od: $customSongOverallDifficulty,
                hp: $customSongOverallHealth,
                pass_count: $customSongPassCount,
                custom_indicator: $customSongCustomIndicator,
                database_handle: $customSongDatabase
            );
        } else {
            // TODO: UPDATE query here (change the 'view' table only, not the actual table if all data stay the same).
            error_log(message: 'Custom song data exist, simply ignoring it...', message_type: 0);
        };
    }
    return null;
}


function checkCustomSongData(
    int $id,
    object $database_handle
): int | bool {
    $checkQuery = "
    SELECT
        COUNT(beatmapId)
    FROM
        VotBeatmap
    WHERE
        beatmapId = :beatmapId
        AND beatmapCustom = 1;
    ";

    $checkStatement = $database_handle->prepare($checkQuery);
    $checkStatement->bindParam(':beatmapId', $id, PDO::PARAM_INT);

    $successCheckLogMessage    = sprintf("Check successfully for custom song ID: %d", $id);
    $unsuccessCheckLogMessage  = sprintf("Check unsuccessfully for custom song ID: %d", $id);

    if ($checkStatement->execute()) {
        error_log(message: $successCheckLogMessage, message_type: 0);
        $checkAllCustomSongData = $checkStatement->fetchColumn(
            column: 0
        );
        return $checkAllCustomSongData;
    } else {
        error_log(message: $unsuccessCheckLogMessage, message_type: 0);
        return false;
    }
}


// Create
function createCustomSongData(
    int $id,
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
    bool $custom_indicator,
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
            beatmapPassCount,
            beatmapCustom
        )
    VALUES
        (
            :customSongId,
            :roundId,
            :tournamentId,
            :customSongType,
            :customSongImage,
            :customSongUrl,
            :customSongName,
            :customSongDifficultyName,
            :customSongFeatureArtist,
            :customSongMapper,
            :customSongMapperUrl,
            :customSongDifficulty,
            :customSongLength,
            :customSongOverallSpeed,
            :customSongOverallDifficulty,
            :customSongOverallHealth,
            :customSongPassCount,
            :customSongIndicator
        );
    ";

    $insertStatement = $database_handle->prepare($insertQuery);
    // Surprisingly, PDO method doesn't have a proper way to handle float data
    // type. Reference: https://stackoverflow.com/a/1335191
    $insertStatement->bindParam(':customSongId',                    $id,                PDO::PARAM_INT);
    $insertStatement->bindParam(':roundId',                         $round_id,          PDO::PARAM_STR);
    $insertStatement->bindParam(':tournamentId',                    $tournament_id,     PDO::PARAM_STR);
    $insertStatement->bindParam(':customSongType',                  $type,              PDO::PARAM_STR);
    $insertStatement->bindParam(':customSongImage',                 $image,             PDO::PARAM_STR);
    $insertStatement->bindParam(':customSongUrl',                   $url,               PDO::PARAM_STR);
    $insertStatement->bindParam(':customSongName',                  $name,              PDO::PARAM_STR);
    $insertStatement->bindParam(':customSongDifficultyName',        $diff_name,         PDO::PARAM_STR);
    $insertStatement->bindParam(':customSongFeatureArtist',         $fa,                PDO::PARAM_STR);
    $insertStatement->bindParam(':customSongMapper',                $mapper,            PDO::PARAM_STR);
    $insertStatement->bindParam(':customSongMapperUrl',             $mapper_url,        PDO::PARAM_STR);
    $insertStatement->bindParam(':customSongDifficulty',            $diff,              PDO::PARAM_STR);
    $insertStatement->bindParam(':customSongLength',                $length,            PDO::PARAM_INT);
    $insertStatement->bindParam(':customSongOverallSpeed',          $bpm,               PDO::PARAM_STR);
    $insertStatement->bindParam(':customSongOverallDifficulty',     $od,                PDO::PARAM_STR);
    $insertStatement->bindParam(':customSongOverallHealth',         $hp,                PDO::PARAM_STR);
    $insertStatement->bindParam(':customSongPassCount',             $pass_count,        PDO::PARAM_INT);
    $insertStatement->bindParam(':customSongIndicator',             $custom_indicator,  PDO::PARAM_INT); // Boolean data type just basically means '1' & '0' in MariaDB

    $successInsertLogMessage    = sprintf("Insert successfully for custom song ID: %d", $id);
    $unsuccessInsertLogMessage  = sprintf("Insert unsuccessfully for custom song ID: %d", $id);

    if ($insertStatement->execute()) {
        error_log(message: $successInsertLogMessage, message_type: 0);
        $totalInsertLogMessage = sprintf(
            "Total custom song data successfully inserted: %d",
            $insertStatement->rowCount()
        );
        return $totalInsertLogMessage;
    } else {
        error_log(message: $unsuccessInsertLogMessage, message_type: 0);
        return false;
    }
}


// Read
function readCustomSongData(
    string $name,
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
            VotTournament vt ON vr.tournamentId = vt.tournamentId
        WHERE
            vt.tournamentId = :tournamentId
            AND vb.beatmapCustom = 1
        ORDER BY
            vt.tournamentId ASC;
    ";

    if ($name !== 'DEFAULT') {
        // Edge case not needed, perform the reading logic as usual
        $readStatement = $database_handle->prepare($readQuery);
        $readStatement->bindParam(':tournamentId', $name, PDO::PARAM_STR);

        $successReadLogMessage = sprintf(
            "Read successfully for all custom song data from %s",
            $name
        );
        $unsuccessReadLogMessage = sprintf(
            "Read unsuccessfully for all custom song data from %s",
            $name
        );

        if ($readStatement->execute()) {
            error_log(message: $successReadLogMessage, message_type: 0);
            $readAllCustomSongData = $readStatement->fetchAll(mode: PDO::FETCH_ASSOC);
            return $readAllCustomSongData;
        } else {
            error_log(message: $unsuccessReadLogMessage, message_type: 0);
            return false;
        }
    } else {
        // bindParam() 2nd parameter is a 'lvalue' type so I can't pass the string straight away
        $customSongEdgeCaseFirstByPass      = 'VOT4';
        $customSongEdgeCaseSecondByPass     = 'VOT3';
        $customSongEdgeCaseThirdByPass      = 'VOT2';
        $customSongEdgeCaseForthByPass      = 'VOT1';

        /*
        Because filter custom song data by default is basically fetching all
        custom song information within the database of a specific tournament, so
        I'll just being a bit hacky here by reading the data for each individual
        tournament straight away. Take a look at the 'SongController.php' file
        at line 35 to see why doing so is beneficial
        */

        $newReadQuery = str_replace(
            " = :tournamentId",
            " IN (:tournamentFirstId, :tournamentSecondId, :tournamentThirdId, :tournamentForthId)",
            $readQuery
        );

        $newReadStatement = $database_handle->prepare($newReadQuery);
        $newReadStatement->bindParam(':tournamentFirstId',      $customSongEdgeCaseFirstByPass,     PDO::PARAM_STR);
        $newReadStatement->bindParam(':tournamentSecondId',     $customSongEdgeCaseSecondByPass,    PDO::PARAM_STR);
        $newReadStatement->bindParam(':tournamentThirdId',      $customSongEdgeCaseThirdByPass,     PDO::PARAM_STR);
        $newReadStatement->bindParam(':tournamentForthId',      $customSongEdgeCaseForthByPass,     PDO::PARAM_STR);

        $newSuccessReadLogMessage    = 'Read successfully for all custom song data from all VOT tournaments';
        $newUnsuccessReadLogMessage  = 'Read unsuccessfully for all custom song data from all VOT tournaments';

        if ($newReadStatement->execute()) {
            error_log(message: $newSuccessReadLogMessage, message_type: 0);
            $readAllCustomSongData = $newReadStatement->fetchAll(mode: PDO::FETCH_ASSOC);
            return $readAllCustomSongData;
        } else {
            error_log(message: $newUnsuccessReadLogMessage, message_type: 0);
            return false;
        }
    }
}

// Update
// Delete
