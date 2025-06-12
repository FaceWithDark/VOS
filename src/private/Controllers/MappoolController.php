<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

function getTournamentRoundMappool(string $tournament_name, string $tournament_round)
{
    /* $successMessage = sprintf("Current round: %s", $tournament_round);
    return $successMessage;
    */

    switch ($tournament_name) {
        case 'vot4':
        case 'vot3':
        case 'vot2':
        case 'vot1':
            $tournamentRoundMappoolJsonData = file_get_contents(
                filename: __DIR__ . '/../Datas/Tournament/VotMappoolData.json',
                use_include_path: false,
                context: null,
                offset: 0,
                length: null
            );

            $tournamentRoundMappoolReadableData = json_decode(
                json: $tournamentRoundMappoolJsonData,
                associative: true
            );

            $qualifierRoundMappoolJsonData = $tournamentRoundMappoolReadableData[$tournament_name][$tournament_round];
            // echo '<pre>' . print_r($qualifierRoundMappoolJsonData, true) . '</pre>';

            $qualifierRoundNoModBeatmapJsonData = $qualifierRoundMappoolJsonData['NM'];
            // echo '<pre>' . print_r($qualifierRoundNoModBeatmapJsonData, true) . '</pre>';

            $tournamentRoundBeatmapData = [];

            foreach ($qualifierRoundNoModBeatmapJsonData as $key => $value) {
                // TODO:
                // Created JSON data should only be used for `View` to read from the
                // database only. Else, there will be a simple admin-like panel to
                // create add new data into new JSON file so that it can be added to
                // the database like the logic below (Can only be done by 'Admin'
                // role)
                $qualifierRoundNoModBeatmapData = getTournamentRoundBeatmapData(
                    beatmap_id: $value,
                    access_token: $_COOKIE['vot_access_token']
                );
                // echo '<pre>' . print_r($qualifierRoundNoModBeatmapData, true) . '</pre>';

                $qualifierRoundBeatmapId                    = $qualifierRoundNoModBeatmapData['id'];
                $qualifierRoundId                           = strtoupper(string: $_GET['round']);
                $qualifierTournamentId                      = strtoupper(string: $tournament_name);
                $qualifierRoundBeatmapType                  = $key;
                $qualifierRoundBeatmapImage                 = $qualifierRoundNoModBeatmapData['beatmapset']['covers']['cover'];
                $qualifierRoundBeatmapUrl                   = $qualifierRoundNoModBeatmapData['url'];
                $qualifierRoundBeatmapName                  = $qualifierRoundNoModBeatmapData['beatmapset']['title'];
                $qualifierRoundBeatmapDifficultyName        = $qualifierRoundNoModBeatmapData['version'];
                $qualifierRoundBeatmapFeatureArtist         = $qualifierRoundNoModBeatmapData['beatmapset']['artist'];
                $qualifierRoundBeatmapMapper                = $qualifierRoundNoModBeatmapData['beatmapset']['creator'];
                $qualifierRoundBeatmapMapperUrl             = "https://osu.ppy.sh/users/{$qualifierRoundNoModBeatmapData['beatmapset']['user_id']}";
                $qualifierRoundBeatmapDifficulty            = $qualifierRoundNoModBeatmapData['difficulty_rating'];
                $qualifierRoundBeatmapLength                = $qualifierRoundNoModBeatmapData['total_length'];
                $qualifierRoundBeatmapOverallSpeed          = $qualifierRoundNoModBeatmapData['beatmapset']['bpm'];
                $qualifierRoundBeatmapOverallDifficulty     = $qualifierRoundNoModBeatmapData['accuracy'];
                $qualifierRoundBeatmapOverallHealth         = $qualifierRoundNoModBeatmapData['drain'];
                $qualifierRoundBeatmapPassCount             = $qualifierRoundNoModBeatmapData['passcount'];

                $tournamentRoundBeatmapData[] = [
                    'beatmap_id'        => $qualifierRoundBeatmapId,
                    'round_id'          => $qualifierRoundId,
                    'tournament_id'     => $qualifierTournamentId,
                    'type'              => $qualifierRoundBeatmapType,
                    'image'             => $qualifierRoundBeatmapImage,
                    'url'               => $qualifierRoundBeatmapUrl,
                    'beatmap_name'      => $qualifierRoundBeatmapName,
                    'difficulty_name'   => $qualifierRoundBeatmapDifficultyName,
                    'fa'                => $qualifierRoundBeatmapFeatureArtist,
                    'mapper'            => $qualifierRoundBeatmapMapper,
                    'mapper_url'        => $qualifierRoundBeatmapMapperUrl,
                    'difficulty'        => $qualifierRoundBeatmapDifficulty,
                    'length'            => $qualifierRoundBeatmapLength,
                    'bpm'               => $qualifierRoundBeatmapOverallSpeed,
                    'od'                => $qualifierRoundBeatmapOverallDifficulty,
                    'hp'                => $qualifierRoundBeatmapOverallHealth,
                    'pass_count'        => $qualifierRoundBeatmapPassCount
                ];
            }

            // echo '<pre>' . print_r($tournamentRoundBeatmapData, true) . '</pre>';

            // This will keep the MVC structure as how it is
            getMappoolData(mappool_data: $tournamentRoundBeatmapData);

            return $tournamentRoundMappoolReadableData;
    }
}

function getTournamentRoundBeatmapData(int $beatmap_id, string $access_token): array | bool
{
    $httpAuthorisationType  = $access_token;
    $httpAcceptType         = 'application/json';
    $httpContentType        = 'application/json';

    if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
        $httpHeaderRequest = [
            "Authorization: Bearer {$httpAuthorisationType}",
            "Accept: {$httpAcceptType}",
            "Content-Type: {$httpContentType}",
        ];
    } else {
        $httpHeaderRequest = array(
            "Authorization: Bearer {$httpAuthorisationType}",
            "Accept: {$httpAcceptType}",
            "Content-Type: {$httpContentType}",
        );
    }

    $tournamentRoundBeatmapUrl = "https://osu.ppy.sh/api/v2/beatmaps/{$beatmap_id}";

    # CURL session will be handled manually through curl_setopt()
    $tournamentRoundBeatmapCurlHandle = curl_init(url: null);

    curl_setopt(handle: $tournamentRoundBeatmapCurlHandle, option: CURLOPT_URL, value: $tournamentRoundBeatmapUrl);
    curl_setopt(handle: $tournamentRoundBeatmapCurlHandle, option: CURLOPT_HTTPHEADER, value: $httpHeaderRequest);
    curl_setopt(handle: $tournamentRoundBeatmapCurlHandle, option: CURLOPT_HEADER, value: 0);
    curl_setopt(handle: $tournamentRoundBeatmapCurlHandle, option: CURLOPT_RETURNTRANSFER, value: 1);

    $tournamentRoundBeatmapCurlResponse = curl_exec(handle: $tournamentRoundBeatmapCurlHandle);

    if (curl_errno(handle: $tournamentRoundBeatmapCurlHandle)) {
        error_log(curl_error(handle: $tournamentRoundBeatmapCurlHandle));
        curl_close(handle: $tournamentRoundBeatmapCurlHandle);
        return false; // An error occurred during the API call

    } else {
        $tournamentRoundBeatmapReadableData = json_decode(
            json: $tournamentRoundBeatmapCurlResponse,
            associative: true,
            depth: 512,
            flags: 0
        );

        curl_close(handle: $tournamentRoundBeatmapCurlHandle);
        return $tournamentRoundBeatmapReadableData;
    }
}
