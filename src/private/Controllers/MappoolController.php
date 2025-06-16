<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

function getTournamentRoundMappool(string $tournament_name, string $tournament_round)
{
    /* $successMessage = sprintf("Current round: %s", $tournament_round);
    return $successMessage;
    */

    $tournamentRoundAbbreviationNames = [
        'qualifiers'        => 'QLF',
        'round_of_16'       => 'RO16',
        'quarterfinals'     => 'QF',
        'semifinals'        => 'SF',
        'finals'            => 'FNL',
        'grandfinals'       => 'GF',
        'allstars'          => 'ASTR',
    ];

    $tournamentRoundAbbreviationNameData    = [];
    $tournamentRoundBeatmapData             = [];

    switch ($tournament_name) {
        case 'vot4':
        case 'vot3':
        case 'vot2':
        case 'vot1':
            if (array_key_exists(key: $tournament_round, array: $tournamentRoundAbbreviationNames)) {
                foreach ($tournamentRoundAbbreviationNames as $tournamentRoundAbbreviationName) {
                    $tournamentRoundAbbreviationNameData[] = $tournamentRoundAbbreviationName;
                }
            } else {
                echo 'Sus';
            }

            // echo '<pre>' . print_r($tournamentRoundAbbreviationArray, true) . '</pre>';

            $qualifierRoundAbbreviationName     = $tournamentRoundAbbreviationNameData[0];
            $roundOf16RoundAbbreviationName     = $tournamentRoundAbbreviationNameData[1];
            $quarterFinalRoundAbbreviationName  = $tournamentRoundAbbreviationNameData[2];
            $semiFinalRoundAbbreviationName     = $tournamentRoundAbbreviationNameData[3];
            $finalRoundAbbreviationName         = $tournamentRoundAbbreviationNameData[4];
            $grandFinalRoundAbbreviationName    = $tournamentRoundAbbreviationNameData[5];
            $allStarRoundAbbreviationName       = $tournamentRoundAbbreviationNameData[6];

            $tournamentRoundJsonMappoolData = file_get_contents(
                filename: __DIR__ . '/../Datas/Tournament/VotMappoolData.json',
                use_include_path: false,
                context: null,
                offset: 0,
                length: null
            );

            $tournamentRoundReadableMappoolData = json_decode(
                json: $tournamentRoundJsonMappoolData,
                associative: true
            );

            $qualifierRoundJsonMappoolData = $tournamentRoundReadableMappoolData[$tournament_name][$qualifierRoundAbbreviationName];
            // echo '<pre>' . print_r($qualifierRoundJsonMappoolData, true) . '</pre>';

            $qualifierRoundJsonMappoolNoModData = $qualifierRoundJsonMappoolData['NM'];
            // echo '<pre>' . print_r($qualifierRoundJsonMappoolNoModData, true) . '</pre>';

            foreach ($qualifierRoundJsonMappoolNoModData as $qualifierRoundJsonMappoolNoModTypeData => $qualifierRoundJsonMappoolNoModIdData) {
                // TODO:
                // Created JSON data should only be used for `View` to read from the
                // database only. Else, there will be a simple admin-like panel to
                // create add new data into new JSON file so that it can be added to
                // the database like the logic below (Can only be done by 'Admin'
                // role)
                $qualifierRoundBeatmapNoModData = getTournamentRoundBeatmapData(
                    beatmap_id: $qualifierRoundJsonMappoolNoModIdData,
                    access_token: $_COOKIE['vot_access_token']
                );
                // echo '<pre>' . print_r($tournamentRoundNoModBeatmapData, true) . '</pre>';

                $qualifierRoundBeatmapId                    = $qualifierRoundBeatmapNoModData['id'];
                $qualifierRoundId                           = $qualifierRoundAbbreviationName;
                $qualifierTournamentId                      = strtoupper(string: $tournament_name);
                $qualifierRoundBeatmapType                  = $qualifierRoundJsonMappoolNoModTypeData;
                $qualifierRoundBeatmapImage                 = $qualifierRoundBeatmapNoModData['beatmapset']['covers']['cover'];
                $qualifierRoundBeatmapUrl                   = $qualifierRoundBeatmapNoModData['url'];
                $qualifierRoundBeatmapName                  = $qualifierRoundBeatmapNoModData['beatmapset']['title'];
                $qualifierRoundBeatmapDifficultyName        = $qualifierRoundBeatmapNoModData['version'];
                $qualifierRoundBeatmapFeatureArtist         = $qualifierRoundBeatmapNoModData['beatmapset']['artist'];
                $qualifierRoundBeatmapMapper                = $qualifierRoundBeatmapNoModData['beatmapset']['creator'];
                $qualifierRoundBeatmapMapperUrl             = "https://osu.ppy.sh/users/{$qualifierRoundBeatmapNoModData['beatmapset']['user_id']}";
                $qualifierRoundBeatmapDifficulty            = $qualifierRoundBeatmapNoModData['difficulty_rating'];
                $qualifierRoundBeatmapLength                = $qualifierRoundBeatmapNoModData['total_length'];
                $qualifierRoundBeatmapOverallSpeed          = $qualifierRoundBeatmapNoModData['beatmapset']['bpm'];
                $qualifierRoundBeatmapOverallDifficulty     = $qualifierRoundBeatmapNoModData['accuracy'];
                $qualifierRoundBeatmapOverallHealth         = $qualifierRoundBeatmapNoModData['drain'];
                $qualifierRoundBeatmapPassCount             = $qualifierRoundBeatmapNoModData['passcount'];

                $tournamentRoundBeatmapData[] = [
                    'beatmap_id'                => $qualifierRoundBeatmapId,
                    'beatmap_round_id'          => $qualifierRoundId,
                    'beatmap_tournament_id'     => $qualifierTournamentId,
                    'beatmap_type'              => $qualifierRoundBeatmapType,
                    'beatmap_image'             => $qualifierRoundBeatmapImage,
                    'beatmap_url'               => $qualifierRoundBeatmapUrl,
                    'beatmap_name'              => $qualifierRoundBeatmapName,
                    'beatmap_difficulty_name'   => $qualifierRoundBeatmapDifficultyName,
                    'beatmap_fa'                => $qualifierRoundBeatmapFeatureArtist,
                    'beatmap_mapper'            => $qualifierRoundBeatmapMapper,
                    'beatmap_mapper_url'        => $qualifierRoundBeatmapMapperUrl,
                    'beatmap_difficulty'        => $qualifierRoundBeatmapDifficulty,
                    'beatmap_length'            => $qualifierRoundBeatmapLength,
                    'beatmap_bpm'               => $qualifierRoundBeatmapOverallSpeed,
                    'beatmap_od'                => $qualifierRoundBeatmapOverallDifficulty,
                    'beatmap_hp'                => $qualifierRoundBeatmapOverallHealth,
                    'beatmap_pass_count'        => $qualifierRoundBeatmapPassCount
                ];
            }
            // echo '<pre>' . print_r($tournamentRoundBeatmapData, true) . '</pre>';

            // This will keep the MVC structure as how it is
            getMappoolData(mappool_data: $tournamentRoundBeatmapData);
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
