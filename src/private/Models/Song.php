<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require __DIR__ . '/../Configurations/Database.php';


function getCustomSongData(array $data): null
{
    require_once __DIR__ . '/../Configurations/PrettyArray.php';
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
        $customSongDatabase             = $GLOBALS['votDatabaseHandle'];

        if (
            !checkCustomSongData(
                id: $customSongId,
                round: $customSongRoundId,
                tournament: $customSongTournamentId,
                database_handle: $customSongDatabase
            )
        ) {
            createCustomSongData(
                beatmap_id: $customSongId,
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
    string $round,
    string $tournament,
    object $database_handle
): bool {
    $successCheckLogMessage = sprintf(
        "Custom song data check existed for beatmap ID [%d] within [%s] round in [%s] tournament.",
        $id,
        $round,
        $tournament
    );
    $unsuccessCheckLogMessage = sprintf(
        "Custom song data check not existed for beatmap ID [%d] within [%s] round in [%s] tournament.",
        $id,
        $round,
        $tournament
    );

    $checkQuery = "
        SELECT
            beatmapId,
            roundId,
            tournamentId
        FROM
            VotBeatmap
        WHERE
            beatmapId = :beatmapId
        AND
            roundId = :roundId
        AND
            tournamentId = :tournamentId
        AND
            beatmapCustom = 1;
    ";

    $checkStatement = $database_handle->prepare($checkQuery);
    $checkStatement->bindParam(':beatmapId',        $id,            PDO::PARAM_INT);
    $checkStatement->bindParam(':roundId',          $round,         PDO::PARAM_STR);
    $checkStatement->bindParam(':tournamentId',     $tournament,    PDO::PARAM_STR);

    if ($checkStatement->execute()) {
        // Checking trick: https://www.php.net/manual/en/pdostatement.fetchcolumn.php#100522
        $existBeatmapCustomSong = $checkStatement->fetchColumn(column: 0);

        if (!$existBeatmapCustomSong) {
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
function createCustomSongData(
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
    object $database_handle,
    bool $custom_indicator = true // Indicator for custom song enabled by default
): bool {
    $successInsertLogMessage = sprintf(
        "Insert successfully for custom song with beatmap ID [%d] within [%s] round in [%s] tournament.",
        $beatmap_id,
        $round_id,
        $tournament_id
    );
    $unsuccessInsertLogMessage = sprintf(
        "Insert unsuccessfully for custom song with beatmap ID [%d] within [%s] round in [%s] tournament.",
        $beatmap_id,
        $round_id,
        $tournament_id
    );

    $insertQuery = "
        INSERT INTO VotBeatmap
            (
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
                :customSongIndicator
            );
    ";

    $insertStatement = $database_handle->prepare($insertQuery);
    // Surprisingly, PDO method doesn't have a proper way to handle float data
    // type. Reference: https://stackoverflow.com/a/1335191
    $insertStatement->bindParam(':customSongId',                    $beatmap_id,        PDO::PARAM_INT);
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
    $insertStatement->bindParam(':customSongIndicator',             $custom_indicator,  PDO::PARAM_INT);

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
        die("Insert error!! Reason: " . $exception->getmessage());
    }
}


// Read
function readCustomSongData(
    string $tournament,
    object $database_handle
): array | bool {
    $successReadLogMessage = sprintf(
        "Read successfully for all custom song data in [%s] tournament.",
        $tournament
    );
    $unsuccessReadLogMessage = sprintf(
        "Read unsuccessfully for all custom song data in [%s] tournament.",
        $tournament
    );

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
            vb.beatmapOverallHealth
        FROM
            VotBeatmap vb
        JOIN
            VotRound vr ON vb.roundId = vr.roundId
        JOIN
            VotTournament vt ON vb.tournamentId = vt.tournamentId
        WHERE
            vt.tournamentId = :tournamentId AND vb.beatmapCustom = 1
        ORDER BY
            vt.tournamentId ASC;
    ";

    switch ($tournament) {
        case 'DEFAULT':
            /*
             *==================================================================
             * Because filter custom song data by default is basically fetching
             * all custom song information within the database of a specific
             * tournament, so I'll just being a bit hacky here by reading the
             * data for each individual tournament straight away. Take a look at
             * the 'SongController.php' file at line 31 to see why doing so is
             * beneficial.
             *==================================================================
             */

            // bindParam() 2nd parameter have a reference (&) to it, which deny
            // appending r-value directly just like C++ concepts
            $tournaments = [
                'VOT5',
                'VOT4',
                'VOT3',
                'VOT2',
                'VOT1'
            ];

            $newSearchClause = "
                IN
                    (
                        :tournamentFirstId,
                        :tournamentSecondId,
                        :tournamentThirdId,
                        :tournamentForthId,
                        :tournamentFifthId
                    )
            ";

            $newSuccessReadLogMessage       = 'Read successfully for all custom song data in all [VOT] tournaments.';
            $newUnsuccessReadLogMessage     = 'Read unsuccessfully for all custom song data all [VOT] tournaments.';

            $newReadQuery = str_replace(
                search: " = :tournamentId",
                replace: $newSearchClause,
                subject: $readQuery
            );

            $newReadStatement = $database_handle->prepare($newReadQuery);
            $newReadStatement->bindParam(':tournamentFirstId',      $tournaments[0],    PDO::PARAM_STR);
            $newReadStatement->bindParam(':tournamentSecondId',     $tournaments[1],    PDO::PARAM_STR);
            $newReadStatement->bindParam(':tournamentThirdId',      $tournaments[2],    PDO::PARAM_STR);
            $newReadStatement->bindParam(':tournamentForthId',      $tournaments[3],    PDO::PARAM_STR);
            $newReadStatement->bindParam(':tournamentFifthId',      $tournaments[4],    PDO::PARAM_STR);

            try {
                if ($newReadStatement->execute()) {
                    error_log(
                        message: $newSuccessReadLogMessage,
                        message_type: 0
                    );
                    $newCustomSongViewData = $newReadStatement->fetchAll(mode: PDO::FETCH_ASSOC);
                    return $newCustomSongViewData;
                } else {
                    error_log(
                        message: $newUnsuccessReadLogMessage,
                        message_type: 0
                    );
                    return false;
                }
            } catch (PDOException $exception) {
                // Kills the page and show the error for debugging (most likely no data exists)
                die("Read error!! Reason: " . $exception->getmessage());
            }
            break;

        case 'VOT5':
            $readStatement = $database_handle->prepare($readQuery);
            $readStatement->bindParam(':tournamentId', $tournament, PDO::PARAM_STR);

            try {
                if ($readStatement->execute()) {
                    error_log(
                        message: $successReadLogMessage,
                        message_type: 0
                    );
                    $customSongViewData = $readStatement->fetchAll(mode: PDO::FETCH_ASSOC);
                    return $customSongViewData;
                } else {
                    error_log(
                        message: $unsuccessReadLogMessage,
                        message_type: 0
                    );
                    return false;
                }
            } catch (PDOException $exception) {
                // Kills the page and show the error for debugging (most likely no data exists)
                die("Read error!! Reason: " . $exception->getmessage());
            }
            break;

        case 'VOT4':
            $readStatement = $database_handle->prepare($readQuery);
            $readStatement->bindParam(':tournamentId', $tournament, PDO::PARAM_STR);

            try {
                if ($readStatement->execute()) {
                    error_log(
                        message: $successReadLogMessage,
                        message_type: 0
                    );
                    $customSongViewData = $readStatement->fetchAll(mode: PDO::FETCH_ASSOC);
                    return $customSongViewData;
                } else {
                    error_log(
                        message: $unsuccessReadLogMessage,
                        message_type: 0
                    );
                    return false;
                }
            } catch (PDOException $exception) {
                // Kills the page and show the error for debugging (most likely no data exists)
                die("Read error!! Reason: " . $exception->getmessage());
            }
            break;

        case 'VOT3':
            echo "COMMING SOON!!!";
            return [];
            break;

        case 'VOT2':
            echo "COMMING SOON!!!";
            return [];
            break;

        case 'VOT1':
            echo "COMMING SOON!!!";
            return [];
            break;

        default:
            // TODO: proper invalid GET request handling
            echo "<strong>What are you tryin' to do, huh?</strong>";
            return [];
            break;
    }
}

// Update
// Delete
