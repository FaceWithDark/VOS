<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);


function getTournamentCustomSong(
    string $tournament_name,
): array {
    $customSongJsonData     = __DIR__ . '/../Datas/Song/CustomSongData.json';

    $allCustomSongRoundData = ['GF', 'SF'];
    $allCustomSongData      = [];

    $osuUserAccessToken     = $_COOKIE['vot_access_token'];

    $customSongViewableJsonData = file_get_contents(
        filename: $customSongJsonData,
        use_include_path: false,
        context: null,
        offset: 0,
        length: null
    );

    $customSongReadableJsonData = json_decode(
        json: $customSongViewableJsonData,
        associative: true
    );

    switch ($tournament_name) {
        case 'vot4':
            foreach ($customSongReadableJsonData[$tournament_name] as $customSongJsonData) {
                $customSongJsonId = $customSongJsonData['custom_song_id'];
                $customSongJsonType = $customSongJsonData['custom_song_type'];

                $customSongData = getTournamentCustomSongData(
                    id: $customSongJsonId,
                    access_token: $osuUserAccessToken
                );

                $customSongId                  = $customSongData['id'];
                $customSongRoundId             = $allCustomSongRoundData[0];
                $customSongTournamentId        = strtoupper(string: $tournament_name);
                $customSongType                = $customSongJsonType;
                $customSongImage               = $customSongData['beatmapset']['covers']['cover'];
                $customSongUrl                 = $customSongData['url'];
                $customSongName                = $customSongData['beatmapset']['title'];
                $customSongDifficultyName      = $customSongData['version'];
                $customSongFeatureArtist       = $customSongData['beatmapset']['artist'];
                $customSongMapper              = $customSongData['beatmapset']['creator'];
                $customSongMapperUrl           = "https://osu.ppy.sh/users/{$customSongData['beatmapset']['user_id']}";
                $customSongDifficulty          = $customSongData['difficulty_rating'];
                $customSongLength              = $customSongData['total_length'];
                $customSongOverallSpeed        = $customSongData['beatmapset']['bpm'];
                $customSongOverallDifficulty   = $customSongData['accuracy'];
                $customSongOverallHealth       = $customSongData['drain'];
                $customSongPassCount           = $customSongData['passcount'];
                $customSongCustomIndicator     = true;

                $allCustomSongData[] = [
                    'custom_song_id'                => $customSongId,
                    'custom_song_round_id'          => $customSongRoundId,
                    'custom_song_tournament_id'     => $customSongTournamentId,
                    'custom_song_type'              => $customSongType,
                    'custom_song_image'             => $customSongImage,
                    'custom_song_url'               => $customSongUrl,
                    'custom_song_name'              => $customSongName,
                    'custom_song_difficulty_name'   => $customSongDifficultyName,
                    'custom_song_fa'                => $customSongFeatureArtist,
                    'custom_song_mapper'            => $customSongMapper,
                    'custom_song_mapper_url'        => $customSongMapperUrl,
                    'custom_song_difficulty'        => $customSongDifficulty,
                    'custom_song_length'            => $customSongLength,
                    'custom_song_bpm'               => $customSongOverallSpeed,
                    'custom_song_od'                => $customSongOverallDifficulty,
                    'custom_song_hp'                => $customSongOverallHealth,
                    'custom_song_pass_count'        => $customSongPassCount,
                    'custom_song_custom_indicator'  => $customSongCustomIndicator
                ];
            }

            getCustomSongData(data: $allCustomSongData);
            break;

        // TODO: Add corresponding logics later
        case 'default':
        case 'vot3':
        case 'vot2':
        case 'vot1':
            break;
    };


    return $allCustomSongData;
}

function getTournamentCustomSongData(
    int $id,
    string $access_token
): array | bool {
    $httpAuthorisationType  = $access_token;
    $httpAcceptType         = 'application/json';
    $httpContentType        = 'application/json';

    if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
        $httpHeaderRequest = [
            "Authorization: Bearer {$httpAuthorisationType}",
            "Accept: {$httpAcceptType}",
            "Content-Type: {$httpContentType}",
        ];

        $customSongUrl = "https://osu.ppy.sh/api/v2/beatmaps/{$id}";

        # CURL session will be handled manually through curl_setopt()
        $customSongCurlHandle = curl_init(url: null);

        curl_setopt(handle: $customSongCurlHandle, option: CURLOPT_URL, value: $customSongUrl);
        curl_setopt(handle: $customSongCurlHandle, option: CURLOPT_HTTPHEADER, value: $httpHeaderRequest);
        curl_setopt(handle: $customSongCurlHandle, option: CURLOPT_HEADER, value: 0);
        curl_setopt(handle: $customSongCurlHandle, option: CURLOPT_RETURNTRANSFER, value: 1);

        $customSongCurlResponse = curl_exec(handle: $customSongCurlHandle);

        if (curl_errno(handle: $customSongCurlHandle)) {
            error_log(curl_error(handle: $customSongCurlHandle));
            curl_close(handle: $customSongCurlHandle);
            return false; // An error occurred during the API call

        } else {
            $customSongReadableData = json_decode(
                json: $customSongCurlResponse,
                associative: true,
                depth: 512,
                flags: 0
            );

            curl_close(handle: $customSongCurlHandle);
            return $customSongReadableData;
        }
    } else {
        $httpHeaderRequest = array(
            "Authorization: Bearer {$httpAuthorisationType}",
            "Accept: {$httpAcceptType}",
            "Content-Type: {$httpContentType}",
        );

        $customSongUrl = "https://osu.ppy.sh/api/v2/beatmaps/{$id}";

        # CURL session will be handled manually through curl_setopt()
        $customSongCurlHandle = curl_init(url: null);

        curl_setopt(handle: $customSongCurlHandle, option: CURLOPT_URL, value: $customSongUrl);
        curl_setopt(handle: $customSongCurlHandle, option: CURLOPT_HTTPHEADER, value: $httpHeaderRequest);
        curl_setopt(handle: $customSongCurlHandle, option: CURLOPT_HEADER, value: 0);
        curl_setopt(handle: $customSongCurlHandle, option: CURLOPT_RETURNTRANSFER, value: 1);

        $customSongCurlResponse = curl_exec(handle: $customSongCurlHandle);

        if (curl_errno(handle: $customSongCurlHandle)) {
            error_log(curl_error(handle: $customSongCurlHandle));
            curl_close(handle: $customSongCurlHandle);
            return false; // An error occurred during the API call

        } else {
            $customSongReadableData = json_decode(
                json: $customSongCurlResponse,
                associative: true,
                depth: 512,
                flags: 0
            );

            curl_close(handle: $customSongCurlHandle);
            return $customSongReadableData;
        }
    }
}
