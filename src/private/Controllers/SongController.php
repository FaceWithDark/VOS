<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);


function getTournamentCustomSong(
    string $name
): array {
    $customSongJsonData     = __DIR__ . '/../Datas/Song/CustomSongData.json';

    $allCustomSongData      = [];

    $customSongAccessToken  = $_COOKIE['vot_access_token'];

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

    switch ($name) {
        case 'DEFAULT':
            /*** VOT4 CUSTOM SONG DATA ***/
            foreach ($customSongReadableJsonData['vot4'] as $vot4CustomSongJsonData) {
                /*
                Because filter custom song data by default is basically fetching
                all custom song information within the database of a specific
                tournament, so I'll just being a bit hacky here by reading the
                data for each individual tournament straight away. This will
                prevent me having to create a dedicated 'default' tournament
                type in the database table that just basically the sum of all
                other type, which unesscesary increase the database size.
                */
                $vot4CustomSongRoundData    = $vot4CustomSongJsonData['custom_song_round'];
                $vot4CustomSongIdJsonData   = $vot4CustomSongJsonData['custom_song_id'];
                $vot4CustomSongModData      = $vot4CustomSongJsonData['custom_song_mod'];

                $vot4CustomSongData = getTournamentCustomSongData(
                    id: $vot4CustomSongIdJsonData,
                    token: $customSongAccessToken
                );

                $vot4CustomSongId                   = $vot4CustomSongData['id'];
                $vot4CustomSongRoundId              = $vot4CustomSongRoundData;
                $vot4CustomSongTournamentId         = 'VOT4';
                $vot4CustomSongType                 = $vot4CustomSongModData;
                $vot4CustomSongImage                = $vot4CustomSongData['beatmapset']['covers']['cover'];
                $vot4CustomSongUrl                  = $vot4CustomSongData['url'];
                $vot4CustomSongName                 = $vot4CustomSongData['beatmapset']['title'];
                $vot4CustomSongDifficultyName       = $vot4CustomSongData['version'];
                $vot4CustomSongFeatureArtist        = $vot4CustomSongData['beatmapset']['artist'];
                $vot4CustomSongMapper               = $vot4CustomSongData['beatmapset']['creator'];
                $vot4CustomSongMapperUrl            = "https://osu.ppy.sh/users/{$vot4CustomSongData['beatmapset']['user_id']}";
                $vot4CustomSongDifficulty           = $vot4CustomSongData['difficulty_rating'];
                $vot4CustomSongLength               = $vot4CustomSongData['total_length'];
                $vot4CustomSongOverallSpeed         = $vot4CustomSongData['beatmapset']['bpm'];
                $vot4CustomSongOverallDifficulty    = $vot4CustomSongData['accuracy'];
                $vot4CustomSongOverallHealth        = $vot4CustomSongData['drain'];
                $vot4CustomSongPassCount            = $vot4CustomSongData['passcount'];
                $vot4CustomSongIndicator            = true;

                $allCustomSongData[] = [
                    'custom_song_id'                    => $vot4CustomSongId,
                    'custom_song_round_id'              => $vot4CustomSongRoundId,
                    'custom_song_tournament_id'         => $vot4CustomSongTournamentId,
                    'custom_song_type'                  => $vot4CustomSongType,
                    'custom_song_image'                 => $vot4CustomSongImage,
                    'custom_song_url'                   => $vot4CustomSongUrl,
                    'custom_song_name'                  => $vot4CustomSongName,
                    'custom_song_difficulty_name'       => $vot4CustomSongDifficultyName,
                    'custom_song_feature_artist'        => $vot4CustomSongFeatureArtist,
                    'custom_song_mapper'                => $vot4CustomSongMapper,
                    'custom_song_mapper_url'            => $vot4CustomSongMapperUrl,
                    'custom_song_difficulty'            => $vot4CustomSongDifficulty,
                    'custom_song_length'                => $vot4CustomSongLength,
                    'custom_song_overall_speed'         => $vot4CustomSongOverallSpeed,
                    'custom_song_overall_difficulty'    => $vot4CustomSongOverallDifficulty,
                    'custom_song_overall_health'        => $vot4CustomSongOverallHealth,
                    'custom_song_pass_count'            => $vot4CustomSongPassCount,
                    'custom_song_indicator'             => $vot4CustomSongIndicator
                ];
            }
            break;

        case 'VOT4':
            break;

        case 'VOT3':
            break;

        case 'VOT2':
            break;

        case 'VOT1':
            break;

        default:
            # code...
            break;
    }

    getCustomSongData(data: $allCustomSongData);

    return [
        $allCustomSongData
    ];
}


function getTournamentCustomSongData(
    int $id,
    string $token
): array | bool {
    $httpAuthorisationType  = $token;
    $httpAcceptType         = 'application/json';
    $httpContentType        = 'application/json';
    $customSongUrl          = "https://osu.ppy.sh/api/v2/beatmaps/{$id}";

    if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
        $httpHeaderRequest = [
            "Authorization: Bearer {$httpAuthorisationType}",
            "Accept: {$httpAcceptType}",
            "Content-Type: {$httpContentType}",
        ];

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
