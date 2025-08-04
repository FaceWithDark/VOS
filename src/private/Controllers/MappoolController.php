<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);


function getTournamentMappool(
    string $name,
    string $round
): array {
    $mappoolJsonData = __DIR__ . '/../Datas/Tournament/VotMappoolData.json';

    $allMappoolNoModData            = [];
    $allMappoolHiddenData           = [];
    $allMappoolHardRockData         = [];
    $allMappoolDoubleTimeData       = [];
    $allMappoolFreeModData          = [];
    $allMappoolEasyData             = [];
    $allMappoolHiddenHardRockData   = [];
    $allMappoolTieBreakerData       = [];

    $mappoolAccessToken = $_COOKIE['vot_access_token'];

    switch ($name) {
        case 'vot1':
        case 'vot2':
        case 'vot3':
        case 'vot4':
        case 'vot5':
            $mappoolViewableJsonData = file_get_contents(
                filename: $mappoolJsonData,
                use_include_path: false,
                context: null,
                offset: 0,
                length: null
            );

            $mappoolReadableJsonData = json_decode(
                json: $mappoolViewableJsonData,
                associative: true
            );

            foreach ($mappoolReadableJsonData as $mappoolRoundJsonData) {
                switch ($round) {
                    case 'QLF':
                        /*** QUALIFIER NM BEATMAP DATA ***/
                        $qualifierNoModJsonData = $mappoolRoundJsonData[$round]['NM'];
                        foreach ($qualifierNoModJsonData as $qualifierNoModJsonType => $qualifierNoModJsonId) {
                            $qualifierNoModData = getTournamentMappoolData(
                                id: $qualifierNoModJsonId,
                                token: $mappoolAccessToken
                            );

                            $qualifierNoModId                 = $qualifierNoModData['id'];
                            $qualifierNoModRoundId            = $round;
                            $qualifierNoModTournamentId       = strtoupper(string: $name);
                            $qualifierNoModType               = $qualifierNoModJsonType;
                            $qualifierNoModImage              = $qualifierNoModData['beatmapset']['covers']['cover'];
                            $qualifierNoModUrl                = $qualifierNoModData['url'];
                            $qualifierNoModName               = $qualifierNoModData['beatmapset']['title'];
                            $qualifierNoModDifficultyName     = $qualifierNoModData['version'];
                            $qualifierNoModFeatureArtist      = $qualifierNoModData['beatmapset']['artist'];
                            $qualifierNoModMapper             = $qualifierNoModData['beatmapset']['creator'];
                            $qualifierNoModMapperUrl          = "https://osu.ppy.sh/users/{$qualifierNoModData['beatmapset']['user_id']}";
                            $qualifierNoModDifficulty         = $qualifierNoModData['difficulty_rating'];
                            $qualifierNoModLength             = $qualifierNoModData['total_length'];
                            $qualifierNoModOverallSpeed       = $qualifierNoModData['beatmapset']['bpm'];
                            $qualifierNoModOverallDifficulty  = $qualifierNoModData['accuracy'];
                            $qualifierNoModOverallHealth      = $qualifierNoModData['drain'];
                            $qualifierNoModPassCount          = $qualifierNoModData['passcount'];

                            $allMappoolNoModData[] = [
                                'beatmap_id'                    => $qualifierNoModId,
                                'beatmap_round_id'              => $qualifierNoModRoundId,
                                'beatmap_tournament_id'         => $qualifierNoModTournamentId,
                                'beatmap_type'                  => $qualifierNoModType,
                                'beatmap_image'                 => $qualifierNoModImage,
                                'beatmap_url'                   => $qualifierNoModUrl,
                                'beatmap_name'                  => $qualifierNoModName,
                                'beatmap_difficulty_name'       => $qualifierNoModDifficultyName,
                                'beatmap_feature_artist'        => $qualifierNoModFeatureArtist,
                                'beatmap_mapper'                => $qualifierNoModMapper,
                                'beatmap_mapper_url'            => $qualifierNoModMapperUrl,
                                'beatmap_difficulty'            => $qualifierNoModDifficulty,
                                'beatmap_length'                => $qualifierNoModLength,
                                'beatmap_overall_speed'         => $qualifierNoModOverallSpeed,
                                'beatmap_overall_difficulty'    => $qualifierNoModOverallDifficulty,
                                'beatmap_overall_health'        => $qualifierNoModOverallHealth,
                                'beatmap_pass_count'            => $qualifierNoModPassCount
                            ];
                        }


                        /*** QUALIFIER HD BEATMAP DATA ***/
                        $qualifierHiddenJsonData = $mappoolRoundJsonData[$round]['HD'];
                        foreach ($qualifierHiddenJsonData as $qualifierHiddenJsonType => $qualifierHiddenJsonId) {
                            $qualifierHiddenData = getTournamentMappoolData(
                                id: $qualifierHiddenJsonId,
                                token: $mappoolAccessToken
                            );

                            $qualifierHiddenId                 = $qualifierHiddenData['id'];
                            $qualifierHiddenRoundId            = $round;
                            $qualifierHiddenTournamentId       = strtoupper(string: $name);
                            $qualifierHiddenType               = $qualifierHiddenJsonType;
                            $qualifierHiddenImage              = $qualifierHiddenData['beatmapset']['covers']['cover'];
                            $qualifierHiddenUrl                = $qualifierHiddenData['url'];
                            $qualifierHiddenName               = $qualifierHiddenData['beatmapset']['title'];
                            $qualifierHiddenDifficultyName     = $qualifierHiddenData['version'];
                            $qualifierHiddenFeatureArtist      = $qualifierHiddenData['beatmapset']['artist'];
                            $qualifierHiddenMapper             = $qualifierHiddenData['beatmapset']['creator'];
                            $qualifierHiddenMapperUrl          = "https://osu.ppy.sh/users/{$qualifierHiddenData['beatmapset']['user_id']}";
                            $qualifierHiddenDifficulty         = $qualifierHiddenData['difficulty_rating'];
                            $qualifierHiddenLength             = $qualifierHiddenData['total_length'];
                            $qualifierHiddenOverallSpeed       = $qualifierHiddenData['beatmapset']['bpm'];
                            $qualifierHiddenOverallDifficulty  = $qualifierHiddenData['accuracy'];
                            $qualifierHiddenOverallHealth      = $qualifierHiddenData['drain'];
                            $qualifierHiddenPassCount          = $qualifierHiddenData['passcount'];

                            $allMappoolHiddenData[] = [
                                'beatmap_id'                    => $qualifierHiddenId,
                                'beatmap_round_id'              => $qualifierHiddenRoundId,
                                'beatmap_tournament_id'         => $qualifierHiddenTournamentId,
                                'beatmap_type'                  => $qualifierHiddenType,
                                'beatmap_image'                 => $qualifierHiddenImage,
                                'beatmap_url'                   => $qualifierHiddenUrl,
                                'beatmap_name'                  => $qualifierHiddenName,
                                'beatmap_difficulty_name'       => $qualifierHiddenDifficultyName,
                                'beatmap_feature_artist'        => $qualifierHiddenFeatureArtist,
                                'beatmap_mapper'                => $qualifierHiddenMapper,
                                'beatmap_mapper_url'            => $qualifierHiddenMapperUrl,
                                'beatmap_difficulty'            => $qualifierHiddenDifficulty,
                                'beatmap_length'                => $qualifierHiddenLength,
                                'beatmap_overall_speed'         => $qualifierHiddenOverallSpeed,
                                'beatmap_overall_difficulty'    => $qualifierHiddenOverallDifficulty,
                                'beatmap_overall_health'        => $qualifierHiddenOverallHealth,
                                'beatmap_pass_count'            => $qualifierHiddenPassCount
                            ];
                        }


                        /*** QUALIFIER HR BEATMAP DATA ***/
                        $qualifierHardRockJsonData = $mappoolRoundJsonData[$round]['HR'];
                        foreach ($qualifierHardRockJsonData as $qualifierHardRockJsonType => $qualifierHardRockJsonId) {
                            $qualifierHardRockData = getTournamentMappoolData(
                                id: $qualifierHardRockJsonId,
                                token: $mappoolAccessToken
                            );

                            $qualifierHardRockId                 = $qualifierHardRockData['id'];
                            $qualifierHardRockRoundId            = $round;
                            $qualifierHardRockTournamentId       = strtoupper(string: $name);
                            $qualifierHardRockType               = $qualifierHardRockJsonType;
                            $qualifierHardRockImage              = $qualifierHardRockData['beatmapset']['covers']['cover'];
                            $qualifierHardRockUrl                = $qualifierHardRockData['url'];
                            $qualifierHardRockName               = $qualifierHardRockData['beatmapset']['title'];
                            $qualifierHardRockDifficultyName     = $qualifierHardRockData['version'];
                            $qualifierHardRockFeatureArtist      = $qualifierHardRockData['beatmapset']['artist'];
                            $qualifierHardRockMapper             = $qualifierHardRockData['beatmapset']['creator'];
                            $qualifierHardRockMapperUrl          = "https://osu.ppy.sh/users/{$qualifierHardRockData['beatmapset']['user_id']}";
                            $qualifierHardRockDifficulty         = $qualifierHardRockData['difficulty_rating'];
                            $qualifierHardRockLength             = $qualifierHardRockData['total_length'];
                            $qualifierHardRockOverallSpeed       = $qualifierHardRockData['beatmapset']['bpm'];
                            $qualifierHardRockOverallDifficulty  = $qualifierHardRockData['accuracy'];
                            $qualifierHardRockOverallHealth      = $qualifierHardRockData['drain'];
                            $qualifierHardRockPassCount          = $qualifierHardRockData['passcount'];

                            $allMappoolHardRockData[] = [
                                'beatmap_id'                    => $qualifierHardRockId,
                                'beatmap_round_id'              => $qualifierHardRockRoundId,
                                'beatmap_tournament_id'         => $qualifierHardRockTournamentId,
                                'beatmap_type'                  => $qualifierHardRockType,
                                'beatmap_image'                 => $qualifierHardRockImage,
                                'beatmap_url'                   => $qualifierHardRockUrl,
                                'beatmap_name'                  => $qualifierHardRockName,
                                'beatmap_difficulty_name'       => $qualifierHardRockDifficultyName,
                                'beatmap_feature_artist'        => $qualifierHardRockFeatureArtist,
                                'beatmap_mapper'                => $qualifierHardRockMapper,
                                'beatmap_mapper_url'            => $qualifierHardRockMapperUrl,
                                'beatmap_difficulty'            => $qualifierHardRockDifficulty,
                                'beatmap_length'                => $qualifierHardRockLength,
                                'beatmap_overall_speed'         => $qualifierHardRockOverallSpeed,
                                'beatmap_overall_difficulty'    => $qualifierHardRockOverallDifficulty,
                                'beatmap_overall_health'        => $qualifierHardRockOverallHealth,
                                'beatmap_pass_count'            => $qualifierHardRockPassCount
                            ];
                        }


                        /*** QUALIFIER DT BEATMAP DATA ***/
                        $qualifierDoubleTimeJsonData = $mappoolRoundJsonData[$round]['DT'];
                        foreach ($qualifierDoubleTimeJsonData as $qualifierDoubleTimeJsonType => $qualifierDoubleTimeJsonId) {
                            $qualifierDoubleTimeData = getTournamentMappoolData(
                                id: $qualifierDoubleTimeJsonId,
                                token: $mappoolAccessToken
                            );

                            $qualifierDoubleTimeId                 = $qualifierDoubleTimeData['id'];
                            $qualifierDoubleTimeRoundId            = $round;
                            $qualifierDoubleTimeTournamentId       = strtoupper(string: $name);
                            $qualifierDoubleTimeType               = $qualifierDoubleTimeJsonType;
                            $qualifierDoubleTimeImage              = $qualifierDoubleTimeData['beatmapset']['covers']['cover'];
                            $qualifierDoubleTimeUrl                = $qualifierDoubleTimeData['url'];
                            $qualifierDoubleTimeName               = $qualifierDoubleTimeData['beatmapset']['title'];
                            $qualifierDoubleTimeDifficultyName     = $qualifierDoubleTimeData['version'];
                            $qualifierDoubleTimeFeatureArtist      = $qualifierDoubleTimeData['beatmapset']['artist'];
                            $qualifierDoubleTimeMapper             = $qualifierDoubleTimeData['beatmapset']['creator'];
                            $qualifierDoubleTimeMapperUrl          = "https://osu.ppy.sh/users/{$qualifierDoubleTimeData['beatmapset']['user_id']}";
                            $qualifierDoubleTimeDifficulty         = $qualifierDoubleTimeData['difficulty_rating'];
                            $qualifierDoubleTimeLength             = $qualifierDoubleTimeData['total_length'];
                            $qualifierDoubleTimeOverallSpeed       = $qualifierDoubleTimeData['beatmapset']['bpm'];
                            $qualifierDoubleTimeOverallDifficulty  = $qualifierDoubleTimeData['accuracy'];
                            $qualifierDoubleTimeOverallHealth      = $qualifierDoubleTimeData['drain'];
                            $qualifierDoubleTimePassCount          = $qualifierDoubleTimeData['passcount'];

                            $allMappoolDoubleTimeData[] = [
                                'beatmap_id'                    => $qualifierDoubleTimeId,
                                'beatmap_round_id'              => $qualifierDoubleTimeRoundId,
                                'beatmap_tournament_id'         => $qualifierDoubleTimeTournamentId,
                                'beatmap_type'                  => $qualifierDoubleTimeType,
                                'beatmap_image'                 => $qualifierDoubleTimeImage,
                                'beatmap_url'                   => $qualifierDoubleTimeUrl,
                                'beatmap_name'                  => $qualifierDoubleTimeName,
                                'beatmap_difficulty_name'       => $qualifierDoubleTimeDifficultyName,
                                'beatmap_feature_artist'        => $qualifierDoubleTimeFeatureArtist,
                                'beatmap_mapper'                => $qualifierDoubleTimeMapper,
                                'beatmap_mapper_url'            => $qualifierDoubleTimeMapperUrl,
                                'beatmap_difficulty'            => $qualifierDoubleTimeDifficulty,
                                'beatmap_length'                => $qualifierDoubleTimeLength,
                                'beatmap_overall_speed'         => $qualifierDoubleTimeOverallSpeed,
                                'beatmap_overall_difficulty'    => $qualifierDoubleTimeOverallDifficulty,
                                'beatmap_overall_health'        => $qualifierDoubleTimeOverallHealth,
                                'beatmap_pass_count'            => $qualifierDoubleTimePassCount
                            ];
                        }


                        /*** QUALIFIER FM BEATMAP DATA ***/
                        $qualifierFreeModJsonData = $mappoolRoundJsonData[$round]['FM'];
                        foreach ($qualifierFreeModJsonData as $qualifierFreeModJsonType => $qualifierFreeModJsonId) {
                            $qualifierFreeModData = getTournamentMappoolData(
                                id: $qualifierFreeModJsonId,
                                token: $mappoolAccessToken
                            );

                            $qualifierFreeModId                 = $qualifierFreeModData['id'];
                            $qualifierFreeModRoundId            = $round;
                            $qualifierFreeModTournamentId       = strtoupper(string: $name);
                            $qualifierFreeModType               = $qualifierFreeModJsonType;
                            $qualifierFreeModImage              = $qualifierFreeModData['beatmapset']['covers']['cover'];
                            $qualifierFreeModUrl                = $qualifierFreeModData['url'];
                            $qualifierFreeModName               = $qualifierFreeModData['beatmapset']['title'];
                            $qualifierFreeModDifficultyName     = $qualifierFreeModData['version'];
                            $qualifierFreeModFeatureArtist      = $qualifierFreeModData['beatmapset']['artist'];
                            $qualifierFreeModMapper             = $qualifierFreeModData['beatmapset']['creator'];
                            $qualifierFreeModMapperUrl          = "https://osu.ppy.sh/users/{$qualifierFreeModData['beatmapset']['user_id']}";
                            $qualifierFreeModDifficulty         = $qualifierFreeModData['difficulty_rating'];
                            $qualifierFreeModLength             = $qualifierFreeModData['total_length'];
                            $qualifierFreeModOverallSpeed       = $qualifierFreeModData['beatmapset']['bpm'];
                            $qualifierFreeModOverallDifficulty  = $qualifierFreeModData['accuracy'];
                            $qualifierFreeModOverallHealth      = $qualifierFreeModData['drain'];
                            $qualifierFreeModPassCount          = $qualifierFreeModData['passcount'];

                            $allMappoolFreeModData[] = [
                                'beatmap_id'                    => $qualifierFreeModId,
                                'beatmap_round_id'              => $qualifierFreeModRoundId,
                                'beatmap_tournament_id'         => $qualifierFreeModTournamentId,
                                'beatmap_type'                  => $qualifierFreeModType,
                                'beatmap_image'                 => $qualifierFreeModImage,
                                'beatmap_url'                   => $qualifierFreeModUrl,
                                'beatmap_name'                  => $qualifierFreeModName,
                                'beatmap_difficulty_name'       => $qualifierFreeModDifficultyName,
                                'beatmap_feature_artist'        => $qualifierFreeModFeatureArtist,
                                'beatmap_mapper'                => $qualifierFreeModMapper,
                                'beatmap_mapper_url'            => $qualifierFreeModMapperUrl,
                                'beatmap_difficulty'            => $qualifierFreeModDifficulty,
                                'beatmap_length'                => $qualifierFreeModLength,
                                'beatmap_overall_speed'         => $qualifierFreeModOverallSpeed,
                                'beatmap_overall_difficulty'    => $qualifierFreeModOverallDifficulty,
                                'beatmap_overall_health'        => $qualifierFreeModOverallHealth,
                                'beatmap_pass_count'            => $qualifierFreeModPassCount
                            ];
                        }
                        break;

                    case 'RO16':
                        /*** ROUND OF 16 NM BEATMAP DATA ***/
                        $roundOf16NoModJsonData = $mappoolRoundJsonData[$round]['NM'];
                        foreach ($roundOf16NoModJsonData as $roundOf16NoModJsonType => $roundOf16NoModJsonId) {
                            $roundOf16NoModData = getTournamentMappoolData(
                                id: $roundOf16NoModJsonId,
                                token: $mappoolAccessToken
                            );

                            $roundOf16NoModId                 = $roundOf16NoModData['id'];
                            $roundOf16NoModRoundId            = $round;
                            $roundOf16NoModTournamentId       = strtoupper(string: $name);
                            $roundOf16NoModType               = $roundOf16NoModJsonType;
                            $roundOf16NoModImage              = $roundOf16NoModData['beatmapset']['covers']['cover'];
                            $roundOf16NoModUrl                = $roundOf16NoModData['url'];
                            $roundOf16NoModName               = $roundOf16NoModData['beatmapset']['title'];
                            $roundOf16NoModDifficultyName     = $roundOf16NoModData['version'];
                            $roundOf16NoModFeatureArtist      = $roundOf16NoModData['beatmapset']['artist'];
                            $roundOf16NoModMapper             = $roundOf16NoModData['beatmapset']['creator'];
                            $roundOf16NoModMapperUrl          = "https://osu.ppy.sh/users/{$roundOf16NoModData['beatmapset']['user_id']}";
                            $roundOf16NoModDifficulty         = $roundOf16NoModData['difficulty_rating'];
                            $roundOf16NoModLength             = $roundOf16NoModData['total_length'];
                            $roundOf16NoModOverallSpeed       = $roundOf16NoModData['beatmapset']['bpm'];
                            $roundOf16NoModOverallDifficulty  = $roundOf16NoModData['accuracy'];
                            $roundOf16NoModOverallHealth      = $roundOf16NoModData['drain'];
                            $roundOf16NoModPassCount          = $roundOf16NoModData['passcount'];

                            $allMappoolNoModData[] = [
                                'beatmap_id'                    => $roundOf16NoModId,
                                'beatmap_round_id'              => $roundOf16NoModRoundId,
                                'beatmap_tournament_id'         => $roundOf16NoModTournamentId,
                                'beatmap_type'                  => $roundOf16NoModType,
                                'beatmap_image'                 => $roundOf16NoModImage,
                                'beatmap_url'                   => $roundOf16NoModUrl,
                                'beatmap_name'                  => $roundOf16NoModName,
                                'beatmap_difficulty_name'       => $roundOf16NoModDifficultyName,
                                'beatmap_feature_artist'        => $roundOf16NoModFeatureArtist,
                                'beatmap_mapper'                => $roundOf16NoModMapper,
                                'beatmap_mapper_url'            => $roundOf16NoModMapperUrl,
                                'beatmap_difficulty'            => $roundOf16NoModDifficulty,
                                'beatmap_length'                => $roundOf16NoModLength,
                                'beatmap_overall_speed'         => $roundOf16NoModOverallSpeed,
                                'beatmap_overall_difficulty'    => $roundOf16NoModOverallDifficulty,
                                'beatmap_overall_health'        => $roundOf16NoModOverallHealth,
                                'beatmap_pass_count'            => $roundOf16NoModPassCount
                            ];
                        }


                        /*** ROUND OF 16 HD BEATMAP DATA ***/
                        $roundOf16HiddenJsonData = $mappoolRoundJsonData[$round]['HD'];
                        foreach ($roundOf16HiddenJsonData as $roundOf16HiddenJsonType => $roundOf16HiddenJsonId) {
                            $roundOf16HiddenData = getTournamentMappoolData(
                                id: $roundOf16HiddenJsonId,
                                token: $mappoolAccessToken
                            );

                            $roundOf16HiddenId                 = $roundOf16HiddenData['id'];
                            $roundOf16HiddenRoundId            = $round;
                            $roundOf16HiddenTournamentId       = strtoupper(string: $name);
                            $roundOf16HiddenType               = $roundOf16HiddenJsonType;
                            $roundOf16HiddenImage              = $roundOf16HiddenData['beatmapset']['covers']['cover'];
                            $roundOf16HiddenUrl                = $roundOf16HiddenData['url'];
                            $roundOf16HiddenName               = $roundOf16HiddenData['beatmapset']['title'];
                            $roundOf16HiddenDifficultyName     = $roundOf16HiddenData['version'];
                            $roundOf16HiddenFeatureArtist      = $roundOf16HiddenData['beatmapset']['artist'];
                            $roundOf16HiddenMapper             = $roundOf16HiddenData['beatmapset']['creator'];
                            $roundOf16HiddenMapperUrl          = "https://osu.ppy.sh/users/{$roundOf16HiddenData['beatmapset']['user_id']}";
                            $roundOf16HiddenDifficulty         = $roundOf16HiddenData['difficulty_rating'];
                            $roundOf16HiddenLength             = $roundOf16HiddenData['total_length'];
                            $roundOf16HiddenOverallSpeed       = $roundOf16HiddenData['beatmapset']['bpm'];
                            $roundOf16HiddenOverallDifficulty  = $roundOf16HiddenData['accuracy'];
                            $roundOf16HiddenOverallHealth      = $roundOf16HiddenData['drain'];
                            $roundOf16HiddenPassCount          = $roundOf16HiddenData['passcount'];

                            $allMappoolHiddenData[] = [
                                'beatmap_id'                    => $roundOf16HiddenId,
                                'beatmap_round_id'              => $roundOf16HiddenRoundId,
                                'beatmap_tournament_id'         => $roundOf16HiddenTournamentId,
                                'beatmap_type'                  => $roundOf16HiddenType,
                                'beatmap_image'                 => $roundOf16HiddenImage,
                                'beatmap_url'                   => $roundOf16HiddenUrl,
                                'beatmap_name'                  => $roundOf16HiddenName,
                                'beatmap_difficulty_name'       => $roundOf16HiddenDifficultyName,
                                'beatmap_feature_artist'        => $roundOf16HiddenFeatureArtist,
                                'beatmap_mapper'                => $roundOf16HiddenMapper,
                                'beatmap_mapper_url'            => $roundOf16HiddenMapperUrl,
                                'beatmap_difficulty'            => $roundOf16HiddenDifficulty,
                                'beatmap_length'                => $roundOf16HiddenLength,
                                'beatmap_overall_speed'         => $roundOf16HiddenOverallSpeed,
                                'beatmap_overall_difficulty'    => $roundOf16HiddenOverallDifficulty,
                                'beatmap_overall_health'        => $roundOf16HiddenOverallHealth,
                                'beatmap_pass_count'            => $roundOf16HiddenPassCount
                            ];
                        }


                        /*** ROUND OF 16 HR BEATMAP DATA ***/
                        $roundOf16HardRockJsonData = $mappoolRoundJsonData[$round]['HR'];
                        foreach ($roundOf16HardRockJsonData as $roundOf16HardRockJsonType => $roundOf16HardRockJsonId) {
                            $roundOf16HardRockData = getTournamentMappoolData(
                                id: $roundOf16HardRockJsonId,
                                token: $mappoolAccessToken
                            );

                            $roundOf16HardRockId                 = $roundOf16HardRockData['id'];
                            $roundOf16HardRockRoundId            = $round;
                            $roundOf16HardRockTournamentId       = strtoupper(string: $name);
                            $roundOf16HardRockType               = $roundOf16HardRockJsonType;
                            $roundOf16HardRockImage              = $roundOf16HardRockData['beatmapset']['covers']['cover'];
                            $roundOf16HardRockUrl                = $roundOf16HardRockData['url'];
                            $roundOf16HardRockName               = $roundOf16HardRockData['beatmapset']['title'];
                            $roundOf16HardRockDifficultyName     = $roundOf16HardRockData['version'];
                            $roundOf16HardRockFeatureArtist      = $roundOf16HardRockData['beatmapset']['artist'];
                            $roundOf16HardRockMapper             = $roundOf16HardRockData['beatmapset']['creator'];
                            $roundOf16HardRockMapperUrl          = "https://osu.ppy.sh/users/{$roundOf16HardRockData['beatmapset']['user_id']}";
                            $roundOf16HardRockDifficulty         = $roundOf16HardRockData['difficulty_rating'];
                            $roundOf16HardRockLength             = $roundOf16HardRockData['total_length'];
                            $roundOf16HardRockOverallSpeed       = $roundOf16HardRockData['beatmapset']['bpm'];
                            $roundOf16HardRockOverallDifficulty  = $roundOf16HardRockData['accuracy'];
                            $roundOf16HardRockOverallHealth      = $roundOf16HardRockData['drain'];
                            $roundOf16HardRockPassCount          = $roundOf16HardRockData['passcount'];

                            $allMappoolHardRockData[] = [
                                'beatmap_id'                    => $roundOf16HardRockId,
                                'beatmap_round_id'              => $roundOf16HardRockRoundId,
                                'beatmap_tournament_id'         => $roundOf16HardRockTournamentId,
                                'beatmap_type'                  => $roundOf16HardRockType,
                                'beatmap_image'                 => $roundOf16HardRockImage,
                                'beatmap_url'                   => $roundOf16HardRockUrl,
                                'beatmap_name'                  => $roundOf16HardRockName,
                                'beatmap_difficulty_name'       => $roundOf16HardRockDifficultyName,
                                'beatmap_feature_artist'        => $roundOf16HardRockFeatureArtist,
                                'beatmap_mapper'                => $roundOf16HardRockMapper,
                                'beatmap_mapper_url'            => $roundOf16HardRockMapperUrl,
                                'beatmap_difficulty'            => $roundOf16HardRockDifficulty,
                                'beatmap_length'                => $roundOf16HardRockLength,
                                'beatmap_overall_speed'         => $roundOf16HardRockOverallSpeed,
                                'beatmap_overall_difficulty'    => $roundOf16HardRockOverallDifficulty,
                                'beatmap_overall_health'        => $roundOf16HardRockOverallHealth,
                                'beatmap_pass_count'            => $roundOf16HardRockPassCount
                            ];
                        }


                        /*** ROUND OF 16 DT BEATMAP DATA ***/
                        $roundOf16DoubleTimeJsonData = $mappoolRoundJsonData[$round]['DT'];
                        foreach ($roundOf16DoubleTimeJsonData as $roundOf16DoubleTimeJsonType => $roundOf16DoubleTimeJsonId) {
                            $roundOf16DoubleTimeData = getTournamentMappoolData(
                                id: $roundOf16DoubleTimeJsonId,
                                token: $mappoolAccessToken
                            );

                            $roundOf16DoubleTimeId                 = $roundOf16DoubleTimeData['id'];
                            $roundOf16DoubleTimeRoundId            = $round;
                            $roundOf16DoubleTimeTournamentId       = strtoupper(string: $name);
                            $roundOf16DoubleTimeType               = $roundOf16DoubleTimeJsonType;
                            $roundOf16DoubleTimeImage              = $roundOf16DoubleTimeData['beatmapset']['covers']['cover'];
                            $roundOf16DoubleTimeUrl                = $roundOf16DoubleTimeData['url'];
                            $roundOf16DoubleTimeName               = $roundOf16DoubleTimeData['beatmapset']['title'];
                            $roundOf16DoubleTimeDifficultyName     = $roundOf16DoubleTimeData['version'];
                            $roundOf16DoubleTimeFeatureArtist      = $roundOf16DoubleTimeData['beatmapset']['artist'];
                            $roundOf16DoubleTimeMapper             = $roundOf16DoubleTimeData['beatmapset']['creator'];
                            $roundOf16DoubleTimeMapperUrl          = "https://osu.ppy.sh/users/{$roundOf16DoubleTimeData['beatmapset']['user_id']}";
                            $roundOf16DoubleTimeDifficulty         = $roundOf16DoubleTimeData['difficulty_rating'];
                            $roundOf16DoubleTimeLength             = $roundOf16DoubleTimeData['total_length'];
                            $roundOf16DoubleTimeOverallSpeed       = $roundOf16DoubleTimeData['beatmapset']['bpm'];
                            $roundOf16DoubleTimeOverallDifficulty  = $roundOf16DoubleTimeData['accuracy'];
                            $roundOf16DoubleTimeOverallHealth      = $roundOf16DoubleTimeData['drain'];
                            $roundOf16DoubleTimePassCount          = $roundOf16DoubleTimeData['passcount'];

                            $allMappoolDoubleTimeData[] = [
                                'beatmap_id'                    => $roundOf16DoubleTimeId,
                                'beatmap_round_id'              => $roundOf16DoubleTimeRoundId,
                                'beatmap_tournament_id'         => $roundOf16DoubleTimeTournamentId,
                                'beatmap_type'                  => $roundOf16DoubleTimeType,
                                'beatmap_image'                 => $roundOf16DoubleTimeImage,
                                'beatmap_url'                   => $roundOf16DoubleTimeUrl,
                                'beatmap_name'                  => $roundOf16DoubleTimeName,
                                'beatmap_difficulty_name'       => $roundOf16DoubleTimeDifficultyName,
                                'beatmap_feature_artist'        => $roundOf16DoubleTimeFeatureArtist,
                                'beatmap_mapper'                => $roundOf16DoubleTimeMapper,
                                'beatmap_mapper_url'            => $roundOf16DoubleTimeMapperUrl,
                                'beatmap_difficulty'            => $roundOf16DoubleTimeDifficulty,
                                'beatmap_length'                => $roundOf16DoubleTimeLength,
                                'beatmap_overall_speed'         => $roundOf16DoubleTimeOverallSpeed,
                                'beatmap_overall_difficulty'    => $roundOf16DoubleTimeOverallDifficulty,
                                'beatmap_overall_health'        => $roundOf16DoubleTimeOverallHealth,
                                'beatmap_pass_count'            => $roundOf16DoubleTimePassCount
                            ];
                        }


                        /*** ROUND OF 16 FM BEATMAP DATA ***/
                        $roundOf16FreeModJsonData = $mappoolRoundJsonData[$round]['FM'];
                        foreach ($roundOf16FreeModJsonData as $roundOf16FreeModJsonType => $roundOf16FreeModJsonId) {
                            $roundOf16FreeModData = getTournamentMappoolData(
                                id: $roundOf16FreeModJsonId,
                                token: $mappoolAccessToken
                            );

                            $roundOf16FreeModId                 = $roundOf16FreeModData['id'];
                            $roundOf16FreeModRoundId            = $round;
                            $roundOf16FreeModTournamentId       = strtoupper(string: $name);
                            $roundOf16FreeModType               = $roundOf16FreeModJsonType;
                            $roundOf16FreeModImage              = $roundOf16FreeModData['beatmapset']['covers']['cover'];
                            $roundOf16FreeModUrl                = $roundOf16FreeModData['url'];
                            $roundOf16FreeModName               = $roundOf16FreeModData['beatmapset']['title'];
                            $roundOf16FreeModDifficultyName     = $roundOf16FreeModData['version'];
                            $roundOf16FreeModFeatureArtist      = $roundOf16FreeModData['beatmapset']['artist'];
                            $roundOf16FreeModMapper             = $roundOf16FreeModData['beatmapset']['creator'];
                            $roundOf16FreeModMapperUrl          = "https://osu.ppy.sh/users/{$roundOf16FreeModData['beatmapset']['user_id']}";
                            $roundOf16FreeModDifficulty         = $roundOf16FreeModData['difficulty_rating'];
                            $roundOf16FreeModLength             = $roundOf16FreeModData['total_length'];
                            $roundOf16FreeModOverallSpeed       = $roundOf16FreeModData['beatmapset']['bpm'];
                            $roundOf16FreeModOverallDifficulty  = $roundOf16FreeModData['accuracy'];
                            $roundOf16FreeModOverallHealth      = $roundOf16FreeModData['drain'];
                            $roundOf16FreeModPassCount          = $roundOf16FreeModData['passcount'];

                            $allMappoolFreeModData[] = [
                                'beatmap_id'                    => $roundOf16FreeModId,
                                'beatmap_round_id'              => $roundOf16FreeModRoundId,
                                'beatmap_tournament_id'         => $roundOf16FreeModTournamentId,
                                'beatmap_type'                  => $roundOf16FreeModType,
                                'beatmap_image'                 => $roundOf16FreeModImage,
                                'beatmap_url'                   => $roundOf16FreeModUrl,
                                'beatmap_name'                  => $roundOf16FreeModName,
                                'beatmap_difficulty_name'       => $roundOf16FreeModDifficultyName,
                                'beatmap_feature_artist'        => $roundOf16FreeModFeatureArtist,
                                'beatmap_mapper'                => $roundOf16FreeModMapper,
                                'beatmap_mapper_url'            => $roundOf16FreeModMapperUrl,
                                'beatmap_difficulty'            => $roundOf16FreeModDifficulty,
                                'beatmap_length'                => $roundOf16FreeModLength,
                                'beatmap_overall_speed'         => $roundOf16FreeModOverallSpeed,
                                'beatmap_overall_difficulty'    => $roundOf16FreeModOverallDifficulty,
                                'beatmap_overall_health'        => $roundOf16FreeModOverallHealth,
                                'beatmap_pass_count'            => $roundOf16FreeModPassCount
                            ];
                        }


                        /*** ROUND OF 16 EZ BEATMAP DATA ***/
                        $roundOf16EasyJsonData = $mappoolRoundJsonData[$round]['FM'];
                        foreach ($roundOf16EasyJsonData as $roundOf16EasyJsonType => $roundOf16EasyJsonId) {
                            $roundOf16EasyData = getTournamentMappoolData(
                                id: $roundOf16EasyJsonId,
                                token: $mappoolAccessToken
                            );

                            $roundOf16EasyId                 = $roundOf16EasyData['id'];
                            $roundOf16EasyRoundId            = $round;
                            $roundOf16EasyTournamentId       = strtoupper(string: $name);
                            $roundOf16EasyType               = $roundOf16EasyJsonType;
                            $roundOf16EasyImage              = $roundOf16EasyData['beatmapset']['covers']['cover'];
                            $roundOf16EasyUrl                = $roundOf16EasyData['url'];
                            $roundOf16EasyName               = $roundOf16EasyData['beatmapset']['title'];
                            $roundOf16EasyDifficultyName     = $roundOf16EasyData['version'];
                            $roundOf16EasyFeatureArtist      = $roundOf16EasyData['beatmapset']['artist'];
                            $roundOf16EasyMapper             = $roundOf16EasyData['beatmapset']['creator'];
                            $roundOf16EasyMapperUrl          = "https://osu.ppy.sh/users/{$roundOf16EasyData['beatmapset']['user_id']}";
                            $roundOf16EasyDifficulty         = $roundOf16EasyData['difficulty_rating'];
                            $roundOf16EasyLength             = $roundOf16EasyData['total_length'];
                            $roundOf16EasyOverallSpeed       = $roundOf16EasyData['beatmapset']['bpm'];
                            $roundOf16EasyOverallDifficulty  = $roundOf16EasyData['accuracy'];
                            $roundOf16EasyOverallHealth      = $roundOf16EasyData['drain'];
                            $roundOf16EasyPassCount          = $roundOf16EasyData['passcount'];

                            $allMappoolEasyData[] = [
                                'beatmap_id'                    => $roundOf16EasyId,
                                'beatmap_round_id'              => $roundOf16EasyRoundId,
                                'beatmap_tournament_id'         => $roundOf16EasyTournamentId,
                                'beatmap_type'                  => $roundOf16EasyType,
                                'beatmap_image'                 => $roundOf16EasyImage,
                                'beatmap_url'                   => $roundOf16EasyUrl,
                                'beatmap_name'                  => $roundOf16EasyName,
                                'beatmap_difficulty_name'       => $roundOf16EasyDifficultyName,
                                'beatmap_feature_artist'        => $roundOf16EasyFeatureArtist,
                                'beatmap_mapper'                => $roundOf16EasyMapper,
                                'beatmap_mapper_url'            => $roundOf16EasyMapperUrl,
                                'beatmap_difficulty'            => $roundOf16EasyDifficulty,
                                'beatmap_length'                => $roundOf16EasyLength,
                                'beatmap_overall_speed'         => $roundOf16EasyOverallSpeed,
                                'beatmap_overall_difficulty'    => $roundOf16EasyOverallDifficulty,
                                'beatmap_overall_health'        => $roundOf16EasyOverallHealth,
                                'beatmap_pass_count'            => $roundOf16EasyPassCount
                            ];
                        }

                        /*** ROUND OF 16 HDHR BEATMAP DATA ***/
                        $roundOf16HiddenHardRockJsonData = $mappoolRoundJsonData[$round]['FM'];
                        foreach ($roundOf16HiddenHardRockJsonData as $roundOf16HiddenHardRockJsonType => $roundOf16HiddenHardRockJsonId) {
                            $roundOf16HiddenHardRockData = getTournamentMappoolData(
                                id: $roundOf16HiddenHardRockJsonId,
                                token: $mappoolAccessToken
                            );

                            $roundOf16HiddenHardRockId                 = $roundOf16HiddenHardRockData['id'];
                            $roundOf16HiddenHardRockRoundId            = $round;
                            $roundOf16HiddenHardRockTournamentId       = strtoupper(string: $name);
                            $roundOf16HiddenHardRockType               = $roundOf16HiddenHardRockJsonType;
                            $roundOf16HiddenHardRockImage              = $roundOf16HiddenHardRockData['beatmapset']['covers']['cover'];
                            $roundOf16HiddenHardRockUrl                = $roundOf16HiddenHardRockData['url'];
                            $roundOf16HiddenHardRockName               = $roundOf16HiddenHardRockData['beatmapset']['title'];
                            $roundOf16HiddenHardRockDifficultyName     = $roundOf16HiddenHardRockData['version'];
                            $roundOf16HiddenHardRockFeatureArtist      = $roundOf16HiddenHardRockData['beatmapset']['artist'];
                            $roundOf16HiddenHardRockMapper             = $roundOf16HiddenHardRockData['beatmapset']['creator'];
                            $roundOf16HiddenHardRockMapperUrl          = "https://osu.ppy.sh/users/{$roundOf16HiddenHardRockData['beatmapset']['user_id']}";
                            $roundOf16HiddenHardRockDifficulty         = $roundOf16HiddenHardRockData['difficulty_rating'];
                            $roundOf16HiddenHardRockLength             = $roundOf16HiddenHardRockData['total_length'];
                            $roundOf16HiddenHardRockOverallSpeed       = $roundOf16HiddenHardRockData['beatmapset']['bpm'];
                            $roundOf16HiddenHardRockOverallDifficulty  = $roundOf16HiddenHardRockData['accuracy'];
                            $roundOf16HiddenHardRockOverallHealth      = $roundOf16HiddenHardRockData['drain'];
                            $roundOf16HiddenHardRockPassCount          = $roundOf16HiddenHardRockData['passcount'];

                            $allMappoolHiddenHardRockData[] = [
                                'beatmap_id'                    => $roundOf16HiddenHardRockId,
                                'beatmap_round_id'              => $roundOf16HiddenHardRockRoundId,
                                'beatmap_tournament_id'         => $roundOf16HiddenHardRockTournamentId,
                                'beatmap_type'                  => $roundOf16HiddenHardRockType,
                                'beatmap_image'                 => $roundOf16HiddenHardRockImage,
                                'beatmap_url'                   => $roundOf16HiddenHardRockUrl,
                                'beatmap_name'                  => $roundOf16HiddenHardRockName,
                                'beatmap_difficulty_name'       => $roundOf16HiddenHardRockDifficultyName,
                                'beatmap_feature_artist'        => $roundOf16HiddenHardRockFeatureArtist,
                                'beatmap_mapper'                => $roundOf16HiddenHardRockMapper,
                                'beatmap_mapper_url'            => $roundOf16HiddenHardRockMapperUrl,
                                'beatmap_difficulty'            => $roundOf16HiddenHardRockDifficulty,
                                'beatmap_length'                => $roundOf16HiddenHardRockLength,
                                'beatmap_overall_speed'         => $roundOf16HiddenHardRockOverallSpeed,
                                'beatmap_overall_difficulty'    => $roundOf16HiddenHardRockOverallDifficulty,
                                'beatmap_overall_health'        => $roundOf16HiddenHardRockOverallHealth,
                                'beatmap_pass_count'            => $roundOf16HiddenHardRockPassCount
                            ];
                        }


                        /*** ROUND OF 16 TB BEATMAP DATA ***/
                        $roundOf16TieBreakerJsonData = $mappoolRoundJsonData[$round]['FM'];
                        foreach ($roundOf16TieBreakerJsonData as $roundOf16TieBreakerJsonType => $roundOf16TieBreakerJsonId) {
                            $roundOf16TieBreakerData = getTournamentMappoolData(
                                id: $roundOf16TieBreakerJsonId,
                                token: $mappoolAccessToken
                            );

                            $roundOf16TieBreakerId                 = $roundOf16TieBreakerData['id'];
                            $roundOf16TieBreakerRoundId            = $round;
                            $roundOf16TieBreakerTournamentId       = strtoupper(string: $name);
                            $roundOf16TieBreakerType               = $roundOf16TieBreakerJsonType;
                            $roundOf16TieBreakerImage              = $roundOf16TieBreakerData['beatmapset']['covers']['cover'];
                            $roundOf16TieBreakerUrl                = $roundOf16TieBreakerData['url'];
                            $roundOf16TieBreakerName               = $roundOf16TieBreakerData['beatmapset']['title'];
                            $roundOf16TieBreakerDifficultyName     = $roundOf16TieBreakerData['version'];
                            $roundOf16TieBreakerFeatureArtist      = $roundOf16TieBreakerData['beatmapset']['artist'];
                            $roundOf16TieBreakerMapper             = $roundOf16TieBreakerData['beatmapset']['creator'];
                            $roundOf16TieBreakerMapperUrl          = "https://osu.ppy.sh/users/{$roundOf16TieBreakerData['beatmapset']['user_id']}";
                            $roundOf16TieBreakerDifficulty         = $roundOf16TieBreakerData['difficulty_rating'];
                            $roundOf16TieBreakerLength             = $roundOf16TieBreakerData['total_length'];
                            $roundOf16TieBreakerOverallSpeed       = $roundOf16TieBreakerData['beatmapset']['bpm'];
                            $roundOf16TieBreakerOverallDifficulty  = $roundOf16TieBreakerData['accuracy'];
                            $roundOf16TieBreakerOverallHealth      = $roundOf16TieBreakerData['drain'];
                            $roundOf16TieBreakerPassCount          = $roundOf16TieBreakerData['passcount'];

                            $allMappoolTieBreakerData[] = [
                                'beatmap_id'                    => $roundOf16TieBreakerId,
                                'beatmap_round_id'              => $roundOf16TieBreakerRoundId,
                                'beatmap_tournament_id'         => $roundOf16TieBreakerTournamentId,
                                'beatmap_type'                  => $roundOf16TieBreakerType,
                                'beatmap_image'                 => $roundOf16TieBreakerImage,
                                'beatmap_url'                   => $roundOf16TieBreakerUrl,
                                'beatmap_name'                  => $roundOf16TieBreakerName,
                                'beatmap_difficulty_name'       => $roundOf16TieBreakerDifficultyName,
                                'beatmap_feature_artist'        => $roundOf16TieBreakerFeatureArtist,
                                'beatmap_mapper'                => $roundOf16TieBreakerMapper,
                                'beatmap_mapper_url'            => $roundOf16TieBreakerMapperUrl,
                                'beatmap_difficulty'            => $roundOf16TieBreakerDifficulty,
                                'beatmap_length'                => $roundOf16TieBreakerLength,
                                'beatmap_overall_speed'         => $roundOf16TieBreakerOverallSpeed,
                                'beatmap_overall_difficulty'    => $roundOf16TieBreakerOverallDifficulty,
                                'beatmap_overall_health'        => $roundOf16TieBreakerOverallHealth,
                                'beatmap_pass_count'            => $roundOf16TieBreakerPassCount
                            ];
                        }
                        break;

                    case 'QF':
                        /*** QUARTER FINAL NM BEATMAP DATA ***/
                        $quarterFinalNoModJsonData = $mappoolRoundJsonData[$round]['NM'];
                        foreach ($quarterFinalNoModJsonData as $quarterFinalNoModJsonType => $quarterFinalNoModJsonId) {
                            $quarterFinalNoModData = getTournamentMappoolData(
                                id: $quarterFinalNoModJsonId,
                                token: $mappoolAccessToken
                            );

                            $quarterFinalNoModId                 = $quarterFinalNoModData['id'];
                            $quarterFinalNoModRoundId            = $round;
                            $quarterFinalNoModTournamentId       = strtoupper(string: $name);
                            $quarterFinalNoModType               = $quarterFinalNoModJsonType;
                            $quarterFinalNoModImage              = $quarterFinalNoModData['beatmapset']['covers']['cover'];
                            $quarterFinalNoModUrl                = $quarterFinalNoModData['url'];
                            $quarterFinalNoModName               = $quarterFinalNoModData['beatmapset']['title'];
                            $quarterFinalNoModDifficultyName     = $quarterFinalNoModData['version'];
                            $quarterFinalNoModFeatureArtist      = $quarterFinalNoModData['beatmapset']['artist'];
                            $quarterFinalNoModMapper             = $quarterFinalNoModData['beatmapset']['creator'];
                            $quarterFinalNoModMapperUrl          = "https://osu.ppy.sh/users/{$quarterFinalNoModData['beatmapset']['user_id']}";
                            $quarterFinalNoModDifficulty         = $quarterFinalNoModData['difficulty_rating'];
                            $quarterFinalNoModLength             = $quarterFinalNoModData['total_length'];
                            $quarterFinalNoModOverallSpeed       = $quarterFinalNoModData['beatmapset']['bpm'];
                            $quarterFinalNoModOverallDifficulty  = $quarterFinalNoModData['accuracy'];
                            $quarterFinalNoModOverallHealth      = $quarterFinalNoModData['drain'];
                            $quarterFinalNoModPassCount          = $quarterFinalNoModData['passcount'];

                            $allMappoolNoModData[] = [
                                'beatmap_id'                    => $quarterFinalNoModId,
                                'beatmap_round_id'              => $quarterFinalNoModRoundId,
                                'beatmap_tournament_id'         => $quarterFinalNoModTournamentId,
                                'beatmap_type'                  => $quarterFinalNoModType,
                                'beatmap_image'                 => $quarterFinalNoModImage,
                                'beatmap_url'                   => $quarterFinalNoModUrl,
                                'beatmap_name'                  => $quarterFinalNoModName,
                                'beatmap_difficulty_name'       => $quarterFinalNoModDifficultyName,
                                'beatmap_feature_artist'        => $quarterFinalNoModFeatureArtist,
                                'beatmap_mapper'                => $quarterFinalNoModMapper,
                                'beatmap_mapper_url'            => $quarterFinalNoModMapperUrl,
                                'beatmap_difficulty'            => $quarterFinalNoModDifficulty,
                                'beatmap_length'                => $quarterFinalNoModLength,
                                'beatmap_overall_speed'         => $quarterFinalNoModOverallSpeed,
                                'beatmap_overall_difficulty'    => $quarterFinalNoModOverallDifficulty,
                                'beatmap_overall_health'        => $quarterFinalNoModOverallHealth,
                                'beatmap_pass_count'            => $quarterFinalNoModPassCount
                            ];
                        }


                        /*** QUARTER FINAL HD BEATMAP DATA ***/
                        $quarterFinalHiddenJsonData = $mappoolRoundJsonData[$round]['HD'];
                        foreach ($quarterFinalHiddenJsonData as $quarterFinalHiddenJsonType => $quarterFinalHiddenJsonId) {
                            $quarterFinalHiddenData = getTournamentMappoolData(
                                id: $quarterFinalHiddenJsonId,
                                token: $mappoolAccessToken
                            );

                            $quarterFinalHiddenId                 = $quarterFinalHiddenData['id'];
                            $quarterFinalHiddenRoundId            = $round;
                            $quarterFinalHiddenTournamentId       = strtoupper(string: $name);
                            $quarterFinalHiddenType               = $quarterFinalHiddenJsonType;
                            $quarterFinalHiddenImage              = $quarterFinalHiddenData['beatmapset']['covers']['cover'];
                            $quarterFinalHiddenUrl                = $quarterFinalHiddenData['url'];
                            $quarterFinalHiddenName               = $quarterFinalHiddenData['beatmapset']['title'];
                            $quarterFinalHiddenDifficultyName     = $quarterFinalHiddenData['version'];
                            $quarterFinalHiddenFeatureArtist      = $quarterFinalHiddenData['beatmapset']['artist'];
                            $quarterFinalHiddenMapper             = $quarterFinalHiddenData['beatmapset']['creator'];
                            $quarterFinalHiddenMapperUrl          = "https://osu.ppy.sh/users/{$quarterFinalHiddenData['beatmapset']['user_id']}";
                            $quarterFinalHiddenDifficulty         = $quarterFinalHiddenData['difficulty_rating'];
                            $quarterFinalHiddenLength             = $quarterFinalHiddenData['total_length'];
                            $quarterFinalHiddenOverallSpeed       = $quarterFinalHiddenData['beatmapset']['bpm'];
                            $quarterFinalHiddenOverallDifficulty  = $quarterFinalHiddenData['accuracy'];
                            $quarterFinalHiddenOverallHealth      = $quarterFinalHiddenData['drain'];
                            $quarterFinalHiddenPassCount          = $quarterFinalHiddenData['passcount'];

                            $allMappoolHiddenData[] = [
                                'beatmap_id'                    => $quarterFinalHiddenId,
                                'beatmap_round_id'              => $quarterFinalHiddenRoundId,
                                'beatmap_tournament_id'         => $quarterFinalHiddenTournamentId,
                                'beatmap_type'                  => $quarterFinalHiddenType,
                                'beatmap_image'                 => $quarterFinalHiddenImage,
                                'beatmap_url'                   => $quarterFinalHiddenUrl,
                                'beatmap_name'                  => $quarterFinalHiddenName,
                                'beatmap_difficulty_name'       => $quarterFinalHiddenDifficultyName,
                                'beatmap_feature_artist'        => $quarterFinalHiddenFeatureArtist,
                                'beatmap_mapper'                => $quarterFinalHiddenMapper,
                                'beatmap_mapper_url'            => $quarterFinalHiddenMapperUrl,
                                'beatmap_difficulty'            => $quarterFinalHiddenDifficulty,
                                'beatmap_length'                => $quarterFinalHiddenLength,
                                'beatmap_overall_speed'         => $quarterFinalHiddenOverallSpeed,
                                'beatmap_overall_difficulty'    => $quarterFinalHiddenOverallDifficulty,
                                'beatmap_overall_health'        => $quarterFinalHiddenOverallHealth,
                                'beatmap_pass_count'            => $quarterFinalHiddenPassCount
                            ];
                        }


                        /*** QUARTER FINAL HR BEATMAP DATA ***/
                        $quarterFinalHardRockJsonData = $mappoolRoundJsonData[$round]['HR'];
                        foreach ($quarterFinalHardRockJsonData as $quarterFinalHardRockJsonType => $quarterFinalHardRockJsonId) {
                            $quarterFinalHardRockData = getTournamentMappoolData(
                                id: $quarterFinalHardRockJsonId,
                                token: $mappoolAccessToken
                            );

                            $quarterFinalHardRockId                 = $quarterFinalHardRockData['id'];
                            $quarterFinalHardRockRoundId            = $round;
                            $quarterFinalHardRockTournamentId       = strtoupper(string: $name);
                            $quarterFinalHardRockType               = $quarterFinalHardRockJsonType;
                            $quarterFinalHardRockImage              = $quarterFinalHardRockData['beatmapset']['covers']['cover'];
                            $quarterFinalHardRockUrl                = $quarterFinalHardRockData['url'];
                            $quarterFinalHardRockName               = $quarterFinalHardRockData['beatmapset']['title'];
                            $quarterFinalHardRockDifficultyName     = $quarterFinalHardRockData['version'];
                            $quarterFinalHardRockFeatureArtist      = $quarterFinalHardRockData['beatmapset']['artist'];
                            $quarterFinalHardRockMapper             = $quarterFinalHardRockData['beatmapset']['creator'];
                            $quarterFinalHardRockMapperUrl          = "https://osu.ppy.sh/users/{$quarterFinalHardRockData['beatmapset']['user_id']}";
                            $quarterFinalHardRockDifficulty         = $quarterFinalHardRockData['difficulty_rating'];
                            $quarterFinalHardRockLength             = $quarterFinalHardRockData['total_length'];
                            $quarterFinalHardRockOverallSpeed       = $quarterFinalHardRockData['beatmapset']['bpm'];
                            $quarterFinalHardRockOverallDifficulty  = $quarterFinalHardRockData['accuracy'];
                            $quarterFinalHardRockOverallHealth      = $quarterFinalHardRockData['drain'];
                            $quarterFinalHardRockPassCount          = $quarterFinalHardRockData['passcount'];

                            $allMappoolHardRockData[] = [
                                'beatmap_id'                    => $quarterFinalHardRockId,
                                'beatmap_round_id'              => $quarterFinalHardRockRoundId,
                                'beatmap_tournament_id'         => $quarterFinalHardRockTournamentId,
                                'beatmap_type'                  => $quarterFinalHardRockType,
                                'beatmap_image'                 => $quarterFinalHardRockImage,
                                'beatmap_url'                   => $quarterFinalHardRockUrl,
                                'beatmap_name'                  => $quarterFinalHardRockName,
                                'beatmap_difficulty_name'       => $quarterFinalHardRockDifficultyName,
                                'beatmap_feature_artist'        => $quarterFinalHardRockFeatureArtist,
                                'beatmap_mapper'                => $quarterFinalHardRockMapper,
                                'beatmap_mapper_url'            => $quarterFinalHardRockMapperUrl,
                                'beatmap_difficulty'            => $quarterFinalHardRockDifficulty,
                                'beatmap_length'                => $quarterFinalHardRockLength,
                                'beatmap_overall_speed'         => $quarterFinalHardRockOverallSpeed,
                                'beatmap_overall_difficulty'    => $quarterFinalHardRockOverallDifficulty,
                                'beatmap_overall_health'        => $quarterFinalHardRockOverallHealth,
                                'beatmap_pass_count'            => $quarterFinalHardRockPassCount
                            ];
                        }


                        /*** QUARTER FINAL DT BEATMAP DATA ***/
                        $quarterFinalDoubleTimeJsonData = $mappoolRoundJsonData[$round]['DT'];
                        foreach ($quarterFinalDoubleTimeJsonData as $quarterFinalDoubleTimeJsonType => $quarterFinalDoubleTimeJsonId) {
                            $quarterFinalDoubleTimeData = getTournamentMappoolData(
                                id: $quarterFinalDoubleTimeJsonId,
                                token: $mappoolAccessToken
                            );

                            $quarterFinalDoubleTimeId                 = $quarterFinalDoubleTimeData['id'];
                            $quarterFinalDoubleTimeRoundId            = $round;
                            $quarterFinalDoubleTimeTournamentId       = strtoupper(string: $name);
                            $quarterFinalDoubleTimeType               = $quarterFinalDoubleTimeJsonType;
                            $quarterFinalDoubleTimeImage              = $quarterFinalDoubleTimeData['beatmapset']['covers']['cover'];
                            $quarterFinalDoubleTimeUrl                = $quarterFinalDoubleTimeData['url'];
                            $quarterFinalDoubleTimeName               = $quarterFinalDoubleTimeData['beatmapset']['title'];
                            $quarterFinalDoubleTimeDifficultyName     = $quarterFinalDoubleTimeData['version'];
                            $quarterFinalDoubleTimeFeatureArtist      = $quarterFinalDoubleTimeData['beatmapset']['artist'];
                            $quarterFinalDoubleTimeMapper             = $quarterFinalDoubleTimeData['beatmapset']['creator'];
                            $quarterFinalDoubleTimeMapperUrl          = "https://osu.ppy.sh/users/{$quarterFinalDoubleTimeData['beatmapset']['user_id']}";
                            $quarterFinalDoubleTimeDifficulty         = $quarterFinalDoubleTimeData['difficulty_rating'];
                            $quarterFinalDoubleTimeLength             = $quarterFinalDoubleTimeData['total_length'];
                            $quarterFinalDoubleTimeOverallSpeed       = $quarterFinalDoubleTimeData['beatmapset']['bpm'];
                            $quarterFinalDoubleTimeOverallDifficulty  = $quarterFinalDoubleTimeData['accuracy'];
                            $quarterFinalDoubleTimeOverallHealth      = $quarterFinalDoubleTimeData['drain'];
                            $quarterFinalDoubleTimePassCount          = $quarterFinalDoubleTimeData['passcount'];

                            $allMappoolDoubleTimeData[] = [
                                'beatmap_id'                    => $quarterFinalDoubleTimeId,
                                'beatmap_round_id'              => $quarterFinalDoubleTimeRoundId,
                                'beatmap_tournament_id'         => $quarterFinalDoubleTimeTournamentId,
                                'beatmap_type'                  => $quarterFinalDoubleTimeType,
                                'beatmap_image'                 => $quarterFinalDoubleTimeImage,
                                'beatmap_url'                   => $quarterFinalDoubleTimeUrl,
                                'beatmap_name'                  => $quarterFinalDoubleTimeName,
                                'beatmap_difficulty_name'       => $quarterFinalDoubleTimeDifficultyName,
                                'beatmap_feature_artist'        => $quarterFinalDoubleTimeFeatureArtist,
                                'beatmap_mapper'                => $quarterFinalDoubleTimeMapper,
                                'beatmap_mapper_url'            => $quarterFinalDoubleTimeMapperUrl,
                                'beatmap_difficulty'            => $quarterFinalDoubleTimeDifficulty,
                                'beatmap_length'                => $quarterFinalDoubleTimeLength,
                                'beatmap_overall_speed'         => $quarterFinalDoubleTimeOverallSpeed,
                                'beatmap_overall_difficulty'    => $quarterFinalDoubleTimeOverallDifficulty,
                                'beatmap_overall_health'        => $quarterFinalDoubleTimeOverallHealth,
                                'beatmap_pass_count'            => $quarterFinalDoubleTimePassCount
                            ];
                        }


                        /*** QUARTER FINAL FM BEATMAP DATA ***/
                        $quarterFinalFreeModJsonData = $mappoolRoundJsonData[$round]['FM'];
                        foreach ($quarterFinalFreeModJsonData as $quarterFinalFreeModJsonType => $quarterFinalFreeModJsonId) {
                            $quarterFinalFreeModData = getTournamentMappoolData(
                                id: $quarterFinalFreeModJsonId,
                                token: $mappoolAccessToken
                            );

                            $quarterFinalFreeModId                 = $quarterFinalFreeModData['id'];
                            $quarterFinalFreeModRoundId            = $round;
                            $quarterFinalFreeModTournamentId       = strtoupper(string: $name);
                            $quarterFinalFreeModType               = $quarterFinalFreeModJsonType;
                            $quarterFinalFreeModImage              = $quarterFinalFreeModData['beatmapset']['covers']['cover'];
                            $quarterFinalFreeModUrl                = $quarterFinalFreeModData['url'];
                            $quarterFinalFreeModName               = $quarterFinalFreeModData['beatmapset']['title'];
                            $quarterFinalFreeModDifficultyName     = $quarterFinalFreeModData['version'];
                            $quarterFinalFreeModFeatureArtist      = $quarterFinalFreeModData['beatmapset']['artist'];
                            $quarterFinalFreeModMapper             = $quarterFinalFreeModData['beatmapset']['creator'];
                            $quarterFinalFreeModMapperUrl          = "https://osu.ppy.sh/users/{$quarterFinalFreeModData['beatmapset']['user_id']}";
                            $quarterFinalFreeModDifficulty         = $quarterFinalFreeModData['difficulty_rating'];
                            $quarterFinalFreeModLength             = $quarterFinalFreeModData['total_length'];
                            $quarterFinalFreeModOverallSpeed       = $quarterFinalFreeModData['beatmapset']['bpm'];
                            $quarterFinalFreeModOverallDifficulty  = $quarterFinalFreeModData['accuracy'];
                            $quarterFinalFreeModOverallHealth      = $quarterFinalFreeModData['drain'];
                            $quarterFinalFreeModPassCount          = $quarterFinalFreeModData['passcount'];

                            $allMappoolFreeModData[] = [
                                'beatmap_id'                    => $quarterFinalFreeModId,
                                'beatmap_round_id'              => $quarterFinalFreeModRoundId,
                                'beatmap_tournament_id'         => $quarterFinalFreeModTournamentId,
                                'beatmap_type'                  => $quarterFinalFreeModType,
                                'beatmap_image'                 => $quarterFinalFreeModImage,
                                'beatmap_url'                   => $quarterFinalFreeModUrl,
                                'beatmap_name'                  => $quarterFinalFreeModName,
                                'beatmap_difficulty_name'       => $quarterFinalFreeModDifficultyName,
                                'beatmap_feature_artist'        => $quarterFinalFreeModFeatureArtist,
                                'beatmap_mapper'                => $quarterFinalFreeModMapper,
                                'beatmap_mapper_url'            => $quarterFinalFreeModMapperUrl,
                                'beatmap_difficulty'            => $quarterFinalFreeModDifficulty,
                                'beatmap_length'                => $quarterFinalFreeModLength,
                                'beatmap_overall_speed'         => $quarterFinalFreeModOverallSpeed,
                                'beatmap_overall_difficulty'    => $quarterFinalFreeModOverallDifficulty,
                                'beatmap_overall_health'        => $quarterFinalFreeModOverallHealth,
                                'beatmap_pass_count'            => $quarterFinalFreeModPassCount
                            ];
                        }


                        /*** QUARTER FINAL EZ BEATMAP DATA ***/
                        $quarterFinalEasyJsonData = $mappoolRoundJsonData[$round]['FM'];
                        foreach ($quarterFinalEasyJsonData as $quarterFinalEasyJsonType => $quarterFinalEasyJsonId) {
                            $quarterFinalEasyData = getTournamentMappoolData(
                                id: $quarterFinalEasyJsonId,
                                token: $mappoolAccessToken
                            );

                            $quarterFinalEasyId                 = $quarterFinalEasyData['id'];
                            $quarterFinalEasyRoundId            = $round;
                            $quarterFinalEasyTournamentId       = strtoupper(string: $name);
                            $quarterFinalEasyType               = $quarterFinalEasyJsonType;
                            $quarterFinalEasyImage              = $quarterFinalEasyData['beatmapset']['covers']['cover'];
                            $quarterFinalEasyUrl                = $quarterFinalEasyData['url'];
                            $quarterFinalEasyName               = $quarterFinalEasyData['beatmapset']['title'];
                            $quarterFinalEasyDifficultyName     = $quarterFinalEasyData['version'];
                            $quarterFinalEasyFeatureArtist      = $quarterFinalEasyData['beatmapset']['artist'];
                            $quarterFinalEasyMapper             = $quarterFinalEasyData['beatmapset']['creator'];
                            $quarterFinalEasyMapperUrl          = "https://osu.ppy.sh/users/{$quarterFinalEasyData['beatmapset']['user_id']}";
                            $quarterFinalEasyDifficulty         = $quarterFinalEasyData['difficulty_rating'];
                            $quarterFinalEasyLength             = $quarterFinalEasyData['total_length'];
                            $quarterFinalEasyOverallSpeed       = $quarterFinalEasyData['beatmapset']['bpm'];
                            $quarterFinalEasyOverallDifficulty  = $quarterFinalEasyData['accuracy'];
                            $quarterFinalEasyOverallHealth      = $quarterFinalEasyData['drain'];
                            $quarterFinalEasyPassCount          = $quarterFinalEasyData['passcount'];

                            $allMappoolEasyData[] = [
                                'beatmap_id'                    => $quarterFinalEasyId,
                                'beatmap_round_id'              => $quarterFinalEasyRoundId,
                                'beatmap_tournament_id'         => $quarterFinalEasyTournamentId,
                                'beatmap_type'                  => $quarterFinalEasyType,
                                'beatmap_image'                 => $quarterFinalEasyImage,
                                'beatmap_url'                   => $quarterFinalEasyUrl,
                                'beatmap_name'                  => $quarterFinalEasyName,
                                'beatmap_difficulty_name'       => $quarterFinalEasyDifficultyName,
                                'beatmap_feature_artist'        => $quarterFinalEasyFeatureArtist,
                                'beatmap_mapper'                => $quarterFinalEasyMapper,
                                'beatmap_mapper_url'            => $quarterFinalEasyMapperUrl,
                                'beatmap_difficulty'            => $quarterFinalEasyDifficulty,
                                'beatmap_length'                => $quarterFinalEasyLength,
                                'beatmap_overall_speed'         => $quarterFinalEasyOverallSpeed,
                                'beatmap_overall_difficulty'    => $quarterFinalEasyOverallDifficulty,
                                'beatmap_overall_health'        => $quarterFinalEasyOverallHealth,
                                'beatmap_pass_count'            => $quarterFinalEasyPassCount
                            ];
                        }

                        /*** QUARTER FINAL HDHR BEATMAP DATA ***/
                        $quarterFinalHiddenHardRockJsonData = $mappoolRoundJsonData[$round]['FM'];
                        foreach ($quarterFinalHiddenHardRockJsonData as $quarterFinalHiddenHardRockJsonType => $quarterFinalHiddenHardRockJsonId) {
                            $quarterFinalHiddenHardRockData = getTournamentMappoolData(
                                id: $quarterFinalHiddenHardRockJsonId,
                                token: $mappoolAccessToken
                            );

                            $quarterFinalHiddenHardRockId                 = $quarterFinalHiddenHardRockData['id'];
                            $quarterFinalHiddenHardRockRoundId            = $round;
                            $quarterFinalHiddenHardRockTournamentId       = strtoupper(string: $name);
                            $quarterFinalHiddenHardRockType               = $quarterFinalHiddenHardRockJsonType;
                            $quarterFinalHiddenHardRockImage              = $quarterFinalHiddenHardRockData['beatmapset']['covers']['cover'];
                            $quarterFinalHiddenHardRockUrl                = $quarterFinalHiddenHardRockData['url'];
                            $quarterFinalHiddenHardRockName               = $quarterFinalHiddenHardRockData['beatmapset']['title'];
                            $quarterFinalHiddenHardRockDifficultyName     = $quarterFinalHiddenHardRockData['version'];
                            $quarterFinalHiddenHardRockFeatureArtist      = $quarterFinalHiddenHardRockData['beatmapset']['artist'];
                            $quarterFinalHiddenHardRockMapper             = $quarterFinalHiddenHardRockData['beatmapset']['creator'];
                            $quarterFinalHiddenHardRockMapperUrl          = "https://osu.ppy.sh/users/{$quarterFinalHiddenHardRockData['beatmapset']['user_id']}";
                            $quarterFinalHiddenHardRockDifficulty         = $quarterFinalHiddenHardRockData['difficulty_rating'];
                            $quarterFinalHiddenHardRockLength             = $quarterFinalHiddenHardRockData['total_length'];
                            $quarterFinalHiddenHardRockOverallSpeed       = $quarterFinalHiddenHardRockData['beatmapset']['bpm'];
                            $quarterFinalHiddenHardRockOverallDifficulty  = $quarterFinalHiddenHardRockData['accuracy'];
                            $quarterFinalHiddenHardRockOverallHealth      = $quarterFinalHiddenHardRockData['drain'];
                            $quarterFinalHiddenHardRockPassCount          = $quarterFinalHiddenHardRockData['passcount'];

                            $allMappoolHiddenHardRockData[] = [
                                'beatmap_id'                    => $quarterFinalHiddenHardRockId,
                                'beatmap_round_id'              => $quarterFinalHiddenHardRockRoundId,
                                'beatmap_tournament_id'         => $quarterFinalHiddenHardRockTournamentId,
                                'beatmap_type'                  => $quarterFinalHiddenHardRockType,
                                'beatmap_image'                 => $quarterFinalHiddenHardRockImage,
                                'beatmap_url'                   => $quarterFinalHiddenHardRockUrl,
                                'beatmap_name'                  => $quarterFinalHiddenHardRockName,
                                'beatmap_difficulty_name'       => $quarterFinalHiddenHardRockDifficultyName,
                                'beatmap_feature_artist'        => $quarterFinalHiddenHardRockFeatureArtist,
                                'beatmap_mapper'                => $quarterFinalHiddenHardRockMapper,
                                'beatmap_mapper_url'            => $quarterFinalHiddenHardRockMapperUrl,
                                'beatmap_difficulty'            => $quarterFinalHiddenHardRockDifficulty,
                                'beatmap_length'                => $quarterFinalHiddenHardRockLength,
                                'beatmap_overall_speed'         => $quarterFinalHiddenHardRockOverallSpeed,
                                'beatmap_overall_difficulty'    => $quarterFinalHiddenHardRockOverallDifficulty,
                                'beatmap_overall_health'        => $quarterFinalHiddenHardRockOverallHealth,
                                'beatmap_pass_count'            => $quarterFinalHiddenHardRockPassCount
                            ];
                        }


                        /*** QUARTER FINAL TB BEATMAP DATA ***/
                        $quarterFinalTieBreakerJsonData = $mappoolRoundJsonData[$round]['FM'];
                        foreach ($quarterFinalTieBreakerJsonData as $quarterFinalTieBreakerJsonType => $quarterFinalTieBreakerJsonId) {
                            $quarterFinalTieBreakerData = getTournamentMappoolData(
                                id: $quarterFinalTieBreakerJsonId,
                                token: $mappoolAccessToken
                            );

                            $quarterFinalTieBreakerId                 = $quarterFinalTieBreakerData['id'];
                            $quarterFinalTieBreakerRoundId            = $round;
                            $quarterFinalTieBreakerTournamentId       = strtoupper(string: $name);
                            $quarterFinalTieBreakerType               = $quarterFinalTieBreakerJsonType;
                            $quarterFinalTieBreakerImage              = $quarterFinalTieBreakerData['beatmapset']['covers']['cover'];
                            $quarterFinalTieBreakerUrl                = $quarterFinalTieBreakerData['url'];
                            $quarterFinalTieBreakerName               = $quarterFinalTieBreakerData['beatmapset']['title'];
                            $quarterFinalTieBreakerDifficultyName     = $quarterFinalTieBreakerData['version'];
                            $quarterFinalTieBreakerFeatureArtist      = $quarterFinalTieBreakerData['beatmapset']['artist'];
                            $quarterFinalTieBreakerMapper             = $quarterFinalTieBreakerData['beatmapset']['creator'];
                            $quarterFinalTieBreakerMapperUrl          = "https://osu.ppy.sh/users/{$quarterFinalTieBreakerData['beatmapset']['user_id']}";
                            $quarterFinalTieBreakerDifficulty         = $quarterFinalTieBreakerData['difficulty_rating'];
                            $quarterFinalTieBreakerLength             = $quarterFinalTieBreakerData['total_length'];
                            $quarterFinalTieBreakerOverallSpeed       = $quarterFinalTieBreakerData['beatmapset']['bpm'];
                            $quarterFinalTieBreakerOverallDifficulty  = $quarterFinalTieBreakerData['accuracy'];
                            $quarterFinalTieBreakerOverallHealth      = $quarterFinalTieBreakerData['drain'];
                            $quarterFinalTieBreakerPassCount          = $quarterFinalTieBreakerData['passcount'];

                            $allMappoolTieBreakerData[] = [
                                'beatmap_id'                    => $quarterFinalTieBreakerId,
                                'beatmap_round_id'              => $quarterFinalTieBreakerRoundId,
                                'beatmap_tournament_id'         => $quarterFinalTieBreakerTournamentId,
                                'beatmap_type'                  => $quarterFinalTieBreakerType,
                                'beatmap_image'                 => $quarterFinalTieBreakerImage,
                                'beatmap_url'                   => $quarterFinalTieBreakerUrl,
                                'beatmap_name'                  => $quarterFinalTieBreakerName,
                                'beatmap_difficulty_name'       => $quarterFinalTieBreakerDifficultyName,
                                'beatmap_feature_artist'        => $quarterFinalTieBreakerFeatureArtist,
                                'beatmap_mapper'                => $quarterFinalTieBreakerMapper,
                                'beatmap_mapper_url'            => $quarterFinalTieBreakerMapperUrl,
                                'beatmap_difficulty'            => $quarterFinalTieBreakerDifficulty,
                                'beatmap_length'                => $quarterFinalTieBreakerLength,
                                'beatmap_overall_speed'         => $quarterFinalTieBreakerOverallSpeed,
                                'beatmap_overall_difficulty'    => $quarterFinalTieBreakerOverallDifficulty,
                                'beatmap_overall_health'        => $quarterFinalTieBreakerOverallHealth,
                                'beatmap_pass_count'            => $quarterFinalTieBreakerPassCount
                            ];
                        }
                        break;

                    case 'SF':
                        /*** SEMI FINAL NM BEATMAP DATA ***/
                        $semiFinalNoModJsonData = $mappoolRoundJsonData[$round]['NM'];
                        foreach ($semiFinalNoModJsonData as $semiFinalNoModJsonType => $semiFinalNoModJsonId) {
                            $semiFinalNoModData = getTournamentMappoolData(
                                id: $semiFinalNoModJsonId,
                                token: $mappoolAccessToken
                            );

                            $semiFinalNoModId                 = $semiFinalNoModData['id'];
                            $semiFinalNoModRoundId            = $round;
                            $semiFinalNoModTournamentId       = strtoupper(string: $name);
                            $semiFinalNoModType               = $semiFinalNoModJsonType;
                            $semiFinalNoModImage              = $semiFinalNoModData['beatmapset']['covers']['cover'];
                            $semiFinalNoModUrl                = $semiFinalNoModData['url'];
                            $semiFinalNoModName               = $semiFinalNoModData['beatmapset']['title'];
                            $semiFinalNoModDifficultyName     = $semiFinalNoModData['version'];
                            $semiFinalNoModFeatureArtist      = $semiFinalNoModData['beatmapset']['artist'];
                            $semiFinalNoModMapper             = $semiFinalNoModData['beatmapset']['creator'];
                            $semiFinalNoModMapperUrl          = "https://osu.ppy.sh/users/{$semiFinalNoModData['beatmapset']['user_id']}";
                            $semiFinalNoModDifficulty         = $semiFinalNoModData['difficulty_rating'];
                            $semiFinalNoModLength             = $semiFinalNoModData['total_length'];
                            $semiFinalNoModOverallSpeed       = $semiFinalNoModData['beatmapset']['bpm'];
                            $semiFinalNoModOverallDifficulty  = $semiFinalNoModData['accuracy'];
                            $semiFinalNoModOverallHealth      = $semiFinalNoModData['drain'];
                            $semiFinalNoModPassCount          = $semiFinalNoModData['passcount'];

                            $allMappoolNoModData[] = [
                                'beatmap_id'                    => $semiFinalNoModId,
                                'beatmap_round_id'              => $semiFinalNoModRoundId,
                                'beatmap_tournament_id'         => $semiFinalNoModTournamentId,
                                'beatmap_type'                  => $semiFinalNoModType,
                                'beatmap_image'                 => $semiFinalNoModImage,
                                'beatmap_url'                   => $semiFinalNoModUrl,
                                'beatmap_name'                  => $semiFinalNoModName,
                                'beatmap_difficulty_name'       => $semiFinalNoModDifficultyName,
                                'beatmap_feature_artist'        => $semiFinalNoModFeatureArtist,
                                'beatmap_mapper'                => $semiFinalNoModMapper,
                                'beatmap_mapper_url'            => $semiFinalNoModMapperUrl,
                                'beatmap_difficulty'            => $semiFinalNoModDifficulty,
                                'beatmap_length'                => $semiFinalNoModLength,
                                'beatmap_overall_speed'         => $semiFinalNoModOverallSpeed,
                                'beatmap_overall_difficulty'    => $semiFinalNoModOverallDifficulty,
                                'beatmap_overall_health'        => $semiFinalNoModOverallHealth,
                                'beatmap_pass_count'            => $semiFinalNoModPassCount
                            ];
                        }


                        /*** SEMI FINAL HD BEATMAP DATA ***/
                        $semiFinalHiddenJsonData = $mappoolRoundJsonData[$round]['HD'];
                        foreach ($semiFinalHiddenJsonData as $semiFinalHiddenJsonType => $semiFinalHiddenJsonId) {
                            $semiFinalHiddenData = getTournamentMappoolData(
                                id: $semiFinalHiddenJsonId,
                                token: $mappoolAccessToken
                            );

                            $semiFinalHiddenId                 = $semiFinalHiddenData['id'];
                            $semiFinalHiddenRoundId            = $round;
                            $semiFinalHiddenTournamentId       = strtoupper(string: $name);
                            $semiFinalHiddenType               = $semiFinalHiddenJsonType;
                            $semiFinalHiddenImage              = $semiFinalHiddenData['beatmapset']['covers']['cover'];
                            $semiFinalHiddenUrl                = $semiFinalHiddenData['url'];
                            $semiFinalHiddenName               = $semiFinalHiddenData['beatmapset']['title'];
                            $semiFinalHiddenDifficultyName     = $semiFinalHiddenData['version'];
                            $semiFinalHiddenFeatureArtist      = $semiFinalHiddenData['beatmapset']['artist'];
                            $semiFinalHiddenMapper             = $semiFinalHiddenData['beatmapset']['creator'];
                            $semiFinalHiddenMapperUrl          = "https://osu.ppy.sh/users/{$semiFinalHiddenData['beatmapset']['user_id']}";
                            $semiFinalHiddenDifficulty         = $semiFinalHiddenData['difficulty_rating'];
                            $semiFinalHiddenLength             = $semiFinalHiddenData['total_length'];
                            $semiFinalHiddenOverallSpeed       = $semiFinalHiddenData['beatmapset']['bpm'];
                            $semiFinalHiddenOverallDifficulty  = $semiFinalHiddenData['accuracy'];
                            $semiFinalHiddenOverallHealth      = $semiFinalHiddenData['drain'];
                            $semiFinalHiddenPassCount          = $semiFinalHiddenData['passcount'];

                            $allMappoolHiddenData[] = [
                                'beatmap_id'                    => $semiFinalHiddenId,
                                'beatmap_round_id'              => $semiFinalHiddenRoundId,
                                'beatmap_tournament_id'         => $semiFinalHiddenTournamentId,
                                'beatmap_type'                  => $semiFinalHiddenType,
                                'beatmap_image'                 => $semiFinalHiddenImage,
                                'beatmap_url'                   => $semiFinalHiddenUrl,
                                'beatmap_name'                  => $semiFinalHiddenName,
                                'beatmap_difficulty_name'       => $semiFinalHiddenDifficultyName,
                                'beatmap_feature_artist'        => $semiFinalHiddenFeatureArtist,
                                'beatmap_mapper'                => $semiFinalHiddenMapper,
                                'beatmap_mapper_url'            => $semiFinalHiddenMapperUrl,
                                'beatmap_difficulty'            => $semiFinalHiddenDifficulty,
                                'beatmap_length'                => $semiFinalHiddenLength,
                                'beatmap_overall_speed'         => $semiFinalHiddenOverallSpeed,
                                'beatmap_overall_difficulty'    => $semiFinalHiddenOverallDifficulty,
                                'beatmap_overall_health'        => $semiFinalHiddenOverallHealth,
                                'beatmap_pass_count'            => $semiFinalHiddenPassCount
                            ];
                        }


                        /*** SEMI FINAL HR BEATMAP DATA ***/
                        $semiFinalHardRockJsonData = $mappoolRoundJsonData[$round]['HR'];
                        foreach ($semiFinalHardRockJsonData as $semiFinalHardRockJsonType => $semiFinalHardRockJsonId) {
                            $semiFinalHardRockData = getTournamentMappoolData(
                                id: $semiFinalHardRockJsonId,
                                token: $mappoolAccessToken
                            );

                            $semiFinalHardRockId                 = $semiFinalHardRockData['id'];
                            $semiFinalHardRockRoundId            = $round;
                            $semiFinalHardRockTournamentId       = strtoupper(string: $name);
                            $semiFinalHardRockType               = $semiFinalHardRockJsonType;
                            $semiFinalHardRockImage              = $semiFinalHardRockData['beatmapset']['covers']['cover'];
                            $semiFinalHardRockUrl                = $semiFinalHardRockData['url'];
                            $semiFinalHardRockName               = $semiFinalHardRockData['beatmapset']['title'];
                            $semiFinalHardRockDifficultyName     = $semiFinalHardRockData['version'];
                            $semiFinalHardRockFeatureArtist      = $semiFinalHardRockData['beatmapset']['artist'];
                            $semiFinalHardRockMapper             = $semiFinalHardRockData['beatmapset']['creator'];
                            $semiFinalHardRockMapperUrl          = "https://osu.ppy.sh/users/{$semiFinalHardRockData['beatmapset']['user_id']}";
                            $semiFinalHardRockDifficulty         = $semiFinalHardRockData['difficulty_rating'];
                            $semiFinalHardRockLength             = $semiFinalHardRockData['total_length'];
                            $semiFinalHardRockOverallSpeed       = $semiFinalHardRockData['beatmapset']['bpm'];
                            $semiFinalHardRockOverallDifficulty  = $semiFinalHardRockData['accuracy'];
                            $semiFinalHardRockOverallHealth      = $semiFinalHardRockData['drain'];
                            $semiFinalHardRockPassCount          = $semiFinalHardRockData['passcount'];

                            $allMappoolHardRockData[] = [
                                'beatmap_id'                    => $semiFinalHardRockId,
                                'beatmap_round_id'              => $semiFinalHardRockRoundId,
                                'beatmap_tournament_id'         => $semiFinalHardRockTournamentId,
                                'beatmap_type'                  => $semiFinalHardRockType,
                                'beatmap_image'                 => $semiFinalHardRockImage,
                                'beatmap_url'                   => $semiFinalHardRockUrl,
                                'beatmap_name'                  => $semiFinalHardRockName,
                                'beatmap_difficulty_name'       => $semiFinalHardRockDifficultyName,
                                'beatmap_feature_artist'        => $semiFinalHardRockFeatureArtist,
                                'beatmap_mapper'                => $semiFinalHardRockMapper,
                                'beatmap_mapper_url'            => $semiFinalHardRockMapperUrl,
                                'beatmap_difficulty'            => $semiFinalHardRockDifficulty,
                                'beatmap_length'                => $semiFinalHardRockLength,
                                'beatmap_overall_speed'         => $semiFinalHardRockOverallSpeed,
                                'beatmap_overall_difficulty'    => $semiFinalHardRockOverallDifficulty,
                                'beatmap_overall_health'        => $semiFinalHardRockOverallHealth,
                                'beatmap_pass_count'            => $semiFinalHardRockPassCount
                            ];
                        }


                        /*** SEMI FINAL DT BEATMAP DATA ***/
                        $semiFinalDoubleTimeJsonData = $mappoolRoundJsonData[$round]['DT'];
                        foreach ($semiFinalDoubleTimeJsonData as $semiFinalDoubleTimeJsonType => $semiFinalDoubleTimeJsonId) {
                            $semiFinalDoubleTimeData = getTournamentMappoolData(
                                id: $semiFinalDoubleTimeJsonId,
                                token: $mappoolAccessToken
                            );

                            $semiFinalDoubleTimeId                 = $semiFinalDoubleTimeData['id'];
                            $semiFinalDoubleTimeRoundId            = $round;
                            $semiFinalDoubleTimeTournamentId       = strtoupper(string: $name);
                            $semiFinalDoubleTimeType               = $semiFinalDoubleTimeJsonType;
                            $semiFinalDoubleTimeImage              = $semiFinalDoubleTimeData['beatmapset']['covers']['cover'];
                            $semiFinalDoubleTimeUrl                = $semiFinalDoubleTimeData['url'];
                            $semiFinalDoubleTimeName               = $semiFinalDoubleTimeData['beatmapset']['title'];
                            $semiFinalDoubleTimeDifficultyName     = $semiFinalDoubleTimeData['version'];
                            $semiFinalDoubleTimeFeatureArtist      = $semiFinalDoubleTimeData['beatmapset']['artist'];
                            $semiFinalDoubleTimeMapper             = $semiFinalDoubleTimeData['beatmapset']['creator'];
                            $semiFinalDoubleTimeMapperUrl          = "https://osu.ppy.sh/users/{$semiFinalDoubleTimeData['beatmapset']['user_id']}";
                            $semiFinalDoubleTimeDifficulty         = $semiFinalDoubleTimeData['difficulty_rating'];
                            $semiFinalDoubleTimeLength             = $semiFinalDoubleTimeData['total_length'];
                            $semiFinalDoubleTimeOverallSpeed       = $semiFinalDoubleTimeData['beatmapset']['bpm'];
                            $semiFinalDoubleTimeOverallDifficulty  = $semiFinalDoubleTimeData['accuracy'];
                            $semiFinalDoubleTimeOverallHealth      = $semiFinalDoubleTimeData['drain'];
                            $semiFinalDoubleTimePassCount          = $semiFinalDoubleTimeData['passcount'];

                            $allMappoolDoubleTimeData[] = [
                                'beatmap_id'                    => $semiFinalDoubleTimeId,
                                'beatmap_round_id'              => $semiFinalDoubleTimeRoundId,
                                'beatmap_tournament_id'         => $semiFinalDoubleTimeTournamentId,
                                'beatmap_type'                  => $semiFinalDoubleTimeType,
                                'beatmap_image'                 => $semiFinalDoubleTimeImage,
                                'beatmap_url'                   => $semiFinalDoubleTimeUrl,
                                'beatmap_name'                  => $semiFinalDoubleTimeName,
                                'beatmap_difficulty_name'       => $semiFinalDoubleTimeDifficultyName,
                                'beatmap_feature_artist'        => $semiFinalDoubleTimeFeatureArtist,
                                'beatmap_mapper'                => $semiFinalDoubleTimeMapper,
                                'beatmap_mapper_url'            => $semiFinalDoubleTimeMapperUrl,
                                'beatmap_difficulty'            => $semiFinalDoubleTimeDifficulty,
                                'beatmap_length'                => $semiFinalDoubleTimeLength,
                                'beatmap_overall_speed'         => $semiFinalDoubleTimeOverallSpeed,
                                'beatmap_overall_difficulty'    => $semiFinalDoubleTimeOverallDifficulty,
                                'beatmap_overall_health'        => $semiFinalDoubleTimeOverallHealth,
                                'beatmap_pass_count'            => $semiFinalDoubleTimePassCount
                            ];
                        }


                        /*** SEMI FINAL FM BEATMAP DATA ***/
                        $semiFinalFreeModJsonData = $mappoolRoundJsonData[$round]['FM'];
                        foreach ($semiFinalFreeModJsonData as $semiFinalFreeModJsonType => $semiFinalFreeModJsonId) {
                            $semiFinalFreeModData = getTournamentMappoolData(
                                id: $semiFinalFreeModJsonId,
                                token: $mappoolAccessToken
                            );

                            $semiFinalFreeModId                 = $semiFinalFreeModData['id'];
                            $semiFinalFreeModRoundId            = $round;
                            $semiFinalFreeModTournamentId       = strtoupper(string: $name);
                            $semiFinalFreeModType               = $semiFinalFreeModJsonType;
                            $semiFinalFreeModImage              = $semiFinalFreeModData['beatmapset']['covers']['cover'];
                            $semiFinalFreeModUrl                = $semiFinalFreeModData['url'];
                            $semiFinalFreeModName               = $semiFinalFreeModData['beatmapset']['title'];
                            $semiFinalFreeModDifficultyName     = $semiFinalFreeModData['version'];
                            $semiFinalFreeModFeatureArtist      = $semiFinalFreeModData['beatmapset']['artist'];
                            $semiFinalFreeModMapper             = $semiFinalFreeModData['beatmapset']['creator'];
                            $semiFinalFreeModMapperUrl          = "https://osu.ppy.sh/users/{$semiFinalFreeModData['beatmapset']['user_id']}";
                            $semiFinalFreeModDifficulty         = $semiFinalFreeModData['difficulty_rating'];
                            $semiFinalFreeModLength             = $semiFinalFreeModData['total_length'];
                            $semiFinalFreeModOverallSpeed       = $semiFinalFreeModData['beatmapset']['bpm'];
                            $semiFinalFreeModOverallDifficulty  = $semiFinalFreeModData['accuracy'];
                            $semiFinalFreeModOverallHealth      = $semiFinalFreeModData['drain'];
                            $semiFinalFreeModPassCount          = $semiFinalFreeModData['passcount'];

                            $allMappoolFreeModData[] = [
                                'beatmap_id'                    => $semiFinalFreeModId,
                                'beatmap_round_id'              => $semiFinalFreeModRoundId,
                                'beatmap_tournament_id'         => $semiFinalFreeModTournamentId,
                                'beatmap_type'                  => $semiFinalFreeModType,
                                'beatmap_image'                 => $semiFinalFreeModImage,
                                'beatmap_url'                   => $semiFinalFreeModUrl,
                                'beatmap_name'                  => $semiFinalFreeModName,
                                'beatmap_difficulty_name'       => $semiFinalFreeModDifficultyName,
                                'beatmap_feature_artist'        => $semiFinalFreeModFeatureArtist,
                                'beatmap_mapper'                => $semiFinalFreeModMapper,
                                'beatmap_mapper_url'            => $semiFinalFreeModMapperUrl,
                                'beatmap_difficulty'            => $semiFinalFreeModDifficulty,
                                'beatmap_length'                => $semiFinalFreeModLength,
                                'beatmap_overall_speed'         => $semiFinalFreeModOverallSpeed,
                                'beatmap_overall_difficulty'    => $semiFinalFreeModOverallDifficulty,
                                'beatmap_overall_health'        => $semiFinalFreeModOverallHealth,
                                'beatmap_pass_count'            => $semiFinalFreeModPassCount
                            ];
                        }


                        /*** SEMI FINAL EZ BEATMAP DATA ***/
                        $semiFinalEasyJsonData = $mappoolRoundJsonData[$round]['FM'];
                        foreach ($semiFinalEasyJsonData as $semiFinalEasyJsonType => $semiFinalEasyJsonId) {
                            $semiFinalEasyData = getTournamentMappoolData(
                                id: $semiFinalEasyJsonId,
                                token: $mappoolAccessToken
                            );

                            $semiFinalEasyId                 = $semiFinalEasyData['id'];
                            $semiFinalEasyRoundId            = $round;
                            $semiFinalEasyTournamentId       = strtoupper(string: $name);
                            $semiFinalEasyType               = $semiFinalEasyJsonType;
                            $semiFinalEasyImage              = $semiFinalEasyData['beatmapset']['covers']['cover'];
                            $semiFinalEasyUrl                = $semiFinalEasyData['url'];
                            $semiFinalEasyName               = $semiFinalEasyData['beatmapset']['title'];
                            $semiFinalEasyDifficultyName     = $semiFinalEasyData['version'];
                            $semiFinalEasyFeatureArtist      = $semiFinalEasyData['beatmapset']['artist'];
                            $semiFinalEasyMapper             = $semiFinalEasyData['beatmapset']['creator'];
                            $semiFinalEasyMapperUrl          = "https://osu.ppy.sh/users/{$semiFinalEasyData['beatmapset']['user_id']}";
                            $semiFinalEasyDifficulty         = $semiFinalEasyData['difficulty_rating'];
                            $semiFinalEasyLength             = $semiFinalEasyData['total_length'];
                            $semiFinalEasyOverallSpeed       = $semiFinalEasyData['beatmapset']['bpm'];
                            $semiFinalEasyOverallDifficulty  = $semiFinalEasyData['accuracy'];
                            $semiFinalEasyOverallHealth      = $semiFinalEasyData['drain'];
                            $semiFinalEasyPassCount          = $semiFinalEasyData['passcount'];

                            $allMappoolEasyData[] = [
                                'beatmap_id'                    => $semiFinalEasyId,
                                'beatmap_round_id'              => $semiFinalEasyRoundId,
                                'beatmap_tournament_id'         => $semiFinalEasyTournamentId,
                                'beatmap_type'                  => $semiFinalEasyType,
                                'beatmap_image'                 => $semiFinalEasyImage,
                                'beatmap_url'                   => $semiFinalEasyUrl,
                                'beatmap_name'                  => $semiFinalEasyName,
                                'beatmap_difficulty_name'       => $semiFinalEasyDifficultyName,
                                'beatmap_feature_artist'        => $semiFinalEasyFeatureArtist,
                                'beatmap_mapper'                => $semiFinalEasyMapper,
                                'beatmap_mapper_url'            => $semiFinalEasyMapperUrl,
                                'beatmap_difficulty'            => $semiFinalEasyDifficulty,
                                'beatmap_length'                => $semiFinalEasyLength,
                                'beatmap_overall_speed'         => $semiFinalEasyOverallSpeed,
                                'beatmap_overall_difficulty'    => $semiFinalEasyOverallDifficulty,
                                'beatmap_overall_health'        => $semiFinalEasyOverallHealth,
                                'beatmap_pass_count'            => $semiFinalEasyPassCount
                            ];
                        }

                        /*** SEMI FINAL HDHR BEATMAP DATA ***/
                        $semiFinalHiddenHardRockJsonData = $mappoolRoundJsonData[$round]['FM'];
                        foreach ($semiFinalHiddenHardRockJsonData as $semiFinalHiddenHardRockJsonType => $semiFinalHiddenHardRockJsonId) {
                            $semiFinalHiddenHardRockData = getTournamentMappoolData(
                                id: $semiFinalHiddenHardRockJsonId,
                                token: $mappoolAccessToken
                            );

                            $semiFinalHiddenHardRockId                 = $semiFinalHiddenHardRockData['id'];
                            $semiFinalHiddenHardRockRoundId            = $round;
                            $semiFinalHiddenHardRockTournamentId       = strtoupper(string: $name);
                            $semiFinalHiddenHardRockType               = $semiFinalHiddenHardRockJsonType;
                            $semiFinalHiddenHardRockImage              = $semiFinalHiddenHardRockData['beatmapset']['covers']['cover'];
                            $semiFinalHiddenHardRockUrl                = $semiFinalHiddenHardRockData['url'];
                            $semiFinalHiddenHardRockName               = $semiFinalHiddenHardRockData['beatmapset']['title'];
                            $semiFinalHiddenHardRockDifficultyName     = $semiFinalHiddenHardRockData['version'];
                            $semiFinalHiddenHardRockFeatureArtist      = $semiFinalHiddenHardRockData['beatmapset']['artist'];
                            $semiFinalHiddenHardRockMapper             = $semiFinalHiddenHardRockData['beatmapset']['creator'];
                            $semiFinalHiddenHardRockMapperUrl          = "https://osu.ppy.sh/users/{$semiFinalHiddenHardRockData['beatmapset']['user_id']}";
                            $semiFinalHiddenHardRockDifficulty         = $semiFinalHiddenHardRockData['difficulty_rating'];
                            $semiFinalHiddenHardRockLength             = $semiFinalHiddenHardRockData['total_length'];
                            $semiFinalHiddenHardRockOverallSpeed       = $semiFinalHiddenHardRockData['beatmapset']['bpm'];
                            $semiFinalHiddenHardRockOverallDifficulty  = $semiFinalHiddenHardRockData['accuracy'];
                            $semiFinalHiddenHardRockOverallHealth      = $semiFinalHiddenHardRockData['drain'];
                            $semiFinalHiddenHardRockPassCount          = $semiFinalHiddenHardRockData['passcount'];

                            $allMappoolHiddenHardRockData[] = [
                                'beatmap_id'                    => $semiFinalHiddenHardRockId,
                                'beatmap_round_id'              => $semiFinalHiddenHardRockRoundId,
                                'beatmap_tournament_id'         => $semiFinalHiddenHardRockTournamentId,
                                'beatmap_type'                  => $semiFinalHiddenHardRockType,
                                'beatmap_image'                 => $semiFinalHiddenHardRockImage,
                                'beatmap_url'                   => $semiFinalHiddenHardRockUrl,
                                'beatmap_name'                  => $semiFinalHiddenHardRockName,
                                'beatmap_difficulty_name'       => $semiFinalHiddenHardRockDifficultyName,
                                'beatmap_feature_artist'        => $semiFinalHiddenHardRockFeatureArtist,
                                'beatmap_mapper'                => $semiFinalHiddenHardRockMapper,
                                'beatmap_mapper_url'            => $semiFinalHiddenHardRockMapperUrl,
                                'beatmap_difficulty'            => $semiFinalHiddenHardRockDifficulty,
                                'beatmap_length'                => $semiFinalHiddenHardRockLength,
                                'beatmap_overall_speed'         => $semiFinalHiddenHardRockOverallSpeed,
                                'beatmap_overall_difficulty'    => $semiFinalHiddenHardRockOverallDifficulty,
                                'beatmap_overall_health'        => $semiFinalHiddenHardRockOverallHealth,
                                'beatmap_pass_count'            => $semiFinalHiddenHardRockPassCount
                            ];
                        }


                        /*** SEMI FINAL TB BEATMAP DATA ***/
                        $semiFinalTieBreakerJsonData = $mappoolRoundJsonData[$round]['FM'];
                        foreach ($semiFinalTieBreakerJsonData as $semiFinalTieBreakerJsonType => $semiFinalTieBreakerJsonId) {
                            $semiFinalTieBreakerData = getTournamentMappoolData(
                                id: $semiFinalTieBreakerJsonId,
                                token: $mappoolAccessToken
                            );

                            $semiFinalTieBreakerId                 = $semiFinalTieBreakerData['id'];
                            $semiFinalTieBreakerRoundId            = $round;
                            $semiFinalTieBreakerTournamentId       = strtoupper(string: $name);
                            $semiFinalTieBreakerType               = $semiFinalTieBreakerJsonType;
                            $semiFinalTieBreakerImage              = $semiFinalTieBreakerData['beatmapset']['covers']['cover'];
                            $semiFinalTieBreakerUrl                = $semiFinalTieBreakerData['url'];
                            $semiFinalTieBreakerName               = $semiFinalTieBreakerData['beatmapset']['title'];
                            $semiFinalTieBreakerDifficultyName     = $semiFinalTieBreakerData['version'];
                            $semiFinalTieBreakerFeatureArtist      = $semiFinalTieBreakerData['beatmapset']['artist'];
                            $semiFinalTieBreakerMapper             = $semiFinalTieBreakerData['beatmapset']['creator'];
                            $semiFinalTieBreakerMapperUrl          = "https://osu.ppy.sh/users/{$semiFinalTieBreakerData['beatmapset']['user_id']}";
                            $semiFinalTieBreakerDifficulty         = $semiFinalTieBreakerData['difficulty_rating'];
                            $semiFinalTieBreakerLength             = $semiFinalTieBreakerData['total_length'];
                            $semiFinalTieBreakerOverallSpeed       = $semiFinalTieBreakerData['beatmapset']['bpm'];
                            $semiFinalTieBreakerOverallDifficulty  = $semiFinalTieBreakerData['accuracy'];
                            $semiFinalTieBreakerOverallHealth      = $semiFinalTieBreakerData['drain'];
                            $semiFinalTieBreakerPassCount          = $semiFinalTieBreakerData['passcount'];

                            $allMappoolTieBreakerData[] = [
                                'beatmap_id'                    => $semiFinalTieBreakerId,
                                'beatmap_round_id'              => $semiFinalTieBreakerRoundId,
                                'beatmap_tournament_id'         => $semiFinalTieBreakerTournamentId,
                                'beatmap_type'                  => $semiFinalTieBreakerType,
                                'beatmap_image'                 => $semiFinalTieBreakerImage,
                                'beatmap_url'                   => $semiFinalTieBreakerUrl,
                                'beatmap_name'                  => $semiFinalTieBreakerName,
                                'beatmap_difficulty_name'       => $semiFinalTieBreakerDifficultyName,
                                'beatmap_feature_artist'        => $semiFinalTieBreakerFeatureArtist,
                                'beatmap_mapper'                => $semiFinalTieBreakerMapper,
                                'beatmap_mapper_url'            => $semiFinalTieBreakerMapperUrl,
                                'beatmap_difficulty'            => $semiFinalTieBreakerDifficulty,
                                'beatmap_length'                => $semiFinalTieBreakerLength,
                                'beatmap_overall_speed'         => $semiFinalTieBreakerOverallSpeed,
                                'beatmap_overall_difficulty'    => $semiFinalTieBreakerOverallDifficulty,
                                'beatmap_overall_health'        => $semiFinalTieBreakerOverallHealth,
                                'beatmap_pass_count'            => $semiFinalTieBreakerPassCount
                            ];
                        }
                        break;

                    case 'FNL':
                        /*** FINAL NM BEATMAP DATA ***/
                        $finalNoModJsonData = $mappoolRoundJsonData[$round]['NM'];
                        foreach ($finalNoModJsonData as $finalNoModJsonType => $finalNoModJsonId) {
                            $finalNoModData = getTournamentMappoolData(
                                id: $finalNoModJsonId,
                                token: $mappoolAccessToken
                            );

                            $finalNoModId                 = $finalNoModData['id'];
                            $finalNoModRoundId            = $round;
                            $finalNoModTournamentId       = strtoupper(string: $name);
                            $finalNoModType               = $finalNoModJsonType;
                            $finalNoModImage              = $finalNoModData['beatmapset']['covers']['cover'];
                            $finalNoModUrl                = $finalNoModData['url'];
                            $finalNoModName               = $finalNoModData['beatmapset']['title'];
                            $finalNoModDifficultyName     = $finalNoModData['version'];
                            $finalNoModFeatureArtist      = $finalNoModData['beatmapset']['artist'];
                            $finalNoModMapper             = $finalNoModData['beatmapset']['creator'];
                            $finalNoModMapperUrl          = "https://osu.ppy.sh/users/{$finalNoModData['beatmapset']['user_id']}";
                            $finalNoModDifficulty         = $finalNoModData['difficulty_rating'];
                            $finalNoModLength             = $finalNoModData['total_length'];
                            $finalNoModOverallSpeed       = $finalNoModData['beatmapset']['bpm'];
                            $finalNoModOverallDifficulty  = $finalNoModData['accuracy'];
                            $finalNoModOverallHealth      = $finalNoModData['drain'];
                            $finalNoModPassCount          = $finalNoModData['passcount'];

                            $allMappoolNoModData[] = [
                                'beatmap_id'                    => $finalNoModId,
                                'beatmap_round_id'              => $finalNoModRoundId,
                                'beatmap_tournament_id'         => $finalNoModTournamentId,
                                'beatmap_type'                  => $finalNoModType,
                                'beatmap_image'                 => $finalNoModImage,
                                'beatmap_url'                   => $finalNoModUrl,
                                'beatmap_name'                  => $finalNoModName,
                                'beatmap_difficulty_name'       => $finalNoModDifficultyName,
                                'beatmap_feature_artist'        => $finalNoModFeatureArtist,
                                'beatmap_mapper'                => $finalNoModMapper,
                                'beatmap_mapper_url'            => $finalNoModMapperUrl,
                                'beatmap_difficulty'            => $finalNoModDifficulty,
                                'beatmap_length'                => $finalNoModLength,
                                'beatmap_overall_speed'         => $finalNoModOverallSpeed,
                                'beatmap_overall_difficulty'    => $finalNoModOverallDifficulty,
                                'beatmap_overall_health'        => $finalNoModOverallHealth,
                                'beatmap_pass_count'            => $finalNoModPassCount
                            ];
                        }


                        /*** FINAL HD BEATMAP DATA ***/
                        $finalHiddenJsonData = $mappoolRoundJsonData[$round]['HD'];
                        foreach ($finalHiddenJsonData as $finalHiddenJsonType => $finalHiddenJsonId) {
                            $finalHiddenData = getTournamentMappoolData(
                                id: $finalHiddenJsonId,
                                token: $mappoolAccessToken
                            );

                            $finalHiddenId                 = $finalHiddenData['id'];
                            $finalHiddenRoundId            = $round;
                            $finalHiddenTournamentId       = strtoupper(string: $name);
                            $finalHiddenType               = $finalHiddenJsonType;
                            $finalHiddenImage              = $finalHiddenData['beatmapset']['covers']['cover'];
                            $finalHiddenUrl                = $finalHiddenData['url'];
                            $finalHiddenName               = $finalHiddenData['beatmapset']['title'];
                            $finalHiddenDifficultyName     = $finalHiddenData['version'];
                            $finalHiddenFeatureArtist      = $finalHiddenData['beatmapset']['artist'];
                            $finalHiddenMapper             = $finalHiddenData['beatmapset']['creator'];
                            $finalHiddenMapperUrl          = "https://osu.ppy.sh/users/{$finalHiddenData['beatmapset']['user_id']}";
                            $finalHiddenDifficulty         = $finalHiddenData['difficulty_rating'];
                            $finalHiddenLength             = $finalHiddenData['total_length'];
                            $finalHiddenOverallSpeed       = $finalHiddenData['beatmapset']['bpm'];
                            $finalHiddenOverallDifficulty  = $finalHiddenData['accuracy'];
                            $finalHiddenOverallHealth      = $finalHiddenData['drain'];
                            $finalHiddenPassCount          = $finalHiddenData['passcount'];

                            $allMappoolHiddenData[] = [
                                'beatmap_id'                    => $finalHiddenId,
                                'beatmap_round_id'              => $finalHiddenRoundId,
                                'beatmap_tournament_id'         => $finalHiddenTournamentId,
                                'beatmap_type'                  => $finalHiddenType,
                                'beatmap_image'                 => $finalHiddenImage,
                                'beatmap_url'                   => $finalHiddenUrl,
                                'beatmap_name'                  => $finalHiddenName,
                                'beatmap_difficulty_name'       => $finalHiddenDifficultyName,
                                'beatmap_feature_artist'        => $finalHiddenFeatureArtist,
                                'beatmap_mapper'                => $finalHiddenMapper,
                                'beatmap_mapper_url'            => $finalHiddenMapperUrl,
                                'beatmap_difficulty'            => $finalHiddenDifficulty,
                                'beatmap_length'                => $finalHiddenLength,
                                'beatmap_overall_speed'         => $finalHiddenOverallSpeed,
                                'beatmap_overall_difficulty'    => $finalHiddenOverallDifficulty,
                                'beatmap_overall_health'        => $finalHiddenOverallHealth,
                                'beatmap_pass_count'            => $finalHiddenPassCount
                            ];
                        }


                        /*** FINAL HR BEATMAP DATA ***/
                        $finalHardRockJsonData = $mappoolRoundJsonData[$round]['HR'];
                        foreach ($finalHardRockJsonData as $finalHardRockJsonType => $finalHardRockJsonId) {
                            $finalHardRockData = getTournamentMappoolData(
                                id: $finalHardRockJsonId,
                                token: $mappoolAccessToken
                            );

                            $finalHardRockId                 = $finalHardRockData['id'];
                            $finalHardRockRoundId            = $round;
                            $finalHardRockTournamentId       = strtoupper(string: $name);
                            $finalHardRockType               = $finalHardRockJsonType;
                            $finalHardRockImage              = $finalHardRockData['beatmapset']['covers']['cover'];
                            $finalHardRockUrl                = $finalHardRockData['url'];
                            $finalHardRockName               = $finalHardRockData['beatmapset']['title'];
                            $finalHardRockDifficultyName     = $finalHardRockData['version'];
                            $finalHardRockFeatureArtist      = $finalHardRockData['beatmapset']['artist'];
                            $finalHardRockMapper             = $finalHardRockData['beatmapset']['creator'];
                            $finalHardRockMapperUrl          = "https://osu.ppy.sh/users/{$finalHardRockData['beatmapset']['user_id']}";
                            $finalHardRockDifficulty         = $finalHardRockData['difficulty_rating'];
                            $finalHardRockLength             = $finalHardRockData['total_length'];
                            $finalHardRockOverallSpeed       = $finalHardRockData['beatmapset']['bpm'];
                            $finalHardRockOverallDifficulty  = $finalHardRockData['accuracy'];
                            $finalHardRockOverallHealth      = $finalHardRockData['drain'];
                            $finalHardRockPassCount          = $finalHardRockData['passcount'];

                            $allMappoolHardRockData[] = [
                                'beatmap_id'                    => $finalHardRockId,
                                'beatmap_round_id'              => $finalHardRockRoundId,
                                'beatmap_tournament_id'         => $finalHardRockTournamentId,
                                'beatmap_type'                  => $finalHardRockType,
                                'beatmap_image'                 => $finalHardRockImage,
                                'beatmap_url'                   => $finalHardRockUrl,
                                'beatmap_name'                  => $finalHardRockName,
                                'beatmap_difficulty_name'       => $finalHardRockDifficultyName,
                                'beatmap_feature_artist'        => $finalHardRockFeatureArtist,
                                'beatmap_mapper'                => $finalHardRockMapper,
                                'beatmap_mapper_url'            => $finalHardRockMapperUrl,
                                'beatmap_difficulty'            => $finalHardRockDifficulty,
                                'beatmap_length'                => $finalHardRockLength,
                                'beatmap_overall_speed'         => $finalHardRockOverallSpeed,
                                'beatmap_overall_difficulty'    => $finalHardRockOverallDifficulty,
                                'beatmap_overall_health'        => $finalHardRockOverallHealth,
                                'beatmap_pass_count'            => $finalHardRockPassCount
                            ];
                        }


                        /*** FINAL DT BEATMAP DATA ***/
                        $finalDoubleTimeJsonData = $mappoolRoundJsonData[$round]['DT'];
                        foreach ($finalDoubleTimeJsonData as $finalDoubleTimeJsonType => $finalDoubleTimeJsonId) {
                            $finalDoubleTimeData = getTournamentMappoolData(
                                id: $finalDoubleTimeJsonId,
                                token: $mappoolAccessToken
                            );

                            $finalDoubleTimeId                 = $finalDoubleTimeData['id'];
                            $finalDoubleTimeRoundId            = $round;
                            $finalDoubleTimeTournamentId       = strtoupper(string: $name);
                            $finalDoubleTimeType               = $finalDoubleTimeJsonType;
                            $finalDoubleTimeImage              = $finalDoubleTimeData['beatmapset']['covers']['cover'];
                            $finalDoubleTimeUrl                = $finalDoubleTimeData['url'];
                            $finalDoubleTimeName               = $finalDoubleTimeData['beatmapset']['title'];
                            $finalDoubleTimeDifficultyName     = $finalDoubleTimeData['version'];
                            $finalDoubleTimeFeatureArtist      = $finalDoubleTimeData['beatmapset']['artist'];
                            $finalDoubleTimeMapper             = $finalDoubleTimeData['beatmapset']['creator'];
                            $finalDoubleTimeMapperUrl          = "https://osu.ppy.sh/users/{$finalDoubleTimeData['beatmapset']['user_id']}";
                            $finalDoubleTimeDifficulty         = $finalDoubleTimeData['difficulty_rating'];
                            $finalDoubleTimeLength             = $finalDoubleTimeData['total_length'];
                            $finalDoubleTimeOverallSpeed       = $finalDoubleTimeData['beatmapset']['bpm'];
                            $finalDoubleTimeOverallDifficulty  = $finalDoubleTimeData['accuracy'];
                            $finalDoubleTimeOverallHealth      = $finalDoubleTimeData['drain'];
                            $finalDoubleTimePassCount          = $finalDoubleTimeData['passcount'];

                            $allMappoolDoubleTimeData[] = [
                                'beatmap_id'                    => $finalDoubleTimeId,
                                'beatmap_round_id'              => $finalDoubleTimeRoundId,
                                'beatmap_tournament_id'         => $finalDoubleTimeTournamentId,
                                'beatmap_type'                  => $finalDoubleTimeType,
                                'beatmap_image'                 => $finalDoubleTimeImage,
                                'beatmap_url'                   => $finalDoubleTimeUrl,
                                'beatmap_name'                  => $finalDoubleTimeName,
                                'beatmap_difficulty_name'       => $finalDoubleTimeDifficultyName,
                                'beatmap_feature_artist'        => $finalDoubleTimeFeatureArtist,
                                'beatmap_mapper'                => $finalDoubleTimeMapper,
                                'beatmap_mapper_url'            => $finalDoubleTimeMapperUrl,
                                'beatmap_difficulty'            => $finalDoubleTimeDifficulty,
                                'beatmap_length'                => $finalDoubleTimeLength,
                                'beatmap_overall_speed'         => $finalDoubleTimeOverallSpeed,
                                'beatmap_overall_difficulty'    => $finalDoubleTimeOverallDifficulty,
                                'beatmap_overall_health'        => $finalDoubleTimeOverallHealth,
                                'beatmap_pass_count'            => $finalDoubleTimePassCount
                            ];
                        }


                        /*** FINAL FM BEATMAP DATA ***/
                        $finalFreeModJsonData = $mappoolRoundJsonData[$round]['FM'];
                        foreach ($finalFreeModJsonData as $finalFreeModJsonType => $finalFreeModJsonId) {
                            $finalFreeModData = getTournamentMappoolData(
                                id: $finalFreeModJsonId,
                                token: $mappoolAccessToken
                            );

                            $finalFreeModId                 = $finalFreeModData['id'];
                            $finalFreeModRoundId            = $round;
                            $finalFreeModTournamentId       = strtoupper(string: $name);
                            $finalFreeModType               = $finalFreeModJsonType;
                            $finalFreeModImage              = $finalFreeModData['beatmapset']['covers']['cover'];
                            $finalFreeModUrl                = $finalFreeModData['url'];
                            $finalFreeModName               = $finalFreeModData['beatmapset']['title'];
                            $finalFreeModDifficultyName     = $finalFreeModData['version'];
                            $finalFreeModFeatureArtist      = $finalFreeModData['beatmapset']['artist'];
                            $finalFreeModMapper             = $finalFreeModData['beatmapset']['creator'];
                            $finalFreeModMapperUrl          = "https://osu.ppy.sh/users/{$finalFreeModData['beatmapset']['user_id']}";
                            $finalFreeModDifficulty         = $finalFreeModData['difficulty_rating'];
                            $finalFreeModLength             = $finalFreeModData['total_length'];
                            $finalFreeModOverallSpeed       = $finalFreeModData['beatmapset']['bpm'];
                            $finalFreeModOverallDifficulty  = $finalFreeModData['accuracy'];
                            $finalFreeModOverallHealth      = $finalFreeModData['drain'];
                            $finalFreeModPassCount          = $finalFreeModData['passcount'];

                            $allMappoolFreeModData[] = [
                                'beatmap_id'                    => $finalFreeModId,
                                'beatmap_round_id'              => $finalFreeModRoundId,
                                'beatmap_tournament_id'         => $finalFreeModTournamentId,
                                'beatmap_type'                  => $finalFreeModType,
                                'beatmap_image'                 => $finalFreeModImage,
                                'beatmap_url'                   => $finalFreeModUrl,
                                'beatmap_name'                  => $finalFreeModName,
                                'beatmap_difficulty_name'       => $finalFreeModDifficultyName,
                                'beatmap_feature_artist'        => $finalFreeModFeatureArtist,
                                'beatmap_mapper'                => $finalFreeModMapper,
                                'beatmap_mapper_url'            => $finalFreeModMapperUrl,
                                'beatmap_difficulty'            => $finalFreeModDifficulty,
                                'beatmap_length'                => $finalFreeModLength,
                                'beatmap_overall_speed'         => $finalFreeModOverallSpeed,
                                'beatmap_overall_difficulty'    => $finalFreeModOverallDifficulty,
                                'beatmap_overall_health'        => $finalFreeModOverallHealth,
                                'beatmap_pass_count'            => $finalFreeModPassCount
                            ];
                        }


                        /*** FINAL EZ BEATMAP DATA ***/
                        $finalEasyJsonData = $mappoolRoundJsonData[$round]['FM'];
                        foreach ($finalEasyJsonData as $finalEasyJsonType => $finalEasyJsonId) {
                            $finalEasyData = getTournamentMappoolData(
                                id: $finalEasyJsonId,
                                token: $mappoolAccessToken
                            );

                            $finalEasyId                 = $finalEasyData['id'];
                            $finalEasyRoundId            = $round;
                            $finalEasyTournamentId       = strtoupper(string: $name);
                            $finalEasyType               = $finalEasyJsonType;
                            $finalEasyImage              = $finalEasyData['beatmapset']['covers']['cover'];
                            $finalEasyUrl                = $finalEasyData['url'];
                            $finalEasyName               = $finalEasyData['beatmapset']['title'];
                            $finalEasyDifficultyName     = $finalEasyData['version'];
                            $finalEasyFeatureArtist      = $finalEasyData['beatmapset']['artist'];
                            $finalEasyMapper             = $finalEasyData['beatmapset']['creator'];
                            $finalEasyMapperUrl          = "https://osu.ppy.sh/users/{$finalEasyData['beatmapset']['user_id']}";
                            $finalEasyDifficulty         = $finalEasyData['difficulty_rating'];
                            $finalEasyLength             = $finalEasyData['total_length'];
                            $finalEasyOverallSpeed       = $finalEasyData['beatmapset']['bpm'];
                            $finalEasyOverallDifficulty  = $finalEasyData['accuracy'];
                            $finalEasyOverallHealth      = $finalEasyData['drain'];
                            $finalEasyPassCount          = $finalEasyData['passcount'];

                            $allMappoolEasyData[] = [
                                'beatmap_id'                    => $finalEasyId,
                                'beatmap_round_id'              => $finalEasyRoundId,
                                'beatmap_tournament_id'         => $finalEasyTournamentId,
                                'beatmap_type'                  => $finalEasyType,
                                'beatmap_image'                 => $finalEasyImage,
                                'beatmap_url'                   => $finalEasyUrl,
                                'beatmap_name'                  => $finalEasyName,
                                'beatmap_difficulty_name'       => $finalEasyDifficultyName,
                                'beatmap_feature_artist'        => $finalEasyFeatureArtist,
                                'beatmap_mapper'                => $finalEasyMapper,
                                'beatmap_mapper_url'            => $finalEasyMapperUrl,
                                'beatmap_difficulty'            => $finalEasyDifficulty,
                                'beatmap_length'                => $finalEasyLength,
                                'beatmap_overall_speed'         => $finalEasyOverallSpeed,
                                'beatmap_overall_difficulty'    => $finalEasyOverallDifficulty,
                                'beatmap_overall_health'        => $finalEasyOverallHealth,
                                'beatmap_pass_count'            => $finalEasyPassCount
                            ];
                        }

                        /*** FINAL HDHR BEATMAP DATA ***/
                        $finalHiddenHardRockJsonData = $mappoolRoundJsonData[$round]['FM'];
                        foreach ($finalHiddenHardRockJsonData as $finalHiddenHardRockJsonType => $finalHiddenHardRockJsonId) {
                            $finalHiddenHardRockData = getTournamentMappoolData(
                                id: $finalHiddenHardRockJsonId,
                                token: $mappoolAccessToken
                            );

                            $finalHiddenHardRockId                 = $finalHiddenHardRockData['id'];
                            $finalHiddenHardRockRoundId            = $round;
                            $finalHiddenHardRockTournamentId       = strtoupper(string: $name);
                            $finalHiddenHardRockType               = $finalHiddenHardRockJsonType;
                            $finalHiddenHardRockImage              = $finalHiddenHardRockData['beatmapset']['covers']['cover'];
                            $finalHiddenHardRockUrl                = $finalHiddenHardRockData['url'];
                            $finalHiddenHardRockName               = $finalHiddenHardRockData['beatmapset']['title'];
                            $finalHiddenHardRockDifficultyName     = $finalHiddenHardRockData['version'];
                            $finalHiddenHardRockFeatureArtist      = $finalHiddenHardRockData['beatmapset']['artist'];
                            $finalHiddenHardRockMapper             = $finalHiddenHardRockData['beatmapset']['creator'];
                            $finalHiddenHardRockMapperUrl          = "https://osu.ppy.sh/users/{$finalHiddenHardRockData['beatmapset']['user_id']}";
                            $finalHiddenHardRockDifficulty         = $finalHiddenHardRockData['difficulty_rating'];
                            $finalHiddenHardRockLength             = $finalHiddenHardRockData['total_length'];
                            $finalHiddenHardRockOverallSpeed       = $finalHiddenHardRockData['beatmapset']['bpm'];
                            $finalHiddenHardRockOverallDifficulty  = $finalHiddenHardRockData['accuracy'];
                            $finalHiddenHardRockOverallHealth      = $finalHiddenHardRockData['drain'];
                            $finalHiddenHardRockPassCount          = $finalHiddenHardRockData['passcount'];

                            $allMappoolHiddenHardRockData[] = [
                                'beatmap_id'                    => $finalHiddenHardRockId,
                                'beatmap_round_id'              => $finalHiddenHardRockRoundId,
                                'beatmap_tournament_id'         => $finalHiddenHardRockTournamentId,
                                'beatmap_type'                  => $finalHiddenHardRockType,
                                'beatmap_image'                 => $finalHiddenHardRockImage,
                                'beatmap_url'                   => $finalHiddenHardRockUrl,
                                'beatmap_name'                  => $finalHiddenHardRockName,
                                'beatmap_difficulty_name'       => $finalHiddenHardRockDifficultyName,
                                'beatmap_feature_artist'        => $finalHiddenHardRockFeatureArtist,
                                'beatmap_mapper'                => $finalHiddenHardRockMapper,
                                'beatmap_mapper_url'            => $finalHiddenHardRockMapperUrl,
                                'beatmap_difficulty'            => $finalHiddenHardRockDifficulty,
                                'beatmap_length'                => $finalHiddenHardRockLength,
                                'beatmap_overall_speed'         => $finalHiddenHardRockOverallSpeed,
                                'beatmap_overall_difficulty'    => $finalHiddenHardRockOverallDifficulty,
                                'beatmap_overall_health'        => $finalHiddenHardRockOverallHealth,
                                'beatmap_pass_count'            => $finalHiddenHardRockPassCount
                            ];
                        }


                        /*** FINAL TB BEATMAP DATA ***/
                        $finalTieBreakerJsonData = $mappoolRoundJsonData[$round]['FM'];
                        foreach ($finalTieBreakerJsonData as $finalTieBreakerJsonType => $finalTieBreakerJsonId) {
                            $finalTieBreakerData = getTournamentMappoolData(
                                id: $finalTieBreakerJsonId,
                                token: $mappoolAccessToken
                            );

                            $finalTieBreakerId                 = $finalTieBreakerData['id'];
                            $finalTieBreakerRoundId            = $round;
                            $finalTieBreakerTournamentId       = strtoupper(string: $name);
                            $finalTieBreakerType               = $finalTieBreakerJsonType;
                            $finalTieBreakerImage              = $finalTieBreakerData['beatmapset']['covers']['cover'];
                            $finalTieBreakerUrl                = $finalTieBreakerData['url'];
                            $finalTieBreakerName               = $finalTieBreakerData['beatmapset']['title'];
                            $finalTieBreakerDifficultyName     = $finalTieBreakerData['version'];
                            $finalTieBreakerFeatureArtist      = $finalTieBreakerData['beatmapset']['artist'];
                            $finalTieBreakerMapper             = $finalTieBreakerData['beatmapset']['creator'];
                            $finalTieBreakerMapperUrl          = "https://osu.ppy.sh/users/{$finalTieBreakerData['beatmapset']['user_id']}";
                            $finalTieBreakerDifficulty         = $finalTieBreakerData['difficulty_rating'];
                            $finalTieBreakerLength             = $finalTieBreakerData['total_length'];
                            $finalTieBreakerOverallSpeed       = $finalTieBreakerData['beatmapset']['bpm'];
                            $finalTieBreakerOverallDifficulty  = $finalTieBreakerData['accuracy'];
                            $finalTieBreakerOverallHealth      = $finalTieBreakerData['drain'];
                            $finalTieBreakerPassCount          = $finalTieBreakerData['passcount'];

                            $allMappoolTieBreakerData[] = [
                                'beatmap_id'                    => $finalTieBreakerId,
                                'beatmap_round_id'              => $finalTieBreakerRoundId,
                                'beatmap_tournament_id'         => $finalTieBreakerTournamentId,
                                'beatmap_type'                  => $finalTieBreakerType,
                                'beatmap_image'                 => $finalTieBreakerImage,
                                'beatmap_url'                   => $finalTieBreakerUrl,
                                'beatmap_name'                  => $finalTieBreakerName,
                                'beatmap_difficulty_name'       => $finalTieBreakerDifficultyName,
                                'beatmap_feature_artist'        => $finalTieBreakerFeatureArtist,
                                'beatmap_mapper'                => $finalTieBreakerMapper,
                                'beatmap_mapper_url'            => $finalTieBreakerMapperUrl,
                                'beatmap_difficulty'            => $finalTieBreakerDifficulty,
                                'beatmap_length'                => $finalTieBreakerLength,
                                'beatmap_overall_speed'         => $finalTieBreakerOverallSpeed,
                                'beatmap_overall_difficulty'    => $finalTieBreakerOverallDifficulty,
                                'beatmap_overall_health'        => $finalTieBreakerOverallHealth,
                                'beatmap_pass_count'            => $finalTieBreakerPassCount
                            ];
                        }
                        break;

                    case 'GF':
                        /*** GRAND FINAL NM BEATMAP DATA ***/
                        $grandFinalNoModJsonData = $mappoolRoundJsonData[$round]['NM'];
                        foreach ($grandFinalNoModJsonData as $grandFinalNoModJsonType => $grandFinalNoModJsonId) {
                            $grandFinalNoModData = getTournamentMappoolData(
                                id: $grandFinalNoModJsonId,
                                token: $mappoolAccessToken
                            );

                            $grandFinalNoModId                 = $grandFinalNoModData['id'];
                            $grandFinalNoModRoundId            = $round;
                            $grandFinalNoModTournamentId       = strtoupper(string: $name);
                            $grandFinalNoModType               = $grandFinalNoModJsonType;
                            $grandFinalNoModImage              = $grandFinalNoModData['beatmapset']['covers']['cover'];
                            $grandFinalNoModUrl                = $grandFinalNoModData['url'];
                            $grandFinalNoModName               = $grandFinalNoModData['beatmapset']['title'];
                            $grandFinalNoModDifficultyName     = $grandFinalNoModData['version'];
                            $grandFinalNoModFeatureArtist      = $grandFinalNoModData['beatmapset']['artist'];
                            $grandFinalNoModMapper             = $grandFinalNoModData['beatmapset']['creator'];
                            $grandFinalNoModMapperUrl          = "https://osu.ppy.sh/users/{$grandFinalNoModData['beatmapset']['user_id']}";
                            $grandFinalNoModDifficulty         = $grandFinalNoModData['difficulty_rating'];
                            $grandFinalNoModLength             = $grandFinalNoModData['total_length'];
                            $grandFinalNoModOverallSpeed       = $grandFinalNoModData['beatmapset']['bpm'];
                            $grandFinalNoModOverallDifficulty  = $grandFinalNoModData['accuracy'];
                            $grandFinalNoModOverallHealth      = $grandFinalNoModData['drain'];
                            $grandFinalNoModPassCount          = $grandFinalNoModData['passcount'];

                            $allMappoolNoModData[] = [
                                'beatmap_id'                    => $grandFinalNoModId,
                                'beatmap_round_id'              => $grandFinalNoModRoundId,
                                'beatmap_tournament_id'         => $grandFinalNoModTournamentId,
                                'beatmap_type'                  => $grandFinalNoModType,
                                'beatmap_image'                 => $grandFinalNoModImage,
                                'beatmap_url'                   => $grandFinalNoModUrl,
                                'beatmap_name'                  => $grandFinalNoModName,
                                'beatmap_difficulty_name'       => $grandFinalNoModDifficultyName,
                                'beatmap_feature_artist'        => $grandFinalNoModFeatureArtist,
                                'beatmap_mapper'                => $grandFinalNoModMapper,
                                'beatmap_mapper_url'            => $grandFinalNoModMapperUrl,
                                'beatmap_difficulty'            => $grandFinalNoModDifficulty,
                                'beatmap_length'                => $grandFinalNoModLength,
                                'beatmap_overall_speed'         => $grandFinalNoModOverallSpeed,
                                'beatmap_overall_difficulty'    => $grandFinalNoModOverallDifficulty,
                                'beatmap_overall_health'        => $grandFinalNoModOverallHealth,
                                'beatmap_pass_count'            => $grandFinalNoModPassCount
                            ];
                        }


                        /*** GRAND FINAL HD BEATMAP DATA ***/
                        $grandFinalHiddenJsonData = $mappoolRoundJsonData[$round]['HD'];
                        foreach ($grandFinalHiddenJsonData as $grandFinalHiddenJsonType => $grandFinalHiddenJsonId) {
                            $grandFinalHiddenData = getTournamentMappoolData(
                                id: $grandFinalHiddenJsonId,
                                token: $mappoolAccessToken
                            );

                            $grandFinalHiddenId                 = $grandFinalHiddenData['id'];
                            $grandFinalHiddenRoundId            = $round;
                            $grandFinalHiddenTournamentId       = strtoupper(string: $name);
                            $grandFinalHiddenType               = $grandFinalHiddenJsonType;
                            $grandFinalHiddenImage              = $grandFinalHiddenData['beatmapset']['covers']['cover'];
                            $grandFinalHiddenUrl                = $grandFinalHiddenData['url'];
                            $grandFinalHiddenName               = $grandFinalHiddenData['beatmapset']['title'];
                            $grandFinalHiddenDifficultyName     = $grandFinalHiddenData['version'];
                            $grandFinalHiddenFeatureArtist      = $grandFinalHiddenData['beatmapset']['artist'];
                            $grandFinalHiddenMapper             = $grandFinalHiddenData['beatmapset']['creator'];
                            $grandFinalHiddenMapperUrl          = "https://osu.ppy.sh/users/{$grandFinalHiddenData['beatmapset']['user_id']}";
                            $grandFinalHiddenDifficulty         = $grandFinalHiddenData['difficulty_rating'];
                            $grandFinalHiddenLength             = $grandFinalHiddenData['total_length'];
                            $grandFinalHiddenOverallSpeed       = $grandFinalHiddenData['beatmapset']['bpm'];
                            $grandFinalHiddenOverallDifficulty  = $grandFinalHiddenData['accuracy'];
                            $grandFinalHiddenOverallHealth      = $grandFinalHiddenData['drain'];
                            $grandFinalHiddenPassCount          = $grandFinalHiddenData['passcount'];

                            $allMappoolHiddenData[] = [
                                'beatmap_id'                    => $grandFinalHiddenId,
                                'beatmap_round_id'              => $grandFinalHiddenRoundId,
                                'beatmap_tournament_id'         => $grandFinalHiddenTournamentId,
                                'beatmap_type'                  => $grandFinalHiddenType,
                                'beatmap_image'                 => $grandFinalHiddenImage,
                                'beatmap_url'                   => $grandFinalHiddenUrl,
                                'beatmap_name'                  => $grandFinalHiddenName,
                                'beatmap_difficulty_name'       => $grandFinalHiddenDifficultyName,
                                'beatmap_feature_artist'        => $grandFinalHiddenFeatureArtist,
                                'beatmap_mapper'                => $grandFinalHiddenMapper,
                                'beatmap_mapper_url'            => $grandFinalHiddenMapperUrl,
                                'beatmap_difficulty'            => $grandFinalHiddenDifficulty,
                                'beatmap_length'                => $grandFinalHiddenLength,
                                'beatmap_overall_speed'         => $grandFinalHiddenOverallSpeed,
                                'beatmap_overall_difficulty'    => $grandFinalHiddenOverallDifficulty,
                                'beatmap_overall_health'        => $grandFinalHiddenOverallHealth,
                                'beatmap_pass_count'            => $grandFinalHiddenPassCount
                            ];
                        }


                        /*** GRAND FINAL HR BEATMAP DATA ***/
                        $grandFinalHardRockJsonData = $mappoolRoundJsonData[$round]['HR'];
                        foreach ($grandFinalHardRockJsonData as $grandFinalHardRockJsonType => $grandFinalHardRockJsonId) {
                            $grandFinalHardRockData = getTournamentMappoolData(
                                id: $grandFinalHardRockJsonId,
                                token: $mappoolAccessToken
                            );

                            $grandFinalHardRockId                 = $grandFinalHardRockData['id'];
                            $grandFinalHardRockRoundId            = $round;
                            $grandFinalHardRockTournamentId       = strtoupper(string: $name);
                            $grandFinalHardRockType               = $grandFinalHardRockJsonType;
                            $grandFinalHardRockImage              = $grandFinalHardRockData['beatmapset']['covers']['cover'];
                            $grandFinalHardRockUrl                = $grandFinalHardRockData['url'];
                            $grandFinalHardRockName               = $grandFinalHardRockData['beatmapset']['title'];
                            $grandFinalHardRockDifficultyName     = $grandFinalHardRockData['version'];
                            $grandFinalHardRockFeatureArtist      = $grandFinalHardRockData['beatmapset']['artist'];
                            $grandFinalHardRockMapper             = $grandFinalHardRockData['beatmapset']['creator'];
                            $grandFinalHardRockMapperUrl          = "https://osu.ppy.sh/users/{$grandFinalHardRockData['beatmapset']['user_id']}";
                            $grandFinalHardRockDifficulty         = $grandFinalHardRockData['difficulty_rating'];
                            $grandFinalHardRockLength             = $grandFinalHardRockData['total_length'];
                            $grandFinalHardRockOverallSpeed       = $grandFinalHardRockData['beatmapset']['bpm'];
                            $grandFinalHardRockOverallDifficulty  = $grandFinalHardRockData['accuracy'];
                            $grandFinalHardRockOverallHealth      = $grandFinalHardRockData['drain'];
                            $grandFinalHardRockPassCount          = $grandFinalHardRockData['passcount'];

                            $allMappoolHardRockData[] = [
                                'beatmap_id'                    => $grandFinalHardRockId,
                                'beatmap_round_id'              => $grandFinalHardRockRoundId,
                                'beatmap_tournament_id'         => $grandFinalHardRockTournamentId,
                                'beatmap_type'                  => $grandFinalHardRockType,
                                'beatmap_image'                 => $grandFinalHardRockImage,
                                'beatmap_url'                   => $grandFinalHardRockUrl,
                                'beatmap_name'                  => $grandFinalHardRockName,
                                'beatmap_difficulty_name'       => $grandFinalHardRockDifficultyName,
                                'beatmap_feature_artist'        => $grandFinalHardRockFeatureArtist,
                                'beatmap_mapper'                => $grandFinalHardRockMapper,
                                'beatmap_mapper_url'            => $grandFinalHardRockMapperUrl,
                                'beatmap_difficulty'            => $grandFinalHardRockDifficulty,
                                'beatmap_length'                => $grandFinalHardRockLength,
                                'beatmap_overall_speed'         => $grandFinalHardRockOverallSpeed,
                                'beatmap_overall_difficulty'    => $grandFinalHardRockOverallDifficulty,
                                'beatmap_overall_health'        => $grandFinalHardRockOverallHealth,
                                'beatmap_pass_count'            => $grandFinalHardRockPassCount
                            ];
                        }


                        /*** GRAND FINAL DT BEATMAP DATA ***/
                        $grandFinalDoubleTimeJsonData = $mappoolRoundJsonData[$round]['DT'];
                        foreach ($grandFinalDoubleTimeJsonData as $grandFinalDoubleTimeJsonType => $grandFinalDoubleTimeJsonId) {
                            $grandFinalDoubleTimeData = getTournamentMappoolData(
                                id: $grandFinalDoubleTimeJsonId,
                                token: $mappoolAccessToken
                            );

                            $grandFinalDoubleTimeId                 = $grandFinalDoubleTimeData['id'];
                            $grandFinalDoubleTimeRoundId            = $round;
                            $grandFinalDoubleTimeTournamentId       = strtoupper(string: $name);
                            $grandFinalDoubleTimeType               = $grandFinalDoubleTimeJsonType;
                            $grandFinalDoubleTimeImage              = $grandFinalDoubleTimeData['beatmapset']['covers']['cover'];
                            $grandFinalDoubleTimeUrl                = $grandFinalDoubleTimeData['url'];
                            $grandFinalDoubleTimeName               = $grandFinalDoubleTimeData['beatmapset']['title'];
                            $grandFinalDoubleTimeDifficultyName     = $grandFinalDoubleTimeData['version'];
                            $grandFinalDoubleTimeFeatureArtist      = $grandFinalDoubleTimeData['beatmapset']['artist'];
                            $grandFinalDoubleTimeMapper             = $grandFinalDoubleTimeData['beatmapset']['creator'];
                            $grandFinalDoubleTimeMapperUrl          = "https://osu.ppy.sh/users/{$grandFinalDoubleTimeData['beatmapset']['user_id']}";
                            $grandFinalDoubleTimeDifficulty         = $grandFinalDoubleTimeData['difficulty_rating'];
                            $grandFinalDoubleTimeLength             = $grandFinalDoubleTimeData['total_length'];
                            $grandFinalDoubleTimeOverallSpeed       = $grandFinalDoubleTimeData['beatmapset']['bpm'];
                            $grandFinalDoubleTimeOverallDifficulty  = $grandFinalDoubleTimeData['accuracy'];
                            $grandFinalDoubleTimeOverallHealth      = $grandFinalDoubleTimeData['drain'];
                            $grandFinalDoubleTimePassCount          = $grandFinalDoubleTimeData['passcount'];

                            $allMappoolDoubleTimeData[] = [
                                'beatmap_id'                    => $grandFinalDoubleTimeId,
                                'beatmap_round_id'              => $grandFinalDoubleTimeRoundId,
                                'beatmap_tournament_id'         => $grandFinalDoubleTimeTournamentId,
                                'beatmap_type'                  => $grandFinalDoubleTimeType,
                                'beatmap_image'                 => $grandFinalDoubleTimeImage,
                                'beatmap_url'                   => $grandFinalDoubleTimeUrl,
                                'beatmap_name'                  => $grandFinalDoubleTimeName,
                                'beatmap_difficulty_name'       => $grandFinalDoubleTimeDifficultyName,
                                'beatmap_feature_artist'        => $grandFinalDoubleTimeFeatureArtist,
                                'beatmap_mapper'                => $grandFinalDoubleTimeMapper,
                                'beatmap_mapper_url'            => $grandFinalDoubleTimeMapperUrl,
                                'beatmap_difficulty'            => $grandFinalDoubleTimeDifficulty,
                                'beatmap_length'                => $grandFinalDoubleTimeLength,
                                'beatmap_overall_speed'         => $grandFinalDoubleTimeOverallSpeed,
                                'beatmap_overall_difficulty'    => $grandFinalDoubleTimeOverallDifficulty,
                                'beatmap_overall_health'        => $grandFinalDoubleTimeOverallHealth,
                                'beatmap_pass_count'            => $grandFinalDoubleTimePassCount
                            ];
                        }


                        /*** GRAND FINAL FM BEATMAP DATA ***/
                        $grandFinalFreeModJsonData = $mappoolRoundJsonData[$round]['FM'];
                        foreach ($grandFinalFreeModJsonData as $grandFinalFreeModJsonType => $grandFinalFreeModJsonId) {
                            $grandFinalFreeModData = getTournamentMappoolData(
                                id: $grandFinalFreeModJsonId,
                                token: $mappoolAccessToken
                            );

                            $grandFinalFreeModId                 = $grandFinalFreeModData['id'];
                            $grandFinalFreeModRoundId            = $round;
                            $grandFinalFreeModTournamentId       = strtoupper(string: $name);
                            $grandFinalFreeModType               = $grandFinalFreeModJsonType;
                            $grandFinalFreeModImage              = $grandFinalFreeModData['beatmapset']['covers']['cover'];
                            $grandFinalFreeModUrl                = $grandFinalFreeModData['url'];
                            $grandFinalFreeModName               = $grandFinalFreeModData['beatmapset']['title'];
                            $grandFinalFreeModDifficultyName     = $grandFinalFreeModData['version'];
                            $grandFinalFreeModFeatureArtist      = $grandFinalFreeModData['beatmapset']['artist'];
                            $grandFinalFreeModMapper             = $grandFinalFreeModData['beatmapset']['creator'];
                            $grandFinalFreeModMapperUrl          = "https://osu.ppy.sh/users/{$grandFinalFreeModData['beatmapset']['user_id']}";
                            $grandFinalFreeModDifficulty         = $grandFinalFreeModData['difficulty_rating'];
                            $grandFinalFreeModLength             = $grandFinalFreeModData['total_length'];
                            $grandFinalFreeModOverallSpeed       = $grandFinalFreeModData['beatmapset']['bpm'];
                            $grandFinalFreeModOverallDifficulty  = $grandFinalFreeModData['accuracy'];
                            $grandFinalFreeModOverallHealth      = $grandFinalFreeModData['drain'];
                            $grandFinalFreeModPassCount          = $grandFinalFreeModData['passcount'];

                            $allMappoolFreeModData[] = [
                                'beatmap_id'                    => $grandFinalFreeModId,
                                'beatmap_round_id'              => $grandFinalFreeModRoundId,
                                'beatmap_tournament_id'         => $grandFinalFreeModTournamentId,
                                'beatmap_type'                  => $grandFinalFreeModType,
                                'beatmap_image'                 => $grandFinalFreeModImage,
                                'beatmap_url'                   => $grandFinalFreeModUrl,
                                'beatmap_name'                  => $grandFinalFreeModName,
                                'beatmap_difficulty_name'       => $grandFinalFreeModDifficultyName,
                                'beatmap_feature_artist'        => $grandFinalFreeModFeatureArtist,
                                'beatmap_mapper'                => $grandFinalFreeModMapper,
                                'beatmap_mapper_url'            => $grandFinalFreeModMapperUrl,
                                'beatmap_difficulty'            => $grandFinalFreeModDifficulty,
                                'beatmap_length'                => $grandFinalFreeModLength,
                                'beatmap_overall_speed'         => $grandFinalFreeModOverallSpeed,
                                'beatmap_overall_difficulty'    => $grandFinalFreeModOverallDifficulty,
                                'beatmap_overall_health'        => $grandFinalFreeModOverallHealth,
                                'beatmap_pass_count'            => $grandFinalFreeModPassCount
                            ];
                        }


                        /*** GRAND FINAL EZ BEATMAP DATA ***/
                        $grandFinalEasyJsonData = $mappoolRoundJsonData[$round]['FM'];
                        foreach ($grandFinalEasyJsonData as $grandFinalEasyJsonType => $grandFinalEasyJsonId) {
                            $grandFinalEasyData = getTournamentMappoolData(
                                id: $grandFinalEasyJsonId,
                                token: $mappoolAccessToken
                            );

                            $grandFinalEasyId                 = $grandFinalEasyData['id'];
                            $grandFinalEasyRoundId            = $round;
                            $grandFinalEasyTournamentId       = strtoupper(string: $name);
                            $grandFinalEasyType               = $grandFinalEasyJsonType;
                            $grandFinalEasyImage              = $grandFinalEasyData['beatmapset']['covers']['cover'];
                            $grandFinalEasyUrl                = $grandFinalEasyData['url'];
                            $grandFinalEasyName               = $grandFinalEasyData['beatmapset']['title'];
                            $grandFinalEasyDifficultyName     = $grandFinalEasyData['version'];
                            $grandFinalEasyFeatureArtist      = $grandFinalEasyData['beatmapset']['artist'];
                            $grandFinalEasyMapper             = $grandFinalEasyData['beatmapset']['creator'];
                            $grandFinalEasyMapperUrl          = "https://osu.ppy.sh/users/{$grandFinalEasyData['beatmapset']['user_id']}";
                            $grandFinalEasyDifficulty         = $grandFinalEasyData['difficulty_rating'];
                            $grandFinalEasyLength             = $grandFinalEasyData['total_length'];
                            $grandFinalEasyOverallSpeed       = $grandFinalEasyData['beatmapset']['bpm'];
                            $grandFinalEasyOverallDifficulty  = $grandFinalEasyData['accuracy'];
                            $grandFinalEasyOverallHealth      = $grandFinalEasyData['drain'];
                            $grandFinalEasyPassCount          = $grandFinalEasyData['passcount'];

                            $allMappoolEasyData[] = [
                                'beatmap_id'                    => $grandFinalEasyId,
                                'beatmap_round_id'              => $grandFinalEasyRoundId,
                                'beatmap_tournament_id'         => $grandFinalEasyTournamentId,
                                'beatmap_type'                  => $grandFinalEasyType,
                                'beatmap_image'                 => $grandFinalEasyImage,
                                'beatmap_url'                   => $grandFinalEasyUrl,
                                'beatmap_name'                  => $grandFinalEasyName,
                                'beatmap_difficulty_name'       => $grandFinalEasyDifficultyName,
                                'beatmap_feature_artist'        => $grandFinalEasyFeatureArtist,
                                'beatmap_mapper'                => $grandFinalEasyMapper,
                                'beatmap_mapper_url'            => $grandFinalEasyMapperUrl,
                                'beatmap_difficulty'            => $grandFinalEasyDifficulty,
                                'beatmap_length'                => $grandFinalEasyLength,
                                'beatmap_overall_speed'         => $grandFinalEasyOverallSpeed,
                                'beatmap_overall_difficulty'    => $grandFinalEasyOverallDifficulty,
                                'beatmap_overall_health'        => $grandFinalEasyOverallHealth,
                                'beatmap_pass_count'            => $grandFinalEasyPassCount
                            ];
                        }

                        /*** GRAND FINAL HDHR BEATMAP DATA ***/
                        $grandFinalHiddenHardRockJsonData = $mappoolRoundJsonData[$round]['FM'];
                        foreach ($grandFinalHiddenHardRockJsonData as $grandFinalHiddenHardRockJsonType => $grandFinalHiddenHardRockJsonId) {
                            $grandFinalHiddenHardRockData = getTournamentMappoolData(
                                id: $grandFinalHiddenHardRockJsonId,
                                token: $mappoolAccessToken
                            );

                            $grandFinalHiddenHardRockId                 = $grandFinalHiddenHardRockData['id'];
                            $grandFinalHiddenHardRockRoundId            = $round;
                            $grandFinalHiddenHardRockTournamentId       = strtoupper(string: $name);
                            $grandFinalHiddenHardRockType               = $grandFinalHiddenHardRockJsonType;
                            $grandFinalHiddenHardRockImage              = $grandFinalHiddenHardRockData['beatmapset']['covers']['cover'];
                            $grandFinalHiddenHardRockUrl                = $grandFinalHiddenHardRockData['url'];
                            $grandFinalHiddenHardRockName               = $grandFinalHiddenHardRockData['beatmapset']['title'];
                            $grandFinalHiddenHardRockDifficultyName     = $grandFinalHiddenHardRockData['version'];
                            $grandFinalHiddenHardRockFeatureArtist      = $grandFinalHiddenHardRockData['beatmapset']['artist'];
                            $grandFinalHiddenHardRockMapper             = $grandFinalHiddenHardRockData['beatmapset']['creator'];
                            $grandFinalHiddenHardRockMapperUrl          = "https://osu.ppy.sh/users/{$grandFinalHiddenHardRockData['beatmapset']['user_id']}";
                            $grandFinalHiddenHardRockDifficulty         = $grandFinalHiddenHardRockData['difficulty_rating'];
                            $grandFinalHiddenHardRockLength             = $grandFinalHiddenHardRockData['total_length'];
                            $grandFinalHiddenHardRockOverallSpeed       = $grandFinalHiddenHardRockData['beatmapset']['bpm'];
                            $grandFinalHiddenHardRockOverallDifficulty  = $grandFinalHiddenHardRockData['accuracy'];
                            $grandFinalHiddenHardRockOverallHealth      = $grandFinalHiddenHardRockData['drain'];
                            $grandFinalHiddenHardRockPassCount          = $grandFinalHiddenHardRockData['passcount'];

                            $allMappoolHiddenHardRockData[] = [
                                'beatmap_id'                    => $grandFinalHiddenHardRockId,
                                'beatmap_round_id'              => $grandFinalHiddenHardRockRoundId,
                                'beatmap_tournament_id'         => $grandFinalHiddenHardRockTournamentId,
                                'beatmap_type'                  => $grandFinalHiddenHardRockType,
                                'beatmap_image'                 => $grandFinalHiddenHardRockImage,
                                'beatmap_url'                   => $grandFinalHiddenHardRockUrl,
                                'beatmap_name'                  => $grandFinalHiddenHardRockName,
                                'beatmap_difficulty_name'       => $grandFinalHiddenHardRockDifficultyName,
                                'beatmap_feature_artist'        => $grandFinalHiddenHardRockFeatureArtist,
                                'beatmap_mapper'                => $grandFinalHiddenHardRockMapper,
                                'beatmap_mapper_url'            => $grandFinalHiddenHardRockMapperUrl,
                                'beatmap_difficulty'            => $grandFinalHiddenHardRockDifficulty,
                                'beatmap_length'                => $grandFinalHiddenHardRockLength,
                                'beatmap_overall_speed'         => $grandFinalHiddenHardRockOverallSpeed,
                                'beatmap_overall_difficulty'    => $grandFinalHiddenHardRockOverallDifficulty,
                                'beatmap_overall_health'        => $grandFinalHiddenHardRockOverallHealth,
                                'beatmap_pass_count'            => $grandFinalHiddenHardRockPassCount
                            ];
                        }


                        /*** GRAND FINAL TB BEATMAP DATA ***/
                        $grandFinalTieBreakerJsonData = $mappoolRoundJsonData[$round]['FM'];
                        foreach ($grandFinalTieBreakerJsonData as $grandFinalTieBreakerJsonType => $grandFinalTieBreakerJsonId) {
                            $grandFinalTieBreakerData = getTournamentMappoolData(
                                id: $grandFinalTieBreakerJsonId,
                                token: $mappoolAccessToken
                            );

                            $grandFinalTieBreakerId                 = $grandFinalTieBreakerData['id'];
                            $grandFinalTieBreakerRoundId            = $round;
                            $grandFinalTieBreakerTournamentId       = strtoupper(string: $name);
                            $grandFinalTieBreakerType               = $grandFinalTieBreakerJsonType;
                            $grandFinalTieBreakerImage              = $grandFinalTieBreakerData['beatmapset']['covers']['cover'];
                            $grandFinalTieBreakerUrl                = $grandFinalTieBreakerData['url'];
                            $grandFinalTieBreakerName               = $grandFinalTieBreakerData['beatmapset']['title'];
                            $grandFinalTieBreakerDifficultyName     = $grandFinalTieBreakerData['version'];
                            $grandFinalTieBreakerFeatureArtist      = $grandFinalTieBreakerData['beatmapset']['artist'];
                            $grandFinalTieBreakerMapper             = $grandFinalTieBreakerData['beatmapset']['creator'];
                            $grandFinalTieBreakerMapperUrl          = "https://osu.ppy.sh/users/{$grandFinalTieBreakerData['beatmapset']['user_id']}";
                            $grandFinalTieBreakerDifficulty         = $grandFinalTieBreakerData['difficulty_rating'];
                            $grandFinalTieBreakerLength             = $grandFinalTieBreakerData['total_length'];
                            $grandFinalTieBreakerOverallSpeed       = $grandFinalTieBreakerData['beatmapset']['bpm'];
                            $grandFinalTieBreakerOverallDifficulty  = $grandFinalTieBreakerData['accuracy'];
                            $grandFinalTieBreakerOverallHealth      = $grandFinalTieBreakerData['drain'];
                            $grandFinalTieBreakerPassCount          = $grandFinalTieBreakerData['passcount'];

                            $allMappoolTieBreakerData[] = [
                                'beatmap_id'                    => $grandFinalTieBreakerId,
                                'beatmap_round_id'              => $grandFinalTieBreakerRoundId,
                                'beatmap_tournament_id'         => $grandFinalTieBreakerTournamentId,
                                'beatmap_type'                  => $grandFinalTieBreakerType,
                                'beatmap_image'                 => $grandFinalTieBreakerImage,
                                'beatmap_url'                   => $grandFinalTieBreakerUrl,
                                'beatmap_name'                  => $grandFinalTieBreakerName,
                                'beatmap_difficulty_name'       => $grandFinalTieBreakerDifficultyName,
                                'beatmap_feature_artist'        => $grandFinalTieBreakerFeatureArtist,
                                'beatmap_mapper'                => $grandFinalTieBreakerMapper,
                                'beatmap_mapper_url'            => $grandFinalTieBreakerMapperUrl,
                                'beatmap_difficulty'            => $grandFinalTieBreakerDifficulty,
                                'beatmap_length'                => $grandFinalTieBreakerLength,
                                'beatmap_overall_speed'         => $grandFinalTieBreakerOverallSpeed,
                                'beatmap_overall_difficulty'    => $grandFinalTieBreakerOverallDifficulty,
                                'beatmap_overall_health'        => $grandFinalTieBreakerOverallHealth,
                                'beatmap_pass_count'            => $grandFinalTieBreakerPassCount
                            ];
                        }
                        break;

                    case 'ASTR':
                        /*** ALL STAR NM BEATMAP DATA ***/
                        $allStarEdgeCaseName = 'GF'; // It's easier to replace a variable than having to type multiple same string together

                        $allStarNoModJsonData = $mappoolRoundJsonData[$allStarEdgeCaseName]['NM'];
                        /*
                        Because All Star mappool is basically the same as Grand
                        Final mappool, so I'll just being a bit hacky here by
                        using Grand Final mappool data straight away. This'll
                        prevent me adding the same beatmap ID into the database,
                        which violate the uniqueness of ID column in the table.
                        */
                        foreach ($allStarNoModJsonData as $allStarNoModJsonType => $allStarNoModJsonId) {
                            $allStarNoModData = getTournamentMappoolData(
                                id: $allStarNoModJsonId,
                                token: $mappoolAccessToken
                            );

                            $allStarNoModId                 = $allStarNoModData['id'];
                            $allStarNoModRoundId            = $allStarEdgeCaseName;
                            $allStarNoModTournamentId       = strtoupper(string: $name);
                            $allStarNoModType               = $allStarNoModJsonType;
                            $allStarNoModImage              = $allStarNoModData['beatmapset']['covers']['cover'];
                            $allStarNoModUrl                = $allStarNoModData['url'];
                            $allStarNoModName               = $allStarNoModData['beatmapset']['title'];
                            $allStarNoModDifficultyName     = $allStarNoModData['version'];
                            $allStarNoModFeatureArtist      = $allStarNoModData['beatmapset']['artist'];
                            $allStarNoModMapper             = $allStarNoModData['beatmapset']['creator'];
                            $allStarNoModMapperUrl          = "https://osu.ppy.sh/users/{$allStarNoModData['beatmapset']['user_id']}";
                            $allStarNoModDifficulty         = $allStarNoModData['difficulty_rating'];
                            $allStarNoModLength             = $allStarNoModData['total_length'];
                            $allStarNoModOverallSpeed       = $allStarNoModData['beatmapset']['bpm'];
                            $allStarNoModOverallDifficulty  = $allStarNoModData['accuracy'];
                            $allStarNoModOverallHealth      = $allStarNoModData['drain'];
                            $allStarNoModPassCount          = $allStarNoModData['passcount'];

                            $allMappoolNoModData[] = [
                                'beatmap_id'                    => $allStarNoModId,
                                'beatmap_round_id'              => $allStarNoModRoundId,
                                'beatmap_tournament_id'         => $allStarNoModTournamentId,
                                'beatmap_type'                  => $allStarNoModType,
                                'beatmap_image'                 => $allStarNoModImage,
                                'beatmap_url'                   => $allStarNoModUrl,
                                'beatmap_name'                  => $allStarNoModName,
                                'beatmap_difficulty_name'       => $allStarNoModDifficultyName,
                                'beatmap_feature_artist'        => $allStarNoModFeatureArtist,
                                'beatmap_mapper'                => $allStarNoModMapper,
                                'beatmap_mapper_url'            => $allStarNoModMapperUrl,
                                'beatmap_difficulty'            => $allStarNoModDifficulty,
                                'beatmap_length'                => $allStarNoModLength,
                                'beatmap_overall_speed'         => $allStarNoModOverallSpeed,
                                'beatmap_overall_difficulty'    => $allStarNoModOverallDifficulty,
                                'beatmap_overall_health'        => $allStarNoModOverallHealth,
                                'beatmap_pass_count'            => $allStarNoModPassCount
                            ];
                        }


                        /*** ALL STAR HD BEATMAP DATA ***/
                        $allStarHiddenJsonData = $mappoolRoundJsonData[$allStarEdgeCaseName]['HD'];
                        foreach ($allStarHiddenJsonData as $allStarHiddenJsonType => $allStarHiddenJsonId) {
                            $allStarHiddenData = getTournamentMappoolData(
                                id: $allStarHiddenJsonId,
                                token: $mappoolAccessToken
                            );

                            $allStarHiddenId                 = $allStarHiddenData['id'];
                            $allStarHiddenRoundId            = $allStarEdgeCaseName;
                            $allStarHiddenTournamentId       = strtoupper(string: $name);
                            $allStarHiddenType               = $allStarHiddenJsonType;
                            $allStarHiddenImage              = $allStarHiddenData['beatmapset']['covers']['cover'];
                            $allStarHiddenUrl                = $allStarHiddenData['url'];
                            $allStarHiddenName               = $allStarHiddenData['beatmapset']['title'];
                            $allStarHiddenDifficultyName     = $allStarHiddenData['version'];
                            $allStarHiddenFeatureArtist      = $allStarHiddenData['beatmapset']['artist'];
                            $allStarHiddenMapper             = $allStarHiddenData['beatmapset']['creator'];
                            $allStarHiddenMapperUrl          = "https://osu.ppy.sh/users/{$allStarHiddenData['beatmapset']['user_id']}";
                            $allStarHiddenDifficulty         = $allStarHiddenData['difficulty_rating'];
                            $allStarHiddenLength             = $allStarHiddenData['total_length'];
                            $allStarHiddenOverallSpeed       = $allStarHiddenData['beatmapset']['bpm'];
                            $allStarHiddenOverallDifficulty  = $allStarHiddenData['accuracy'];
                            $allStarHiddenOverallHealth      = $allStarHiddenData['drain'];
                            $allStarHiddenPassCount          = $allStarHiddenData['passcount'];

                            $allMappoolHiddenData[] = [
                                'beatmap_id'                    => $allStarHiddenId,
                                'beatmap_round_id'              => $allStarHiddenRoundId,
                                'beatmap_tournament_id'         => $allStarHiddenTournamentId,
                                'beatmap_type'                  => $allStarHiddenType,
                                'beatmap_image'                 => $allStarHiddenImage,
                                'beatmap_url'                   => $allStarHiddenUrl,
                                'beatmap_name'                  => $allStarHiddenName,
                                'beatmap_difficulty_name'       => $allStarHiddenDifficultyName,
                                'beatmap_feature_artist'        => $allStarHiddenFeatureArtist,
                                'beatmap_mapper'                => $allStarHiddenMapper,
                                'beatmap_mapper_url'            => $allStarHiddenMapperUrl,
                                'beatmap_difficulty'            => $allStarHiddenDifficulty,
                                'beatmap_length'                => $allStarHiddenLength,
                                'beatmap_overall_speed'         => $allStarHiddenOverallSpeed,
                                'beatmap_overall_difficulty'    => $allStarHiddenOverallDifficulty,
                                'beatmap_overall_health'        => $allStarHiddenOverallHealth,
                                'beatmap_pass_count'            => $allStarHiddenPassCount
                            ];
                        }


                        /*** ALL STAR HR BEATMAP DATA ***/
                        $allStarHardRockJsonData = $mappoolRoundJsonData[$allStarEdgeCaseName]['HR'];
                        foreach ($allStarHardRockJsonData as $allStarHardRockJsonType => $allStarHardRockJsonId) {
                            $allStarHardRockData = getTournamentMappoolData(
                                id: $allStarHardRockJsonId,
                                token: $mappoolAccessToken
                            );

                            $allStarHardRockId                 = $allStarHardRockData['id'];
                            $allStarHardRockRoundId            = $allStarEdgeCaseName;
                            $allStarHardRockTournamentId       = strtoupper(string: $name);
                            $allStarHardRockType               = $allStarHardRockJsonType;
                            $allStarHardRockImage              = $allStarHardRockData['beatmapset']['covers']['cover'];
                            $allStarHardRockUrl                = $allStarHardRockData['url'];
                            $allStarHardRockName               = $allStarHardRockData['beatmapset']['title'];
                            $allStarHardRockDifficultyName     = $allStarHardRockData['version'];
                            $allStarHardRockFeatureArtist      = $allStarHardRockData['beatmapset']['artist'];
                            $allStarHardRockMapper             = $allStarHardRockData['beatmapset']['creator'];
                            $allStarHardRockMapperUrl          = "https://osu.ppy.sh/users/{$allStarHardRockData['beatmapset']['user_id']}";
                            $allStarHardRockDifficulty         = $allStarHardRockData['difficulty_rating'];
                            $allStarHardRockLength             = $allStarHardRockData['total_length'];
                            $allStarHardRockOverallSpeed       = $allStarHardRockData['beatmapset']['bpm'];
                            $allStarHardRockOverallDifficulty  = $allStarHardRockData['accuracy'];
                            $allStarHardRockOverallHealth      = $allStarHardRockData['drain'];
                            $allStarHardRockPassCount          = $allStarHardRockData['passcount'];

                            $allMappoolHardRockData[] = [
                                'beatmap_id'                    => $allStarHardRockId,
                                'beatmap_round_id'              => $allStarHardRockRoundId,
                                'beatmap_tournament_id'         => $allStarHardRockTournamentId,
                                'beatmap_type'                  => $allStarHardRockType,
                                'beatmap_image'                 => $allStarHardRockImage,
                                'beatmap_url'                   => $allStarHardRockUrl,
                                'beatmap_name'                  => $allStarHardRockName,
                                'beatmap_difficulty_name'       => $allStarHardRockDifficultyName,
                                'beatmap_feature_artist'        => $allStarHardRockFeatureArtist,
                                'beatmap_mapper'                => $allStarHardRockMapper,
                                'beatmap_mapper_url'            => $allStarHardRockMapperUrl,
                                'beatmap_difficulty'            => $allStarHardRockDifficulty,
                                'beatmap_length'                => $allStarHardRockLength,
                                'beatmap_overall_speed'         => $allStarHardRockOverallSpeed,
                                'beatmap_overall_difficulty'    => $allStarHardRockOverallDifficulty,
                                'beatmap_overall_health'        => $allStarHardRockOverallHealth,
                                'beatmap_pass_count'            => $allStarHardRockPassCount
                            ];
                        }


                        /*** ALL STAR DT BEATMAP DATA ***/
                        $allStarDoubleTimeJsonData = $mappoolRoundJsonData[$allStarEdgeCaseName]['DT'];
                        foreach ($allStarDoubleTimeJsonData as $allStarDoubleTimeJsonType => $allStarDoubleTimeJsonId) {
                            $allStarDoubleTimeData = getTournamentMappoolData(
                                id: $allStarDoubleTimeJsonId,
                                token: $mappoolAccessToken
                            );

                            $allStarDoubleTimeId                 = $allStarDoubleTimeData['id'];
                            $allStarDoubleTimeRoundId            = $allStarEdgeCaseName;
                            $allStarDoubleTimeTournamentId       = strtoupper(string: $name);
                            $allStarDoubleTimeType               = $allStarDoubleTimeJsonType;
                            $allStarDoubleTimeImage              = $allStarDoubleTimeData['beatmapset']['covers']['cover'];
                            $allStarDoubleTimeUrl                = $allStarDoubleTimeData['url'];
                            $allStarDoubleTimeName               = $allStarDoubleTimeData['beatmapset']['title'];
                            $allStarDoubleTimeDifficultyName     = $allStarDoubleTimeData['version'];
                            $allStarDoubleTimeFeatureArtist      = $allStarDoubleTimeData['beatmapset']['artist'];
                            $allStarDoubleTimeMapper             = $allStarDoubleTimeData['beatmapset']['creator'];
                            $allStarDoubleTimeMapperUrl          = "https://osu.ppy.sh/users/{$allStarDoubleTimeData['beatmapset']['user_id']}";
                            $allStarDoubleTimeDifficulty         = $allStarDoubleTimeData['difficulty_rating'];
                            $allStarDoubleTimeLength             = $allStarDoubleTimeData['total_length'];
                            $allStarDoubleTimeOverallSpeed       = $allStarDoubleTimeData['beatmapset']['bpm'];
                            $allStarDoubleTimeOverallDifficulty  = $allStarDoubleTimeData['accuracy'];
                            $allStarDoubleTimeOverallHealth      = $allStarDoubleTimeData['drain'];
                            $allStarDoubleTimePassCount          = $allStarDoubleTimeData['passcount'];

                            $allMappoolDoubleTimeData[] = [
                                'beatmap_id'                    => $allStarDoubleTimeId,
                                'beatmap_round_id'              => $allStarDoubleTimeRoundId,
                                'beatmap_tournament_id'         => $allStarDoubleTimeTournamentId,
                                'beatmap_type'                  => $allStarDoubleTimeType,
                                'beatmap_image'                 => $allStarDoubleTimeImage,
                                'beatmap_url'                   => $allStarDoubleTimeUrl,
                                'beatmap_name'                  => $allStarDoubleTimeName,
                                'beatmap_difficulty_name'       => $allStarDoubleTimeDifficultyName,
                                'beatmap_feature_artist'        => $allStarDoubleTimeFeatureArtist,
                                'beatmap_mapper'                => $allStarDoubleTimeMapper,
                                'beatmap_mapper_url'            => $allStarDoubleTimeMapperUrl,
                                'beatmap_difficulty'            => $allStarDoubleTimeDifficulty,
                                'beatmap_length'                => $allStarDoubleTimeLength,
                                'beatmap_overall_speed'         => $allStarDoubleTimeOverallSpeed,
                                'beatmap_overall_difficulty'    => $allStarDoubleTimeOverallDifficulty,
                                'beatmap_overall_health'        => $allStarDoubleTimeOverallHealth,
                                'beatmap_pass_count'            => $allStarDoubleTimePassCount
                            ];
                        }


                        /*** ALL STAR FM BEATMAP DATA ***/
                        $allStarFreeModJsonData = $mappoolRoundJsonData[$allStarEdgeCaseName]['FM'];
                        foreach ($allStarFreeModJsonData as $allStarFreeModJsonType => $allStarFreeModJsonId) {
                            $allStarFreeModData = getTournamentMappoolData(
                                id: $allStarFreeModJsonId,
                                token: $mappoolAccessToken
                            );

                            $allStarFreeModId                 = $allStarFreeModData['id'];
                            $allStarFreeModRoundId            = $allStarEdgeCaseName;
                            $allStarFreeModTournamentId       = strtoupper(string: $name);
                            $allStarFreeModType               = $allStarFreeModJsonType;
                            $allStarFreeModImage              = $allStarFreeModData['beatmapset']['covers']['cover'];
                            $allStarFreeModUrl                = $allStarFreeModData['url'];
                            $allStarFreeModName               = $allStarFreeModData['beatmapset']['title'];
                            $allStarFreeModDifficultyName     = $allStarFreeModData['version'];
                            $allStarFreeModFeatureArtist      = $allStarFreeModData['beatmapset']['artist'];
                            $allStarFreeModMapper             = $allStarFreeModData['beatmapset']['creator'];
                            $allStarFreeModMapperUrl          = "https://osu.ppy.sh/users/{$allStarFreeModData['beatmapset']['user_id']}";
                            $allStarFreeModDifficulty         = $allStarFreeModData['difficulty_rating'];
                            $allStarFreeModLength             = $allStarFreeModData['total_length'];
                            $allStarFreeModOverallSpeed       = $allStarFreeModData['beatmapset']['bpm'];
                            $allStarFreeModOverallDifficulty  = $allStarFreeModData['accuracy'];
                            $allStarFreeModOverallHealth      = $allStarFreeModData['drain'];
                            $allStarFreeModPassCount          = $allStarFreeModData['passcount'];

                            $allMappoolFreeModData[] = [
                                'beatmap_id'                    => $allStarFreeModId,
                                'beatmap_round_id'              => $allStarFreeModRoundId,
                                'beatmap_tournament_id'         => $allStarFreeModTournamentId,
                                'beatmap_type'                  => $allStarFreeModType,
                                'beatmap_image'                 => $allStarFreeModImage,
                                'beatmap_url'                   => $allStarFreeModUrl,
                                'beatmap_name'                  => $allStarFreeModName,
                                'beatmap_difficulty_name'       => $allStarFreeModDifficultyName,
                                'beatmap_feature_artist'        => $allStarFreeModFeatureArtist,
                                'beatmap_mapper'                => $allStarFreeModMapper,
                                'beatmap_mapper_url'            => $allStarFreeModMapperUrl,
                                'beatmap_difficulty'            => $allStarFreeModDifficulty,
                                'beatmap_length'                => $allStarFreeModLength,
                                'beatmap_overall_speed'         => $allStarFreeModOverallSpeed,
                                'beatmap_overall_difficulty'    => $allStarFreeModOverallDifficulty,
                                'beatmap_overall_health'        => $allStarFreeModOverallHealth,
                                'beatmap_pass_count'            => $allStarFreeModPassCount
                            ];
                        }


                        /*** ALL STAR EZ BEATMAP DATA ***/
                        $allStarEasyJsonData = $mappoolRoundJsonData[$allStarEdgeCaseName]['FM'];
                        foreach ($allStarEasyJsonData as $allStarEasyJsonType => $allStarEasyJsonId) {
                            $allStarEasyData = getTournamentMappoolData(
                                id: $allStarEasyJsonId,
                                token: $mappoolAccessToken
                            );

                            $allStarEasyId                 = $allStarEasyData['id'];
                            $allStarEasyRoundId            = $allStarEdgeCaseName;
                            $allStarEasyTournamentId       = strtoupper(string: $name);
                            $allStarEasyType               = $allStarEasyJsonType;
                            $allStarEasyImage              = $allStarEasyData['beatmapset']['covers']['cover'];
                            $allStarEasyUrl                = $allStarEasyData['url'];
                            $allStarEasyName               = $allStarEasyData['beatmapset']['title'];
                            $allStarEasyDifficultyName     = $allStarEasyData['version'];
                            $allStarEasyFeatureArtist      = $allStarEasyData['beatmapset']['artist'];
                            $allStarEasyMapper             = $allStarEasyData['beatmapset']['creator'];
                            $allStarEasyMapperUrl          = "https://osu.ppy.sh/users/{$allStarEasyData['beatmapset']['user_id']}";
                            $allStarEasyDifficulty         = $allStarEasyData['difficulty_rating'];
                            $allStarEasyLength             = $allStarEasyData['total_length'];
                            $allStarEasyOverallSpeed       = $allStarEasyData['beatmapset']['bpm'];
                            $allStarEasyOverallDifficulty  = $allStarEasyData['accuracy'];
                            $allStarEasyOverallHealth      = $allStarEasyData['drain'];
                            $allStarEasyPassCount          = $allStarEasyData['passcount'];

                            $allMappoolEasyData[] = [
                                'beatmap_id'                    => $allStarEasyId,
                                'beatmap_round_id'              => $allStarEasyRoundId,
                                'beatmap_tournament_id'         => $allStarEasyTournamentId,
                                'beatmap_type'                  => $allStarEasyType,
                                'beatmap_image'                 => $allStarEasyImage,
                                'beatmap_url'                   => $allStarEasyUrl,
                                'beatmap_name'                  => $allStarEasyName,
                                'beatmap_difficulty_name'       => $allStarEasyDifficultyName,
                                'beatmap_feature_artist'        => $allStarEasyFeatureArtist,
                                'beatmap_mapper'                => $allStarEasyMapper,
                                'beatmap_mapper_url'            => $allStarEasyMapperUrl,
                                'beatmap_difficulty'            => $allStarEasyDifficulty,
                                'beatmap_length'                => $allStarEasyLength,
                                'beatmap_overall_speed'         => $allStarEasyOverallSpeed,
                                'beatmap_overall_difficulty'    => $allStarEasyOverallDifficulty,
                                'beatmap_overall_health'        => $allStarEasyOverallHealth,
                                'beatmap_pass_count'            => $allStarEasyPassCount
                            ];
                        }

                        /*** ALL STAR HDHR BEATMAP DATA ***/
                        $allStarHiddenHardRockJsonData = $mappoolRoundJsonData[$allStarEdgeCaseName]['FM'];
                        foreach ($allStarHiddenHardRockJsonData as $allStarHiddenHardRockJsonType => $allStarHiddenHardRockJsonId) {
                            $allStarHiddenHardRockData = getTournamentMappoolData(
                                id: $allStarHiddenHardRockJsonId,
                                token: $mappoolAccessToken
                            );

                            $allStarHiddenHardRockId                 = $allStarHiddenHardRockData['id'];
                            $allStarHiddenHardRockRoundId            = $allStarEdgeCaseName;
                            $allStarHiddenHardRockTournamentId       = strtoupper(string: $name);
                            $allStarHiddenHardRockType               = $allStarHiddenHardRockJsonType;
                            $allStarHiddenHardRockImage              = $allStarHiddenHardRockData['beatmapset']['covers']['cover'];
                            $allStarHiddenHardRockUrl                = $allStarHiddenHardRockData['url'];
                            $allStarHiddenHardRockName               = $allStarHiddenHardRockData['beatmapset']['title'];
                            $allStarHiddenHardRockDifficultyName     = $allStarHiddenHardRockData['version'];
                            $allStarHiddenHardRockFeatureArtist      = $allStarHiddenHardRockData['beatmapset']['artist'];
                            $allStarHiddenHardRockMapper             = $allStarHiddenHardRockData['beatmapset']['creator'];
                            $allStarHiddenHardRockMapperUrl          = "https://osu.ppy.sh/users/{$allStarHiddenHardRockData['beatmapset']['user_id']}";
                            $allStarHiddenHardRockDifficulty         = $allStarHiddenHardRockData['difficulty_rating'];
                            $allStarHiddenHardRockLength             = $allStarHiddenHardRockData['total_length'];
                            $allStarHiddenHardRockOverallSpeed       = $allStarHiddenHardRockData['beatmapset']['bpm'];
                            $allStarHiddenHardRockOverallDifficulty  = $allStarHiddenHardRockData['accuracy'];
                            $allStarHiddenHardRockOverallHealth      = $allStarHiddenHardRockData['drain'];
                            $allStarHiddenHardRockPassCount          = $allStarHiddenHardRockData['passcount'];

                            $allMappoolHiddenHardRockData[] = [
                                'beatmap_id'                    => $allStarHiddenHardRockId,
                                'beatmap_round_id'              => $allStarHiddenHardRockRoundId,
                                'beatmap_tournament_id'         => $allStarHiddenHardRockTournamentId,
                                'beatmap_type'                  => $allStarHiddenHardRockType,
                                'beatmap_image'                 => $allStarHiddenHardRockImage,
                                'beatmap_url'                   => $allStarHiddenHardRockUrl,
                                'beatmap_name'                  => $allStarHiddenHardRockName,
                                'beatmap_difficulty_name'       => $allStarHiddenHardRockDifficultyName,
                                'beatmap_feature_artist'        => $allStarHiddenHardRockFeatureArtist,
                                'beatmap_mapper'                => $allStarHiddenHardRockMapper,
                                'beatmap_mapper_url'            => $allStarHiddenHardRockMapperUrl,
                                'beatmap_difficulty'            => $allStarHiddenHardRockDifficulty,
                                'beatmap_length'                => $allStarHiddenHardRockLength,
                                'beatmap_overall_speed'         => $allStarHiddenHardRockOverallSpeed,
                                'beatmap_overall_difficulty'    => $allStarHiddenHardRockOverallDifficulty,
                                'beatmap_overall_health'        => $allStarHiddenHardRockOverallHealth,
                                'beatmap_pass_count'            => $allStarHiddenHardRockPassCount
                            ];
                        }


                        /*** ALL STAR TB BEATMAP DATA ***/
                        $allStarTieBreakerJsonData = $mappoolRoundJsonData[$allStarEdgeCaseName]['FM'];
                        foreach ($allStarTieBreakerJsonData as $allStarTieBreakerJsonType => $allStarTieBreakerJsonId) {
                            $allStarTieBreakerData = getTournamentMappoolData(
                                id: $allStarTieBreakerJsonId,
                                token: $mappoolAccessToken
                            );

                            $allStarTieBreakerId                 = $allStarTieBreakerData['id'];
                            $allStarTieBreakerRoundId            = $allStarEdgeCaseName;
                            $allStarTieBreakerTournamentId       = strtoupper(string: $name);
                            $allStarTieBreakerType               = $allStarTieBreakerJsonType;
                            $allStarTieBreakerImage              = $allStarTieBreakerData['beatmapset']['covers']['cover'];
                            $allStarTieBreakerUrl                = $allStarTieBreakerData['url'];
                            $allStarTieBreakerName               = $allStarTieBreakerData['beatmapset']['title'];
                            $allStarTieBreakerDifficultyName     = $allStarTieBreakerData['version'];
                            $allStarTieBreakerFeatureArtist      = $allStarTieBreakerData['beatmapset']['artist'];
                            $allStarTieBreakerMapper             = $allStarTieBreakerData['beatmapset']['creator'];
                            $allStarTieBreakerMapperUrl          = "https://osu.ppy.sh/users/{$allStarTieBreakerData['beatmapset']['user_id']}";
                            $allStarTieBreakerDifficulty         = $allStarTieBreakerData['difficulty_rating'];
                            $allStarTieBreakerLength             = $allStarTieBreakerData['total_length'];
                            $allStarTieBreakerOverallSpeed       = $allStarTieBreakerData['beatmapset']['bpm'];
                            $allStarTieBreakerOverallDifficulty  = $allStarTieBreakerData['accuracy'];
                            $allStarTieBreakerOverallHealth      = $allStarTieBreakerData['drain'];
                            $allStarTieBreakerPassCount          = $allStarTieBreakerData['passcount'];

                            $allMappoolTieBreakerData[] = [
                                'beatmap_id'                    => $allStarTieBreakerId,
                                'beatmap_round_id'              => $allStarTieBreakerRoundId,
                                'beatmap_tournament_id'         => $allStarTieBreakerTournamentId,
                                'beatmap_type'                  => $allStarTieBreakerType,
                                'beatmap_image'                 => $allStarTieBreakerImage,
                                'beatmap_url'                   => $allStarTieBreakerUrl,
                                'beatmap_name'                  => $allStarTieBreakerName,
                                'beatmap_difficulty_name'       => $allStarTieBreakerDifficultyName,
                                'beatmap_feature_artist'        => $allStarTieBreakerFeatureArtist,
                                'beatmap_mapper'                => $allStarTieBreakerMapper,
                                'beatmap_mapper_url'            => $allStarTieBreakerMapperUrl,
                                'beatmap_difficulty'            => $allStarTieBreakerDifficulty,
                                'beatmap_length'                => $allStarTieBreakerLength,
                                'beatmap_overall_speed'         => $allStarTieBreakerOverallSpeed,
                                'beatmap_overall_difficulty'    => $allStarTieBreakerOverallDifficulty,
                                'beatmap_overall_health'        => $allStarTieBreakerOverallHealth,
                                'beatmap_pass_count'            => $allStarTieBreakerPassCount
                            ];
                        }
                        break;

                    default:
                        # code...
                        break;
                }
            }
            break;

        default:
            # code...
            break;
    }

    getBeatmapData(data: $allMappoolNoModData);
    getBeatmapData(data: $allMappoolHiddenData);
    getBeatmapData(data: $allMappoolHardRockData);
    getBeatmapData(data: $allMappoolDoubleTimeData);
    getBeatmapData(data: $allMappoolFreeModData);
    getBeatmapData(data: $allMappoolEasyData);
    getBeatmapData(data: $allMappoolHiddenHardRockData);
    getBeatmapData(data: $allMappoolTieBreakerData);

    return [
        $allMappoolNoModData,
        $allMappoolHiddenData,
        $allMappoolHardRockData,
        $allMappoolDoubleTimeData,
        $allMappoolFreeModData,
        $allMappoolEasyData,
        $allMappoolHiddenHardRockData,
        $allMappoolTieBreakerData
    ];
}


function getTournamentMappoolData(
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
            $mappoolReadableData = json_decode(
                json: $mappoolCurlResponse,
                associative: true,
                depth: 512,
                flags: 0
            );

            curl_close(handle: $mappoolCurlHandle);
            return $mappoolReadableData;
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
            $mappoolReadableData = json_decode(
                json: $mappoolCurlResponse,
                associative: true,
                depth: 512,
                flags: 0
            );

            curl_close(handle: $mappoolCurlHandle);
            return $mappoolReadableData;
        }
    }
}
