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
        $customSongFeatureArtist        = $custom_song_data['custom_song_fa'];
        $customSongMapper               = $custom_song_data['custom_song_mapper'];
        $customSongMapperUrl            = $custom_song_data['custom_song_mapper_url'];
        $customSongDifficulty           = $custom_song_data['custom_song_difficulty'];
        $customSongLength               = $custom_song_data['custom_song_length'];
        $customSongOverallSpeed         = $custom_song_data['custom_song_bpm'];
        $customSongOverallDifficulty    = $custom_song_data['custom_song_od'];
        $customSongOverallHealth        = $custom_song_data['custom_song_hp'];
        $customSongPassCount            = $custom_song_data['custom_song_pass_count'];
        $customSongCustomIndicator      = $custom_song_data['custom_song_custom_indicator'];
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
                difficulty_name: $customSongDifficultyName,
                feature_artist: $customSongFeatureArtist,
                mapper: $customSongMapper,
                mapper_url: $customSongMapperUrl,
                difficulty: $customSongDifficulty,
                length: $customSongLength,
                overall_speed: $customSongOverallSpeed,
                overall_difficulty: $customSongOverallDifficulty,
                overall_health: $customSongOverallHealth,
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
        SELECT COUNT(beatmapId)
        FROM VotTournamentBeatmap
        WHERE beatmapId = :beatmapId
        AND beatmapCustom = 1;
    ";

    $checkStatement = $database_handle->prepare($checkQuery);

    // I can't type hint this due to `var` parameter has an address (&) assigned to it, which syntactically
    // not valid for a PHP code. Therefore, to let this method works, I have to make this one as an
    // exceptional for letting my code to be strictly-typed.
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
    bool $custom_indicator,
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
            beatmapPassCount,
            beatmapCustom
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
            :beatmapPassCount,
            :beatmapCustom
        );
    ";

    $insertStatement = $database_handle->prepare($insertQuery);

    // I can't type hint this due to `var` parameter has an address (&) assigned to it, which syntactically
    // not valid for a PHP code. Therefore, to let this method works, I have to make this one as an
    // exceptional for letting my code to be strictly-typed.
    //
    // Surprisingly, PDO method doesn't have a proper way to handle float data type. Reference: https://stackoverflow.com/a/1335191
    $insertStatement->bindParam(':beatmapId',                   $id,                    PDO::PARAM_INT);
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
    $insertStatement->bindParam(':beatmapCustom',               $custom_indicator,      PDO::PARAM_INT); // boolean data type just basically means '1' & '0' in MySQL

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
            vb.beatmapCustom = 1
            AND vt.tournamentId = :tournamentId
        ORDER BY
            vt.tournamentId;
    ";

    $readStatement = $database_handle->prepare($readQuery);

    // I can't type hint this due to `var` parameter has an address (&) assigned to it, which syntactically
    // not valid for a PHP code. Therefore, to let this method works, I have to make this one as an
    // exceptional for letting my code to be strictly-typed.
    $readStatement->bindParam(':tournamentId',  $tournament_name,   PDO::PARAM_INT);

    $successReadLogMessage    = sprintf("Read successfully for all custom song data from: %s", $tournament_name);
    $unsuccessReadLogMessage  = sprintf("Read unsuccessfully for all custom song data from: %s", $tournament_name);

    if ($readStatement->execute()) {
        error_log(message: $successReadLogMessage, message_type: 0);
        $readAllCustomSongData = $readStatement->fetchAll(mode: PDO::FETCH_ASSOC);
        return $readAllCustomSongData;
    } else {
        error_log(message: $unsuccessReadLogMessage, message_type: 0);
        return false;
    }
}

// Update
// Delete
