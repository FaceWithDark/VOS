<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require __DIR__ . '/../Configurations/Database.php';


// Read
function readSongData(
    string $tournament
): array {
    // IDE cannot recognise PDO object at runtime somehow
    global $votDatabaseHandle;

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
             * the 'SongController.php' file at line 24 to see why doing so is
             * beneficial.
             *==================================================================
             */

            // bindParam() 2nd parameter have a reference (&) to it, which deny
            // appending r-value directly just like C++ concepts
            $tournamentNames = [
                'VOT5',
                'VOT4',
                'VOT3',
                'VOT2',
                'VOT1'
            ];

            $readStatement = $votDatabaseHandle->prepare($readQuery);
            foreach ($tournamentNames as $tournamentName) {
                $readStatement->bindParam(':tournamentId', $tournamentName, PDO::PARAM_STR);

                $newSuccessReadLogMessage = sprintf(
                    "Read successfully for all song data in [%s] tournament.",
                    $tournamentName
                );
                $newUnsuccessReadLogMessage = sprintf(
                    'Read unsuccessfully for all song data [%s] tournament.',
                    $tournamentName
                );

                try {
                    if ($readStatement->execute()) {
                        error_log(
                            message: $newSuccessReadLogMessage,
                            message_type: 0
                        );
                        $newSongViewData = $readStatement->fetchAll(mode: PDO::FETCH_ASSOC);
                        return $newSongViewData;
                    } else {
                        error_log(
                            message: $newUnsuccessReadLogMessage,
                            message_type: 0
                        );
                        return [];
                        break;
                    }
                } catch (PDOException $exception) {
                    // Kills the page and show the error for debugging (most likely no data exists)
                    die("Read error!! Reason: " . $exception->getmessage());
                    break;
                }
            }

        case 'VOT5':
            $readStatement = $votDatabaseHandle->prepare($readQuery);
            $readStatement->bindParam(':tournamentId', $tournament, PDO::PARAM_STR);

            try {
                if ($readStatement->execute()) {
                    error_log(
                        message: $successReadLogMessage,
                        message_type: 0
                    );
                    $songViewData = $readStatement->fetchAll(mode: PDO::FETCH_ASSOC);
                    return $songViewData;
                } else {
                    error_log(
                        message: $unsuccessReadLogMessage,
                        message_type: 0
                    );
                    return [];
                    break;
                }
            } catch (PDOException $exception) {
                // Kills the page and show the error for debugging (most likely no data exists)
                die("Read error!! Reason: " . $exception->getmessage());
                break;
            }

        case 'VOT4':
            $readStatement = $votDatabaseHandle->prepare($readQuery);
            $readStatement->bindParam(':tournamentId', $tournament, PDO::PARAM_STR);

            try {
                if ($readStatement->execute()) {
                    error_log(
                        message: $successReadLogMessage,
                        message_type: 0
                    );
                    $songViewData = $readStatement->fetchAll(mode: PDO::FETCH_ASSOC);
                    return $songViewData;
                } else {
                    error_log(
                        message: $unsuccessReadLogMessage,
                        message_type: 0
                    );
                    return [];
                }
            } catch (PDOException $exception) {
                // Kills the page and show the error for debugging (most likely no data exists)
                die("Read error!! Reason: " . $exception->getmessage());
            }
            break;

        case 'VOT3':
            return ['NOT UDAPTED YET!!'];
            break;

        case 'VOT2':
            return ['NOT UDAPTED YET!!'];
            break;

        case 'VOT1':
            return ['NOT UDAPTED YET!!'];
            break;

        default:
            // TODO: proper invalid GET request handling
            $invalidReadLogMessage = sprintf(
                "No records of song data for [%s] tournament. What are u tryin' to do bro...",
                $tournament
            );

            return [$invalidReadLogMessage];
            break;
    }
}

// Update
// Delete
