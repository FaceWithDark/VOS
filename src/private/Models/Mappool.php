<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require __DIR__ . '/../Configurations/Database.php';


function getMappoolData(
    int $id,
    string $token
): array | bool {
    $httpAuthorisationType  = $token;
    $httpAcceptType         = 'application/json';
    $httpContentType        = 'application/json';
    $beatmapUrl             = "https://osu.ppy.sh/api/v2/beatmaps/{$id}";

    if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
        $httpHeaderRequest = [
            "Authorization: Bearer {$httpAuthorisationType}",
            "Accept: {$httpAcceptType}",
            "Content-Type: {$httpContentType}",
        ];

        # CURL session will be handled manually through curl_setopt()
        $mappoolCurlHandle = curl_init(url: null);

        curl_setopt(handle: $mappoolCurlHandle, option: CURLOPT_URL, value: $beatmapUrl);
        curl_setopt(handle: $mappoolCurlHandle, option: CURLOPT_HTTPHEADER, value: $httpHeaderRequest);
        curl_setopt(handle: $mappoolCurlHandle, option: CURLOPT_HEADER, value: 0);
        curl_setopt(handle: $mappoolCurlHandle, option: CURLOPT_RETURNTRANSFER, value: 1);

        $mappoolCurlResponse = curl_exec(handle: $mappoolCurlHandle);

        if (curl_errno(handle: $mappoolCurlHandle)) {
            error_log(curl_error(handle: $mappoolCurlHandle));
            curl_close(handle: $mappoolCurlHandle);
            return false; // An error occurred during the API call

        } else {
            $mappoolUsableData = json_decode(
                json: $mappoolCurlResponse,
                associative: true,
                depth: 512,
                flags: 0
            );

            curl_close(handle: $mappoolCurlHandle);
            return $mappoolUsableData;
        }
    } else {
        $httpHeaderRequest = array(
            "Authorization: Bearer {$httpAuthorisationType}",
            "Accept: {$httpAcceptType}",
            "Content-Type: {$httpContentType}",
        );

        # CURL session will be handled manually through curl_setopt()
        $mappoolCurlHandle = curl_init(url: null);

        curl_setopt(handle: $mappoolCurlHandle, option: CURLOPT_URL, value: $beatmapUrl);
        curl_setopt(handle: $mappoolCurlHandle, option: CURLOPT_HTTPHEADER, value: $httpHeaderRequest);
        curl_setopt(handle: $mappoolCurlHandle, option: CURLOPT_HEADER, value: 0);
        curl_setopt(handle: $mappoolCurlHandle, option: CURLOPT_RETURNTRANSFER, value: 1);

        $mappoolCurlResponse = curl_exec(handle: $mappoolCurlHandle);

        if (curl_errno(handle: $mappoolCurlHandle)) {
            error_log(curl_error(handle: $mappoolCurlHandle));
            curl_close(handle: $mappoolCurlHandle);
            return false; // An error occurred during the API call

        } else {
            $mappoolUsableData = json_decode(
                json: $mappoolCurlResponse,
                associative: true,
                depth: 512,
                flags: 0
            );

            curl_close(handle: $mappoolCurlHandle);
            return $mappoolUsableData;
        }
    }
}


function checkMappoolData(
    int $id,
    string $round,
    string $tournament
): bool {
    // IDE cannot recognise PDO object at runtime somehow
    global $votDatabaseHandle;

    $successCheckLogMessage = sprintf(
        "[%s] mappool data check in [%s] tournament existed for beatmap ID [%d].",
        $round,
        $tournament,
        $id
    );
    $unsuccessCheckLogMessage = sprintf(
        "[%s] mappool data check in [%s] tournament not existed for beatmap ID [%d].",
        $round,
        $tournament,
        $id
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
            tournamentId = :tournamentId;
    ";

    $checkStatement = $votDatabaseHandle->prepare($checkQuery);
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
function createMappoolData(
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
    bool $custom_indicator = false // Indicator for non-custom song enabled by default
): bool {
    // IDE cannot recognise PDO object at runtime somehow
    global $votDatabaseHandle;

    $successInsertLogMessage = sprintf(
        "Insert successfully for [%s] mappool in [%s] tournament with beatmap ID [%d].",
        $round_id,
        $tournament_id,
        $beatmap_id
    );
    $unsuccessInsertLogMessage = sprintf(
        "Insert unsuccessfully for [%s] mappool in [%s] tournament with beatmap ID [%d].",
        $round_id,
        $tournament_id,
        $beatmap_id
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

    $insertStatement = $votDatabaseHandle->prepare($insertQuery);
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
function readMappoolData(
    string $round,
    string $tournament
): array {
    // IDE cannot recognise PDO object at runtime somehow
    global $votDatabaseHandle;

    $successReadLogMessage = sprintf(
        "Read successfully for all [%s] mappool data in [%s] tournament.",
        $round,
        $tournament
    );
    $unsuccessReadLogMessage = sprintf(
        "Read unsuccessfully for all [%s] mappool data in [%s] tournament.",
        $round,
        $tournament
    );

    $readQuery = "
        SELECT
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
            vr.roundId = :roundId AND vt.tournamentId = :tournamentId
        ORDER BY
            (CASE vb.beatmapType
                WHEN 'NM1' THEN 1
                WHEN 'NM2' THEN 2
                WHEN 'NM3' THEN 3
                WHEN 'NM4' THEN 4
                WHEN 'NM5' THEN 5
                WHEN 'NM6' THEN 6
                WHEN 'NM7' THEN 7

                WHEN 'HD1' THEN 8
                WHEN 'HD2' THEN 9

                WHEN 'HR1' THEN 10
                WHEN 'HR2' THEN 11

                WHEN 'DT1' THEN 12
                WHEN 'NC1' THEN 12
                WHEN 'DT2' THEN 13
                WHEN 'NC2' THEN 13

                WHEN 'FM1' THEN 14
                WHEN 'FM2' THEN 15
                WHEN 'FM3' THEN 16

                WHEN 'EZ' THEN 17

                WHEN 'HDHR' THEN 18

                WHEN 'FL' THEN 19

                WHEN 'TB' THEN 20

                ELSE 21
            END) ASC;
        ";

    switch ($tournament) {
        case 'VOT5':
            $readStatement = $votDatabaseHandle->prepare($readQuery);
            $readStatement->bindParam(':tournamentId', $tournament, PDO::PARAM_STR);

            switch ($round) {
                // No need to handle [ASTR] case as mentioned in
                // <MappoolController.php> file at line 491.
                case 'QLF':
                case 'RO16':
                case 'GSW1':
                case 'GSW2':
                case 'SF':
                case 'FNL':
                case 'GF':
                    $readStatement->bindParam(':roundId', $round, PDO::PARAM_STR);

                    try {
                        if ($readStatement->execute()) {
                            error_log(
                                message: $successReadLogMessage,
                                message_type: 0
                            );
                            $mappoolViewData = $readStatement->fetchAll(mode: PDO::FETCH_ASSOC);
                            return $mappoolViewData;
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

                default:
                    // TODO: proper invalid GET request handling
                    echo "<strong>What are you tryin' to do, huh?</strong>";
                    return [];
                    break;
            }

        case 'VOT4':
            $readStatement = $votDatabaseHandle->prepare($readQuery);
            $readStatement->bindParam(':tournamentId', $tournament, PDO::PARAM_STR);

            switch ($round) {
                // No need to handle [ASTR] case as mentioned in
                // <MappoolController.php> file at line 1067.
                case 'QLF':
                case 'RO16':
                case 'QF':
                case 'SF':
                case 'FNL':
                case 'GF':
                    $readStatement->bindParam(':roundId', $round, PDO::PARAM_STR);

                    try {
                        if ($readStatement->execute()) {
                            error_log(
                                message: $successReadLogMessage,
                                message_type: 0
                            );
                            $mappoolViewData = $readStatement->fetchAll(mode: PDO::FETCH_ASSOC);
                            return $mappoolViewData;
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

                default:
                    // TODO: proper invalid GET request handling
                    echo "<strong>What are you tryin' to do, huh?</strong>";
                    return [];
                    break;
            }

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
