<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

function getTournamentRoundMappool(
    string $tournament_name,
    string $tournament_round
): array {
    $tournamentRoundAbbreviationNames = [
        'qualifiers'        => 'QLF',
        'round_of_16'       => 'RO16',
        'quarterfinals'     => 'QF',
        'semifinals'        => 'SF',
        'finals'            => 'FNL',
        'grandfinals'       => 'GF',
        'allstars'          => 'ASTR',
    ];

    $tournamentRoundAbbreviationNameData        = [];

    $tournamentRoundBeatmapNoModData            = [];
    $tournamentRoundBeatmapHiddenData           = [];
    $tournamentRoundBeatmapHardRockData         = [];
    $tournamentRoundBeatmapDoubleTimeData       = [];
    $tournamentRoundBeatmapFreeModData          = [];
    $tournamentRoundBeatmapEasyData             = [];
    $tournamentRoundBeatmapHiddenHardRockData   = [];
    $tournamentRoundBeatmapTiebreakerData       = [];

    $tournamentRoundJsonMappoolData             = __DIR__ . '/../Datas/Tournament/VotMappoolData.json';

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
                http_response_code(404);
            }

            $tournamentRoundViewableMappoolData = file_get_contents(
                filename: $tournamentRoundJsonMappoolData,
                use_include_path: false,
                context: null,
                offset: 0,
                length: null
            );

            $tournamentRoundReadableMappoolData = json_decode(
                json: $tournamentRoundViewableMappoolData,
                associative: true
            );


            /* TODO:
             * I haven't found the best approach to do this yet but for now,
             * this will works perfectly with an exchange for page loading
             * performance.
             * Possibly, what I can think of doing this more efficiency is to
             * cache it and then call the cache.
             */


            /* QUALIFIER MAPPOOL LOOPING DATA */
            $qualifierRoundAbbreviationName         = $tournamentRoundAbbreviationNameData[0];
            $qualifierRoundJsonMappoolData          = $tournamentRoundReadableMappoolData[$tournament_name][$qualifierRoundAbbreviationName];
            $qualifierRoundJsonNoModData            = $qualifierRoundJsonMappoolData['NM'];
            $qualifierRoundJsonHiddenData           = $qualifierRoundJsonMappoolData['HD'];
            $qualifierRoundJsonHardRockData         = $qualifierRoundJsonMappoolData['HR'];
            $qualifierRoundJsonDoubleTimeData       = $qualifierRoundJsonMappoolData['DT'];
            $qualifierRoundJsonFreeModData          = $qualifierRoundJsonMappoolData['FM'];

            foreach ($qualifierRoundJsonNoModData as $qualifierRoundNoModType => $qualifierRoundJsonNoModId) {
                $qualifierRoundBeatmapNoModData = getTournamentRoundBeatmapData(
                    beatmap_id: $qualifierRoundJsonNoModId,
                    access_token: $_COOKIE['vot_access_token']
                );

                $qualifierRoundBeatmapNoModId                   = $qualifierRoundBeatmapNoModData['id'];
                $qualifierRoundId                               = $qualifierRoundAbbreviationName;
                $qualifierTournamentId                          = strtoupper(string: $tournament_name);
                $qualifierRoundBeatmapNoModType                 = $qualifierRoundNoModType;
                $qualifierRoundBeatmapNoModImage                = $qualifierRoundBeatmapNoModData['beatmapset']['covers']['cover'];
                $qualifierRoundBeatmapNoModUrl                  = $qualifierRoundBeatmapNoModData['url'];
                $qualifierRoundBeatmapNoModName                 = $qualifierRoundBeatmapNoModData['beatmapset']['title'];
                $qualifierRoundBeatmapNoModDifficultyName       = $qualifierRoundBeatmapNoModData['version'];
                $qualifierRoundBeatmapNoModFeatureArtist        = $qualifierRoundBeatmapNoModData['beatmapset']['artist'];
                $qualifierRoundBeatmapNoModMapper               = $qualifierRoundBeatmapNoModData['beatmapset']['creator'];
                $qualifierRoundBeatmapNoModMapperUrl            = "https://osu.ppy.sh/users/{$qualifierRoundBeatmapNoModData['beatmapset']['user_id']}";
                $qualifierRoundBeatmapNoModDifficulty           = $qualifierRoundBeatmapNoModData['difficulty_rating'];
                $qualifierRoundBeatmapNoModLength               = $qualifierRoundBeatmapNoModData['total_length'];
                $qualifierRoundBeatmapNoModOverallSpeed         = $qualifierRoundBeatmapNoModData['beatmapset']['bpm'];
                $qualifierRoundBeatmapNoModOverallDifficulty    = $qualifierRoundBeatmapNoModData['accuracy'];
                $qualifierRoundBeatmapNoModOverallHealth        = $qualifierRoundBeatmapNoModData['drain'];
                $qualifierRoundBeatmapNoModPassCount            = $qualifierRoundBeatmapNoModData['passcount'];

                $tournamentRoundBeatmapNoModData[] = [
                    'beatmap_id'                => $qualifierRoundBeatmapNoModId,
                    'beatmap_round_id'          => $qualifierRoundId,
                    'beatmap_tournament_id'     => $qualifierTournamentId,
                    'beatmap_type'              => $qualifierRoundBeatmapNoModType,
                    'beatmap_image'             => $qualifierRoundBeatmapNoModImage,
                    'beatmap_url'               => $qualifierRoundBeatmapNoModUrl,
                    'beatmap_name'              => $qualifierRoundBeatmapNoModName,
                    'beatmap_difficulty_name'   => $qualifierRoundBeatmapNoModDifficultyName,
                    'beatmap_fa'                => $qualifierRoundBeatmapNoModFeatureArtist,
                    'beatmap_mapper'            => $qualifierRoundBeatmapNoModMapper,
                    'beatmap_mapper_url'        => $qualifierRoundBeatmapNoModMapperUrl,
                    'beatmap_difficulty'        => $qualifierRoundBeatmapNoModDifficulty,
                    'beatmap_length'            => $qualifierRoundBeatmapNoModLength,
                    'beatmap_bpm'               => $qualifierRoundBeatmapNoModOverallSpeed,
                    'beatmap_od'                => $qualifierRoundBeatmapNoModOverallDifficulty,
                    'beatmap_hp'                => $qualifierRoundBeatmapNoModOverallHealth,
                    'beatmap_pass_count'        => $qualifierRoundBeatmapNoModPassCount
                ];
            }

            foreach ($qualifierRoundJsonHiddenData as $qualifierRoundHiddenType => $qualifierRoundJsonHiddenId) {
                $qualifierRoundBeatmapHiddenData = getTournamentRoundBeatmapData(
                    beatmap_id: $qualifierRoundJsonHiddenId,
                    access_token: $_COOKIE['vot_access_token']
                );

                $qualifierRoundBeatmapHiddenId                  = $qualifierRoundBeatmapHiddenData['id'];
                $qualifierRoundId                               = $qualifierRoundAbbreviationName;
                $qualifierTournamentId                          = strtoupper(string: $tournament_name);
                $qualifierRoundBeatmapHiddenType                = $qualifierRoundHiddenType;
                $qualifierRoundBeatmapHiddenImage               = $qualifierRoundBeatmapHiddenData['beatmapset']['covers']['cover'];
                $qualifierRoundBeatmapHiddenUrl                 = $qualifierRoundBeatmapHiddenData['url'];
                $qualifierRoundBeatmapHiddenName                = $qualifierRoundBeatmapHiddenData['beatmapset']['title'];
                $qualifierRoundBeatmapHiddenDifficultyName      = $qualifierRoundBeatmapHiddenData['version'];
                $qualifierRoundBeatmapHiddenFeatureArtist       = $qualifierRoundBeatmapHiddenData['beatmapset']['artist'];
                $qualifierRoundBeatmapHiddenMapper              = $qualifierRoundBeatmapHiddenData['beatmapset']['creator'];
                $qualifierRoundBeatmapHiddenMapperUrl           = "https://osu.ppy.sh/users/{$qualifierRoundBeatmapHiddenData['beatmapset']['user_id']}";
                $qualifierRoundBeatmapHiddenDifficulty          = $qualifierRoundBeatmapHiddenData['difficulty_rating'];
                $qualifierRoundBeatmapHiddenLength              = $qualifierRoundBeatmapHiddenData['total_length'];
                $qualifierRoundBeatmapHiddenOverallSpeed        = $qualifierRoundBeatmapHiddenData['beatmapset']['bpm'];
                $qualifierRoundBeatmapHiddenOverallDifficulty   = $qualifierRoundBeatmapHiddenData['accuracy'];
                $qualifierRoundBeatmapHiddenOverallHealth       = $qualifierRoundBeatmapHiddenData['drain'];
                $qualifierRoundBeatmapHiddenPassCount           = $qualifierRoundBeatmapHiddenData['passcount'];

                $tournamentRoundBeatmapHiddenData[] = [
                    'beatmap_id'                => $qualifierRoundBeatmapHiddenId,
                    'beatmap_round_id'          => $qualifierRoundId,
                    'beatmap_tournament_id'     => $qualifierTournamentId,
                    'beatmap_type'              => $qualifierRoundBeatmapHiddenType,
                    'beatmap_image'             => $qualifierRoundBeatmapHiddenImage,
                    'beatmap_url'               => $qualifierRoundBeatmapHiddenUrl,
                    'beatmap_name'              => $qualifierRoundBeatmapHiddenName,
                    'beatmap_difficulty_name'   => $qualifierRoundBeatmapHiddenDifficultyName,
                    'beatmap_fa'                => $qualifierRoundBeatmapHiddenFeatureArtist,
                    'beatmap_mapper'            => $qualifierRoundBeatmapHiddenMapper,
                    'beatmap_mapper_url'        => $qualifierRoundBeatmapHiddenMapperUrl,
                    'beatmap_difficulty'        => $qualifierRoundBeatmapHiddenDifficulty,
                    'beatmap_length'            => $qualifierRoundBeatmapHiddenLength,
                    'beatmap_bpm'               => $qualifierRoundBeatmapHiddenOverallSpeed,
                    'beatmap_od'                => $qualifierRoundBeatmapHiddenOverallDifficulty,
                    'beatmap_hp'                => $qualifierRoundBeatmapHiddenOverallHealth,
                    'beatmap_pass_count'        => $qualifierRoundBeatmapHiddenPassCount
                ];
            }

            foreach ($qualifierRoundJsonHardRockData as $qualifierRoundHardRockType => $qualifierRoundJsonHardRockId) {
                $qualifierRoundBeatmapHardRockData = getTournamentRoundBeatmapData(
                    beatmap_id: $qualifierRoundJsonHardRockId,
                    access_token: $_COOKIE['vot_access_token']
                );

                $qualifierRoundBeatmapHardRockId                    = $qualifierRoundBeatmapHardRockData['id'];
                $qualifierRoundId                                   = $qualifierRoundAbbreviationName;
                $qualifierTournamentId                              = strtoupper(string: $tournament_name);
                $qualifierRoundBeatmapHardRockType                  = $qualifierRoundHardRockType;
                $qualifierRoundBeatmapHardRockImage                 = $qualifierRoundBeatmapHardRockData['beatmapset']['covers']['cover'];
                $qualifierRoundBeatmapHardRockUrl                   = $qualifierRoundBeatmapHardRockData['url'];
                $qualifierRoundBeatmapHardRockName                  = $qualifierRoundBeatmapHardRockData['beatmapset']['title'];
                $qualifierRoundBeatmapHardRockDifficultyName        = $qualifierRoundBeatmapHardRockData['version'];
                $qualifierRoundBeatmapHardRockFeatureArtist         = $qualifierRoundBeatmapHardRockData['beatmapset']['artist'];
                $qualifierRoundBeatmapHardRockMapper                = $qualifierRoundBeatmapHardRockData['beatmapset']['creator'];
                $qualifierRoundBeatmapHardRockMapperUrl             = "https://osu.ppy.sh/users/{$qualifierRoundBeatmapHardRockData['beatmapset']['user_id']}";
                $qualifierRoundBeatmapHardRockDifficulty            = $qualifierRoundBeatmapHardRockData['difficulty_rating'];
                $qualifierRoundBeatmapHardRockLength                = $qualifierRoundBeatmapHardRockData['total_length'];
                $qualifierRoundBeatmapHardRockOverallSpeed          = $qualifierRoundBeatmapHardRockData['beatmapset']['bpm'];
                $qualifierRoundBeatmapHardRockOverallDifficulty     = $qualifierRoundBeatmapHardRockData['accuracy'];
                $qualifierRoundBeatmapHardRockOverallHealth         = $qualifierRoundBeatmapHardRockData['drain'];
                $qualifierRoundBeatmapHardRockPassCount             = $qualifierRoundBeatmapHardRockData['passcount'];

                $tournamentRoundBeatmapHardRockData[] = [
                    'beatmap_id'                => $qualifierRoundBeatmapHardRockId,
                    'beatmap_round_id'          => $qualifierRoundId,
                    'beatmap_tournament_id'     => $qualifierTournamentId,
                    'beatmap_type'              => $qualifierRoundBeatmapHardRockType,
                    'beatmap_image'             => $qualifierRoundBeatmapHardRockImage,
                    'beatmap_url'               => $qualifierRoundBeatmapHardRockUrl,
                    'beatmap_name'              => $qualifierRoundBeatmapHardRockName,
                    'beatmap_difficulty_name'   => $qualifierRoundBeatmapHardRockDifficultyName,
                    'beatmap_fa'                => $qualifierRoundBeatmapHardRockFeatureArtist,
                    'beatmap_mapper'            => $qualifierRoundBeatmapHardRockMapper,
                    'beatmap_mapper_url'        => $qualifierRoundBeatmapHardRockMapperUrl,
                    'beatmap_difficulty'        => $qualifierRoundBeatmapHardRockDifficulty,
                    'beatmap_length'            => $qualifierRoundBeatmapHardRockLength,
                    'beatmap_bpm'               => $qualifierRoundBeatmapHardRockOverallSpeed,
                    'beatmap_od'                => $qualifierRoundBeatmapHardRockOverallDifficulty,
                    'beatmap_hp'                => $qualifierRoundBeatmapHardRockOverallHealth,
                    'beatmap_pass_count'        => $qualifierRoundBeatmapHardRockPassCount
                ];
            }

            foreach ($qualifierRoundJsonDoubleTimeData as $qualifierRoundDoubleTimeType => $qualifierRoundJsonDoubleTimeId) {
                $qualifierRoundBeatmapDoubleTimeData = getTournamentRoundBeatmapData(
                    beatmap_id: $qualifierRoundJsonDoubleTimeId,
                    access_token: $_COOKIE['vot_access_token']
                );

                $qualifierRoundBeatmapDoubleTimeId                  = $qualifierRoundBeatmapDoubleTimeData['id'];
                $qualifierRoundId                                   = $qualifierRoundAbbreviationName;
                $qualifierTournamentId                              = strtoupper(string: $tournament_name);
                $qualifierRoundBeatmapDoubleTimeType                = $qualifierRoundDoubleTimeType;
                $qualifierRoundBeatmapDoubleTimeImage               = $qualifierRoundBeatmapDoubleTimeData['beatmapset']['covers']['cover'];
                $qualifierRoundBeatmapDoubleTimeUrl                 = $qualifierRoundBeatmapDoubleTimeData['url'];
                $qualifierRoundBeatmapDoubleTimeName                = $qualifierRoundBeatmapDoubleTimeData['beatmapset']['title'];
                $qualifierRoundBeatmapDoubleTimeDifficultyName      = $qualifierRoundBeatmapDoubleTimeData['version'];
                $qualifierRoundBeatmapDoubleTimeFeatureArtist       = $qualifierRoundBeatmapDoubleTimeData['beatmapset']['artist'];
                $qualifierRoundBeatmapDoubleTimeMapper              = $qualifierRoundBeatmapDoubleTimeData['beatmapset']['creator'];
                $qualifierRoundBeatmapDoubleTimeMapperUrl           = "https://osu.ppy.sh/users/{$qualifierRoundBeatmapDoubleTimeData['beatmapset']['user_id']}";
                $qualifierRoundBeatmapDoubleTimeDifficulty          = $qualifierRoundBeatmapDoubleTimeData['difficulty_rating'];
                $qualifierRoundBeatmapDoubleTimeLength              = $qualifierRoundBeatmapDoubleTimeData['total_length'];
                $qualifierRoundBeatmapDoubleTimeOverallSpeed        = $qualifierRoundBeatmapDoubleTimeData['beatmapset']['bpm'];
                $qualifierRoundBeatmapDoubleTimeOverallDifficulty   = $qualifierRoundBeatmapDoubleTimeData['accuracy'];
                $qualifierRoundBeatmapDoubleTimeOverallHealth       = $qualifierRoundBeatmapDoubleTimeData['drain'];
                $qualifierRoundBeatmapDoubleTimePassCount           = $qualifierRoundBeatmapDoubleTimeData['passcount'];

                $tournamentRoundBeatmapDoubleTimeData[] = [
                    'beatmap_id'                => $qualifierRoundBeatmapDoubleTimeId,
                    'beatmap_round_id'          => $qualifierRoundId,
                    'beatmap_tournament_id'     => $qualifierTournamentId,
                    'beatmap_type'              => $qualifierRoundBeatmapDoubleTimeType,
                    'beatmap_image'             => $qualifierRoundBeatmapDoubleTimeImage,
                    'beatmap_url'               => $qualifierRoundBeatmapDoubleTimeUrl,
                    'beatmap_name'              => $qualifierRoundBeatmapDoubleTimeName,
                    'beatmap_difficulty_name'   => $qualifierRoundBeatmapDoubleTimeDifficultyName,
                    'beatmap_fa'                => $qualifierRoundBeatmapDoubleTimeFeatureArtist,
                    'beatmap_mapper'            => $qualifierRoundBeatmapDoubleTimeMapper,
                    'beatmap_mapper_url'        => $qualifierRoundBeatmapDoubleTimeMapperUrl,
                    'beatmap_difficulty'        => $qualifierRoundBeatmapDoubleTimeDifficulty,
                    'beatmap_length'            => $qualifierRoundBeatmapDoubleTimeLength,
                    'beatmap_bpm'               => $qualifierRoundBeatmapDoubleTimeOverallSpeed,
                    'beatmap_od'                => $qualifierRoundBeatmapDoubleTimeOverallDifficulty,
                    'beatmap_hp'                => $qualifierRoundBeatmapDoubleTimeOverallHealth,
                    'beatmap_pass_count'        => $qualifierRoundBeatmapDoubleTimePassCount
                ];
            }

            foreach ($qualifierRoundJsonFreeModData as $qualifierRoundFreeModType => $qualifierRoundJsonFreeModId) {
                $qualifierRoundBeatmapFreeModData = getTournamentRoundBeatmapData(
                    beatmap_id: $qualifierRoundJsonFreeModId,
                    access_token: $_COOKIE['vot_access_token']
                );

                $qualifierRoundBeatmapFreeModId                 = $qualifierRoundBeatmapFreeModData['id'];
                $qualifierRoundId                               = $qualifierRoundAbbreviationName;
                $qualifierTournamentId                          = strtoupper(string: $tournament_name);
                $qualifierRoundBeatmapFreeModType               = $qualifierRoundFreeModType;
                $qualifierRoundBeatmapFreeModImage              = $qualifierRoundBeatmapFreeModData['beatmapset']['covers']['cover'];
                $qualifierRoundBeatmapFreeModUrl                = $qualifierRoundBeatmapFreeModData['url'];
                $qualifierRoundBeatmapFreeModName               = $qualifierRoundBeatmapFreeModData['beatmapset']['title'];
                $qualifierRoundBeatmapFreeModDifficultyName     = $qualifierRoundBeatmapFreeModData['version'];
                $qualifierRoundBeatmapFreeModFeatureArtist      = $qualifierRoundBeatmapFreeModData['beatmapset']['artist'];
                $qualifierRoundBeatmapFreeModMapper             = $qualifierRoundBeatmapFreeModData['beatmapset']['creator'];
                $qualifierRoundBeatmapFreeModMapperUrl          = "https://osu.ppy.sh/users/{$qualifierRoundBeatmapFreeModData['beatmapset']['user_id']}";
                $qualifierRoundBeatmapFreeModDifficulty         = $qualifierRoundBeatmapFreeModData['difficulty_rating'];
                $qualifierRoundBeatmapFreeModLength             = $qualifierRoundBeatmapFreeModData['total_length'];
                $qualifierRoundBeatmapFreeModOverallSpeed       = $qualifierRoundBeatmapFreeModData['beatmapset']['bpm'];
                $qualifierRoundBeatmapFreeModOverallDifficulty  = $qualifierRoundBeatmapFreeModData['accuracy'];
                $qualifierRoundBeatmapFreeModOverallHealth      = $qualifierRoundBeatmapFreeModData['drain'];
                $qualifierRoundBeatmapFreeModPassCount          = $qualifierRoundBeatmapFreeModData['passcount'];

                $tournamentRoundBeatmapFreeModData[] = [
                    'beatmap_id'                => $qualifierRoundBeatmapFreeModId,
                    'beatmap_round_id'          => $qualifierRoundId,
                    'beatmap_tournament_id'     => $qualifierTournamentId,
                    'beatmap_type'              => $qualifierRoundBeatmapFreeModType,
                    'beatmap_image'             => $qualifierRoundBeatmapFreeModImage,
                    'beatmap_url'               => $qualifierRoundBeatmapFreeModUrl,
                    'beatmap_name'              => $qualifierRoundBeatmapFreeModName,
                    'beatmap_difficulty_name'   => $qualifierRoundBeatmapFreeModDifficultyName,
                    'beatmap_fa'                => $qualifierRoundBeatmapFreeModFeatureArtist,
                    'beatmap_mapper'            => $qualifierRoundBeatmapFreeModMapper,
                    'beatmap_mapper_url'        => $qualifierRoundBeatmapFreeModMapperUrl,
                    'beatmap_difficulty'        => $qualifierRoundBeatmapFreeModDifficulty,
                    'beatmap_length'            => $qualifierRoundBeatmapFreeModLength,
                    'beatmap_bpm'               => $qualifierRoundBeatmapFreeModOverallSpeed,
                    'beatmap_od'                => $qualifierRoundBeatmapFreeModOverallDifficulty,
                    'beatmap_hp'                => $qualifierRoundBeatmapFreeModOverallHealth,
                    'beatmap_pass_count'        => $qualifierRoundBeatmapFreeModPassCount
                ];
            }


            /* ROUND OF 16 MAPPOOL LOOPING DATA */
            $roundOf16RoundAbbreviationName         = $tournamentRoundAbbreviationNameData[1];
            $roundOf16RoundJsonMappoolData          = $tournamentRoundReadableMappoolData[$tournament_name][$roundOf16RoundAbbreviationName];
            $roundOf16RoundJsonNoModData            = $roundOf16RoundJsonMappoolData['NM'];
            $roundOf16RoundJsonHiddenData           = $roundOf16RoundJsonMappoolData['HD'];
            $roundOf16RoundJsonHardRockData         = $roundOf16RoundJsonMappoolData['HR'];
            $roundOf16RoundJsonDoubleTimeData       = $roundOf16RoundJsonMappoolData['DT'];
            $roundOf16RoundJsonFreeModData          = $roundOf16RoundJsonMappoolData['FM'];
            $roundOf16RoundJsonEasyData             = $roundOf16RoundJsonMappoolData['EZ'];
            $roundOf16RoundJsonHiddenHardRockData   = $roundOf16RoundJsonMappoolData['HDHR'];
            $roundOf16RoundJsonTiebreakerData       = $roundOf16RoundJsonMappoolData['TB'];

            foreach ($roundOf16RoundJsonNoModData as $roundOf16RoundNoModType => $roundOf16RoundJsonNoModId) {
                $roundOf16RoundBeatmapNoModData = getTournamentRoundBeatmapData(
                    beatmap_id: $roundOf16RoundJsonNoModId,
                    access_token: $_COOKIE['vot_access_token']
                );

                $roundOf16RoundBeatmapNoModId                   = $roundOf16RoundBeatmapNoModData['id'];
                $roundOf16RoundId                               = $roundOf16RoundAbbreviationName;
                $roundOf16TournamentId                          = strtoupper(string: $tournament_name);
                $roundOf16RoundBeatmapNoModType                 = $roundOf16RoundNoModType;
                $roundOf16RoundBeatmapNoModImage                = $roundOf16RoundBeatmapNoModData['beatmapset']['covers']['cover'];
                $roundOf16RoundBeatmapNoModUrl                  = $roundOf16RoundBeatmapNoModData['url'];
                $roundOf16RoundBeatmapNoModName                 = $roundOf16RoundBeatmapNoModData['beatmapset']['title'];
                $roundOf16RoundBeatmapNoModDifficultyName       = $roundOf16RoundBeatmapNoModData['version'];
                $roundOf16RoundBeatmapNoModFeatureArtist        = $roundOf16RoundBeatmapNoModData['beatmapset']['artist'];
                $roundOf16RoundBeatmapNoModMapper               = $roundOf16RoundBeatmapNoModData['beatmapset']['creator'];
                $roundOf16RoundBeatmapNoModMapperUrl            = "https://osu.ppy.sh/users/{$roundOf16RoundBeatmapNoModData['beatmapset']['user_id']}";
                $roundOf16RoundBeatmapNoModDifficulty           = $roundOf16RoundBeatmapNoModData['difficulty_rating'];
                $roundOf16RoundBeatmapNoModLength               = $roundOf16RoundBeatmapNoModData['total_length'];
                $roundOf16RoundBeatmapNoModOverallSpeed         = $roundOf16RoundBeatmapNoModData['beatmapset']['bpm'];
                $roundOf16RoundBeatmapNoModOverallDifficulty    = $roundOf16RoundBeatmapNoModData['accuracy'];
                $roundOf16RoundBeatmapNoModOverallHealth        = $roundOf16RoundBeatmapNoModData['drain'];
                $roundOf16RoundBeatmapNoModPassCount            = $roundOf16RoundBeatmapNoModData['passcount'];

                $tournamentRoundBeatmapNoModData[] = [
                    'beatmap_id'                => $roundOf16RoundBeatmapNoModId,
                    'beatmap_round_id'          => $roundOf16RoundId,
                    'beatmap_tournament_id'     => $roundOf16TournamentId,
                    'beatmap_type'              => $roundOf16RoundBeatmapNoModType,
                    'beatmap_image'             => $roundOf16RoundBeatmapNoModImage,
                    'beatmap_url'               => $roundOf16RoundBeatmapNoModUrl,
                    'beatmap_name'              => $roundOf16RoundBeatmapNoModName,
                    'beatmap_difficulty_name'   => $roundOf16RoundBeatmapNoModDifficultyName,
                    'beatmap_fa'                => $roundOf16RoundBeatmapNoModFeatureArtist,
                    'beatmap_mapper'            => $roundOf16RoundBeatmapNoModMapper,
                    'beatmap_mapper_url'        => $roundOf16RoundBeatmapNoModMapperUrl,
                    'beatmap_difficulty'        => $roundOf16RoundBeatmapNoModDifficulty,
                    'beatmap_length'            => $roundOf16RoundBeatmapNoModLength,
                    'beatmap_bpm'               => $roundOf16RoundBeatmapNoModOverallSpeed,
                    'beatmap_od'                => $roundOf16RoundBeatmapNoModOverallDifficulty,
                    'beatmap_hp'                => $roundOf16RoundBeatmapNoModOverallHealth,
                    'beatmap_pass_count'        => $roundOf16RoundBeatmapNoModPassCount
                ];
            }

            foreach ($roundOf16RoundJsonHiddenData as $roundOf16RoundHiddenType => $roundOf16RoundJsonHiddenId) {
                $roundOf16RoundBeatmapHiddenData = getTournamentRoundBeatmapData(
                    beatmap_id: $roundOf16RoundJsonHiddenId,
                    access_token: $_COOKIE['vot_access_token']
                );

                $roundOf16RoundBeatmapHiddenId                  = $roundOf16RoundBeatmapHiddenData['id'];
                $roundOf16RoundId                               = $roundOf16RoundAbbreviationName;
                $roundOf16TournamentId                          = strtoupper(string: $tournament_name);
                $roundOf16RoundBeatmapHiddenType                = $roundOf16RoundHiddenType;
                $roundOf16RoundBeatmapHiddenImage               = $roundOf16RoundBeatmapHiddenData['beatmapset']['covers']['cover'];
                $roundOf16RoundBeatmapHiddenUrl                 = $roundOf16RoundBeatmapHiddenData['url'];
                $roundOf16RoundBeatmapHiddenName                = $roundOf16RoundBeatmapHiddenData['beatmapset']['title'];
                $roundOf16RoundBeatmapHiddenDifficultyName      = $roundOf16RoundBeatmapHiddenData['version'];
                $roundOf16RoundBeatmapHiddenFeatureArtist       = $roundOf16RoundBeatmapHiddenData['beatmapset']['artist'];
                $roundOf16RoundBeatmapHiddenMapper              = $roundOf16RoundBeatmapHiddenData['beatmapset']['creator'];
                $roundOf16RoundBeatmapHiddenMapperUrl           = "https://osu.ppy.sh/users/{$roundOf16RoundBeatmapHiddenData['beatmapset']['user_id']}";
                $roundOf16RoundBeatmapHiddenDifficulty          = $roundOf16RoundBeatmapHiddenData['difficulty_rating'];
                $roundOf16RoundBeatmapHiddenLength              = $roundOf16RoundBeatmapHiddenData['total_length'];
                $roundOf16RoundBeatmapHiddenOverallSpeed        = $roundOf16RoundBeatmapHiddenData['beatmapset']['bpm'];
                $roundOf16RoundBeatmapHiddenOverallDifficulty   = $roundOf16RoundBeatmapHiddenData['accuracy'];
                $roundOf16RoundBeatmapHiddenOverallHealth       = $roundOf16RoundBeatmapHiddenData['drain'];
                $roundOf16RoundBeatmapHiddenPassCount           = $roundOf16RoundBeatmapHiddenData['passcount'];

                $tournamentRoundBeatmapHiddenData[] = [
                    'beatmap_id'                => $roundOf16RoundBeatmapHiddenId,
                    'beatmap_round_id'          => $roundOf16RoundId,
                    'beatmap_tournament_id'     => $roundOf16TournamentId,
                    'beatmap_type'              => $roundOf16RoundBeatmapHiddenType,
                    'beatmap_image'             => $roundOf16RoundBeatmapHiddenImage,
                    'beatmap_url'               => $roundOf16RoundBeatmapHiddenUrl,
                    'beatmap_name'              => $roundOf16RoundBeatmapHiddenName,
                    'beatmap_difficulty_name'   => $roundOf16RoundBeatmapHiddenDifficultyName,
                    'beatmap_fa'                => $roundOf16RoundBeatmapHiddenFeatureArtist,
                    'beatmap_mapper'            => $roundOf16RoundBeatmapHiddenMapper,
                    'beatmap_mapper_url'        => $roundOf16RoundBeatmapHiddenMapperUrl,
                    'beatmap_difficulty'        => $roundOf16RoundBeatmapHiddenDifficulty,
                    'beatmap_length'            => $roundOf16RoundBeatmapHiddenLength,
                    'beatmap_bpm'               => $roundOf16RoundBeatmapHiddenOverallSpeed,
                    'beatmap_od'                => $roundOf16RoundBeatmapHiddenOverallDifficulty,
                    'beatmap_hp'                => $roundOf16RoundBeatmapHiddenOverallHealth,
                    'beatmap_pass_count'        => $roundOf16RoundBeatmapHiddenPassCount
                ];
            }

            foreach ($roundOf16RoundJsonHardRockData as $roundOf16RoundHardRockType => $roundOf16RoundJsonHardRockId) {
                $roundOf16RoundBeatmapHardRockData = getTournamentRoundBeatmapData(
                    beatmap_id: $roundOf16RoundJsonHardRockId,
                    access_token: $_COOKIE['vot_access_token']
                );

                $roundOf16RoundBeatmapHardRockId                    = $roundOf16RoundBeatmapHardRockData['id'];
                $roundOf16RoundId                                   = $roundOf16RoundAbbreviationName;
                $roundOf16TournamentId                              = strtoupper(string: $tournament_name);
                $roundOf16RoundBeatmapHardRockType                  = $roundOf16RoundHardRockType;
                $roundOf16RoundBeatmapHardRockImage                 = $roundOf16RoundBeatmapHardRockData['beatmapset']['covers']['cover'];
                $roundOf16RoundBeatmapHardRockUrl                   = $roundOf16RoundBeatmapHardRockData['url'];
                $roundOf16RoundBeatmapHardRockName                  = $roundOf16RoundBeatmapHardRockData['beatmapset']['title'];
                $roundOf16RoundBeatmapHardRockDifficultyName        = $roundOf16RoundBeatmapHardRockData['version'];
                $roundOf16RoundBeatmapHardRockFeatureArtist         = $roundOf16RoundBeatmapHardRockData['beatmapset']['artist'];
                $roundOf16RoundBeatmapHardRockMapper                = $roundOf16RoundBeatmapHardRockData['beatmapset']['creator'];
                $roundOf16RoundBeatmapHardRockMapperUrl             = "https://osu.ppy.sh/users/{$roundOf16RoundBeatmapHardRockData['beatmapset']['user_id']}";
                $roundOf16RoundBeatmapHardRockDifficulty            = $roundOf16RoundBeatmapHardRockData['difficulty_rating'];
                $roundOf16RoundBeatmapHardRockLength                = $roundOf16RoundBeatmapHardRockData['total_length'];
                $roundOf16RoundBeatmapHardRockOverallSpeed          = $roundOf16RoundBeatmapHardRockData['beatmapset']['bpm'];
                $roundOf16RoundBeatmapHardRockOverallDifficulty     = $roundOf16RoundBeatmapHardRockData['accuracy'];
                $roundOf16RoundBeatmapHardRockOverallHealth         = $roundOf16RoundBeatmapHardRockData['drain'];
                $roundOf16RoundBeatmapHardRockPassCount             = $roundOf16RoundBeatmapHardRockData['passcount'];

                $tournamentRoundBeatmapHardRockData[] = [
                    'beatmap_id'                => $roundOf16RoundBeatmapHardRockId,
                    'beatmap_round_id'          => $roundOf16RoundId,
                    'beatmap_tournament_id'     => $roundOf16TournamentId,
                    'beatmap_type'              => $roundOf16RoundBeatmapHardRockType,
                    'beatmap_image'             => $roundOf16RoundBeatmapHardRockImage,
                    'beatmap_url'               => $roundOf16RoundBeatmapHardRockUrl,
                    'beatmap_name'              => $roundOf16RoundBeatmapHardRockName,
                    'beatmap_difficulty_name'   => $roundOf16RoundBeatmapHardRockDifficultyName,
                    'beatmap_fa'                => $roundOf16RoundBeatmapHardRockFeatureArtist,
                    'beatmap_mapper'            => $roundOf16RoundBeatmapHardRockMapper,
                    'beatmap_mapper_url'        => $roundOf16RoundBeatmapHardRockMapperUrl,
                    'beatmap_difficulty'        => $roundOf16RoundBeatmapHardRockDifficulty,
                    'beatmap_length'            => $roundOf16RoundBeatmapHardRockLength,
                    'beatmap_bpm'               => $roundOf16RoundBeatmapHardRockOverallSpeed,
                    'beatmap_od'                => $roundOf16RoundBeatmapHardRockOverallDifficulty,
                    'beatmap_hp'                => $roundOf16RoundBeatmapHardRockOverallHealth,
                    'beatmap_pass_count'        => $roundOf16RoundBeatmapHardRockPassCount
                ];
            }

            foreach ($roundOf16RoundJsonDoubleTimeData as $roundOf16RoundDoubleTimeType => $roundOf16RoundJsonDoubleTimeId) {
                $roundOf16RoundBeatmapDoubleTimeData = getTournamentRoundBeatmapData(
                    beatmap_id: $roundOf16RoundJsonDoubleTimeId,
                    access_token: $_COOKIE['vot_access_token']
                );

                $roundOf16RoundBeatmapDoubleTimeId                  = $roundOf16RoundBeatmapDoubleTimeData['id'];
                $roundOf16RoundId                                   = $roundOf16RoundAbbreviationName;
                $roundOf16TournamentId                              = strtoupper(string: $tournament_name);
                $roundOf16RoundBeatmapDoubleTimeType                = $roundOf16RoundDoubleTimeType;
                $roundOf16RoundBeatmapDoubleTimeImage               = $roundOf16RoundBeatmapDoubleTimeData['beatmapset']['covers']['cover'];
                $roundOf16RoundBeatmapDoubleTimeUrl                 = $roundOf16RoundBeatmapDoubleTimeData['url'];
                $roundOf16RoundBeatmapDoubleTimeName                = $roundOf16RoundBeatmapDoubleTimeData['beatmapset']['title'];
                $roundOf16RoundBeatmapDoubleTimeDifficultyName      = $roundOf16RoundBeatmapDoubleTimeData['version'];
                $roundOf16RoundBeatmapDoubleTimeFeatureArtist       = $roundOf16RoundBeatmapDoubleTimeData['beatmapset']['artist'];
                $roundOf16RoundBeatmapDoubleTimeMapper              = $roundOf16RoundBeatmapDoubleTimeData['beatmapset']['creator'];
                $roundOf16RoundBeatmapDoubleTimeMapperUrl           = "https://osu.ppy.sh/users/{$roundOf16RoundBeatmapDoubleTimeData['beatmapset']['user_id']}";
                $roundOf16RoundBeatmapDoubleTimeDifficulty          = $roundOf16RoundBeatmapDoubleTimeData['difficulty_rating'];
                $roundOf16RoundBeatmapDoubleTimeLength              = $roundOf16RoundBeatmapDoubleTimeData['total_length'];
                $roundOf16RoundBeatmapDoubleTimeOverallSpeed        = $roundOf16RoundBeatmapDoubleTimeData['beatmapset']['bpm'];
                $roundOf16RoundBeatmapDoubleTimeOverallDifficulty   = $roundOf16RoundBeatmapDoubleTimeData['accuracy'];
                $roundOf16RoundBeatmapDoubleTimeOverallHealth       = $roundOf16RoundBeatmapDoubleTimeData['drain'];
                $roundOf16RoundBeatmapDoubleTimePassCount           = $roundOf16RoundBeatmapDoubleTimeData['passcount'];

                $tournamentRoundBeatmapDoubleTimeData[] = [
                    'beatmap_id'                => $roundOf16RoundBeatmapDoubleTimeId,
                    'beatmap_round_id'          => $roundOf16RoundId,
                    'beatmap_tournament_id'     => $roundOf16TournamentId,
                    'beatmap_type'              => $roundOf16RoundBeatmapDoubleTimeType,
                    'beatmap_image'             => $roundOf16RoundBeatmapDoubleTimeImage,
                    'beatmap_url'               => $roundOf16RoundBeatmapDoubleTimeUrl,
                    'beatmap_name'              => $roundOf16RoundBeatmapDoubleTimeName,
                    'beatmap_difficulty_name'   => $roundOf16RoundBeatmapDoubleTimeDifficultyName,
                    'beatmap_fa'                => $roundOf16RoundBeatmapDoubleTimeFeatureArtist,
                    'beatmap_mapper'            => $roundOf16RoundBeatmapDoubleTimeMapper,
                    'beatmap_mapper_url'        => $roundOf16RoundBeatmapDoubleTimeMapperUrl,
                    'beatmap_difficulty'        => $roundOf16RoundBeatmapDoubleTimeDifficulty,
                    'beatmap_length'            => $roundOf16RoundBeatmapDoubleTimeLength,
                    'beatmap_bpm'               => $roundOf16RoundBeatmapDoubleTimeOverallSpeed,
                    'beatmap_od'                => $roundOf16RoundBeatmapDoubleTimeOverallDifficulty,
                    'beatmap_hp'                => $roundOf16RoundBeatmapDoubleTimeOverallHealth,
                    'beatmap_pass_count'        => $roundOf16RoundBeatmapDoubleTimePassCount
                ];
            }

            foreach ($roundOf16RoundJsonFreeModData as $roundOf16RoundFreeModType => $roundOf16RoundJsonFreeModId) {
                $roundOf16RoundBeatmapFreeModData = getTournamentRoundBeatmapData(
                    beatmap_id: $roundOf16RoundJsonFreeModId,
                    access_token: $_COOKIE['vot_access_token']
                );

                $roundOf16RoundBeatmapFreeModId                 = $roundOf16RoundBeatmapFreeModData['id'];
                $roundOf16RoundId                               = $roundOf16RoundAbbreviationName;
                $roundOf16TournamentId                          = strtoupper(string: $tournament_name);
                $roundOf16RoundBeatmapFreeModType               = $roundOf16RoundFreeModType;
                $roundOf16RoundBeatmapFreeModImage              = $roundOf16RoundBeatmapFreeModData['beatmapset']['covers']['cover'];
                $roundOf16RoundBeatmapFreeModUrl                = $roundOf16RoundBeatmapFreeModData['url'];
                $roundOf16RoundBeatmapFreeModName               = $roundOf16RoundBeatmapFreeModData['beatmapset']['title'];
                $roundOf16RoundBeatmapFreeModDifficultyName     = $roundOf16RoundBeatmapFreeModData['version'];
                $roundOf16RoundBeatmapFreeModFeatureArtist      = $roundOf16RoundBeatmapFreeModData['beatmapset']['artist'];
                $roundOf16RoundBeatmapFreeModMapper             = $roundOf16RoundBeatmapFreeModData['beatmapset']['creator'];
                $roundOf16RoundBeatmapFreeModMapperUrl          = "https://osu.ppy.sh/users/{$roundOf16RoundBeatmapFreeModData['beatmapset']['user_id']}";
                $roundOf16RoundBeatmapFreeModDifficulty         = $roundOf16RoundBeatmapFreeModData['difficulty_rating'];
                $roundOf16RoundBeatmapFreeModLength             = $roundOf16RoundBeatmapFreeModData['total_length'];
                $roundOf16RoundBeatmapFreeModOverallSpeed       = $roundOf16RoundBeatmapFreeModData['beatmapset']['bpm'];
                $roundOf16RoundBeatmapFreeModOverallDifficulty  = $roundOf16RoundBeatmapFreeModData['accuracy'];
                $roundOf16RoundBeatmapFreeModOverallHealth      = $roundOf16RoundBeatmapFreeModData['drain'];
                $roundOf16RoundBeatmapFreeModPassCount          = $roundOf16RoundBeatmapFreeModData['passcount'];

                $tournamentRoundBeatmapFreeModData[] = [
                    'beatmap_id'                => $roundOf16RoundBeatmapFreeModId,
                    'beatmap_round_id'          => $roundOf16RoundId,
                    'beatmap_tournament_id'     => $roundOf16TournamentId,
                    'beatmap_type'              => $roundOf16RoundBeatmapFreeModType,
                    'beatmap_image'             => $roundOf16RoundBeatmapFreeModImage,
                    'beatmap_url'               => $roundOf16RoundBeatmapFreeModUrl,
                    'beatmap_name'              => $roundOf16RoundBeatmapFreeModName,
                    'beatmap_difficulty_name'   => $roundOf16RoundBeatmapFreeModDifficultyName,
                    'beatmap_fa'                => $roundOf16RoundBeatmapFreeModFeatureArtist,
                    'beatmap_mapper'            => $roundOf16RoundBeatmapFreeModMapper,
                    'beatmap_mapper_url'        => $roundOf16RoundBeatmapFreeModMapperUrl,
                    'beatmap_difficulty'        => $roundOf16RoundBeatmapFreeModDifficulty,
                    'beatmap_length'            => $roundOf16RoundBeatmapFreeModLength,
                    'beatmap_bpm'               => $roundOf16RoundBeatmapFreeModOverallSpeed,
                    'beatmap_od'                => $roundOf16RoundBeatmapFreeModOverallDifficulty,
                    'beatmap_hp'                => $roundOf16RoundBeatmapFreeModOverallHealth,
                    'beatmap_pass_count'        => $roundOf16RoundBeatmapFreeModPassCount
                ];
            }

            foreach ($roundOf16RoundJsonEasyData as $roundOf16RoundEasyType => $roundOf16RoundJsonEasyId) {
                $roundOf16RoundBeatmapEasyData = getTournamentRoundBeatmapData(
                    beatmap_id: $roundOf16RoundJsonEasyId,
                    access_token: $_COOKIE['vot_access_token']
                );

                $roundOf16RoundBeatmapEasyId                 = $roundOf16RoundBeatmapEasyData['id'];
                $roundOf16RoundId                               = $roundOf16RoundAbbreviationName;
                $roundOf16TournamentId                          = strtoupper(string: $tournament_name);
                $roundOf16RoundBeatmapEasyType               = $roundOf16RoundEasyType;
                $roundOf16RoundBeatmapEasyImage              = $roundOf16RoundBeatmapEasyData['beatmapset']['covers']['cover'];
                $roundOf16RoundBeatmapEasyUrl                = $roundOf16RoundBeatmapEasyData['url'];
                $roundOf16RoundBeatmapEasyName               = $roundOf16RoundBeatmapEasyData['beatmapset']['title'];
                $roundOf16RoundBeatmapEasyDifficultyName     = $roundOf16RoundBeatmapEasyData['version'];
                $roundOf16RoundBeatmapEasyFeatureArtist      = $roundOf16RoundBeatmapEasyData['beatmapset']['artist'];
                $roundOf16RoundBeatmapEasyMapper             = $roundOf16RoundBeatmapEasyData['beatmapset']['creator'];
                $roundOf16RoundBeatmapEasyMapperUrl          = "https://osu.ppy.sh/users/{$roundOf16RoundBeatmapEasyData['beatmapset']['user_id']}";
                $roundOf16RoundBeatmapEasyDifficulty         = $roundOf16RoundBeatmapEasyData['difficulty_rating'];
                $roundOf16RoundBeatmapEasyLength             = $roundOf16RoundBeatmapEasyData['total_length'];
                $roundOf16RoundBeatmapEasyOverallSpeed       = $roundOf16RoundBeatmapEasyData['beatmapset']['bpm'];
                $roundOf16RoundBeatmapEasyOverallDifficulty  = $roundOf16RoundBeatmapEasyData['accuracy'];
                $roundOf16RoundBeatmapEasyOverallHealth      = $roundOf16RoundBeatmapEasyData['drain'];
                $roundOf16RoundBeatmapEasyPassCount          = $roundOf16RoundBeatmapEasyData['passcount'];

                $tournamentRoundBeatmapEasyData[] = [
                    'beatmap_id'                => $roundOf16RoundBeatmapEasyId,
                    'beatmap_round_id'          => $roundOf16RoundId,
                    'beatmap_tournament_id'     => $roundOf16TournamentId,
                    'beatmap_type'              => $roundOf16RoundBeatmapEasyType,
                    'beatmap_image'             => $roundOf16RoundBeatmapEasyImage,
                    'beatmap_url'               => $roundOf16RoundBeatmapEasyUrl,
                    'beatmap_name'              => $roundOf16RoundBeatmapEasyName,
                    'beatmap_difficulty_name'   => $roundOf16RoundBeatmapEasyDifficultyName,
                    'beatmap_fa'                => $roundOf16RoundBeatmapEasyFeatureArtist,
                    'beatmap_mapper'            => $roundOf16RoundBeatmapEasyMapper,
                    'beatmap_mapper_url'        => $roundOf16RoundBeatmapEasyMapperUrl,
                    'beatmap_difficulty'        => $roundOf16RoundBeatmapEasyDifficulty,
                    'beatmap_length'            => $roundOf16RoundBeatmapEasyLength,
                    'beatmap_bpm'               => $roundOf16RoundBeatmapEasyOverallSpeed,
                    'beatmap_od'                => $roundOf16RoundBeatmapEasyOverallDifficulty,
                    'beatmap_hp'                => $roundOf16RoundBeatmapEasyOverallHealth,
                    'beatmap_pass_count'        => $roundOf16RoundBeatmapEasyPassCount
                ];
            }

            foreach ($roundOf16RoundJsonHiddenHardRockData as $roundOf16RoundHiddenHardRockType => $roundOf16RoundJsonHiddenHardRockId) {
                $roundOf16RoundBeatmapHiddenHardRockData = getTournamentRoundBeatmapData(
                    beatmap_id: $roundOf16RoundJsonHiddenHardRockId,
                    access_token: $_COOKIE['vot_access_token']
                );

                $roundOf16RoundBeatmapHiddenHardRockId                 = $roundOf16RoundBeatmapHiddenHardRockData['id'];
                $roundOf16RoundId                               = $roundOf16RoundAbbreviationName;
                $roundOf16TournamentId                          = strtoupper(string: $tournament_name);
                $roundOf16RoundBeatmapHiddenHardRockType               = $roundOf16RoundHiddenHardRockType;
                $roundOf16RoundBeatmapHiddenHardRockImage              = $roundOf16RoundBeatmapHiddenHardRockData['beatmapset']['covers']['cover'];
                $roundOf16RoundBeatmapHiddenHardRockUrl                = $roundOf16RoundBeatmapHiddenHardRockData['url'];
                $roundOf16RoundBeatmapHiddenHardRockName               = $roundOf16RoundBeatmapHiddenHardRockData['beatmapset']['title'];
                $roundOf16RoundBeatmapHiddenHardRockDifficultyName     = $roundOf16RoundBeatmapHiddenHardRockData['version'];
                $roundOf16RoundBeatmapHiddenHardRockFeatureArtist      = $roundOf16RoundBeatmapHiddenHardRockData['beatmapset']['artist'];
                $roundOf16RoundBeatmapHiddenHardRockMapper             = $roundOf16RoundBeatmapHiddenHardRockData['beatmapset']['creator'];
                $roundOf16RoundBeatmapHiddenHardRockMapperUrl          = "https://osu.ppy.sh/users/{$roundOf16RoundBeatmapHiddenHardRockData['beatmapset']['user_id']}";
                $roundOf16RoundBeatmapHiddenHardRockDifficulty         = $roundOf16RoundBeatmapHiddenHardRockData['difficulty_rating'];
                $roundOf16RoundBeatmapHiddenHardRockLength             = $roundOf16RoundBeatmapHiddenHardRockData['total_length'];
                $roundOf16RoundBeatmapHiddenHardRockOverallSpeed       = $roundOf16RoundBeatmapHiddenHardRockData['beatmapset']['bpm'];
                $roundOf16RoundBeatmapHiddenHardRockOverallDifficulty  = $roundOf16RoundBeatmapHiddenHardRockData['accuracy'];
                $roundOf16RoundBeatmapHiddenHardRockOverallHealth      = $roundOf16RoundBeatmapHiddenHardRockData['drain'];
                $roundOf16RoundBeatmapHiddenHardRockPassCount          = $roundOf16RoundBeatmapHiddenHardRockData['passcount'];

                $tournamentRoundBeatmapHiddenHardRockData[] = [
                    'beatmap_id'                => $roundOf16RoundBeatmapHiddenHardRockId,
                    'beatmap_round_id'          => $roundOf16RoundId,
                    'beatmap_tournament_id'     => $roundOf16TournamentId,
                    'beatmap_type'              => $roundOf16RoundBeatmapHiddenHardRockType,
                    'beatmap_image'             => $roundOf16RoundBeatmapHiddenHardRockImage,
                    'beatmap_url'               => $roundOf16RoundBeatmapHiddenHardRockUrl,
                    'beatmap_name'              => $roundOf16RoundBeatmapHiddenHardRockName,
                    'beatmap_difficulty_name'   => $roundOf16RoundBeatmapHiddenHardRockDifficultyName,
                    'beatmap_fa'                => $roundOf16RoundBeatmapHiddenHardRockFeatureArtist,
                    'beatmap_mapper'            => $roundOf16RoundBeatmapHiddenHardRockMapper,
                    'beatmap_mapper_url'        => $roundOf16RoundBeatmapHiddenHardRockMapperUrl,
                    'beatmap_difficulty'        => $roundOf16RoundBeatmapHiddenHardRockDifficulty,
                    'beatmap_length'            => $roundOf16RoundBeatmapHiddenHardRockLength,
                    'beatmap_bpm'               => $roundOf16RoundBeatmapHiddenHardRockOverallSpeed,
                    'beatmap_od'                => $roundOf16RoundBeatmapHiddenHardRockOverallDifficulty,
                    'beatmap_hp'                => $roundOf16RoundBeatmapHiddenHardRockOverallHealth,
                    'beatmap_pass_count'        => $roundOf16RoundBeatmapHiddenHardRockPassCount
                ];
            }

            foreach ($roundOf16RoundJsonTiebreakerData as $roundOf16RoundTiebreakerType => $roundOf16RoundJsonTiebreakerId) {
                $roundOf16RoundBeatmapTiebreakerData = getTournamentRoundBeatmapData(
                    beatmap_id: $roundOf16RoundJsonTiebreakerId,
                    access_token: $_COOKIE['vot_access_token']
                );

                $roundOf16RoundBeatmapTiebreakerId                 = $roundOf16RoundBeatmapTiebreakerData['id'];
                $roundOf16RoundId                               = $roundOf16RoundAbbreviationName;
                $roundOf16TournamentId                          = strtoupper(string: $tournament_name);
                $roundOf16RoundBeatmapTiebreakerType               = $roundOf16RoundTiebreakerType;
                $roundOf16RoundBeatmapTiebreakerImage              = $roundOf16RoundBeatmapTiebreakerData['beatmapset']['covers']['cover'];
                $roundOf16RoundBeatmapTiebreakerUrl                = $roundOf16RoundBeatmapTiebreakerData['url'];
                $roundOf16RoundBeatmapTiebreakerName               = $roundOf16RoundBeatmapTiebreakerData['beatmapset']['title'];
                $roundOf16RoundBeatmapTiebreakerDifficultyName     = $roundOf16RoundBeatmapTiebreakerData['version'];
                $roundOf16RoundBeatmapTiebreakerFeatureArtist      = $roundOf16RoundBeatmapTiebreakerData['beatmapset']['artist'];
                $roundOf16RoundBeatmapTiebreakerMapper             = $roundOf16RoundBeatmapTiebreakerData['beatmapset']['creator'];
                $roundOf16RoundBeatmapTiebreakerMapperUrl          = "https://osu.ppy.sh/users/{$roundOf16RoundBeatmapTiebreakerData['beatmapset']['user_id']}";
                $roundOf16RoundBeatmapTiebreakerDifficulty         = $roundOf16RoundBeatmapTiebreakerData['difficulty_rating'];
                $roundOf16RoundBeatmapTiebreakerLength             = $roundOf16RoundBeatmapTiebreakerData['total_length'];
                $roundOf16RoundBeatmapTiebreakerOverallSpeed       = $roundOf16RoundBeatmapTiebreakerData['beatmapset']['bpm'];
                $roundOf16RoundBeatmapTiebreakerOverallDifficulty  = $roundOf16RoundBeatmapTiebreakerData['accuracy'];
                $roundOf16RoundBeatmapTiebreakerOverallHealth      = $roundOf16RoundBeatmapTiebreakerData['drain'];
                $roundOf16RoundBeatmapTiebreakerPassCount          = $roundOf16RoundBeatmapTiebreakerData['passcount'];

                $tournamentRoundBeatmapTiebreakerData[] = [
                    'beatmap_id'                => $roundOf16RoundBeatmapTiebreakerId,
                    'beatmap_round_id'          => $roundOf16RoundId,
                    'beatmap_tournament_id'     => $roundOf16TournamentId,
                    'beatmap_type'              => $roundOf16RoundBeatmapTiebreakerType,
                    'beatmap_image'             => $roundOf16RoundBeatmapTiebreakerImage,
                    'beatmap_url'               => $roundOf16RoundBeatmapTiebreakerUrl,
                    'beatmap_name'              => $roundOf16RoundBeatmapTiebreakerName,
                    'beatmap_difficulty_name'   => $roundOf16RoundBeatmapTiebreakerDifficultyName,
                    'beatmap_fa'                => $roundOf16RoundBeatmapTiebreakerFeatureArtist,
                    'beatmap_mapper'            => $roundOf16RoundBeatmapTiebreakerMapper,
                    'beatmap_mapper_url'        => $roundOf16RoundBeatmapTiebreakerMapperUrl,
                    'beatmap_difficulty'        => $roundOf16RoundBeatmapTiebreakerDifficulty,
                    'beatmap_length'            => $roundOf16RoundBeatmapTiebreakerLength,
                    'beatmap_bpm'               => $roundOf16RoundBeatmapTiebreakerOverallSpeed,
                    'beatmap_od'                => $roundOf16RoundBeatmapTiebreakerOverallDifficulty,
                    'beatmap_hp'                => $roundOf16RoundBeatmapTiebreakerOverallHealth,
                    'beatmap_pass_count'        => $roundOf16RoundBeatmapTiebreakerPassCount
                ];
            }


            /* QUARTER FINAL MAPPOOL LOOPING DATA */
            $quarterFinalRoundAbbreviationName          = $tournamentRoundAbbreviationNameData[2];
            $quarterFinalRoundJsonMappoolData           = $tournamentRoundReadableMappoolData[$tournament_name][$quarterFinalRoundAbbreviationName];
            $quarterFinalRoundJsonNoModData             = $quarterFinalRoundJsonMappoolData['NM'];
            $quarterFinalRoundJsonHiddenData            = $quarterFinalRoundJsonMappoolData['HD'];
            $quarterFinalRoundJsonHardRockData          = $quarterFinalRoundJsonMappoolData['HR'];
            $quarterFinalRoundJsonDoubleTimeData        = $quarterFinalRoundJsonMappoolData['DT'];
            $quarterFinalRoundJsonFreeModData           = $quarterFinalRoundJsonMappoolData['FM'];
            $quarterFinalRoundJsonEasyData              = $quarterFinalRoundJsonMappoolData['EZ'];
            $quarterFinalRoundJsonHiddenHardRockData    = $quarterFinalRoundJsonMappoolData['HDHR'];
            $quarterFinalRoundJsonTiebreakerData        = $quarterFinalRoundJsonMappoolData['TB'];

            foreach ($quarterFinalRoundJsonNoModData as $quarterFinalRoundNoModType => $quarterFinalRoundJsonNoModId) {
                $quarterFinalRoundBeatmapNoModData = getTournamentRoundBeatmapData(
                    beatmap_id: $quarterFinalRoundJsonNoModId,
                    access_token: $_COOKIE['vot_access_token']
                );

                $quarterFinalRoundBeatmapNoModId                   = $quarterFinalRoundBeatmapNoModData['id'];
                $quarterFinalRoundId                               = $quarterFinalRoundAbbreviationName;
                $quarterFinalTournamentId                          = strtoupper(string: $tournament_name);
                $quarterFinalRoundBeatmapNoModType                 = $quarterFinalRoundNoModType;
                $quarterFinalRoundBeatmapNoModImage                = $quarterFinalRoundBeatmapNoModData['beatmapset']['covers']['cover'];
                $quarterFinalRoundBeatmapNoModUrl                  = $quarterFinalRoundBeatmapNoModData['url'];
                $quarterFinalRoundBeatmapNoModName                 = $quarterFinalRoundBeatmapNoModData['beatmapset']['title'];
                $quarterFinalRoundBeatmapNoModDifficultyName       = $quarterFinalRoundBeatmapNoModData['version'];
                $quarterFinalRoundBeatmapNoModFeatureArtist        = $quarterFinalRoundBeatmapNoModData['beatmapset']['artist'];
                $quarterFinalRoundBeatmapNoModMapper               = $quarterFinalRoundBeatmapNoModData['beatmapset']['creator'];
                $quarterFinalRoundBeatmapNoModMapperUrl            = "https://osu.ppy.sh/users/{$quarterFinalRoundBeatmapNoModData['beatmapset']['user_id']}";
                $quarterFinalRoundBeatmapNoModDifficulty           = $quarterFinalRoundBeatmapNoModData['difficulty_rating'];
                $quarterFinalRoundBeatmapNoModLength               = $quarterFinalRoundBeatmapNoModData['total_length'];
                $quarterFinalRoundBeatmapNoModOverallSpeed         = $quarterFinalRoundBeatmapNoModData['beatmapset']['bpm'];
                $quarterFinalRoundBeatmapNoModOverallDifficulty    = $quarterFinalRoundBeatmapNoModData['accuracy'];
                $quarterFinalRoundBeatmapNoModOverallHealth        = $quarterFinalRoundBeatmapNoModData['drain'];
                $quarterFinalRoundBeatmapNoModPassCount            = $quarterFinalRoundBeatmapNoModData['passcount'];

                $tournamentRoundBeatmapNoModData[] = [
                    'beatmap_id'                => $quarterFinalRoundBeatmapNoModId,
                    'beatmap_round_id'          => $quarterFinalRoundId,
                    'beatmap_tournament_id'     => $quarterFinalTournamentId,
                    'beatmap_type'              => $quarterFinalRoundBeatmapNoModType,
                    'beatmap_image'             => $quarterFinalRoundBeatmapNoModImage,
                    'beatmap_url'               => $quarterFinalRoundBeatmapNoModUrl,
                    'beatmap_name'              => $quarterFinalRoundBeatmapNoModName,
                    'beatmap_difficulty_name'   => $quarterFinalRoundBeatmapNoModDifficultyName,
                    'beatmap_fa'                => $quarterFinalRoundBeatmapNoModFeatureArtist,
                    'beatmap_mapper'            => $quarterFinalRoundBeatmapNoModMapper,
                    'beatmap_mapper_url'        => $quarterFinalRoundBeatmapNoModMapperUrl,
                    'beatmap_difficulty'        => $quarterFinalRoundBeatmapNoModDifficulty,
                    'beatmap_length'            => $quarterFinalRoundBeatmapNoModLength,
                    'beatmap_bpm'               => $quarterFinalRoundBeatmapNoModOverallSpeed,
                    'beatmap_od'                => $quarterFinalRoundBeatmapNoModOverallDifficulty,
                    'beatmap_hp'                => $quarterFinalRoundBeatmapNoModOverallHealth,
                    'beatmap_pass_count'        => $quarterFinalRoundBeatmapNoModPassCount
                ];
            }

            foreach ($quarterFinalRoundJsonHiddenData as $quarterFinalRoundHiddenType => $quarterFinalRoundJsonHiddenId) {
                $quarterFinalRoundBeatmapHiddenData = getTournamentRoundBeatmapData(
                    beatmap_id: $quarterFinalRoundJsonHiddenId,
                    access_token: $_COOKIE['vot_access_token']
                );

                $quarterFinalRoundBeatmapHiddenId                  = $quarterFinalRoundBeatmapHiddenData['id'];
                $quarterFinalRoundId                               = $quarterFinalRoundAbbreviationName;
                $quarterFinalTournamentId                          = strtoupper(string: $tournament_name);
                $quarterFinalRoundBeatmapHiddenType                = $quarterFinalRoundHiddenType;
                $quarterFinalRoundBeatmapHiddenImage               = $quarterFinalRoundBeatmapHiddenData['beatmapset']['covers']['cover'];
                $quarterFinalRoundBeatmapHiddenUrl                 = $quarterFinalRoundBeatmapHiddenData['url'];
                $quarterFinalRoundBeatmapHiddenName                = $quarterFinalRoundBeatmapHiddenData['beatmapset']['title'];
                $quarterFinalRoundBeatmapHiddenDifficultyName      = $quarterFinalRoundBeatmapHiddenData['version'];
                $quarterFinalRoundBeatmapHiddenFeatureArtist       = $quarterFinalRoundBeatmapHiddenData['beatmapset']['artist'];
                $quarterFinalRoundBeatmapHiddenMapper              = $quarterFinalRoundBeatmapHiddenData['beatmapset']['creator'];
                $quarterFinalRoundBeatmapHiddenMapperUrl           = "https://osu.ppy.sh/users/{$quarterFinalRoundBeatmapHiddenData['beatmapset']['user_id']}";
                $quarterFinalRoundBeatmapHiddenDifficulty          = $quarterFinalRoundBeatmapHiddenData['difficulty_rating'];
                $quarterFinalRoundBeatmapHiddenLength              = $quarterFinalRoundBeatmapHiddenData['total_length'];
                $quarterFinalRoundBeatmapHiddenOverallSpeed        = $quarterFinalRoundBeatmapHiddenData['beatmapset']['bpm'];
                $quarterFinalRoundBeatmapHiddenOverallDifficulty   = $quarterFinalRoundBeatmapHiddenData['accuracy'];
                $quarterFinalRoundBeatmapHiddenOverallHealth       = $quarterFinalRoundBeatmapHiddenData['drain'];
                $quarterFinalRoundBeatmapHiddenPassCount           = $quarterFinalRoundBeatmapHiddenData['passcount'];

                $tournamentRoundBeatmapHiddenData[] = [
                    'beatmap_id'                => $quarterFinalRoundBeatmapHiddenId,
                    'beatmap_round_id'          => $quarterFinalRoundId,
                    'beatmap_tournament_id'     => $quarterFinalTournamentId,
                    'beatmap_type'              => $quarterFinalRoundBeatmapHiddenType,
                    'beatmap_image'             => $quarterFinalRoundBeatmapHiddenImage,
                    'beatmap_url'               => $quarterFinalRoundBeatmapHiddenUrl,
                    'beatmap_name'              => $quarterFinalRoundBeatmapHiddenName,
                    'beatmap_difficulty_name'   => $quarterFinalRoundBeatmapHiddenDifficultyName,
                    'beatmap_fa'                => $quarterFinalRoundBeatmapHiddenFeatureArtist,
                    'beatmap_mapper'            => $quarterFinalRoundBeatmapHiddenMapper,
                    'beatmap_mapper_url'        => $quarterFinalRoundBeatmapHiddenMapperUrl,
                    'beatmap_difficulty'        => $quarterFinalRoundBeatmapHiddenDifficulty,
                    'beatmap_length'            => $quarterFinalRoundBeatmapHiddenLength,
                    'beatmap_bpm'               => $quarterFinalRoundBeatmapHiddenOverallSpeed,
                    'beatmap_od'                => $quarterFinalRoundBeatmapHiddenOverallDifficulty,
                    'beatmap_hp'                => $quarterFinalRoundBeatmapHiddenOverallHealth,
                    'beatmap_pass_count'        => $quarterFinalRoundBeatmapHiddenPassCount
                ];
            }

            foreach ($quarterFinalRoundJsonHardRockData as $quarterFinalRoundHardRockType => $quarterFinalRoundJsonHardRockId) {
                $quarterFinalRoundBeatmapHardRockData = getTournamentRoundBeatmapData(
                    beatmap_id: $quarterFinalRoundJsonHardRockId,
                    access_token: $_COOKIE['vot_access_token']
                );

                $quarterFinalRoundBeatmapHardRockId                    = $quarterFinalRoundBeatmapHardRockData['id'];
                $quarterFinalRoundId                                   = $quarterFinalRoundAbbreviationName;
                $quarterFinalTournamentId                              = strtoupper(string: $tournament_name);
                $quarterFinalRoundBeatmapHardRockType                  = $quarterFinalRoundHardRockType;
                $quarterFinalRoundBeatmapHardRockImage                 = $quarterFinalRoundBeatmapHardRockData['beatmapset']['covers']['cover'];
                $quarterFinalRoundBeatmapHardRockUrl                   = $quarterFinalRoundBeatmapHardRockData['url'];
                $quarterFinalRoundBeatmapHardRockName                  = $quarterFinalRoundBeatmapHardRockData['beatmapset']['title'];
                $quarterFinalRoundBeatmapHardRockDifficultyName        = $quarterFinalRoundBeatmapHardRockData['version'];
                $quarterFinalRoundBeatmapHardRockFeatureArtist         = $quarterFinalRoundBeatmapHardRockData['beatmapset']['artist'];
                $quarterFinalRoundBeatmapHardRockMapper                = $quarterFinalRoundBeatmapHardRockData['beatmapset']['creator'];
                $quarterFinalRoundBeatmapHardRockMapperUrl             = "https://osu.ppy.sh/users/{$quarterFinalRoundBeatmapHardRockData['beatmapset']['user_id']}";
                $quarterFinalRoundBeatmapHardRockDifficulty            = $quarterFinalRoundBeatmapHardRockData['difficulty_rating'];
                $quarterFinalRoundBeatmapHardRockLength                = $quarterFinalRoundBeatmapHardRockData['total_length'];
                $quarterFinalRoundBeatmapHardRockOverallSpeed          = $quarterFinalRoundBeatmapHardRockData['beatmapset']['bpm'];
                $quarterFinalRoundBeatmapHardRockOverallDifficulty     = $quarterFinalRoundBeatmapHardRockData['accuracy'];
                $quarterFinalRoundBeatmapHardRockOverallHealth         = $quarterFinalRoundBeatmapHardRockData['drain'];
                $quarterFinalRoundBeatmapHardRockPassCount             = $quarterFinalRoundBeatmapHardRockData['passcount'];

                $tournamentRoundBeatmapHardRockData[] = [
                    'beatmap_id'                => $quarterFinalRoundBeatmapHardRockId,
                    'beatmap_round_id'          => $quarterFinalRoundId,
                    'beatmap_tournament_id'     => $quarterFinalTournamentId,
                    'beatmap_type'              => $quarterFinalRoundBeatmapHardRockType,
                    'beatmap_image'             => $quarterFinalRoundBeatmapHardRockImage,
                    'beatmap_url'               => $quarterFinalRoundBeatmapHardRockUrl,
                    'beatmap_name'              => $quarterFinalRoundBeatmapHardRockName,
                    'beatmap_difficulty_name'   => $quarterFinalRoundBeatmapHardRockDifficultyName,
                    'beatmap_fa'                => $quarterFinalRoundBeatmapHardRockFeatureArtist,
                    'beatmap_mapper'            => $quarterFinalRoundBeatmapHardRockMapper,
                    'beatmap_mapper_url'        => $quarterFinalRoundBeatmapHardRockMapperUrl,
                    'beatmap_difficulty'        => $quarterFinalRoundBeatmapHardRockDifficulty,
                    'beatmap_length'            => $quarterFinalRoundBeatmapHardRockLength,
                    'beatmap_bpm'               => $quarterFinalRoundBeatmapHardRockOverallSpeed,
                    'beatmap_od'                => $quarterFinalRoundBeatmapHardRockOverallDifficulty,
                    'beatmap_hp'                => $quarterFinalRoundBeatmapHardRockOverallHealth,
                    'beatmap_pass_count'        => $quarterFinalRoundBeatmapHardRockPassCount
                ];
            }

            foreach ($quarterFinalRoundJsonDoubleTimeData as $quarterFinalRoundDoubleTimeType => $quarterFinalRoundJsonDoubleTimeId) {
                $quarterFinalRoundBeatmapDoubleTimeData = getTournamentRoundBeatmapData(
                    beatmap_id: $quarterFinalRoundJsonDoubleTimeId,
                    access_token: $_COOKIE['vot_access_token']
                );

                $quarterFinalRoundBeatmapDoubleTimeId                  = $quarterFinalRoundBeatmapDoubleTimeData['id'];
                $quarterFinalRoundId                                   = $quarterFinalRoundAbbreviationName;
                $quarterFinalTournamentId                              = strtoupper(string: $tournament_name);
                $quarterFinalRoundBeatmapDoubleTimeType                = $quarterFinalRoundDoubleTimeType;
                $quarterFinalRoundBeatmapDoubleTimeImage               = $quarterFinalRoundBeatmapDoubleTimeData['beatmapset']['covers']['cover'];
                $quarterFinalRoundBeatmapDoubleTimeUrl                 = $quarterFinalRoundBeatmapDoubleTimeData['url'];
                $quarterFinalRoundBeatmapDoubleTimeName                = $quarterFinalRoundBeatmapDoubleTimeData['beatmapset']['title'];
                $quarterFinalRoundBeatmapDoubleTimeDifficultyName      = $quarterFinalRoundBeatmapDoubleTimeData['version'];
                $quarterFinalRoundBeatmapDoubleTimeFeatureArtist       = $quarterFinalRoundBeatmapDoubleTimeData['beatmapset']['artist'];
                $quarterFinalRoundBeatmapDoubleTimeMapper              = $quarterFinalRoundBeatmapDoubleTimeData['beatmapset']['creator'];
                $quarterFinalRoundBeatmapDoubleTimeMapperUrl           = "https://osu.ppy.sh/users/{$quarterFinalRoundBeatmapDoubleTimeData['beatmapset']['user_id']}";
                $quarterFinalRoundBeatmapDoubleTimeDifficulty          = $quarterFinalRoundBeatmapDoubleTimeData['difficulty_rating'];
                $quarterFinalRoundBeatmapDoubleTimeLength              = $quarterFinalRoundBeatmapDoubleTimeData['total_length'];
                $quarterFinalRoundBeatmapDoubleTimeOverallSpeed        = $quarterFinalRoundBeatmapDoubleTimeData['beatmapset']['bpm'];
                $quarterFinalRoundBeatmapDoubleTimeOverallDifficulty   = $quarterFinalRoundBeatmapDoubleTimeData['accuracy'];
                $quarterFinalRoundBeatmapDoubleTimeOverallHealth       = $quarterFinalRoundBeatmapDoubleTimeData['drain'];
                $quarterFinalRoundBeatmapDoubleTimePassCount           = $quarterFinalRoundBeatmapDoubleTimeData['passcount'];

                $tournamentRoundBeatmapDoubleTimeData[] = [
                    'beatmap_id'                => $quarterFinalRoundBeatmapDoubleTimeId,
                    'beatmap_round_id'          => $quarterFinalRoundId,
                    'beatmap_tournament_id'     => $quarterFinalTournamentId,
                    'beatmap_type'              => $quarterFinalRoundBeatmapDoubleTimeType,
                    'beatmap_image'             => $quarterFinalRoundBeatmapDoubleTimeImage,
                    'beatmap_url'               => $quarterFinalRoundBeatmapDoubleTimeUrl,
                    'beatmap_name'              => $quarterFinalRoundBeatmapDoubleTimeName,
                    'beatmap_difficulty_name'   => $quarterFinalRoundBeatmapDoubleTimeDifficultyName,
                    'beatmap_fa'                => $quarterFinalRoundBeatmapDoubleTimeFeatureArtist,
                    'beatmap_mapper'            => $quarterFinalRoundBeatmapDoubleTimeMapper,
                    'beatmap_mapper_url'        => $quarterFinalRoundBeatmapDoubleTimeMapperUrl,
                    'beatmap_difficulty'        => $quarterFinalRoundBeatmapDoubleTimeDifficulty,
                    'beatmap_length'            => $quarterFinalRoundBeatmapDoubleTimeLength,
                    'beatmap_bpm'               => $quarterFinalRoundBeatmapDoubleTimeOverallSpeed,
                    'beatmap_od'                => $quarterFinalRoundBeatmapDoubleTimeOverallDifficulty,
                    'beatmap_hp'                => $quarterFinalRoundBeatmapDoubleTimeOverallHealth,
                    'beatmap_pass_count'        => $quarterFinalRoundBeatmapDoubleTimePassCount
                ];
            }

            foreach ($quarterFinalRoundJsonFreeModData as $quarterFinalRoundFreeModType => $quarterFinalRoundJsonFreeModId) {
                $quarterFinalRoundBeatmapFreeModData = getTournamentRoundBeatmapData(
                    beatmap_id: $quarterFinalRoundJsonFreeModId,
                    access_token: $_COOKIE['vot_access_token']
                );

                $quarterFinalRoundBeatmapFreeModId                 = $quarterFinalRoundBeatmapFreeModData['id'];
                $quarterFinalRoundId                               = $quarterFinalRoundAbbreviationName;
                $quarterFinalTournamentId                          = strtoupper(string: $tournament_name);
                $quarterFinalRoundBeatmapFreeModType               = $quarterFinalRoundFreeModType;
                $quarterFinalRoundBeatmapFreeModImage              = $quarterFinalRoundBeatmapFreeModData['beatmapset']['covers']['cover'];
                $quarterFinalRoundBeatmapFreeModUrl                = $quarterFinalRoundBeatmapFreeModData['url'];
                $quarterFinalRoundBeatmapFreeModName               = $quarterFinalRoundBeatmapFreeModData['beatmapset']['title'];
                $quarterFinalRoundBeatmapFreeModDifficultyName     = $quarterFinalRoundBeatmapFreeModData['version'];
                $quarterFinalRoundBeatmapFreeModFeatureArtist      = $quarterFinalRoundBeatmapFreeModData['beatmapset']['artist'];
                $quarterFinalRoundBeatmapFreeModMapper             = $quarterFinalRoundBeatmapFreeModData['beatmapset']['creator'];
                $quarterFinalRoundBeatmapFreeModMapperUrl          = "https://osu.ppy.sh/users/{$quarterFinalRoundBeatmapFreeModData['beatmapset']['user_id']}";
                $quarterFinalRoundBeatmapFreeModDifficulty         = $quarterFinalRoundBeatmapFreeModData['difficulty_rating'];
                $quarterFinalRoundBeatmapFreeModLength             = $quarterFinalRoundBeatmapFreeModData['total_length'];
                $quarterFinalRoundBeatmapFreeModOverallSpeed       = $quarterFinalRoundBeatmapFreeModData['beatmapset']['bpm'];
                $quarterFinalRoundBeatmapFreeModOverallDifficulty  = $quarterFinalRoundBeatmapFreeModData['accuracy'];
                $quarterFinalRoundBeatmapFreeModOverallHealth      = $quarterFinalRoundBeatmapFreeModData['drain'];
                $quarterFinalRoundBeatmapFreeModPassCount          = $quarterFinalRoundBeatmapFreeModData['passcount'];

                $tournamentRoundBeatmapFreeModData[] = [
                    'beatmap_id'                => $quarterFinalRoundBeatmapFreeModId,
                    'beatmap_round_id'          => $quarterFinalRoundId,
                    'beatmap_tournament_id'     => $quarterFinalTournamentId,
                    'beatmap_type'              => $quarterFinalRoundBeatmapFreeModType,
                    'beatmap_image'             => $quarterFinalRoundBeatmapFreeModImage,
                    'beatmap_url'               => $quarterFinalRoundBeatmapFreeModUrl,
                    'beatmap_name'              => $quarterFinalRoundBeatmapFreeModName,
                    'beatmap_difficulty_name'   => $quarterFinalRoundBeatmapFreeModDifficultyName,
                    'beatmap_fa'                => $quarterFinalRoundBeatmapFreeModFeatureArtist,
                    'beatmap_mapper'            => $quarterFinalRoundBeatmapFreeModMapper,
                    'beatmap_mapper_url'        => $quarterFinalRoundBeatmapFreeModMapperUrl,
                    'beatmap_difficulty'        => $quarterFinalRoundBeatmapFreeModDifficulty,
                    'beatmap_length'            => $quarterFinalRoundBeatmapFreeModLength,
                    'beatmap_bpm'               => $quarterFinalRoundBeatmapFreeModOverallSpeed,
                    'beatmap_od'                => $quarterFinalRoundBeatmapFreeModOverallDifficulty,
                    'beatmap_hp'                => $quarterFinalRoundBeatmapFreeModOverallHealth,
                    'beatmap_pass_count'        => $quarterFinalRoundBeatmapFreeModPassCount
                ];
            }

            foreach ($quarterFinalRoundJsonEasyData as $quarterFinalRoundEasyType => $quarterFinalRoundJsonEasyId) {
                $quarterFinalRoundBeatmapEasyData = getTournamentRoundBeatmapData(
                    beatmap_id: $quarterFinalRoundJsonEasyId,
                    access_token: $_COOKIE['vot_access_token']
                );

                $quarterFinalRoundBeatmapEasyId                 = $quarterFinalRoundBeatmapEasyData['id'];
                $quarterFinalRoundId                               = $quarterFinalRoundAbbreviationName;
                $quarterFinalTournamentId                          = strtoupper(string: $tournament_name);
                $quarterFinalRoundBeatmapEasyType               = $quarterFinalRoundEasyType;
                $quarterFinalRoundBeatmapEasyImage              = $quarterFinalRoundBeatmapEasyData['beatmapset']['covers']['cover'];
                $quarterFinalRoundBeatmapEasyUrl                = $quarterFinalRoundBeatmapEasyData['url'];
                $quarterFinalRoundBeatmapEasyName               = $quarterFinalRoundBeatmapEasyData['beatmapset']['title'];
                $quarterFinalRoundBeatmapEasyDifficultyName     = $quarterFinalRoundBeatmapEasyData['version'];
                $quarterFinalRoundBeatmapEasyFeatureArtist      = $quarterFinalRoundBeatmapEasyData['beatmapset']['artist'];
                $quarterFinalRoundBeatmapEasyMapper             = $quarterFinalRoundBeatmapEasyData['beatmapset']['creator'];
                $quarterFinalRoundBeatmapEasyMapperUrl          = "https://osu.ppy.sh/users/{$quarterFinalRoundBeatmapEasyData['beatmapset']['user_id']}";
                $quarterFinalRoundBeatmapEasyDifficulty         = $quarterFinalRoundBeatmapEasyData['difficulty_rating'];
                $quarterFinalRoundBeatmapEasyLength             = $quarterFinalRoundBeatmapEasyData['total_length'];
                $quarterFinalRoundBeatmapEasyOverallSpeed       = $quarterFinalRoundBeatmapEasyData['beatmapset']['bpm'];
                $quarterFinalRoundBeatmapEasyOverallDifficulty  = $quarterFinalRoundBeatmapEasyData['accuracy'];
                $quarterFinalRoundBeatmapEasyOverallHealth      = $quarterFinalRoundBeatmapEasyData['drain'];
                $quarterFinalRoundBeatmapEasyPassCount          = $quarterFinalRoundBeatmapEasyData['passcount'];

                $tournamentRoundBeatmapEasyData[] = [
                    'beatmap_id'                => $quarterFinalRoundBeatmapEasyId,
                    'beatmap_round_id'          => $quarterFinalRoundId,
                    'beatmap_tournament_id'     => $quarterFinalTournamentId,
                    'beatmap_type'              => $quarterFinalRoundBeatmapEasyType,
                    'beatmap_image'             => $quarterFinalRoundBeatmapEasyImage,
                    'beatmap_url'               => $quarterFinalRoundBeatmapEasyUrl,
                    'beatmap_name'              => $quarterFinalRoundBeatmapEasyName,
                    'beatmap_difficulty_name'   => $quarterFinalRoundBeatmapEasyDifficultyName,
                    'beatmap_fa'                => $quarterFinalRoundBeatmapEasyFeatureArtist,
                    'beatmap_mapper'            => $quarterFinalRoundBeatmapEasyMapper,
                    'beatmap_mapper_url'        => $quarterFinalRoundBeatmapEasyMapperUrl,
                    'beatmap_difficulty'        => $quarterFinalRoundBeatmapEasyDifficulty,
                    'beatmap_length'            => $quarterFinalRoundBeatmapEasyLength,
                    'beatmap_bpm'               => $quarterFinalRoundBeatmapEasyOverallSpeed,
                    'beatmap_od'                => $quarterFinalRoundBeatmapEasyOverallDifficulty,
                    'beatmap_hp'                => $quarterFinalRoundBeatmapEasyOverallHealth,
                    'beatmap_pass_count'        => $quarterFinalRoundBeatmapEasyPassCount
                ];
            }

            foreach ($quarterFinalRoundJsonHiddenHardRockData as $quarterFinalRoundHiddenHardRockType => $quarterFinalRoundJsonHiddenHardRockId) {
                $quarterFinalRoundBeatmapHiddenHardRockData = getTournamentRoundBeatmapData(
                    beatmap_id: $quarterFinalRoundJsonHiddenHardRockId,
                    access_token: $_COOKIE['vot_access_token']
                );

                $quarterFinalRoundBeatmapHiddenHardRockId                 = $quarterFinalRoundBeatmapHiddenHardRockData['id'];
                $quarterFinalRoundId                               = $quarterFinalRoundAbbreviationName;
                $quarterFinalTournamentId                          = strtoupper(string: $tournament_name);
                $quarterFinalRoundBeatmapHiddenHardRockType               = $quarterFinalRoundHiddenHardRockType;
                $quarterFinalRoundBeatmapHiddenHardRockImage              = $quarterFinalRoundBeatmapHiddenHardRockData['beatmapset']['covers']['cover'];
                $quarterFinalRoundBeatmapHiddenHardRockUrl                = $quarterFinalRoundBeatmapHiddenHardRockData['url'];
                $quarterFinalRoundBeatmapHiddenHardRockName               = $quarterFinalRoundBeatmapHiddenHardRockData['beatmapset']['title'];
                $quarterFinalRoundBeatmapHiddenHardRockDifficultyName     = $quarterFinalRoundBeatmapHiddenHardRockData['version'];
                $quarterFinalRoundBeatmapHiddenHardRockFeatureArtist      = $quarterFinalRoundBeatmapHiddenHardRockData['beatmapset']['artist'];
                $quarterFinalRoundBeatmapHiddenHardRockMapper             = $quarterFinalRoundBeatmapHiddenHardRockData['beatmapset']['creator'];
                $quarterFinalRoundBeatmapHiddenHardRockMapperUrl          = "https://osu.ppy.sh/users/{$quarterFinalRoundBeatmapHiddenHardRockData['beatmapset']['user_id']}";
                $quarterFinalRoundBeatmapHiddenHardRockDifficulty         = $quarterFinalRoundBeatmapHiddenHardRockData['difficulty_rating'];
                $quarterFinalRoundBeatmapHiddenHardRockLength             = $quarterFinalRoundBeatmapHiddenHardRockData['total_length'];
                $quarterFinalRoundBeatmapHiddenHardRockOverallSpeed       = $quarterFinalRoundBeatmapHiddenHardRockData['beatmapset']['bpm'];
                $quarterFinalRoundBeatmapHiddenHardRockOverallDifficulty  = $quarterFinalRoundBeatmapHiddenHardRockData['accuracy'];
                $quarterFinalRoundBeatmapHiddenHardRockOverallHealth      = $quarterFinalRoundBeatmapHiddenHardRockData['drain'];
                $quarterFinalRoundBeatmapHiddenHardRockPassCount          = $quarterFinalRoundBeatmapHiddenHardRockData['passcount'];

                $tournamentRoundBeatmapHiddenHardRockData[] = [
                    'beatmap_id'                => $quarterFinalRoundBeatmapHiddenHardRockId,
                    'beatmap_round_id'          => $quarterFinalRoundId,
                    'beatmap_tournament_id'     => $quarterFinalTournamentId,
                    'beatmap_type'              => $quarterFinalRoundBeatmapHiddenHardRockType,
                    'beatmap_image'             => $quarterFinalRoundBeatmapHiddenHardRockImage,
                    'beatmap_url'               => $quarterFinalRoundBeatmapHiddenHardRockUrl,
                    'beatmap_name'              => $quarterFinalRoundBeatmapHiddenHardRockName,
                    'beatmap_difficulty_name'   => $quarterFinalRoundBeatmapHiddenHardRockDifficultyName,
                    'beatmap_fa'                => $quarterFinalRoundBeatmapHiddenHardRockFeatureArtist,
                    'beatmap_mapper'            => $quarterFinalRoundBeatmapHiddenHardRockMapper,
                    'beatmap_mapper_url'        => $quarterFinalRoundBeatmapHiddenHardRockMapperUrl,
                    'beatmap_difficulty'        => $quarterFinalRoundBeatmapHiddenHardRockDifficulty,
                    'beatmap_length'            => $quarterFinalRoundBeatmapHiddenHardRockLength,
                    'beatmap_bpm'               => $quarterFinalRoundBeatmapHiddenHardRockOverallSpeed,
                    'beatmap_od'                => $quarterFinalRoundBeatmapHiddenHardRockOverallDifficulty,
                    'beatmap_hp'                => $quarterFinalRoundBeatmapHiddenHardRockOverallHealth,
                    'beatmap_pass_count'        => $quarterFinalRoundBeatmapHiddenHardRockPassCount
                ];
            }

            foreach ($quarterFinalRoundJsonTiebreakerData as $quarterFinalRoundTiebreakerType => $quarterFinalRoundJsonTiebreakerId) {
                $quarterFinalRoundBeatmapTiebreakerData = getTournamentRoundBeatmapData(
                    beatmap_id: $quarterFinalRoundJsonTiebreakerId,
                    access_token: $_COOKIE['vot_access_token']
                );

                $quarterFinalRoundBeatmapTiebreakerId                 = $quarterFinalRoundBeatmapTiebreakerData['id'];
                $quarterFinalRoundId                               = $quarterFinalRoundAbbreviationName;
                $quarterFinalTournamentId                          = strtoupper(string: $tournament_name);
                $quarterFinalRoundBeatmapTiebreakerType               = $quarterFinalRoundTiebreakerType;
                $quarterFinalRoundBeatmapTiebreakerImage              = $quarterFinalRoundBeatmapTiebreakerData['beatmapset']['covers']['cover'];
                $quarterFinalRoundBeatmapTiebreakerUrl                = $quarterFinalRoundBeatmapTiebreakerData['url'];
                $quarterFinalRoundBeatmapTiebreakerName               = $quarterFinalRoundBeatmapTiebreakerData['beatmapset']['title'];
                $quarterFinalRoundBeatmapTiebreakerDifficultyName     = $quarterFinalRoundBeatmapTiebreakerData['version'];
                $quarterFinalRoundBeatmapTiebreakerFeatureArtist      = $quarterFinalRoundBeatmapTiebreakerData['beatmapset']['artist'];
                $quarterFinalRoundBeatmapTiebreakerMapper             = $quarterFinalRoundBeatmapTiebreakerData['beatmapset']['creator'];
                $quarterFinalRoundBeatmapTiebreakerMapperUrl          = "https://osu.ppy.sh/users/{$quarterFinalRoundBeatmapTiebreakerData['beatmapset']['user_id']}";
                $quarterFinalRoundBeatmapTiebreakerDifficulty         = $quarterFinalRoundBeatmapTiebreakerData['difficulty_rating'];
                $quarterFinalRoundBeatmapTiebreakerLength             = $quarterFinalRoundBeatmapTiebreakerData['total_length'];
                $quarterFinalRoundBeatmapTiebreakerOverallSpeed       = $quarterFinalRoundBeatmapTiebreakerData['beatmapset']['bpm'];
                $quarterFinalRoundBeatmapTiebreakerOverallDifficulty  = $quarterFinalRoundBeatmapTiebreakerData['accuracy'];
                $quarterFinalRoundBeatmapTiebreakerOverallHealth      = $quarterFinalRoundBeatmapTiebreakerData['drain'];
                $quarterFinalRoundBeatmapTiebreakerPassCount          = $quarterFinalRoundBeatmapTiebreakerData['passcount'];

                $tournamentRoundBeatmapTiebreakerData[] = [
                    'beatmap_id'                => $quarterFinalRoundBeatmapTiebreakerId,
                    'beatmap_round_id'          => $quarterFinalRoundId,
                    'beatmap_tournament_id'     => $quarterFinalTournamentId,
                    'beatmap_type'              => $quarterFinalRoundBeatmapTiebreakerType,
                    'beatmap_image'             => $quarterFinalRoundBeatmapTiebreakerImage,
                    'beatmap_url'               => $quarterFinalRoundBeatmapTiebreakerUrl,
                    'beatmap_name'              => $quarterFinalRoundBeatmapTiebreakerName,
                    'beatmap_difficulty_name'   => $quarterFinalRoundBeatmapTiebreakerDifficultyName,
                    'beatmap_fa'                => $quarterFinalRoundBeatmapTiebreakerFeatureArtist,
                    'beatmap_mapper'            => $quarterFinalRoundBeatmapTiebreakerMapper,
                    'beatmap_mapper_url'        => $quarterFinalRoundBeatmapTiebreakerMapperUrl,
                    'beatmap_difficulty'        => $quarterFinalRoundBeatmapTiebreakerDifficulty,
                    'beatmap_length'            => $quarterFinalRoundBeatmapTiebreakerLength,
                    'beatmap_bpm'               => $quarterFinalRoundBeatmapTiebreakerOverallSpeed,
                    'beatmap_od'                => $quarterFinalRoundBeatmapTiebreakerOverallDifficulty,
                    'beatmap_hp'                => $quarterFinalRoundBeatmapTiebreakerOverallHealth,
                    'beatmap_pass_count'        => $quarterFinalRoundBeatmapTiebreakerPassCount
                ];
            }


            /* SEMI FINAL MAPPOOL LOOPING DATA */
            $semiFinalRoundAbbreviationName             = $tournamentRoundAbbreviationNameData[3];
            $semiFinalRoundJsonMappoolData              = $tournamentRoundReadableMappoolData[$tournament_name][$semiFinalRoundAbbreviationName];
            $semiFinalRoundJsonNoModData                = $semiFinalRoundJsonMappoolData['NM'];
            $semiFinalRoundJsonHiddenData               = $semiFinalRoundJsonMappoolData['HD'];
            $semiFinalRoundJsonHardRockData             = $semiFinalRoundJsonMappoolData['HR'];
            $semiFinalRoundJsonDoubleTimeData           = $semiFinalRoundJsonMappoolData['DT'];
            $semiFinalRoundJsonFreeModData              = $semiFinalRoundJsonMappoolData['FM'];
            $semiFinalRoundJsonEasyData                 = $semiFinalRoundJsonMappoolData['EZ'];
            $semiFinalRoundJsonHiddenHardRockData       = $semiFinalRoundJsonMappoolData['HDHR'];
            $semiFinalRoundJsonTiebreakerData           = $semiFinalRoundJsonMappoolData['TB'];

            foreach ($semiFinalRoundJsonNoModData as $semiFinalRoundNoModType => $semiFinalRoundJsonNoModId) {
                $semiFinalRoundBeatmapNoModData = getTournamentRoundBeatmapData(
                    beatmap_id: $semiFinalRoundJsonNoModId,
                    access_token: $_COOKIE['vot_access_token']
                );

                $semiFinalRoundBeatmapNoModId                   = $semiFinalRoundBeatmapNoModData['id'];
                $semiFinalRoundId                               = $semiFinalRoundAbbreviationName;
                $semiFinalTournamentId                          = strtoupper(string: $tournament_name);
                $semiFinalRoundBeatmapNoModType                 = $semiFinalRoundNoModType;
                $semiFinalRoundBeatmapNoModImage                = $semiFinalRoundBeatmapNoModData['beatmapset']['covers']['cover'];
                $semiFinalRoundBeatmapNoModUrl                  = $semiFinalRoundBeatmapNoModData['url'];
                $semiFinalRoundBeatmapNoModName                 = $semiFinalRoundBeatmapNoModData['beatmapset']['title'];
                $semiFinalRoundBeatmapNoModDifficultyName       = $semiFinalRoundBeatmapNoModData['version'];
                $semiFinalRoundBeatmapNoModFeatureArtist        = $semiFinalRoundBeatmapNoModData['beatmapset']['artist'];
                $semiFinalRoundBeatmapNoModMapper               = $semiFinalRoundBeatmapNoModData['beatmapset']['creator'];
                $semiFinalRoundBeatmapNoModMapperUrl            = "https://osu.ppy.sh/users/{$semiFinalRoundBeatmapNoModData['beatmapset']['user_id']}";
                $semiFinalRoundBeatmapNoModDifficulty           = $semiFinalRoundBeatmapNoModData['difficulty_rating'];
                $semiFinalRoundBeatmapNoModLength               = $semiFinalRoundBeatmapNoModData['total_length'];
                $semiFinalRoundBeatmapNoModOverallSpeed         = $semiFinalRoundBeatmapNoModData['beatmapset']['bpm'];
                $semiFinalRoundBeatmapNoModOverallDifficulty    = $semiFinalRoundBeatmapNoModData['accuracy'];
                $semiFinalRoundBeatmapNoModOverallHealth        = $semiFinalRoundBeatmapNoModData['drain'];
                $semiFinalRoundBeatmapNoModPassCount            = $semiFinalRoundBeatmapNoModData['passcount'];

                $tournamentRoundBeatmapNoModData[] = [
                    'beatmap_id'                => $semiFinalRoundBeatmapNoModId,
                    'beatmap_round_id'          => $semiFinalRoundId,
                    'beatmap_tournament_id'     => $semiFinalTournamentId,
                    'beatmap_type'              => $semiFinalRoundBeatmapNoModType,
                    'beatmap_image'             => $semiFinalRoundBeatmapNoModImage,
                    'beatmap_url'               => $semiFinalRoundBeatmapNoModUrl,
                    'beatmap_name'              => $semiFinalRoundBeatmapNoModName,
                    'beatmap_difficulty_name'   => $semiFinalRoundBeatmapNoModDifficultyName,
                    'beatmap_fa'                => $semiFinalRoundBeatmapNoModFeatureArtist,
                    'beatmap_mapper'            => $semiFinalRoundBeatmapNoModMapper,
                    'beatmap_mapper_url'        => $semiFinalRoundBeatmapNoModMapperUrl,
                    'beatmap_difficulty'        => $semiFinalRoundBeatmapNoModDifficulty,
                    'beatmap_length'            => $semiFinalRoundBeatmapNoModLength,
                    'beatmap_bpm'               => $semiFinalRoundBeatmapNoModOverallSpeed,
                    'beatmap_od'                => $semiFinalRoundBeatmapNoModOverallDifficulty,
                    'beatmap_hp'                => $semiFinalRoundBeatmapNoModOverallHealth,
                    'beatmap_pass_count'        => $semiFinalRoundBeatmapNoModPassCount
                ];
            }

            foreach ($semiFinalRoundJsonHiddenData as $semiFinalRoundHiddenType => $semiFinalRoundJsonHiddenId) {
                $semiFinalRoundBeatmapHiddenData = getTournamentRoundBeatmapData(
                    beatmap_id: $semiFinalRoundJsonHiddenId,
                    access_token: $_COOKIE['vot_access_token']
                );

                $semiFinalRoundBeatmapHiddenId                  = $semiFinalRoundBeatmapHiddenData['id'];
                $semiFinalRoundId                               = $semiFinalRoundAbbreviationName;
                $semiFinalTournamentId                          = strtoupper(string: $tournament_name);
                $semiFinalRoundBeatmapHiddenType                = $semiFinalRoundHiddenType;
                $semiFinalRoundBeatmapHiddenImage               = $semiFinalRoundBeatmapHiddenData['beatmapset']['covers']['cover'];
                $semiFinalRoundBeatmapHiddenUrl                 = $semiFinalRoundBeatmapHiddenData['url'];
                $semiFinalRoundBeatmapHiddenName                = $semiFinalRoundBeatmapHiddenData['beatmapset']['title'];
                $semiFinalRoundBeatmapHiddenDifficultyName      = $semiFinalRoundBeatmapHiddenData['version'];
                $semiFinalRoundBeatmapHiddenFeatureArtist       = $semiFinalRoundBeatmapHiddenData['beatmapset']['artist'];
                $semiFinalRoundBeatmapHiddenMapper              = $semiFinalRoundBeatmapHiddenData['beatmapset']['creator'];
                $semiFinalRoundBeatmapHiddenMapperUrl           = "https://osu.ppy.sh/users/{$semiFinalRoundBeatmapHiddenData['beatmapset']['user_id']}";
                $semiFinalRoundBeatmapHiddenDifficulty          = $semiFinalRoundBeatmapHiddenData['difficulty_rating'];
                $semiFinalRoundBeatmapHiddenLength              = $semiFinalRoundBeatmapHiddenData['total_length'];
                $semiFinalRoundBeatmapHiddenOverallSpeed        = $semiFinalRoundBeatmapHiddenData['beatmapset']['bpm'];
                $semiFinalRoundBeatmapHiddenOverallDifficulty   = $semiFinalRoundBeatmapHiddenData['accuracy'];
                $semiFinalRoundBeatmapHiddenOverallHealth       = $semiFinalRoundBeatmapHiddenData['drain'];
                $semiFinalRoundBeatmapHiddenPassCount           = $semiFinalRoundBeatmapHiddenData['passcount'];

                $tournamentRoundBeatmapHiddenData[] = [
                    'beatmap_id'                => $semiFinalRoundBeatmapHiddenId,
                    'beatmap_round_id'          => $semiFinalRoundId,
                    'beatmap_tournament_id'     => $semiFinalTournamentId,
                    'beatmap_type'              => $semiFinalRoundBeatmapHiddenType,
                    'beatmap_image'             => $semiFinalRoundBeatmapHiddenImage,
                    'beatmap_url'               => $semiFinalRoundBeatmapHiddenUrl,
                    'beatmap_name'              => $semiFinalRoundBeatmapHiddenName,
                    'beatmap_difficulty_name'   => $semiFinalRoundBeatmapHiddenDifficultyName,
                    'beatmap_fa'                => $semiFinalRoundBeatmapHiddenFeatureArtist,
                    'beatmap_mapper'            => $semiFinalRoundBeatmapHiddenMapper,
                    'beatmap_mapper_url'        => $semiFinalRoundBeatmapHiddenMapperUrl,
                    'beatmap_difficulty'        => $semiFinalRoundBeatmapHiddenDifficulty,
                    'beatmap_length'            => $semiFinalRoundBeatmapHiddenLength,
                    'beatmap_bpm'               => $semiFinalRoundBeatmapHiddenOverallSpeed,
                    'beatmap_od'                => $semiFinalRoundBeatmapHiddenOverallDifficulty,
                    'beatmap_hp'                => $semiFinalRoundBeatmapHiddenOverallHealth,
                    'beatmap_pass_count'        => $semiFinalRoundBeatmapHiddenPassCount
                ];
            }

            foreach ($semiFinalRoundJsonHardRockData as $semiFinalRoundHardRockType => $semiFinalRoundJsonHardRockId) {
                $semiFinalRoundBeatmapHardRockData = getTournamentRoundBeatmapData(
                    beatmap_id: $semiFinalRoundJsonHardRockId,
                    access_token: $_COOKIE['vot_access_token']
                );

                $semiFinalRoundBeatmapHardRockId                    = $semiFinalRoundBeatmapHardRockData['id'];
                $semiFinalRoundId                                   = $semiFinalRoundAbbreviationName;
                $semiFinalTournamentId                              = strtoupper(string: $tournament_name);
                $semiFinalRoundBeatmapHardRockType                  = $semiFinalRoundHardRockType;
                $semiFinalRoundBeatmapHardRockImage                 = $semiFinalRoundBeatmapHardRockData['beatmapset']['covers']['cover'];
                $semiFinalRoundBeatmapHardRockUrl                   = $semiFinalRoundBeatmapHardRockData['url'];
                $semiFinalRoundBeatmapHardRockName                  = $semiFinalRoundBeatmapHardRockData['beatmapset']['title'];
                $semiFinalRoundBeatmapHardRockDifficultyName        = $semiFinalRoundBeatmapHardRockData['version'];
                $semiFinalRoundBeatmapHardRockFeatureArtist         = $semiFinalRoundBeatmapHardRockData['beatmapset']['artist'];
                $semiFinalRoundBeatmapHardRockMapper                = $semiFinalRoundBeatmapHardRockData['beatmapset']['creator'];
                $semiFinalRoundBeatmapHardRockMapperUrl             = "https://osu.ppy.sh/users/{$semiFinalRoundBeatmapHardRockData['beatmapset']['user_id']}";
                $semiFinalRoundBeatmapHardRockDifficulty            = $semiFinalRoundBeatmapHardRockData['difficulty_rating'];
                $semiFinalRoundBeatmapHardRockLength                = $semiFinalRoundBeatmapHardRockData['total_length'];
                $semiFinalRoundBeatmapHardRockOverallSpeed          = $semiFinalRoundBeatmapHardRockData['beatmapset']['bpm'];
                $semiFinalRoundBeatmapHardRockOverallDifficulty     = $semiFinalRoundBeatmapHardRockData['accuracy'];
                $semiFinalRoundBeatmapHardRockOverallHealth         = $semiFinalRoundBeatmapHardRockData['drain'];
                $semiFinalRoundBeatmapHardRockPassCount             = $semiFinalRoundBeatmapHardRockData['passcount'];

                $tournamentRoundBeatmapHardRockData[] = [
                    'beatmap_id'                => $semiFinalRoundBeatmapHardRockId,
                    'beatmap_round_id'          => $semiFinalRoundId,
                    'beatmap_tournament_id'     => $semiFinalTournamentId,
                    'beatmap_type'              => $semiFinalRoundBeatmapHardRockType,
                    'beatmap_image'             => $semiFinalRoundBeatmapHardRockImage,
                    'beatmap_url'               => $semiFinalRoundBeatmapHardRockUrl,
                    'beatmap_name'              => $semiFinalRoundBeatmapHardRockName,
                    'beatmap_difficulty_name'   => $semiFinalRoundBeatmapHardRockDifficultyName,
                    'beatmap_fa'                => $semiFinalRoundBeatmapHardRockFeatureArtist,
                    'beatmap_mapper'            => $semiFinalRoundBeatmapHardRockMapper,
                    'beatmap_mapper_url'        => $semiFinalRoundBeatmapHardRockMapperUrl,
                    'beatmap_difficulty'        => $semiFinalRoundBeatmapHardRockDifficulty,
                    'beatmap_length'            => $semiFinalRoundBeatmapHardRockLength,
                    'beatmap_bpm'               => $semiFinalRoundBeatmapHardRockOverallSpeed,
                    'beatmap_od'                => $semiFinalRoundBeatmapHardRockOverallDifficulty,
                    'beatmap_hp'                => $semiFinalRoundBeatmapHardRockOverallHealth,
                    'beatmap_pass_count'        => $semiFinalRoundBeatmapHardRockPassCount
                ];
            }

            foreach ($semiFinalRoundJsonDoubleTimeData as $semiFinalRoundDoubleTimeType => $semiFinalRoundJsonDoubleTimeId) {
                $semiFinalRoundBeatmapDoubleTimeData = getTournamentRoundBeatmapData(
                    beatmap_id: $semiFinalRoundJsonDoubleTimeId,
                    access_token: $_COOKIE['vot_access_token']
                );

                $semiFinalRoundBeatmapDoubleTimeId                  = $semiFinalRoundBeatmapDoubleTimeData['id'];
                $semiFinalRoundId                                   = $semiFinalRoundAbbreviationName;
                $semiFinalTournamentId                              = strtoupper(string: $tournament_name);
                $semiFinalRoundBeatmapDoubleTimeType                = $semiFinalRoundDoubleTimeType;
                $semiFinalRoundBeatmapDoubleTimeImage               = $semiFinalRoundBeatmapDoubleTimeData['beatmapset']['covers']['cover'];
                $semiFinalRoundBeatmapDoubleTimeUrl                 = $semiFinalRoundBeatmapDoubleTimeData['url'];
                $semiFinalRoundBeatmapDoubleTimeName                = $semiFinalRoundBeatmapDoubleTimeData['beatmapset']['title'];
                $semiFinalRoundBeatmapDoubleTimeDifficultyName      = $semiFinalRoundBeatmapDoubleTimeData['version'];
                $semiFinalRoundBeatmapDoubleTimeFeatureArtist       = $semiFinalRoundBeatmapDoubleTimeData['beatmapset']['artist'];
                $semiFinalRoundBeatmapDoubleTimeMapper              = $semiFinalRoundBeatmapDoubleTimeData['beatmapset']['creator'];
                $semiFinalRoundBeatmapDoubleTimeMapperUrl           = "https://osu.ppy.sh/users/{$semiFinalRoundBeatmapDoubleTimeData['beatmapset']['user_id']}";
                $semiFinalRoundBeatmapDoubleTimeDifficulty          = $semiFinalRoundBeatmapDoubleTimeData['difficulty_rating'];
                $semiFinalRoundBeatmapDoubleTimeLength              = $semiFinalRoundBeatmapDoubleTimeData['total_length'];
                $semiFinalRoundBeatmapDoubleTimeOverallSpeed        = $semiFinalRoundBeatmapDoubleTimeData['beatmapset']['bpm'];
                $semiFinalRoundBeatmapDoubleTimeOverallDifficulty   = $semiFinalRoundBeatmapDoubleTimeData['accuracy'];
                $semiFinalRoundBeatmapDoubleTimeOverallHealth       = $semiFinalRoundBeatmapDoubleTimeData['drain'];
                $semiFinalRoundBeatmapDoubleTimePassCount           = $semiFinalRoundBeatmapDoubleTimeData['passcount'];

                $tournamentRoundBeatmapDoubleTimeData[] = [
                    'beatmap_id'                => $semiFinalRoundBeatmapDoubleTimeId,
                    'beatmap_round_id'          => $semiFinalRoundId,
                    'beatmap_tournament_id'     => $semiFinalTournamentId,
                    'beatmap_type'              => $semiFinalRoundBeatmapDoubleTimeType,
                    'beatmap_image'             => $semiFinalRoundBeatmapDoubleTimeImage,
                    'beatmap_url'               => $semiFinalRoundBeatmapDoubleTimeUrl,
                    'beatmap_name'              => $semiFinalRoundBeatmapDoubleTimeName,
                    'beatmap_difficulty_name'   => $semiFinalRoundBeatmapDoubleTimeDifficultyName,
                    'beatmap_fa'                => $semiFinalRoundBeatmapDoubleTimeFeatureArtist,
                    'beatmap_mapper'            => $semiFinalRoundBeatmapDoubleTimeMapper,
                    'beatmap_mapper_url'        => $semiFinalRoundBeatmapDoubleTimeMapperUrl,
                    'beatmap_difficulty'        => $semiFinalRoundBeatmapDoubleTimeDifficulty,
                    'beatmap_length'            => $semiFinalRoundBeatmapDoubleTimeLength,
                    'beatmap_bpm'               => $semiFinalRoundBeatmapDoubleTimeOverallSpeed,
                    'beatmap_od'                => $semiFinalRoundBeatmapDoubleTimeOverallDifficulty,
                    'beatmap_hp'                => $semiFinalRoundBeatmapDoubleTimeOverallHealth,
                    'beatmap_pass_count'        => $semiFinalRoundBeatmapDoubleTimePassCount
                ];
            }

            foreach ($semiFinalRoundJsonFreeModData as $semiFinalRoundFreeModType => $semiFinalRoundJsonFreeModId) {
                $semiFinalRoundBeatmapFreeModData = getTournamentRoundBeatmapData(
                    beatmap_id: $semiFinalRoundJsonFreeModId,
                    access_token: $_COOKIE['vot_access_token']
                );

                $semiFinalRoundBeatmapFreeModId                 = $semiFinalRoundBeatmapFreeModData['id'];
                $semiFinalRoundId                               = $semiFinalRoundAbbreviationName;
                $semiFinalTournamentId                          = strtoupper(string: $tournament_name);
                $semiFinalRoundBeatmapFreeModType               = $semiFinalRoundFreeModType;
                $semiFinalRoundBeatmapFreeModImage              = $semiFinalRoundBeatmapFreeModData['beatmapset']['covers']['cover'];
                $semiFinalRoundBeatmapFreeModUrl                = $semiFinalRoundBeatmapFreeModData['url'];
                $semiFinalRoundBeatmapFreeModName               = $semiFinalRoundBeatmapFreeModData['beatmapset']['title'];
                $semiFinalRoundBeatmapFreeModDifficultyName     = $semiFinalRoundBeatmapFreeModData['version'];
                $semiFinalRoundBeatmapFreeModFeatureArtist      = $semiFinalRoundBeatmapFreeModData['beatmapset']['artist'];
                $semiFinalRoundBeatmapFreeModMapper             = $semiFinalRoundBeatmapFreeModData['beatmapset']['creator'];
                $semiFinalRoundBeatmapFreeModMapperUrl          = "https://osu.ppy.sh/users/{$semiFinalRoundBeatmapFreeModData['beatmapset']['user_id']}";
                $semiFinalRoundBeatmapFreeModDifficulty         = $semiFinalRoundBeatmapFreeModData['difficulty_rating'];
                $semiFinalRoundBeatmapFreeModLength             = $semiFinalRoundBeatmapFreeModData['total_length'];
                $semiFinalRoundBeatmapFreeModOverallSpeed       = $semiFinalRoundBeatmapFreeModData['beatmapset']['bpm'];
                $semiFinalRoundBeatmapFreeModOverallDifficulty  = $semiFinalRoundBeatmapFreeModData['accuracy'];
                $semiFinalRoundBeatmapFreeModOverallHealth      = $semiFinalRoundBeatmapFreeModData['drain'];
                $semiFinalRoundBeatmapFreeModPassCount          = $semiFinalRoundBeatmapFreeModData['passcount'];

                $tournamentRoundBeatmapFreeModData[] = [
                    'beatmap_id'                => $semiFinalRoundBeatmapFreeModId,
                    'beatmap_round_id'          => $semiFinalRoundId,
                    'beatmap_tournament_id'     => $semiFinalTournamentId,
                    'beatmap_type'              => $semiFinalRoundBeatmapFreeModType,
                    'beatmap_image'             => $semiFinalRoundBeatmapFreeModImage,
                    'beatmap_url'               => $semiFinalRoundBeatmapFreeModUrl,
                    'beatmap_name'              => $semiFinalRoundBeatmapFreeModName,
                    'beatmap_difficulty_name'   => $semiFinalRoundBeatmapFreeModDifficultyName,
                    'beatmap_fa'                => $semiFinalRoundBeatmapFreeModFeatureArtist,
                    'beatmap_mapper'            => $semiFinalRoundBeatmapFreeModMapper,
                    'beatmap_mapper_url'        => $semiFinalRoundBeatmapFreeModMapperUrl,
                    'beatmap_difficulty'        => $semiFinalRoundBeatmapFreeModDifficulty,
                    'beatmap_length'            => $semiFinalRoundBeatmapFreeModLength,
                    'beatmap_bpm'               => $semiFinalRoundBeatmapFreeModOverallSpeed,
                    'beatmap_od'                => $semiFinalRoundBeatmapFreeModOverallDifficulty,
                    'beatmap_hp'                => $semiFinalRoundBeatmapFreeModOverallHealth,
                    'beatmap_pass_count'        => $semiFinalRoundBeatmapFreeModPassCount
                ];
            }

            foreach ($semiFinalRoundJsonEasyData as $semiFinalRoundEasyType => $semiFinalRoundJsonEasyId) {
                $semiFinalRoundBeatmapEasyData = getTournamentRoundBeatmapData(
                    beatmap_id: $semiFinalRoundJsonEasyId,
                    access_token: $_COOKIE['vot_access_token']
                );

                $semiFinalRoundBeatmapEasyId                 = $semiFinalRoundBeatmapEasyData['id'];
                $semiFinalRoundId                               = $semiFinalRoundAbbreviationName;
                $semiFinalTournamentId                          = strtoupper(string: $tournament_name);
                $semiFinalRoundBeatmapEasyType               = $semiFinalRoundEasyType;
                $semiFinalRoundBeatmapEasyImage              = $semiFinalRoundBeatmapEasyData['beatmapset']['covers']['cover'];
                $semiFinalRoundBeatmapEasyUrl                = $semiFinalRoundBeatmapEasyData['url'];
                $semiFinalRoundBeatmapEasyName               = $semiFinalRoundBeatmapEasyData['beatmapset']['title'];
                $semiFinalRoundBeatmapEasyDifficultyName     = $semiFinalRoundBeatmapEasyData['version'];
                $semiFinalRoundBeatmapEasyFeatureArtist      = $semiFinalRoundBeatmapEasyData['beatmapset']['artist'];
                $semiFinalRoundBeatmapEasyMapper             = $semiFinalRoundBeatmapEasyData['beatmapset']['creator'];
                $semiFinalRoundBeatmapEasyMapperUrl          = "https://osu.ppy.sh/users/{$semiFinalRoundBeatmapEasyData['beatmapset']['user_id']}";
                $semiFinalRoundBeatmapEasyDifficulty         = $semiFinalRoundBeatmapEasyData['difficulty_rating'];
                $semiFinalRoundBeatmapEasyLength             = $semiFinalRoundBeatmapEasyData['total_length'];
                $semiFinalRoundBeatmapEasyOverallSpeed       = $semiFinalRoundBeatmapEasyData['beatmapset']['bpm'];
                $semiFinalRoundBeatmapEasyOverallDifficulty  = $semiFinalRoundBeatmapEasyData['accuracy'];
                $semiFinalRoundBeatmapEasyOverallHealth      = $semiFinalRoundBeatmapEasyData['drain'];
                $semiFinalRoundBeatmapEasyPassCount          = $semiFinalRoundBeatmapEasyData['passcount'];

                $tournamentRoundBeatmapEasyData[] = [
                    'beatmap_id'                => $semiFinalRoundBeatmapEasyId,
                    'beatmap_round_id'          => $semiFinalRoundId,
                    'beatmap_tournament_id'     => $semiFinalTournamentId,
                    'beatmap_type'              => $semiFinalRoundBeatmapEasyType,
                    'beatmap_image'             => $semiFinalRoundBeatmapEasyImage,
                    'beatmap_url'               => $semiFinalRoundBeatmapEasyUrl,
                    'beatmap_name'              => $semiFinalRoundBeatmapEasyName,
                    'beatmap_difficulty_name'   => $semiFinalRoundBeatmapEasyDifficultyName,
                    'beatmap_fa'                => $semiFinalRoundBeatmapEasyFeatureArtist,
                    'beatmap_mapper'            => $semiFinalRoundBeatmapEasyMapper,
                    'beatmap_mapper_url'        => $semiFinalRoundBeatmapEasyMapperUrl,
                    'beatmap_difficulty'        => $semiFinalRoundBeatmapEasyDifficulty,
                    'beatmap_length'            => $semiFinalRoundBeatmapEasyLength,
                    'beatmap_bpm'               => $semiFinalRoundBeatmapEasyOverallSpeed,
                    'beatmap_od'                => $semiFinalRoundBeatmapEasyOverallDifficulty,
                    'beatmap_hp'                => $semiFinalRoundBeatmapEasyOverallHealth,
                    'beatmap_pass_count'        => $semiFinalRoundBeatmapEasyPassCount
                ];
            }

            foreach ($semiFinalRoundJsonHiddenHardRockData as $semiFinalRoundHiddenHardRockType => $semiFinalRoundJsonHiddenHardRockId) {
                $semiFinalRoundBeatmapHiddenHardRockData = getTournamentRoundBeatmapData(
                    beatmap_id: $semiFinalRoundJsonHiddenHardRockId,
                    access_token: $_COOKIE['vot_access_token']
                );

                $semiFinalRoundBeatmapHiddenHardRockId                 = $semiFinalRoundBeatmapHiddenHardRockData['id'];
                $semiFinalRoundId                               = $semiFinalRoundAbbreviationName;
                $semiFinalTournamentId                          = strtoupper(string: $tournament_name);
                $semiFinalRoundBeatmapHiddenHardRockType               = $semiFinalRoundHiddenHardRockType;
                $semiFinalRoundBeatmapHiddenHardRockImage              = $semiFinalRoundBeatmapHiddenHardRockData['beatmapset']['covers']['cover'];
                $semiFinalRoundBeatmapHiddenHardRockUrl                = $semiFinalRoundBeatmapHiddenHardRockData['url'];
                $semiFinalRoundBeatmapHiddenHardRockName               = $semiFinalRoundBeatmapHiddenHardRockData['beatmapset']['title'];
                $semiFinalRoundBeatmapHiddenHardRockDifficultyName     = $semiFinalRoundBeatmapHiddenHardRockData['version'];
                $semiFinalRoundBeatmapHiddenHardRockFeatureArtist      = $semiFinalRoundBeatmapHiddenHardRockData['beatmapset']['artist'];
                $semiFinalRoundBeatmapHiddenHardRockMapper             = $semiFinalRoundBeatmapHiddenHardRockData['beatmapset']['creator'];
                $semiFinalRoundBeatmapHiddenHardRockMapperUrl          = "https://osu.ppy.sh/users/{$semiFinalRoundBeatmapHiddenHardRockData['beatmapset']['user_id']}";
                $semiFinalRoundBeatmapHiddenHardRockDifficulty         = $semiFinalRoundBeatmapHiddenHardRockData['difficulty_rating'];
                $semiFinalRoundBeatmapHiddenHardRockLength             = $semiFinalRoundBeatmapHiddenHardRockData['total_length'];
                $semiFinalRoundBeatmapHiddenHardRockOverallSpeed       = $semiFinalRoundBeatmapHiddenHardRockData['beatmapset']['bpm'];
                $semiFinalRoundBeatmapHiddenHardRockOverallDifficulty  = $semiFinalRoundBeatmapHiddenHardRockData['accuracy'];
                $semiFinalRoundBeatmapHiddenHardRockOverallHealth      = $semiFinalRoundBeatmapHiddenHardRockData['drain'];
                $semiFinalRoundBeatmapHiddenHardRockPassCount          = $semiFinalRoundBeatmapHiddenHardRockData['passcount'];

                $tournamentRoundBeatmapHiddenHardRockData[] = [
                    'beatmap_id'                => $semiFinalRoundBeatmapHiddenHardRockId,
                    'beatmap_round_id'          => $semiFinalRoundId,
                    'beatmap_tournament_id'     => $semiFinalTournamentId,
                    'beatmap_type'              => $semiFinalRoundBeatmapHiddenHardRockType,
                    'beatmap_image'             => $semiFinalRoundBeatmapHiddenHardRockImage,
                    'beatmap_url'               => $semiFinalRoundBeatmapHiddenHardRockUrl,
                    'beatmap_name'              => $semiFinalRoundBeatmapHiddenHardRockName,
                    'beatmap_difficulty_name'   => $semiFinalRoundBeatmapHiddenHardRockDifficultyName,
                    'beatmap_fa'                => $semiFinalRoundBeatmapHiddenHardRockFeatureArtist,
                    'beatmap_mapper'            => $semiFinalRoundBeatmapHiddenHardRockMapper,
                    'beatmap_mapper_url'        => $semiFinalRoundBeatmapHiddenHardRockMapperUrl,
                    'beatmap_difficulty'        => $semiFinalRoundBeatmapHiddenHardRockDifficulty,
                    'beatmap_length'            => $semiFinalRoundBeatmapHiddenHardRockLength,
                    'beatmap_bpm'               => $semiFinalRoundBeatmapHiddenHardRockOverallSpeed,
                    'beatmap_od'                => $semiFinalRoundBeatmapHiddenHardRockOverallDifficulty,
                    'beatmap_hp'                => $semiFinalRoundBeatmapHiddenHardRockOverallHealth,
                    'beatmap_pass_count'        => $semiFinalRoundBeatmapHiddenHardRockPassCount
                ];
            }

            foreach ($semiFinalRoundJsonTiebreakerData as $semiFinalRoundTiebreakerType => $semiFinalRoundJsonTiebreakerId) {
                $semiFinalRoundBeatmapTiebreakerData = getTournamentRoundBeatmapData(
                    beatmap_id: $semiFinalRoundJsonTiebreakerId,
                    access_token: $_COOKIE['vot_access_token']
                );

                $semiFinalRoundBeatmapTiebreakerId                 = $semiFinalRoundBeatmapTiebreakerData['id'];
                $semiFinalRoundId                               = $semiFinalRoundAbbreviationName;
                $semiFinalTournamentId                          = strtoupper(string: $tournament_name);
                $semiFinalRoundBeatmapTiebreakerType               = $semiFinalRoundTiebreakerType;
                $semiFinalRoundBeatmapTiebreakerImage              = $semiFinalRoundBeatmapTiebreakerData['beatmapset']['covers']['cover'];
                $semiFinalRoundBeatmapTiebreakerUrl                = $semiFinalRoundBeatmapTiebreakerData['url'];
                $semiFinalRoundBeatmapTiebreakerName               = $semiFinalRoundBeatmapTiebreakerData['beatmapset']['title'];
                $semiFinalRoundBeatmapTiebreakerDifficultyName     = $semiFinalRoundBeatmapTiebreakerData['version'];
                $semiFinalRoundBeatmapTiebreakerFeatureArtist      = $semiFinalRoundBeatmapTiebreakerData['beatmapset']['artist'];
                $semiFinalRoundBeatmapTiebreakerMapper             = $semiFinalRoundBeatmapTiebreakerData['beatmapset']['creator'];
                $semiFinalRoundBeatmapTiebreakerMapperUrl          = "https://osu.ppy.sh/users/{$semiFinalRoundBeatmapTiebreakerData['beatmapset']['user_id']}";
                $semiFinalRoundBeatmapTiebreakerDifficulty         = $semiFinalRoundBeatmapTiebreakerData['difficulty_rating'];
                $semiFinalRoundBeatmapTiebreakerLength             = $semiFinalRoundBeatmapTiebreakerData['total_length'];
                $semiFinalRoundBeatmapTiebreakerOverallSpeed       = $semiFinalRoundBeatmapTiebreakerData['beatmapset']['bpm'];
                $semiFinalRoundBeatmapTiebreakerOverallDifficulty  = $semiFinalRoundBeatmapTiebreakerData['accuracy'];
                $semiFinalRoundBeatmapTiebreakerOverallHealth      = $semiFinalRoundBeatmapTiebreakerData['drain'];
                $semiFinalRoundBeatmapTiebreakerPassCount          = $semiFinalRoundBeatmapTiebreakerData['passcount'];

                $tournamentRoundBeatmapTiebreakerData[] = [
                    'beatmap_id'                => $semiFinalRoundBeatmapTiebreakerId,
                    'beatmap_round_id'          => $semiFinalRoundId,
                    'beatmap_tournament_id'     => $semiFinalTournamentId,
                    'beatmap_type'              => $semiFinalRoundBeatmapTiebreakerType,
                    'beatmap_image'             => $semiFinalRoundBeatmapTiebreakerImage,
                    'beatmap_url'               => $semiFinalRoundBeatmapTiebreakerUrl,
                    'beatmap_name'              => $semiFinalRoundBeatmapTiebreakerName,
                    'beatmap_difficulty_name'   => $semiFinalRoundBeatmapTiebreakerDifficultyName,
                    'beatmap_fa'                => $semiFinalRoundBeatmapTiebreakerFeatureArtist,
                    'beatmap_mapper'            => $semiFinalRoundBeatmapTiebreakerMapper,
                    'beatmap_mapper_url'        => $semiFinalRoundBeatmapTiebreakerMapperUrl,
                    'beatmap_difficulty'        => $semiFinalRoundBeatmapTiebreakerDifficulty,
                    'beatmap_length'            => $semiFinalRoundBeatmapTiebreakerLength,
                    'beatmap_bpm'               => $semiFinalRoundBeatmapTiebreakerOverallSpeed,
                    'beatmap_od'                => $semiFinalRoundBeatmapTiebreakerOverallDifficulty,
                    'beatmap_hp'                => $semiFinalRoundBeatmapTiebreakerOverallHealth,
                    'beatmap_pass_count'        => $semiFinalRoundBeatmapTiebreakerPassCount
                ];
            }


            /* FINAL MAPPOOL LOOPING DATA */
            $finalRoundAbbreviationName                 = $tournamentRoundAbbreviationNameData[4];
            $finalRoundJsonMappoolData                  = $tournamentRoundReadableMappoolData[$tournament_name][$finalRoundAbbreviationName];
            $finalRoundJsonNoModData                    = $finalRoundJsonMappoolData['NM'];
            $finalRoundJsonHiddenData                   = $finalRoundJsonMappoolData['HD'];
            $finalRoundJsonHardRockData                 = $finalRoundJsonMappoolData['HR'];
            $finalRoundJsonDoubleTimeData               = $finalRoundJsonMappoolData['DT'];
            $finalRoundJsonFreeModData                  = $finalRoundJsonMappoolData['FM'];
            $finalRoundJsonEasyData                     = $finalRoundJsonMappoolData['EZ'];
            $finalRoundJsonHiddenHardRockData           = $finalRoundJsonMappoolData['HDHR'];
            $finalRoundJsonTiebreakerData               = $finalRoundJsonMappoolData['TB'];

            foreach ($finalRoundJsonNoModData as $finalRoundNoModType => $finalRoundJsonNoModId) {
                $finalRoundBeatmapNoModData = getTournamentRoundBeatmapData(
                    beatmap_id: $finalRoundJsonNoModId,
                    access_token: $_COOKIE['vot_access_token']
                );

                $finalRoundBeatmapNoModId                   = $finalRoundBeatmapNoModData['id'];
                $finalRoundId                               = $finalRoundAbbreviationName;
                $finalTournamentId                          = strtoupper(string: $tournament_name);
                $finalRoundBeatmapNoModType                 = $finalRoundNoModType;
                $finalRoundBeatmapNoModImage                = $finalRoundBeatmapNoModData['beatmapset']['covers']['cover'];
                $finalRoundBeatmapNoModUrl                  = $finalRoundBeatmapNoModData['url'];
                $finalRoundBeatmapNoModName                 = $finalRoundBeatmapNoModData['beatmapset']['title'];
                $finalRoundBeatmapNoModDifficultyName       = $finalRoundBeatmapNoModData['version'];
                $finalRoundBeatmapNoModFeatureArtist        = $finalRoundBeatmapNoModData['beatmapset']['artist'];
                $finalRoundBeatmapNoModMapper               = $finalRoundBeatmapNoModData['beatmapset']['creator'];
                $finalRoundBeatmapNoModMapperUrl            = "https://osu.ppy.sh/users/{$finalRoundBeatmapNoModData['beatmapset']['user_id']}";
                $finalRoundBeatmapNoModDifficulty           = $finalRoundBeatmapNoModData['difficulty_rating'];
                $finalRoundBeatmapNoModLength               = $finalRoundBeatmapNoModData['total_length'];
                $finalRoundBeatmapNoModOverallSpeed         = $finalRoundBeatmapNoModData['beatmapset']['bpm'];
                $finalRoundBeatmapNoModOverallDifficulty    = $finalRoundBeatmapNoModData['accuracy'];
                $finalRoundBeatmapNoModOverallHealth        = $finalRoundBeatmapNoModData['drain'];
                $finalRoundBeatmapNoModPassCount            = $finalRoundBeatmapNoModData['passcount'];

                $tournamentRoundBeatmapNoModData[] = [
                    'beatmap_id'                => $finalRoundBeatmapNoModId,
                    'beatmap_round_id'          => $finalRoundId,
                    'beatmap_tournament_id'     => $finalTournamentId,
                    'beatmap_type'              => $finalRoundBeatmapNoModType,
                    'beatmap_image'             => $finalRoundBeatmapNoModImage,
                    'beatmap_url'               => $finalRoundBeatmapNoModUrl,
                    'beatmap_name'              => $finalRoundBeatmapNoModName,
                    'beatmap_difficulty_name'   => $finalRoundBeatmapNoModDifficultyName,
                    'beatmap_fa'                => $finalRoundBeatmapNoModFeatureArtist,
                    'beatmap_mapper'            => $finalRoundBeatmapNoModMapper,
                    'beatmap_mapper_url'        => $finalRoundBeatmapNoModMapperUrl,
                    'beatmap_difficulty'        => $finalRoundBeatmapNoModDifficulty,
                    'beatmap_length'            => $finalRoundBeatmapNoModLength,
                    'beatmap_bpm'               => $finalRoundBeatmapNoModOverallSpeed,
                    'beatmap_od'                => $finalRoundBeatmapNoModOverallDifficulty,
                    'beatmap_hp'                => $finalRoundBeatmapNoModOverallHealth,
                    'beatmap_pass_count'        => $finalRoundBeatmapNoModPassCount
                ];
            }

            foreach ($finalRoundJsonHiddenData as $finalRoundHiddenType => $finalRoundJsonHiddenId) {
                $finalRoundBeatmapHiddenData = getTournamentRoundBeatmapData(
                    beatmap_id: $finalRoundJsonHiddenId,
                    access_token: $_COOKIE['vot_access_token']
                );

                $finalRoundBeatmapHiddenId                  = $finalRoundBeatmapHiddenData['id'];
                $finalRoundId                               = $finalRoundAbbreviationName;
                $finalTournamentId                          = strtoupper(string: $tournament_name);
                $finalRoundBeatmapHiddenType                = $finalRoundHiddenType;
                $finalRoundBeatmapHiddenImage               = $finalRoundBeatmapHiddenData['beatmapset']['covers']['cover'];
                $finalRoundBeatmapHiddenUrl                 = $finalRoundBeatmapHiddenData['url'];
                $finalRoundBeatmapHiddenName                = $finalRoundBeatmapHiddenData['beatmapset']['title'];
                $finalRoundBeatmapHiddenDifficultyName      = $finalRoundBeatmapHiddenData['version'];
                $finalRoundBeatmapHiddenFeatureArtist       = $finalRoundBeatmapHiddenData['beatmapset']['artist'];
                $finalRoundBeatmapHiddenMapper              = $finalRoundBeatmapHiddenData['beatmapset']['creator'];
                $finalRoundBeatmapHiddenMapperUrl           = "https://osu.ppy.sh/users/{$finalRoundBeatmapHiddenData['beatmapset']['user_id']}";
                $finalRoundBeatmapHiddenDifficulty          = $finalRoundBeatmapHiddenData['difficulty_rating'];
                $finalRoundBeatmapHiddenLength              = $finalRoundBeatmapHiddenData['total_length'];
                $finalRoundBeatmapHiddenOverallSpeed        = $finalRoundBeatmapHiddenData['beatmapset']['bpm'];
                $finalRoundBeatmapHiddenOverallDifficulty   = $finalRoundBeatmapHiddenData['accuracy'];
                $finalRoundBeatmapHiddenOverallHealth       = $finalRoundBeatmapHiddenData['drain'];
                $finalRoundBeatmapHiddenPassCount           = $finalRoundBeatmapHiddenData['passcount'];

                $tournamentRoundBeatmapHiddenData[] = [
                    'beatmap_id'                => $finalRoundBeatmapHiddenId,
                    'beatmap_round_id'          => $finalRoundId,
                    'beatmap_tournament_id'     => $finalTournamentId,
                    'beatmap_type'              => $finalRoundBeatmapHiddenType,
                    'beatmap_image'             => $finalRoundBeatmapHiddenImage,
                    'beatmap_url'               => $finalRoundBeatmapHiddenUrl,
                    'beatmap_name'              => $finalRoundBeatmapHiddenName,
                    'beatmap_difficulty_name'   => $finalRoundBeatmapHiddenDifficultyName,
                    'beatmap_fa'                => $finalRoundBeatmapHiddenFeatureArtist,
                    'beatmap_mapper'            => $finalRoundBeatmapHiddenMapper,
                    'beatmap_mapper_url'        => $finalRoundBeatmapHiddenMapperUrl,
                    'beatmap_difficulty'        => $finalRoundBeatmapHiddenDifficulty,
                    'beatmap_length'            => $finalRoundBeatmapHiddenLength,
                    'beatmap_bpm'               => $finalRoundBeatmapHiddenOverallSpeed,
                    'beatmap_od'                => $finalRoundBeatmapHiddenOverallDifficulty,
                    'beatmap_hp'                => $finalRoundBeatmapHiddenOverallHealth,
                    'beatmap_pass_count'        => $finalRoundBeatmapHiddenPassCount
                ];
            }

            foreach ($finalRoundJsonHardRockData as $finalRoundHardRockType => $finalRoundJsonHardRockId) {
                $finalRoundBeatmapHardRockData = getTournamentRoundBeatmapData(
                    beatmap_id: $finalRoundJsonHardRockId,
                    access_token: $_COOKIE['vot_access_token']
                );

                $finalRoundBeatmapHardRockId                    = $finalRoundBeatmapHardRockData['id'];
                $finalRoundId                                   = $finalRoundAbbreviationName;
                $finalTournamentId                              = strtoupper(string: $tournament_name);
                $finalRoundBeatmapHardRockType                  = $finalRoundHardRockType;
                $finalRoundBeatmapHardRockImage                 = $finalRoundBeatmapHardRockData['beatmapset']['covers']['cover'];
                $finalRoundBeatmapHardRockUrl                   = $finalRoundBeatmapHardRockData['url'];
                $finalRoundBeatmapHardRockName                  = $finalRoundBeatmapHardRockData['beatmapset']['title'];
                $finalRoundBeatmapHardRockDifficultyName        = $finalRoundBeatmapHardRockData['version'];
                $finalRoundBeatmapHardRockFeatureArtist         = $finalRoundBeatmapHardRockData['beatmapset']['artist'];
                $finalRoundBeatmapHardRockMapper                = $finalRoundBeatmapHardRockData['beatmapset']['creator'];
                $finalRoundBeatmapHardRockMapperUrl             = "https://osu.ppy.sh/users/{$finalRoundBeatmapHardRockData['beatmapset']['user_id']}";
                $finalRoundBeatmapHardRockDifficulty            = $finalRoundBeatmapHardRockData['difficulty_rating'];
                $finalRoundBeatmapHardRockLength                = $finalRoundBeatmapHardRockData['total_length'];
                $finalRoundBeatmapHardRockOverallSpeed          = $finalRoundBeatmapHardRockData['beatmapset']['bpm'];
                $finalRoundBeatmapHardRockOverallDifficulty     = $finalRoundBeatmapHardRockData['accuracy'];
                $finalRoundBeatmapHardRockOverallHealth         = $finalRoundBeatmapHardRockData['drain'];
                $finalRoundBeatmapHardRockPassCount             = $finalRoundBeatmapHardRockData['passcount'];

                $tournamentRoundBeatmapHardRockData[] = [
                    'beatmap_id'                => $finalRoundBeatmapHardRockId,
                    'beatmap_round_id'          => $finalRoundId,
                    'beatmap_tournament_id'     => $finalTournamentId,
                    'beatmap_type'              => $finalRoundBeatmapHardRockType,
                    'beatmap_image'             => $finalRoundBeatmapHardRockImage,
                    'beatmap_url'               => $finalRoundBeatmapHardRockUrl,
                    'beatmap_name'              => $finalRoundBeatmapHardRockName,
                    'beatmap_difficulty_name'   => $finalRoundBeatmapHardRockDifficultyName,
                    'beatmap_fa'                => $finalRoundBeatmapHardRockFeatureArtist,
                    'beatmap_mapper'            => $finalRoundBeatmapHardRockMapper,
                    'beatmap_mapper_url'        => $finalRoundBeatmapHardRockMapperUrl,
                    'beatmap_difficulty'        => $finalRoundBeatmapHardRockDifficulty,
                    'beatmap_length'            => $finalRoundBeatmapHardRockLength,
                    'beatmap_bpm'               => $finalRoundBeatmapHardRockOverallSpeed,
                    'beatmap_od'                => $finalRoundBeatmapHardRockOverallDifficulty,
                    'beatmap_hp'                => $finalRoundBeatmapHardRockOverallHealth,
                    'beatmap_pass_count'        => $finalRoundBeatmapHardRockPassCount
                ];
            }

            foreach ($finalRoundJsonDoubleTimeData as $finalRoundDoubleTimeType => $finalRoundJsonDoubleTimeId) {
                $finalRoundBeatmapDoubleTimeData = getTournamentRoundBeatmapData(
                    beatmap_id: $finalRoundJsonDoubleTimeId,
                    access_token: $_COOKIE['vot_access_token']
                );

                $finalRoundBeatmapDoubleTimeId                  = $finalRoundBeatmapDoubleTimeData['id'];
                $finalRoundId                                   = $finalRoundAbbreviationName;
                $finalTournamentId                              = strtoupper(string: $tournament_name);
                $finalRoundBeatmapDoubleTimeType                = $finalRoundDoubleTimeType;
                $finalRoundBeatmapDoubleTimeImage               = $finalRoundBeatmapDoubleTimeData['beatmapset']['covers']['cover'];
                $finalRoundBeatmapDoubleTimeUrl                 = $finalRoundBeatmapDoubleTimeData['url'];
                $finalRoundBeatmapDoubleTimeName                = $finalRoundBeatmapDoubleTimeData['beatmapset']['title'];
                $finalRoundBeatmapDoubleTimeDifficultyName      = $finalRoundBeatmapDoubleTimeData['version'];
                $finalRoundBeatmapDoubleTimeFeatureArtist       = $finalRoundBeatmapDoubleTimeData['beatmapset']['artist'];
                $finalRoundBeatmapDoubleTimeMapper              = $finalRoundBeatmapDoubleTimeData['beatmapset']['creator'];
                $finalRoundBeatmapDoubleTimeMapperUrl           = "https://osu.ppy.sh/users/{$finalRoundBeatmapDoubleTimeData['beatmapset']['user_id']}";
                $finalRoundBeatmapDoubleTimeDifficulty          = $finalRoundBeatmapDoubleTimeData['difficulty_rating'];
                $finalRoundBeatmapDoubleTimeLength              = $finalRoundBeatmapDoubleTimeData['total_length'];
                $finalRoundBeatmapDoubleTimeOverallSpeed        = $finalRoundBeatmapDoubleTimeData['beatmapset']['bpm'];
                $finalRoundBeatmapDoubleTimeOverallDifficulty   = $finalRoundBeatmapDoubleTimeData['accuracy'];
                $finalRoundBeatmapDoubleTimeOverallHealth       = $finalRoundBeatmapDoubleTimeData['drain'];
                $finalRoundBeatmapDoubleTimePassCount           = $finalRoundBeatmapDoubleTimeData['passcount'];

                $tournamentRoundBeatmapDoubleTimeData[] = [
                    'beatmap_id'                => $finalRoundBeatmapDoubleTimeId,
                    'beatmap_round_id'          => $finalRoundId,
                    'beatmap_tournament_id'     => $finalTournamentId,
                    'beatmap_type'              => $finalRoundBeatmapDoubleTimeType,
                    'beatmap_image'             => $finalRoundBeatmapDoubleTimeImage,
                    'beatmap_url'               => $finalRoundBeatmapDoubleTimeUrl,
                    'beatmap_name'              => $finalRoundBeatmapDoubleTimeName,
                    'beatmap_difficulty_name'   => $finalRoundBeatmapDoubleTimeDifficultyName,
                    'beatmap_fa'                => $finalRoundBeatmapDoubleTimeFeatureArtist,
                    'beatmap_mapper'            => $finalRoundBeatmapDoubleTimeMapper,
                    'beatmap_mapper_url'        => $finalRoundBeatmapDoubleTimeMapperUrl,
                    'beatmap_difficulty'        => $finalRoundBeatmapDoubleTimeDifficulty,
                    'beatmap_length'            => $finalRoundBeatmapDoubleTimeLength,
                    'beatmap_bpm'               => $finalRoundBeatmapDoubleTimeOverallSpeed,
                    'beatmap_od'                => $finalRoundBeatmapDoubleTimeOverallDifficulty,
                    'beatmap_hp'                => $finalRoundBeatmapDoubleTimeOverallHealth,
                    'beatmap_pass_count'        => $finalRoundBeatmapDoubleTimePassCount
                ];
            }

            foreach ($finalRoundJsonFreeModData as $finalRoundFreeModType => $finalRoundJsonFreeModId) {
                $finalRoundBeatmapFreeModData = getTournamentRoundBeatmapData(
                    beatmap_id: $finalRoundJsonFreeModId,
                    access_token: $_COOKIE['vot_access_token']
                );

                $finalRoundBeatmapFreeModId                 = $finalRoundBeatmapFreeModData['id'];
                $finalRoundId                               = $finalRoundAbbreviationName;
                $finalTournamentId                          = strtoupper(string: $tournament_name);
                $finalRoundBeatmapFreeModType               = $finalRoundFreeModType;
                $finalRoundBeatmapFreeModImage              = $finalRoundBeatmapFreeModData['beatmapset']['covers']['cover'];
                $finalRoundBeatmapFreeModUrl                = $finalRoundBeatmapFreeModData['url'];
                $finalRoundBeatmapFreeModName               = $finalRoundBeatmapFreeModData['beatmapset']['title'];
                $finalRoundBeatmapFreeModDifficultyName     = $finalRoundBeatmapFreeModData['version'];
                $finalRoundBeatmapFreeModFeatureArtist      = $finalRoundBeatmapFreeModData['beatmapset']['artist'];
                $finalRoundBeatmapFreeModMapper             = $finalRoundBeatmapFreeModData['beatmapset']['creator'];
                $finalRoundBeatmapFreeModMapperUrl          = "https://osu.ppy.sh/users/{$finalRoundBeatmapFreeModData['beatmapset']['user_id']}";
                $finalRoundBeatmapFreeModDifficulty         = $finalRoundBeatmapFreeModData['difficulty_rating'];
                $finalRoundBeatmapFreeModLength             = $finalRoundBeatmapFreeModData['total_length'];
                $finalRoundBeatmapFreeModOverallSpeed       = $finalRoundBeatmapFreeModData['beatmapset']['bpm'];
                $finalRoundBeatmapFreeModOverallDifficulty  = $finalRoundBeatmapFreeModData['accuracy'];
                $finalRoundBeatmapFreeModOverallHealth      = $finalRoundBeatmapFreeModData['drain'];
                $finalRoundBeatmapFreeModPassCount          = $finalRoundBeatmapFreeModData['passcount'];

                $tournamentRoundBeatmapFreeModData[] = [
                    'beatmap_id'                => $finalRoundBeatmapFreeModId,
                    'beatmap_round_id'          => $finalRoundId,
                    'beatmap_tournament_id'     => $finalTournamentId,
                    'beatmap_type'              => $finalRoundBeatmapFreeModType,
                    'beatmap_image'             => $finalRoundBeatmapFreeModImage,
                    'beatmap_url'               => $finalRoundBeatmapFreeModUrl,
                    'beatmap_name'              => $finalRoundBeatmapFreeModName,
                    'beatmap_difficulty_name'   => $finalRoundBeatmapFreeModDifficultyName,
                    'beatmap_fa'                => $finalRoundBeatmapFreeModFeatureArtist,
                    'beatmap_mapper'            => $finalRoundBeatmapFreeModMapper,
                    'beatmap_mapper_url'        => $finalRoundBeatmapFreeModMapperUrl,
                    'beatmap_difficulty'        => $finalRoundBeatmapFreeModDifficulty,
                    'beatmap_length'            => $finalRoundBeatmapFreeModLength,
                    'beatmap_bpm'               => $finalRoundBeatmapFreeModOverallSpeed,
                    'beatmap_od'                => $finalRoundBeatmapFreeModOverallDifficulty,
                    'beatmap_hp'                => $finalRoundBeatmapFreeModOverallHealth,
                    'beatmap_pass_count'        => $finalRoundBeatmapFreeModPassCount
                ];
            }

            foreach ($finalRoundJsonEasyData as $finalRoundEasyType => $finalRoundJsonEasyId) {
                $finalRoundBeatmapEasyData = getTournamentRoundBeatmapData(
                    beatmap_id: $finalRoundJsonEasyId,
                    access_token: $_COOKIE['vot_access_token']
                );

                $finalRoundBeatmapEasyId                 = $finalRoundBeatmapEasyData['id'];
                $finalRoundId                               = $finalRoundAbbreviationName;
                $finalTournamentId                          = strtoupper(string: $tournament_name);
                $finalRoundBeatmapEasyType               = $finalRoundEasyType;
                $finalRoundBeatmapEasyImage              = $finalRoundBeatmapEasyData['beatmapset']['covers']['cover'];
                $finalRoundBeatmapEasyUrl                = $finalRoundBeatmapEasyData['url'];
                $finalRoundBeatmapEasyName               = $finalRoundBeatmapEasyData['beatmapset']['title'];
                $finalRoundBeatmapEasyDifficultyName     = $finalRoundBeatmapEasyData['version'];
                $finalRoundBeatmapEasyFeatureArtist      = $finalRoundBeatmapEasyData['beatmapset']['artist'];
                $finalRoundBeatmapEasyMapper             = $finalRoundBeatmapEasyData['beatmapset']['creator'];
                $finalRoundBeatmapEasyMapperUrl          = "https://osu.ppy.sh/users/{$finalRoundBeatmapEasyData['beatmapset']['user_id']}";
                $finalRoundBeatmapEasyDifficulty         = $finalRoundBeatmapEasyData['difficulty_rating'];
                $finalRoundBeatmapEasyLength             = $finalRoundBeatmapEasyData['total_length'];
                $finalRoundBeatmapEasyOverallSpeed       = $finalRoundBeatmapEasyData['beatmapset']['bpm'];
                $finalRoundBeatmapEasyOverallDifficulty  = $finalRoundBeatmapEasyData['accuracy'];
                $finalRoundBeatmapEasyOverallHealth      = $finalRoundBeatmapEasyData['drain'];
                $finalRoundBeatmapEasyPassCount          = $finalRoundBeatmapEasyData['passcount'];

                $tournamentRoundBeatmapEasyData[] = [
                    'beatmap_id'                => $finalRoundBeatmapEasyId,
                    'beatmap_round_id'          => $finalRoundId,
                    'beatmap_tournament_id'     => $finalTournamentId,
                    'beatmap_type'              => $finalRoundBeatmapEasyType,
                    'beatmap_image'             => $finalRoundBeatmapEasyImage,
                    'beatmap_url'               => $finalRoundBeatmapEasyUrl,
                    'beatmap_name'              => $finalRoundBeatmapEasyName,
                    'beatmap_difficulty_name'   => $finalRoundBeatmapEasyDifficultyName,
                    'beatmap_fa'                => $finalRoundBeatmapEasyFeatureArtist,
                    'beatmap_mapper'            => $finalRoundBeatmapEasyMapper,
                    'beatmap_mapper_url'        => $finalRoundBeatmapEasyMapperUrl,
                    'beatmap_difficulty'        => $finalRoundBeatmapEasyDifficulty,
                    'beatmap_length'            => $finalRoundBeatmapEasyLength,
                    'beatmap_bpm'               => $finalRoundBeatmapEasyOverallSpeed,
                    'beatmap_od'                => $finalRoundBeatmapEasyOverallDifficulty,
                    'beatmap_hp'                => $finalRoundBeatmapEasyOverallHealth,
                    'beatmap_pass_count'        => $finalRoundBeatmapEasyPassCount
                ];
            }

            foreach ($finalRoundJsonHiddenHardRockData as $finalRoundHiddenHardRockType => $finalRoundJsonHiddenHardRockId) {
                $finalRoundBeatmapHiddenHardRockData = getTournamentRoundBeatmapData(
                    beatmap_id: $finalRoundJsonHiddenHardRockId,
                    access_token: $_COOKIE['vot_access_token']
                );

                $finalRoundBeatmapHiddenHardRockId                 = $finalRoundBeatmapHiddenHardRockData['id'];
                $finalRoundId                               = $finalRoundAbbreviationName;
                $finalTournamentId                          = strtoupper(string: $tournament_name);
                $finalRoundBeatmapHiddenHardRockType               = $finalRoundHiddenHardRockType;
                $finalRoundBeatmapHiddenHardRockImage              = $finalRoundBeatmapHiddenHardRockData['beatmapset']['covers']['cover'];
                $finalRoundBeatmapHiddenHardRockUrl                = $finalRoundBeatmapHiddenHardRockData['url'];
                $finalRoundBeatmapHiddenHardRockName               = $finalRoundBeatmapHiddenHardRockData['beatmapset']['title'];
                $finalRoundBeatmapHiddenHardRockDifficultyName     = $finalRoundBeatmapHiddenHardRockData['version'];
                $finalRoundBeatmapHiddenHardRockFeatureArtist      = $finalRoundBeatmapHiddenHardRockData['beatmapset']['artist'];
                $finalRoundBeatmapHiddenHardRockMapper             = $finalRoundBeatmapHiddenHardRockData['beatmapset']['creator'];
                $finalRoundBeatmapHiddenHardRockMapperUrl          = "https://osu.ppy.sh/users/{$finalRoundBeatmapHiddenHardRockData['beatmapset']['user_id']}";
                $finalRoundBeatmapHiddenHardRockDifficulty         = $finalRoundBeatmapHiddenHardRockData['difficulty_rating'];
                $finalRoundBeatmapHiddenHardRockLength             = $finalRoundBeatmapHiddenHardRockData['total_length'];
                $finalRoundBeatmapHiddenHardRockOverallSpeed       = $finalRoundBeatmapHiddenHardRockData['beatmapset']['bpm'];
                $finalRoundBeatmapHiddenHardRockOverallDifficulty  = $finalRoundBeatmapHiddenHardRockData['accuracy'];
                $finalRoundBeatmapHiddenHardRockOverallHealth      = $finalRoundBeatmapHiddenHardRockData['drain'];
                $finalRoundBeatmapHiddenHardRockPassCount          = $finalRoundBeatmapHiddenHardRockData['passcount'];

                $tournamentRoundBeatmapHiddenHardRockData[] = [
                    'beatmap_id'                => $finalRoundBeatmapHiddenHardRockId,
                    'beatmap_round_id'          => $finalRoundId,
                    'beatmap_tournament_id'     => $finalTournamentId,
                    'beatmap_type'              => $finalRoundBeatmapHiddenHardRockType,
                    'beatmap_image'             => $finalRoundBeatmapHiddenHardRockImage,
                    'beatmap_url'               => $finalRoundBeatmapHiddenHardRockUrl,
                    'beatmap_name'              => $finalRoundBeatmapHiddenHardRockName,
                    'beatmap_difficulty_name'   => $finalRoundBeatmapHiddenHardRockDifficultyName,
                    'beatmap_fa'                => $finalRoundBeatmapHiddenHardRockFeatureArtist,
                    'beatmap_mapper'            => $finalRoundBeatmapHiddenHardRockMapper,
                    'beatmap_mapper_url'        => $finalRoundBeatmapHiddenHardRockMapperUrl,
                    'beatmap_difficulty'        => $finalRoundBeatmapHiddenHardRockDifficulty,
                    'beatmap_length'            => $finalRoundBeatmapHiddenHardRockLength,
                    'beatmap_bpm'               => $finalRoundBeatmapHiddenHardRockOverallSpeed,
                    'beatmap_od'                => $finalRoundBeatmapHiddenHardRockOverallDifficulty,
                    'beatmap_hp'                => $finalRoundBeatmapHiddenHardRockOverallHealth,
                    'beatmap_pass_count'        => $finalRoundBeatmapHiddenHardRockPassCount
                ];
            }

            foreach ($finalRoundJsonTiebreakerData as $finalRoundTiebreakerType => $finalRoundJsonTiebreakerId) {
                $finalRoundBeatmapTiebreakerData = getTournamentRoundBeatmapData(
                    beatmap_id: $finalRoundJsonTiebreakerId,
                    access_token: $_COOKIE['vot_access_token']
                );

                $finalRoundBeatmapTiebreakerId                 = $finalRoundBeatmapTiebreakerData['id'];
                $finalRoundId                               = $finalRoundAbbreviationName;
                $finalTournamentId                          = strtoupper(string: $tournament_name);
                $finalRoundBeatmapTiebreakerType               = $finalRoundTiebreakerType;
                $finalRoundBeatmapTiebreakerImage              = $finalRoundBeatmapTiebreakerData['beatmapset']['covers']['cover'];
                $finalRoundBeatmapTiebreakerUrl                = $finalRoundBeatmapTiebreakerData['url'];
                $finalRoundBeatmapTiebreakerName               = $finalRoundBeatmapTiebreakerData['beatmapset']['title'];
                $finalRoundBeatmapTiebreakerDifficultyName     = $finalRoundBeatmapTiebreakerData['version'];
                $finalRoundBeatmapTiebreakerFeatureArtist      = $finalRoundBeatmapTiebreakerData['beatmapset']['artist'];
                $finalRoundBeatmapTiebreakerMapper             = $finalRoundBeatmapTiebreakerData['beatmapset']['creator'];
                $finalRoundBeatmapTiebreakerMapperUrl          = "https://osu.ppy.sh/users/{$finalRoundBeatmapTiebreakerData['beatmapset']['user_id']}";
                $finalRoundBeatmapTiebreakerDifficulty         = $finalRoundBeatmapTiebreakerData['difficulty_rating'];
                $finalRoundBeatmapTiebreakerLength             = $finalRoundBeatmapTiebreakerData['total_length'];
                $finalRoundBeatmapTiebreakerOverallSpeed       = $finalRoundBeatmapTiebreakerData['beatmapset']['bpm'];
                $finalRoundBeatmapTiebreakerOverallDifficulty  = $finalRoundBeatmapTiebreakerData['accuracy'];
                $finalRoundBeatmapTiebreakerOverallHealth      = $finalRoundBeatmapTiebreakerData['drain'];
                $finalRoundBeatmapTiebreakerPassCount          = $finalRoundBeatmapTiebreakerData['passcount'];

                $tournamentRoundBeatmapTiebreakerData[] = [
                    'beatmap_id'                => $finalRoundBeatmapTiebreakerId,
                    'beatmap_round_id'          => $finalRoundId,
                    'beatmap_tournament_id'     => $finalTournamentId,
                    'beatmap_type'              => $finalRoundBeatmapTiebreakerType,
                    'beatmap_image'             => $finalRoundBeatmapTiebreakerImage,
                    'beatmap_url'               => $finalRoundBeatmapTiebreakerUrl,
                    'beatmap_name'              => $finalRoundBeatmapTiebreakerName,
                    'beatmap_difficulty_name'   => $finalRoundBeatmapTiebreakerDifficultyName,
                    'beatmap_fa'                => $finalRoundBeatmapTiebreakerFeatureArtist,
                    'beatmap_mapper'            => $finalRoundBeatmapTiebreakerMapper,
                    'beatmap_mapper_url'        => $finalRoundBeatmapTiebreakerMapperUrl,
                    'beatmap_difficulty'        => $finalRoundBeatmapTiebreakerDifficulty,
                    'beatmap_length'            => $finalRoundBeatmapTiebreakerLength,
                    'beatmap_bpm'               => $finalRoundBeatmapTiebreakerOverallSpeed,
                    'beatmap_od'                => $finalRoundBeatmapTiebreakerOverallDifficulty,
                    'beatmap_hp'                => $finalRoundBeatmapTiebreakerOverallHealth,
                    'beatmap_pass_count'        => $finalRoundBeatmapTiebreakerPassCount
                ];
            }


            /* GRAND FINAL MAPPOOL LOOPING DATA */
            $grandFinalRoundAbbreviationName            = $tournamentRoundAbbreviationNameData[5];
            $grandFinalRoundJsonMappoolData             = $tournamentRoundReadableMappoolData[$tournament_name][$grandFinalRoundAbbreviationName];
            $grandFinalRoundJsonNoModData               = $grandFinalRoundJsonMappoolData['NM'];
            $grandFinalRoundJsonHiddenData              = $grandFinalRoundJsonMappoolData['HD'];
            $grandFinalRoundJsonHardRockData            = $grandFinalRoundJsonMappoolData['HR'];
            $grandFinalRoundJsonDoubleTimeData          = $grandFinalRoundJsonMappoolData['DT'];
            $grandFinalRoundJsonFreeModData             = $grandFinalRoundJsonMappoolData['FM'];
            $grandFinalRoundJsonEasyData                = $grandFinalRoundJsonMappoolData['EZ'];
            $grandFinalRoundJsonHiddenHardRockData      = $grandFinalRoundJsonMappoolData['HDHR'];
            $grandFinalRoundJsonTiebreakerData          = $grandFinalRoundJsonMappoolData['TB'];

            foreach ($grandFinalRoundJsonNoModData as $grandFinalRoundNoModType => $grandFinalRoundJsonNoModId) {
                $grandFinalRoundBeatmapNoModData = getTournamentRoundBeatmapData(
                    beatmap_id: $grandFinalRoundJsonNoModId,
                    access_token: $_COOKIE['vot_access_token']
                );

                $grandFinalRoundBeatmapNoModId                   = $grandFinalRoundBeatmapNoModData['id'];
                $grandFinalRoundId                               = $grandFinalRoundAbbreviationName;
                $grandFinalTournamentId                          = strtoupper(string: $tournament_name);
                $grandFinalRoundBeatmapNoModType                 = $grandFinalRoundNoModType;
                $grandFinalRoundBeatmapNoModImage                = $grandFinalRoundBeatmapNoModData['beatmapset']['covers']['cover'];
                $grandFinalRoundBeatmapNoModUrl                  = $grandFinalRoundBeatmapNoModData['url'];
                $grandFinalRoundBeatmapNoModName                 = $grandFinalRoundBeatmapNoModData['beatmapset']['title'];
                $grandFinalRoundBeatmapNoModDifficultyName       = $grandFinalRoundBeatmapNoModData['version'];
                $grandFinalRoundBeatmapNoModFeatureArtist        = $grandFinalRoundBeatmapNoModData['beatmapset']['artist'];
                $grandFinalRoundBeatmapNoModMapper               = $grandFinalRoundBeatmapNoModData['beatmapset']['creator'];
                $grandFinalRoundBeatmapNoModMapperUrl            = "https://osu.ppy.sh/users/{$grandFinalRoundBeatmapNoModData['beatmapset']['user_id']}";
                $grandFinalRoundBeatmapNoModDifficulty           = $grandFinalRoundBeatmapNoModData['difficulty_rating'];
                $grandFinalRoundBeatmapNoModLength               = $grandFinalRoundBeatmapNoModData['total_length'];
                $grandFinalRoundBeatmapNoModOverallSpeed         = $grandFinalRoundBeatmapNoModData['beatmapset']['bpm'];
                $grandFinalRoundBeatmapNoModOverallDifficulty    = $grandFinalRoundBeatmapNoModData['accuracy'];
                $grandFinalRoundBeatmapNoModOverallHealth        = $grandFinalRoundBeatmapNoModData['drain'];
                $grandFinalRoundBeatmapNoModPassCount            = $grandFinalRoundBeatmapNoModData['passcount'];

                $tournamentRoundBeatmapNoModData[] = [
                    'beatmap_id'                => $grandFinalRoundBeatmapNoModId,
                    'beatmap_round_id'          => $grandFinalRoundId,
                    'beatmap_tournament_id'     => $grandFinalTournamentId,
                    'beatmap_type'              => $grandFinalRoundBeatmapNoModType,
                    'beatmap_image'             => $grandFinalRoundBeatmapNoModImage,
                    'beatmap_url'               => $grandFinalRoundBeatmapNoModUrl,
                    'beatmap_name'              => $grandFinalRoundBeatmapNoModName,
                    'beatmap_difficulty_name'   => $grandFinalRoundBeatmapNoModDifficultyName,
                    'beatmap_fa'                => $grandFinalRoundBeatmapNoModFeatureArtist,
                    'beatmap_mapper'            => $grandFinalRoundBeatmapNoModMapper,
                    'beatmap_mapper_url'        => $grandFinalRoundBeatmapNoModMapperUrl,
                    'beatmap_difficulty'        => $grandFinalRoundBeatmapNoModDifficulty,
                    'beatmap_length'            => $grandFinalRoundBeatmapNoModLength,
                    'beatmap_bpm'               => $grandFinalRoundBeatmapNoModOverallSpeed,
                    'beatmap_od'                => $grandFinalRoundBeatmapNoModOverallDifficulty,
                    'beatmap_hp'                => $grandFinalRoundBeatmapNoModOverallHealth,
                    'beatmap_pass_count'        => $grandFinalRoundBeatmapNoModPassCount
                ];
            }

            foreach ($grandFinalRoundJsonHiddenData as $grandFinalRoundHiddenType => $grandFinalRoundJsonHiddenId) {
                $grandFinalRoundBeatmapHiddenData = getTournamentRoundBeatmapData(
                    beatmap_id: $grandFinalRoundJsonHiddenId,
                    access_token: $_COOKIE['vot_access_token']
                );

                $grandFinalRoundBeatmapHiddenId                  = $grandFinalRoundBeatmapHiddenData['id'];
                $grandFinalRoundId                               = $grandFinalRoundAbbreviationName;
                $grandFinalTournamentId                          = strtoupper(string: $tournament_name);
                $grandFinalRoundBeatmapHiddenType                = $grandFinalRoundHiddenType;
                $grandFinalRoundBeatmapHiddenImage               = $grandFinalRoundBeatmapHiddenData['beatmapset']['covers']['cover'];
                $grandFinalRoundBeatmapHiddenUrl                 = $grandFinalRoundBeatmapHiddenData['url'];
                $grandFinalRoundBeatmapHiddenName                = $grandFinalRoundBeatmapHiddenData['beatmapset']['title'];
                $grandFinalRoundBeatmapHiddenDifficultyName      = $grandFinalRoundBeatmapHiddenData['version'];
                $grandFinalRoundBeatmapHiddenFeatureArtist       = $grandFinalRoundBeatmapHiddenData['beatmapset']['artist'];
                $grandFinalRoundBeatmapHiddenMapper              = $grandFinalRoundBeatmapHiddenData['beatmapset']['creator'];
                $grandFinalRoundBeatmapHiddenMapperUrl           = "https://osu.ppy.sh/users/{$grandFinalRoundBeatmapHiddenData['beatmapset']['user_id']}";
                $grandFinalRoundBeatmapHiddenDifficulty          = $grandFinalRoundBeatmapHiddenData['difficulty_rating'];
                $grandFinalRoundBeatmapHiddenLength              = $grandFinalRoundBeatmapHiddenData['total_length'];
                $grandFinalRoundBeatmapHiddenOverallSpeed        = $grandFinalRoundBeatmapHiddenData['beatmapset']['bpm'];
                $grandFinalRoundBeatmapHiddenOverallDifficulty   = $grandFinalRoundBeatmapHiddenData['accuracy'];
                $grandFinalRoundBeatmapHiddenOverallHealth       = $grandFinalRoundBeatmapHiddenData['drain'];
                $grandFinalRoundBeatmapHiddenPassCount           = $grandFinalRoundBeatmapHiddenData['passcount'];

                $tournamentRoundBeatmapHiddenData[] = [
                    'beatmap_id'                => $grandFinalRoundBeatmapHiddenId,
                    'beatmap_round_id'          => $grandFinalRoundId,
                    'beatmap_tournament_id'     => $grandFinalTournamentId,
                    'beatmap_type'              => $grandFinalRoundBeatmapHiddenType,
                    'beatmap_image'             => $grandFinalRoundBeatmapHiddenImage,
                    'beatmap_url'               => $grandFinalRoundBeatmapHiddenUrl,
                    'beatmap_name'              => $grandFinalRoundBeatmapHiddenName,
                    'beatmap_difficulty_name'   => $grandFinalRoundBeatmapHiddenDifficultyName,
                    'beatmap_fa'                => $grandFinalRoundBeatmapHiddenFeatureArtist,
                    'beatmap_mapper'            => $grandFinalRoundBeatmapHiddenMapper,
                    'beatmap_mapper_url'        => $grandFinalRoundBeatmapHiddenMapperUrl,
                    'beatmap_difficulty'        => $grandFinalRoundBeatmapHiddenDifficulty,
                    'beatmap_length'            => $grandFinalRoundBeatmapHiddenLength,
                    'beatmap_bpm'               => $grandFinalRoundBeatmapHiddenOverallSpeed,
                    'beatmap_od'                => $grandFinalRoundBeatmapHiddenOverallDifficulty,
                    'beatmap_hp'                => $grandFinalRoundBeatmapHiddenOverallHealth,
                    'beatmap_pass_count'        => $grandFinalRoundBeatmapHiddenPassCount
                ];
            }

            foreach ($grandFinalRoundJsonHardRockData as $grandFinalRoundHardRockType => $grandFinalRoundJsonHardRockId) {
                $grandFinalRoundBeatmapHardRockData = getTournamentRoundBeatmapData(
                    beatmap_id: $grandFinalRoundJsonHardRockId,
                    access_token: $_COOKIE['vot_access_token']
                );

                $grandFinalRoundBeatmapHardRockId                    = $grandFinalRoundBeatmapHardRockData['id'];
                $grandFinalRoundId                                   = $grandFinalRoundAbbreviationName;
                $grandFinalTournamentId                              = strtoupper(string: $tournament_name);
                $grandFinalRoundBeatmapHardRockType                  = $grandFinalRoundHardRockType;
                $grandFinalRoundBeatmapHardRockImage                 = $grandFinalRoundBeatmapHardRockData['beatmapset']['covers']['cover'];
                $grandFinalRoundBeatmapHardRockUrl                   = $grandFinalRoundBeatmapHardRockData['url'];
                $grandFinalRoundBeatmapHardRockName                  = $grandFinalRoundBeatmapHardRockData['beatmapset']['title'];
                $grandFinalRoundBeatmapHardRockDifficultyName        = $grandFinalRoundBeatmapHardRockData['version'];
                $grandFinalRoundBeatmapHardRockFeatureArtist         = $grandFinalRoundBeatmapHardRockData['beatmapset']['artist'];
                $grandFinalRoundBeatmapHardRockMapper                = $grandFinalRoundBeatmapHardRockData['beatmapset']['creator'];
                $grandFinalRoundBeatmapHardRockMapperUrl             = "https://osu.ppy.sh/users/{$grandFinalRoundBeatmapHardRockData['beatmapset']['user_id']}";
                $grandFinalRoundBeatmapHardRockDifficulty            = $grandFinalRoundBeatmapHardRockData['difficulty_rating'];
                $grandFinalRoundBeatmapHardRockLength                = $grandFinalRoundBeatmapHardRockData['total_length'];
                $grandFinalRoundBeatmapHardRockOverallSpeed          = $grandFinalRoundBeatmapHardRockData['beatmapset']['bpm'];
                $grandFinalRoundBeatmapHardRockOverallDifficulty     = $grandFinalRoundBeatmapHardRockData['accuracy'];
                $grandFinalRoundBeatmapHardRockOverallHealth         = $grandFinalRoundBeatmapHardRockData['drain'];
                $grandFinalRoundBeatmapHardRockPassCount             = $grandFinalRoundBeatmapHardRockData['passcount'];

                $tournamentRoundBeatmapHardRockData[] = [
                    'beatmap_id'                => $grandFinalRoundBeatmapHardRockId,
                    'beatmap_round_id'          => $grandFinalRoundId,
                    'beatmap_tournament_id'     => $grandFinalTournamentId,
                    'beatmap_type'              => $grandFinalRoundBeatmapHardRockType,
                    'beatmap_image'             => $grandFinalRoundBeatmapHardRockImage,
                    'beatmap_url'               => $grandFinalRoundBeatmapHardRockUrl,
                    'beatmap_name'              => $grandFinalRoundBeatmapHardRockName,
                    'beatmap_difficulty_name'   => $grandFinalRoundBeatmapHardRockDifficultyName,
                    'beatmap_fa'                => $grandFinalRoundBeatmapHardRockFeatureArtist,
                    'beatmap_mapper'            => $grandFinalRoundBeatmapHardRockMapper,
                    'beatmap_mapper_url'        => $grandFinalRoundBeatmapHardRockMapperUrl,
                    'beatmap_difficulty'        => $grandFinalRoundBeatmapHardRockDifficulty,
                    'beatmap_length'            => $grandFinalRoundBeatmapHardRockLength,
                    'beatmap_bpm'               => $grandFinalRoundBeatmapHardRockOverallSpeed,
                    'beatmap_od'                => $grandFinalRoundBeatmapHardRockOverallDifficulty,
                    'beatmap_hp'                => $grandFinalRoundBeatmapHardRockOverallHealth,
                    'beatmap_pass_count'        => $grandFinalRoundBeatmapHardRockPassCount
                ];
            }

            foreach ($grandFinalRoundJsonDoubleTimeData as $grandFinalRoundDoubleTimeType => $grandFinalRoundJsonDoubleTimeId) {
                $grandFinalRoundBeatmapDoubleTimeData = getTournamentRoundBeatmapData(
                    beatmap_id: $grandFinalRoundJsonDoubleTimeId,
                    access_token: $_COOKIE['vot_access_token']
                );

                $grandFinalRoundBeatmapDoubleTimeId                  = $grandFinalRoundBeatmapDoubleTimeData['id'];
                $grandFinalRoundId                                   = $grandFinalRoundAbbreviationName;
                $grandFinalTournamentId                              = strtoupper(string: $tournament_name);
                $grandFinalRoundBeatmapDoubleTimeType                = $grandFinalRoundDoubleTimeType;
                $grandFinalRoundBeatmapDoubleTimeImage               = $grandFinalRoundBeatmapDoubleTimeData['beatmapset']['covers']['cover'];
                $grandFinalRoundBeatmapDoubleTimeUrl                 = $grandFinalRoundBeatmapDoubleTimeData['url'];
                $grandFinalRoundBeatmapDoubleTimeName                = $grandFinalRoundBeatmapDoubleTimeData['beatmapset']['title'];
                $grandFinalRoundBeatmapDoubleTimeDifficultyName      = $grandFinalRoundBeatmapDoubleTimeData['version'];
                $grandFinalRoundBeatmapDoubleTimeFeatureArtist       = $grandFinalRoundBeatmapDoubleTimeData['beatmapset']['artist'];
                $grandFinalRoundBeatmapDoubleTimeMapper              = $grandFinalRoundBeatmapDoubleTimeData['beatmapset']['creator'];
                $grandFinalRoundBeatmapDoubleTimeMapperUrl           = "https://osu.ppy.sh/users/{$grandFinalRoundBeatmapDoubleTimeData['beatmapset']['user_id']}";
                $grandFinalRoundBeatmapDoubleTimeDifficulty          = $grandFinalRoundBeatmapDoubleTimeData['difficulty_rating'];
                $grandFinalRoundBeatmapDoubleTimeLength              = $grandFinalRoundBeatmapDoubleTimeData['total_length'];
                $grandFinalRoundBeatmapDoubleTimeOverallSpeed        = $grandFinalRoundBeatmapDoubleTimeData['beatmapset']['bpm'];
                $grandFinalRoundBeatmapDoubleTimeOverallDifficulty   = $grandFinalRoundBeatmapDoubleTimeData['accuracy'];
                $grandFinalRoundBeatmapDoubleTimeOverallHealth       = $grandFinalRoundBeatmapDoubleTimeData['drain'];
                $grandFinalRoundBeatmapDoubleTimePassCount           = $grandFinalRoundBeatmapDoubleTimeData['passcount'];

                $tournamentRoundBeatmapDoubleTimeData[] = [
                    'beatmap_id'                => $grandFinalRoundBeatmapDoubleTimeId,
                    'beatmap_round_id'          => $grandFinalRoundId,
                    'beatmap_tournament_id'     => $grandFinalTournamentId,
                    'beatmap_type'              => $grandFinalRoundBeatmapDoubleTimeType,
                    'beatmap_image'             => $grandFinalRoundBeatmapDoubleTimeImage,
                    'beatmap_url'               => $grandFinalRoundBeatmapDoubleTimeUrl,
                    'beatmap_name'              => $grandFinalRoundBeatmapDoubleTimeName,
                    'beatmap_difficulty_name'   => $grandFinalRoundBeatmapDoubleTimeDifficultyName,
                    'beatmap_fa'                => $grandFinalRoundBeatmapDoubleTimeFeatureArtist,
                    'beatmap_mapper'            => $grandFinalRoundBeatmapDoubleTimeMapper,
                    'beatmap_mapper_url'        => $grandFinalRoundBeatmapDoubleTimeMapperUrl,
                    'beatmap_difficulty'        => $grandFinalRoundBeatmapDoubleTimeDifficulty,
                    'beatmap_length'            => $grandFinalRoundBeatmapDoubleTimeLength,
                    'beatmap_bpm'               => $grandFinalRoundBeatmapDoubleTimeOverallSpeed,
                    'beatmap_od'                => $grandFinalRoundBeatmapDoubleTimeOverallDifficulty,
                    'beatmap_hp'                => $grandFinalRoundBeatmapDoubleTimeOverallHealth,
                    'beatmap_pass_count'        => $grandFinalRoundBeatmapDoubleTimePassCount
                ];
            }

            foreach ($grandFinalRoundJsonFreeModData as $grandFinalRoundFreeModType => $grandFinalRoundJsonFreeModId) {
                $grandFinalRoundBeatmapFreeModData = getTournamentRoundBeatmapData(
                    beatmap_id: $grandFinalRoundJsonFreeModId,
                    access_token: $_COOKIE['vot_access_token']
                );

                $grandFinalRoundBeatmapFreeModId                 = $grandFinalRoundBeatmapFreeModData['id'];
                $grandFinalRoundId                               = $grandFinalRoundAbbreviationName;
                $grandFinalTournamentId                          = strtoupper(string: $tournament_name);
                $grandFinalRoundBeatmapFreeModType               = $grandFinalRoundFreeModType;
                $grandFinalRoundBeatmapFreeModImage              = $grandFinalRoundBeatmapFreeModData['beatmapset']['covers']['cover'];
                $grandFinalRoundBeatmapFreeModUrl                = $grandFinalRoundBeatmapFreeModData['url'];
                $grandFinalRoundBeatmapFreeModName               = $grandFinalRoundBeatmapFreeModData['beatmapset']['title'];
                $grandFinalRoundBeatmapFreeModDifficultyName     = $grandFinalRoundBeatmapFreeModData['version'];
                $grandFinalRoundBeatmapFreeModFeatureArtist      = $grandFinalRoundBeatmapFreeModData['beatmapset']['artist'];
                $grandFinalRoundBeatmapFreeModMapper             = $grandFinalRoundBeatmapFreeModData['beatmapset']['creator'];
                $grandFinalRoundBeatmapFreeModMapperUrl          = "https://osu.ppy.sh/users/{$grandFinalRoundBeatmapFreeModData['beatmapset']['user_id']}";
                $grandFinalRoundBeatmapFreeModDifficulty         = $grandFinalRoundBeatmapFreeModData['difficulty_rating'];
                $grandFinalRoundBeatmapFreeModLength             = $grandFinalRoundBeatmapFreeModData['total_length'];
                $grandFinalRoundBeatmapFreeModOverallSpeed       = $grandFinalRoundBeatmapFreeModData['beatmapset']['bpm'];
                $grandFinalRoundBeatmapFreeModOverallDifficulty  = $grandFinalRoundBeatmapFreeModData['accuracy'];
                $grandFinalRoundBeatmapFreeModOverallHealth      = $grandFinalRoundBeatmapFreeModData['drain'];
                $grandFinalRoundBeatmapFreeModPassCount          = $grandFinalRoundBeatmapFreeModData['passcount'];

                $tournamentRoundBeatmapFreeModData[] = [
                    'beatmap_id'                => $grandFinalRoundBeatmapFreeModId,
                    'beatmap_round_id'          => $grandFinalRoundId,
                    'beatmap_tournament_id'     => $grandFinalTournamentId,
                    'beatmap_type'              => $grandFinalRoundBeatmapFreeModType,
                    'beatmap_image'             => $grandFinalRoundBeatmapFreeModImage,
                    'beatmap_url'               => $grandFinalRoundBeatmapFreeModUrl,
                    'beatmap_name'              => $grandFinalRoundBeatmapFreeModName,
                    'beatmap_difficulty_name'   => $grandFinalRoundBeatmapFreeModDifficultyName,
                    'beatmap_fa'                => $grandFinalRoundBeatmapFreeModFeatureArtist,
                    'beatmap_mapper'            => $grandFinalRoundBeatmapFreeModMapper,
                    'beatmap_mapper_url'        => $grandFinalRoundBeatmapFreeModMapperUrl,
                    'beatmap_difficulty'        => $grandFinalRoundBeatmapFreeModDifficulty,
                    'beatmap_length'            => $grandFinalRoundBeatmapFreeModLength,
                    'beatmap_bpm'               => $grandFinalRoundBeatmapFreeModOverallSpeed,
                    'beatmap_od'                => $grandFinalRoundBeatmapFreeModOverallDifficulty,
                    'beatmap_hp'                => $grandFinalRoundBeatmapFreeModOverallHealth,
                    'beatmap_pass_count'        => $grandFinalRoundBeatmapFreeModPassCount
                ];
            }

            foreach ($grandFinalRoundJsonEasyData as $grandFinalRoundEasyType => $grandFinalRoundJsonEasyId) {
                $grandFinalRoundBeatmapEasyData = getTournamentRoundBeatmapData(
                    beatmap_id: $grandFinalRoundJsonEasyId,
                    access_token: $_COOKIE['vot_access_token']
                );

                $grandFinalRoundBeatmapEasyId                 = $grandFinalRoundBeatmapEasyData['id'];
                $grandFinalRoundId                               = $grandFinalRoundAbbreviationName;
                $grandFinalTournamentId                          = strtoupper(string: $tournament_name);
                $grandFinalRoundBeatmapEasyType               = $grandFinalRoundEasyType;
                $grandFinalRoundBeatmapEasyImage              = $grandFinalRoundBeatmapEasyData['beatmapset']['covers']['cover'];
                $grandFinalRoundBeatmapEasyUrl                = $grandFinalRoundBeatmapEasyData['url'];
                $grandFinalRoundBeatmapEasyName               = $grandFinalRoundBeatmapEasyData['beatmapset']['title'];
                $grandFinalRoundBeatmapEasyDifficultyName     = $grandFinalRoundBeatmapEasyData['version'];
                $grandFinalRoundBeatmapEasyFeatureArtist      = $grandFinalRoundBeatmapEasyData['beatmapset']['artist'];
                $grandFinalRoundBeatmapEasyMapper             = $grandFinalRoundBeatmapEasyData['beatmapset']['creator'];
                $grandFinalRoundBeatmapEasyMapperUrl          = "https://osu.ppy.sh/users/{$grandFinalRoundBeatmapEasyData['beatmapset']['user_id']}";
                $grandFinalRoundBeatmapEasyDifficulty         = $grandFinalRoundBeatmapEasyData['difficulty_rating'];
                $grandFinalRoundBeatmapEasyLength             = $grandFinalRoundBeatmapEasyData['total_length'];
                $grandFinalRoundBeatmapEasyOverallSpeed       = $grandFinalRoundBeatmapEasyData['beatmapset']['bpm'];
                $grandFinalRoundBeatmapEasyOverallDifficulty  = $grandFinalRoundBeatmapEasyData['accuracy'];
                $grandFinalRoundBeatmapEasyOverallHealth      = $grandFinalRoundBeatmapEasyData['drain'];
                $grandFinalRoundBeatmapEasyPassCount          = $grandFinalRoundBeatmapEasyData['passcount'];

                $tournamentRoundBeatmapEasyData[] = [
                    'beatmap_id'                => $grandFinalRoundBeatmapEasyId,
                    'beatmap_round_id'          => $grandFinalRoundId,
                    'beatmap_tournament_id'     => $grandFinalTournamentId,
                    'beatmap_type'              => $grandFinalRoundBeatmapEasyType,
                    'beatmap_image'             => $grandFinalRoundBeatmapEasyImage,
                    'beatmap_url'               => $grandFinalRoundBeatmapEasyUrl,
                    'beatmap_name'              => $grandFinalRoundBeatmapEasyName,
                    'beatmap_difficulty_name'   => $grandFinalRoundBeatmapEasyDifficultyName,
                    'beatmap_fa'                => $grandFinalRoundBeatmapEasyFeatureArtist,
                    'beatmap_mapper'            => $grandFinalRoundBeatmapEasyMapper,
                    'beatmap_mapper_url'        => $grandFinalRoundBeatmapEasyMapperUrl,
                    'beatmap_difficulty'        => $grandFinalRoundBeatmapEasyDifficulty,
                    'beatmap_length'            => $grandFinalRoundBeatmapEasyLength,
                    'beatmap_bpm'               => $grandFinalRoundBeatmapEasyOverallSpeed,
                    'beatmap_od'                => $grandFinalRoundBeatmapEasyOverallDifficulty,
                    'beatmap_hp'                => $grandFinalRoundBeatmapEasyOverallHealth,
                    'beatmap_pass_count'        => $grandFinalRoundBeatmapEasyPassCount
                ];
            }

            foreach ($grandFinalRoundJsonHiddenHardRockData as $grandFinalRoundHiddenHardRockType => $grandFinalRoundJsonHiddenHardRockId) {
                $grandFinalRoundBeatmapHiddenHardRockData = getTournamentRoundBeatmapData(
                    beatmap_id: $grandFinalRoundJsonHiddenHardRockId,
                    access_token: $_COOKIE['vot_access_token']
                );

                $grandFinalRoundBeatmapHiddenHardRockId                 = $grandFinalRoundBeatmapHiddenHardRockData['id'];
                $grandFinalRoundId                               = $grandFinalRoundAbbreviationName;
                $grandFinalTournamentId                          = strtoupper(string: $tournament_name);
                $grandFinalRoundBeatmapHiddenHardRockType               = $grandFinalRoundHiddenHardRockType;
                $grandFinalRoundBeatmapHiddenHardRockImage              = $grandFinalRoundBeatmapHiddenHardRockData['beatmapset']['covers']['cover'];
                $grandFinalRoundBeatmapHiddenHardRockUrl                = $grandFinalRoundBeatmapHiddenHardRockData['url'];
                $grandFinalRoundBeatmapHiddenHardRockName               = $grandFinalRoundBeatmapHiddenHardRockData['beatmapset']['title'];
                $grandFinalRoundBeatmapHiddenHardRockDifficultyName     = $grandFinalRoundBeatmapHiddenHardRockData['version'];
                $grandFinalRoundBeatmapHiddenHardRockFeatureArtist      = $grandFinalRoundBeatmapHiddenHardRockData['beatmapset']['artist'];
                $grandFinalRoundBeatmapHiddenHardRockMapper             = $grandFinalRoundBeatmapHiddenHardRockData['beatmapset']['creator'];
                $grandFinalRoundBeatmapHiddenHardRockMapperUrl          = "https://osu.ppy.sh/users/{$grandFinalRoundBeatmapHiddenHardRockData['beatmapset']['user_id']}";
                $grandFinalRoundBeatmapHiddenHardRockDifficulty         = $grandFinalRoundBeatmapHiddenHardRockData['difficulty_rating'];
                $grandFinalRoundBeatmapHiddenHardRockLength             = $grandFinalRoundBeatmapHiddenHardRockData['total_length'];
                $grandFinalRoundBeatmapHiddenHardRockOverallSpeed       = $grandFinalRoundBeatmapHiddenHardRockData['beatmapset']['bpm'];
                $grandFinalRoundBeatmapHiddenHardRockOverallDifficulty  = $grandFinalRoundBeatmapHiddenHardRockData['accuracy'];
                $grandFinalRoundBeatmapHiddenHardRockOverallHealth      = $grandFinalRoundBeatmapHiddenHardRockData['drain'];
                $grandFinalRoundBeatmapHiddenHardRockPassCount          = $grandFinalRoundBeatmapHiddenHardRockData['passcount'];

                $tournamentRoundBeatmapHiddenHardRockData[] = [
                    'beatmap_id'                => $grandFinalRoundBeatmapHiddenHardRockId,
                    'beatmap_round_id'          => $grandFinalRoundId,
                    'beatmap_tournament_id'     => $grandFinalTournamentId,
                    'beatmap_type'              => $grandFinalRoundBeatmapHiddenHardRockType,
                    'beatmap_image'             => $grandFinalRoundBeatmapHiddenHardRockImage,
                    'beatmap_url'               => $grandFinalRoundBeatmapHiddenHardRockUrl,
                    'beatmap_name'              => $grandFinalRoundBeatmapHiddenHardRockName,
                    'beatmap_difficulty_name'   => $grandFinalRoundBeatmapHiddenHardRockDifficultyName,
                    'beatmap_fa'                => $grandFinalRoundBeatmapHiddenHardRockFeatureArtist,
                    'beatmap_mapper'            => $grandFinalRoundBeatmapHiddenHardRockMapper,
                    'beatmap_mapper_url'        => $grandFinalRoundBeatmapHiddenHardRockMapperUrl,
                    'beatmap_difficulty'        => $grandFinalRoundBeatmapHiddenHardRockDifficulty,
                    'beatmap_length'            => $grandFinalRoundBeatmapHiddenHardRockLength,
                    'beatmap_bpm'               => $grandFinalRoundBeatmapHiddenHardRockOverallSpeed,
                    'beatmap_od'                => $grandFinalRoundBeatmapHiddenHardRockOverallDifficulty,
                    'beatmap_hp'                => $grandFinalRoundBeatmapHiddenHardRockOverallHealth,
                    'beatmap_pass_count'        => $grandFinalRoundBeatmapHiddenHardRockPassCount
                ];
            }

            foreach ($grandFinalRoundJsonTiebreakerData as $grandFinalRoundTiebreakerType => $grandFinalRoundJsonTiebreakerId) {
                $grandFinalRoundBeatmapTiebreakerData = getTournamentRoundBeatmapData(
                    beatmap_id: $grandFinalRoundJsonTiebreakerId,
                    access_token: $_COOKIE['vot_access_token']
                );

                $grandFinalRoundBeatmapTiebreakerId                 = $grandFinalRoundBeatmapTiebreakerData['id'];
                $grandFinalRoundId                               = $grandFinalRoundAbbreviationName;
                $grandFinalTournamentId                          = strtoupper(string: $tournament_name);
                $grandFinalRoundBeatmapTiebreakerType               = $grandFinalRoundTiebreakerType;
                $grandFinalRoundBeatmapTiebreakerImage              = $grandFinalRoundBeatmapTiebreakerData['beatmapset']['covers']['cover'];
                $grandFinalRoundBeatmapTiebreakerUrl                = $grandFinalRoundBeatmapTiebreakerData['url'];
                $grandFinalRoundBeatmapTiebreakerName               = $grandFinalRoundBeatmapTiebreakerData['beatmapset']['title'];
                $grandFinalRoundBeatmapTiebreakerDifficultyName     = $grandFinalRoundBeatmapTiebreakerData['version'];
                $grandFinalRoundBeatmapTiebreakerFeatureArtist      = $grandFinalRoundBeatmapTiebreakerData['beatmapset']['artist'];
                $grandFinalRoundBeatmapTiebreakerMapper             = $grandFinalRoundBeatmapTiebreakerData['beatmapset']['creator'];
                $grandFinalRoundBeatmapTiebreakerMapperUrl          = "https://osu.ppy.sh/users/{$grandFinalRoundBeatmapTiebreakerData['beatmapset']['user_id']}";
                $grandFinalRoundBeatmapTiebreakerDifficulty         = $grandFinalRoundBeatmapTiebreakerData['difficulty_rating'];
                $grandFinalRoundBeatmapTiebreakerLength             = $grandFinalRoundBeatmapTiebreakerData['total_length'];
                $grandFinalRoundBeatmapTiebreakerOverallSpeed       = $grandFinalRoundBeatmapTiebreakerData['beatmapset']['bpm'];
                $grandFinalRoundBeatmapTiebreakerOverallDifficulty  = $grandFinalRoundBeatmapTiebreakerData['accuracy'];
                $grandFinalRoundBeatmapTiebreakerOverallHealth      = $grandFinalRoundBeatmapTiebreakerData['drain'];
                $grandFinalRoundBeatmapTiebreakerPassCount          = $grandFinalRoundBeatmapTiebreakerData['passcount'];

                $tournamentRoundBeatmapTiebreakerData[] = [
                    'beatmap_id'                => $grandFinalRoundBeatmapTiebreakerId,
                    'beatmap_round_id'          => $grandFinalRoundId,
                    'beatmap_tournament_id'     => $grandFinalTournamentId,
                    'beatmap_type'              => $grandFinalRoundBeatmapTiebreakerType,
                    'beatmap_image'             => $grandFinalRoundBeatmapTiebreakerImage,
                    'beatmap_url'               => $grandFinalRoundBeatmapTiebreakerUrl,
                    'beatmap_name'              => $grandFinalRoundBeatmapTiebreakerName,
                    'beatmap_difficulty_name'   => $grandFinalRoundBeatmapTiebreakerDifficultyName,
                    'beatmap_fa'                => $grandFinalRoundBeatmapTiebreakerFeatureArtist,
                    'beatmap_mapper'            => $grandFinalRoundBeatmapTiebreakerMapper,
                    'beatmap_mapper_url'        => $grandFinalRoundBeatmapTiebreakerMapperUrl,
                    'beatmap_difficulty'        => $grandFinalRoundBeatmapTiebreakerDifficulty,
                    'beatmap_length'            => $grandFinalRoundBeatmapTiebreakerLength,
                    'beatmap_bpm'               => $grandFinalRoundBeatmapTiebreakerOverallSpeed,
                    'beatmap_od'                => $grandFinalRoundBeatmapTiebreakerOverallDifficulty,
                    'beatmap_hp'                => $grandFinalRoundBeatmapTiebreakerOverallHealth,
                    'beatmap_pass_count'        => $grandFinalRoundBeatmapTiebreakerPassCount
                ];
            }


            /* ALL STAR MAPPOOL LOOPING DATA */
            $allStarRoundAbbreviationName               = $tournamentRoundAbbreviationNameData[6];
            $allStarRoundJsonMappoolData                = $tournamentRoundReadableMappoolData[$tournament_name][$allStarRoundAbbreviationName];
            $allStarRoundJsonNoModData                  = $allStarRoundJsonMappoolData['NM'];
            $allStarRoundJsonHiddenData                 = $allStarRoundJsonMappoolData['HD'];
            $allStarRoundJsonHardRockData               = $allStarRoundJsonMappoolData['HR'];
            $allStarRoundJsonDoubleTimeData             = $allStarRoundJsonMappoolData['DT'];
            $allStarRoundJsonFreeModData                = $allStarRoundJsonMappoolData['FM'];
            $allStarRoundJsonEasyData                   = $allStarRoundJsonMappoolData['EZ'];
            $allStarRoundJsonHiddenHardRockData         = $allStarRoundJsonMappoolData['HDHR'];
            $allStarRoundJsonTiebreakerData             = $allStarRoundJsonMappoolData['TB'];

            foreach ($allStarRoundJsonNoModData as $allStarRoundNoModType => $allStarRoundJsonNoModId) {
                $allStarRoundBeatmapNoModData = getTournamentRoundBeatmapData(
                    beatmap_id: $allStarRoundJsonNoModId,
                    access_token: $_COOKIE['vot_access_token']
                );

                $allStarRoundBeatmapNoModId                   = $allStarRoundBeatmapNoModData['id'];
                $allStarRoundId                               = $allStarRoundAbbreviationName;
                $allStarTournamentId                          = strtoupper(string: $tournament_name);
                $allStarRoundBeatmapNoModType                 = $allStarRoundNoModType;
                $allStarRoundBeatmapNoModImage                = $allStarRoundBeatmapNoModData['beatmapset']['covers']['cover'];
                $allStarRoundBeatmapNoModUrl                  = $allStarRoundBeatmapNoModData['url'];
                $allStarRoundBeatmapNoModName                 = $allStarRoundBeatmapNoModData['beatmapset']['title'];
                $allStarRoundBeatmapNoModDifficultyName       = $allStarRoundBeatmapNoModData['version'];
                $allStarRoundBeatmapNoModFeatureArtist        = $allStarRoundBeatmapNoModData['beatmapset']['artist'];
                $allStarRoundBeatmapNoModMapper               = $allStarRoundBeatmapNoModData['beatmapset']['creator'];
                $allStarRoundBeatmapNoModMapperUrl            = "https://osu.ppy.sh/users/{$allStarRoundBeatmapNoModData['beatmapset']['user_id']}";
                $allStarRoundBeatmapNoModDifficulty           = $allStarRoundBeatmapNoModData['difficulty_rating'];
                $allStarRoundBeatmapNoModLength               = $allStarRoundBeatmapNoModData['total_length'];
                $allStarRoundBeatmapNoModOverallSpeed         = $allStarRoundBeatmapNoModData['beatmapset']['bpm'];
                $allStarRoundBeatmapNoModOverallDifficulty    = $allStarRoundBeatmapNoModData['accuracy'];
                $allStarRoundBeatmapNoModOverallHealth        = $allStarRoundBeatmapNoModData['drain'];
                $allStarRoundBeatmapNoModPassCount            = $allStarRoundBeatmapNoModData['passcount'];

                $tournamentRoundBeatmapNoModData[] = [
                    'beatmap_id'                => $allStarRoundBeatmapNoModId,
                    'beatmap_round_id'          => $allStarRoundId,
                    'beatmap_tournament_id'     => $allStarTournamentId,
                    'beatmap_type'              => $allStarRoundBeatmapNoModType,
                    'beatmap_image'             => $allStarRoundBeatmapNoModImage,
                    'beatmap_url'               => $allStarRoundBeatmapNoModUrl,
                    'beatmap_name'              => $allStarRoundBeatmapNoModName,
                    'beatmap_difficulty_name'   => $allStarRoundBeatmapNoModDifficultyName,
                    'beatmap_fa'                => $allStarRoundBeatmapNoModFeatureArtist,
                    'beatmap_mapper'            => $allStarRoundBeatmapNoModMapper,
                    'beatmap_mapper_url'        => $allStarRoundBeatmapNoModMapperUrl,
                    'beatmap_difficulty'        => $allStarRoundBeatmapNoModDifficulty,
                    'beatmap_length'            => $allStarRoundBeatmapNoModLength,
                    'beatmap_bpm'               => $allStarRoundBeatmapNoModOverallSpeed,
                    'beatmap_od'                => $allStarRoundBeatmapNoModOverallDifficulty,
                    'beatmap_hp'                => $allStarRoundBeatmapNoModOverallHealth,
                    'beatmap_pass_count'        => $allStarRoundBeatmapNoModPassCount
                ];
            }

            foreach ($allStarRoundJsonHiddenData as $allStarRoundHiddenType => $allStarRoundJsonHiddenId) {
                $allStarRoundBeatmapHiddenData = getTournamentRoundBeatmapData(
                    beatmap_id: $allStarRoundJsonHiddenId,
                    access_token: $_COOKIE['vot_access_token']
                );

                $allStarRoundBeatmapHiddenId                  = $allStarRoundBeatmapHiddenData['id'];
                $allStarRoundId                               = $allStarRoundAbbreviationName;
                $allStarTournamentId                          = strtoupper(string: $tournament_name);
                $allStarRoundBeatmapHiddenType                = $allStarRoundHiddenType;
                $allStarRoundBeatmapHiddenImage               = $allStarRoundBeatmapHiddenData['beatmapset']['covers']['cover'];
                $allStarRoundBeatmapHiddenUrl                 = $allStarRoundBeatmapHiddenData['url'];
                $allStarRoundBeatmapHiddenName                = $allStarRoundBeatmapHiddenData['beatmapset']['title'];
                $allStarRoundBeatmapHiddenDifficultyName      = $allStarRoundBeatmapHiddenData['version'];
                $allStarRoundBeatmapHiddenFeatureArtist       = $allStarRoundBeatmapHiddenData['beatmapset']['artist'];
                $allStarRoundBeatmapHiddenMapper              = $allStarRoundBeatmapHiddenData['beatmapset']['creator'];
                $allStarRoundBeatmapHiddenMapperUrl           = "https://osu.ppy.sh/users/{$allStarRoundBeatmapHiddenData['beatmapset']['user_id']}";
                $allStarRoundBeatmapHiddenDifficulty          = $allStarRoundBeatmapHiddenData['difficulty_rating'];
                $allStarRoundBeatmapHiddenLength              = $allStarRoundBeatmapHiddenData['total_length'];
                $allStarRoundBeatmapHiddenOverallSpeed        = $allStarRoundBeatmapHiddenData['beatmapset']['bpm'];
                $allStarRoundBeatmapHiddenOverallDifficulty   = $allStarRoundBeatmapHiddenData['accuracy'];
                $allStarRoundBeatmapHiddenOverallHealth       = $allStarRoundBeatmapHiddenData['drain'];
                $allStarRoundBeatmapHiddenPassCount           = $allStarRoundBeatmapHiddenData['passcount'];

                $tournamentRoundBeatmapHiddenData[] = [
                    'beatmap_id'                => $allStarRoundBeatmapHiddenId,
                    'beatmap_round_id'          => $allStarRoundId,
                    'beatmap_tournament_id'     => $allStarTournamentId,
                    'beatmap_type'              => $allStarRoundBeatmapHiddenType,
                    'beatmap_image'             => $allStarRoundBeatmapHiddenImage,
                    'beatmap_url'               => $allStarRoundBeatmapHiddenUrl,
                    'beatmap_name'              => $allStarRoundBeatmapHiddenName,
                    'beatmap_difficulty_name'   => $allStarRoundBeatmapHiddenDifficultyName,
                    'beatmap_fa'                => $allStarRoundBeatmapHiddenFeatureArtist,
                    'beatmap_mapper'            => $allStarRoundBeatmapHiddenMapper,
                    'beatmap_mapper_url'        => $allStarRoundBeatmapHiddenMapperUrl,
                    'beatmap_difficulty'        => $allStarRoundBeatmapHiddenDifficulty,
                    'beatmap_length'            => $allStarRoundBeatmapHiddenLength,
                    'beatmap_bpm'               => $allStarRoundBeatmapHiddenOverallSpeed,
                    'beatmap_od'                => $allStarRoundBeatmapHiddenOverallDifficulty,
                    'beatmap_hp'                => $allStarRoundBeatmapHiddenOverallHealth,
                    'beatmap_pass_count'        => $allStarRoundBeatmapHiddenPassCount
                ];
            }

            foreach ($allStarRoundJsonHardRockData as $allStarRoundHardRockType => $allStarRoundJsonHardRockId) {
                $allStarRoundBeatmapHardRockData = getTournamentRoundBeatmapData(
                    beatmap_id: $allStarRoundJsonHardRockId,
                    access_token: $_COOKIE['vot_access_token']
                );

                $allStarRoundBeatmapHardRockId                    = $allStarRoundBeatmapHardRockData['id'];
                $allStarRoundId                                   = $allStarRoundAbbreviationName;
                $allStarTournamentId                              = strtoupper(string: $tournament_name);
                $allStarRoundBeatmapHardRockType                  = $allStarRoundHardRockType;
                $allStarRoundBeatmapHardRockImage                 = $allStarRoundBeatmapHardRockData['beatmapset']['covers']['cover'];
                $allStarRoundBeatmapHardRockUrl                   = $allStarRoundBeatmapHardRockData['url'];
                $allStarRoundBeatmapHardRockName                  = $allStarRoundBeatmapHardRockData['beatmapset']['title'];
                $allStarRoundBeatmapHardRockDifficultyName        = $allStarRoundBeatmapHardRockData['version'];
                $allStarRoundBeatmapHardRockFeatureArtist         = $allStarRoundBeatmapHardRockData['beatmapset']['artist'];
                $allStarRoundBeatmapHardRockMapper                = $allStarRoundBeatmapHardRockData['beatmapset']['creator'];
                $allStarRoundBeatmapHardRockMapperUrl             = "https://osu.ppy.sh/users/{$allStarRoundBeatmapHardRockData['beatmapset']['user_id']}";
                $allStarRoundBeatmapHardRockDifficulty            = $allStarRoundBeatmapHardRockData['difficulty_rating'];
                $allStarRoundBeatmapHardRockLength                = $allStarRoundBeatmapHardRockData['total_length'];
                $allStarRoundBeatmapHardRockOverallSpeed          = $allStarRoundBeatmapHardRockData['beatmapset']['bpm'];
                $allStarRoundBeatmapHardRockOverallDifficulty     = $allStarRoundBeatmapHardRockData['accuracy'];
                $allStarRoundBeatmapHardRockOverallHealth         = $allStarRoundBeatmapHardRockData['drain'];
                $allStarRoundBeatmapHardRockPassCount             = $allStarRoundBeatmapHardRockData['passcount'];

                $tournamentRoundBeatmapHardRockData[] = [
                    'beatmap_id'                => $allStarRoundBeatmapHardRockId,
                    'beatmap_round_id'          => $allStarRoundId,
                    'beatmap_tournament_id'     => $allStarTournamentId,
                    'beatmap_type'              => $allStarRoundBeatmapHardRockType,
                    'beatmap_image'             => $allStarRoundBeatmapHardRockImage,
                    'beatmap_url'               => $allStarRoundBeatmapHardRockUrl,
                    'beatmap_name'              => $allStarRoundBeatmapHardRockName,
                    'beatmap_difficulty_name'   => $allStarRoundBeatmapHardRockDifficultyName,
                    'beatmap_fa'                => $allStarRoundBeatmapHardRockFeatureArtist,
                    'beatmap_mapper'            => $allStarRoundBeatmapHardRockMapper,
                    'beatmap_mapper_url'        => $allStarRoundBeatmapHardRockMapperUrl,
                    'beatmap_difficulty'        => $allStarRoundBeatmapHardRockDifficulty,
                    'beatmap_length'            => $allStarRoundBeatmapHardRockLength,
                    'beatmap_bpm'               => $allStarRoundBeatmapHardRockOverallSpeed,
                    'beatmap_od'                => $allStarRoundBeatmapHardRockOverallDifficulty,
                    'beatmap_hp'                => $allStarRoundBeatmapHardRockOverallHealth,
                    'beatmap_pass_count'        => $allStarRoundBeatmapHardRockPassCount
                ];
            }

            foreach ($allStarRoundJsonDoubleTimeData as $allStarRoundDoubleTimeType => $allStarRoundJsonDoubleTimeId) {
                $allStarRoundBeatmapDoubleTimeData = getTournamentRoundBeatmapData(
                    beatmap_id: $allStarRoundJsonDoubleTimeId,
                    access_token: $_COOKIE['vot_access_token']
                );

                $allStarRoundBeatmapDoubleTimeId                  = $allStarRoundBeatmapDoubleTimeData['id'];
                $allStarRoundId                                   = $allStarRoundAbbreviationName;
                $allStarTournamentId                              = strtoupper(string: $tournament_name);
                $allStarRoundBeatmapDoubleTimeType                = $allStarRoundDoubleTimeType;
                $allStarRoundBeatmapDoubleTimeImage               = $allStarRoundBeatmapDoubleTimeData['beatmapset']['covers']['cover'];
                $allStarRoundBeatmapDoubleTimeUrl                 = $allStarRoundBeatmapDoubleTimeData['url'];
                $allStarRoundBeatmapDoubleTimeName                = $allStarRoundBeatmapDoubleTimeData['beatmapset']['title'];
                $allStarRoundBeatmapDoubleTimeDifficultyName      = $allStarRoundBeatmapDoubleTimeData['version'];
                $allStarRoundBeatmapDoubleTimeFeatureArtist       = $allStarRoundBeatmapDoubleTimeData['beatmapset']['artist'];
                $allStarRoundBeatmapDoubleTimeMapper              = $allStarRoundBeatmapDoubleTimeData['beatmapset']['creator'];
                $allStarRoundBeatmapDoubleTimeMapperUrl           = "https://osu.ppy.sh/users/{$allStarRoundBeatmapDoubleTimeData['beatmapset']['user_id']}";
                $allStarRoundBeatmapDoubleTimeDifficulty          = $allStarRoundBeatmapDoubleTimeData['difficulty_rating'];
                $allStarRoundBeatmapDoubleTimeLength              = $allStarRoundBeatmapDoubleTimeData['total_length'];
                $allStarRoundBeatmapDoubleTimeOverallSpeed        = $allStarRoundBeatmapDoubleTimeData['beatmapset']['bpm'];
                $allStarRoundBeatmapDoubleTimeOverallDifficulty   = $allStarRoundBeatmapDoubleTimeData['accuracy'];
                $allStarRoundBeatmapDoubleTimeOverallHealth       = $allStarRoundBeatmapDoubleTimeData['drain'];
                $allStarRoundBeatmapDoubleTimePassCount           = $allStarRoundBeatmapDoubleTimeData['passcount'];

                $tournamentRoundBeatmapDoubleTimeData[] = [
                    'beatmap_id'                => $allStarRoundBeatmapDoubleTimeId,
                    'beatmap_round_id'          => $allStarRoundId,
                    'beatmap_tournament_id'     => $allStarTournamentId,
                    'beatmap_type'              => $allStarRoundBeatmapDoubleTimeType,
                    'beatmap_image'             => $allStarRoundBeatmapDoubleTimeImage,
                    'beatmap_url'               => $allStarRoundBeatmapDoubleTimeUrl,
                    'beatmap_name'              => $allStarRoundBeatmapDoubleTimeName,
                    'beatmap_difficulty_name'   => $allStarRoundBeatmapDoubleTimeDifficultyName,
                    'beatmap_fa'                => $allStarRoundBeatmapDoubleTimeFeatureArtist,
                    'beatmap_mapper'            => $allStarRoundBeatmapDoubleTimeMapper,
                    'beatmap_mapper_url'        => $allStarRoundBeatmapDoubleTimeMapperUrl,
                    'beatmap_difficulty'        => $allStarRoundBeatmapDoubleTimeDifficulty,
                    'beatmap_length'            => $allStarRoundBeatmapDoubleTimeLength,
                    'beatmap_bpm'               => $allStarRoundBeatmapDoubleTimeOverallSpeed,
                    'beatmap_od'                => $allStarRoundBeatmapDoubleTimeOverallDifficulty,
                    'beatmap_hp'                => $allStarRoundBeatmapDoubleTimeOverallHealth,
                    'beatmap_pass_count'        => $allStarRoundBeatmapDoubleTimePassCount
                ];
            }

            foreach ($allStarRoundJsonFreeModData as $allStarRoundFreeModType => $allStarRoundJsonFreeModId) {
                $allStarRoundBeatmapFreeModData = getTournamentRoundBeatmapData(
                    beatmap_id: $allStarRoundJsonFreeModId,
                    access_token: $_COOKIE['vot_access_token']
                );

                $allStarRoundBeatmapFreeModId                 = $allStarRoundBeatmapFreeModData['id'];
                $allStarRoundId                               = $allStarRoundAbbreviationName;
                $allStarTournamentId                          = strtoupper(string: $tournament_name);
                $allStarRoundBeatmapFreeModType               = $allStarRoundFreeModType;
                $allStarRoundBeatmapFreeModImage              = $allStarRoundBeatmapFreeModData['beatmapset']['covers']['cover'];
                $allStarRoundBeatmapFreeModUrl                = $allStarRoundBeatmapFreeModData['url'];
                $allStarRoundBeatmapFreeModName               = $allStarRoundBeatmapFreeModData['beatmapset']['title'];
                $allStarRoundBeatmapFreeModDifficultyName     = $allStarRoundBeatmapFreeModData['version'];
                $allStarRoundBeatmapFreeModFeatureArtist      = $allStarRoundBeatmapFreeModData['beatmapset']['artist'];
                $allStarRoundBeatmapFreeModMapper             = $allStarRoundBeatmapFreeModData['beatmapset']['creator'];
                $allStarRoundBeatmapFreeModMapperUrl          = "https://osu.ppy.sh/users/{$allStarRoundBeatmapFreeModData['beatmapset']['user_id']}";
                $allStarRoundBeatmapFreeModDifficulty         = $allStarRoundBeatmapFreeModData['difficulty_rating'];
                $allStarRoundBeatmapFreeModLength             = $allStarRoundBeatmapFreeModData['total_length'];
                $allStarRoundBeatmapFreeModOverallSpeed       = $allStarRoundBeatmapFreeModData['beatmapset']['bpm'];
                $allStarRoundBeatmapFreeModOverallDifficulty  = $allStarRoundBeatmapFreeModData['accuracy'];
                $allStarRoundBeatmapFreeModOverallHealth      = $allStarRoundBeatmapFreeModData['drain'];
                $allStarRoundBeatmapFreeModPassCount          = $allStarRoundBeatmapFreeModData['passcount'];

                $tournamentRoundBeatmapFreeModData[] = [
                    'beatmap_id'                => $allStarRoundBeatmapFreeModId,
                    'beatmap_round_id'          => $allStarRoundId,
                    'beatmap_tournament_id'     => $allStarTournamentId,
                    'beatmap_type'              => $allStarRoundBeatmapFreeModType,
                    'beatmap_image'             => $allStarRoundBeatmapFreeModImage,
                    'beatmap_url'               => $allStarRoundBeatmapFreeModUrl,
                    'beatmap_name'              => $allStarRoundBeatmapFreeModName,
                    'beatmap_difficulty_name'   => $allStarRoundBeatmapFreeModDifficultyName,
                    'beatmap_fa'                => $allStarRoundBeatmapFreeModFeatureArtist,
                    'beatmap_mapper'            => $allStarRoundBeatmapFreeModMapper,
                    'beatmap_mapper_url'        => $allStarRoundBeatmapFreeModMapperUrl,
                    'beatmap_difficulty'        => $allStarRoundBeatmapFreeModDifficulty,
                    'beatmap_length'            => $allStarRoundBeatmapFreeModLength,
                    'beatmap_bpm'               => $allStarRoundBeatmapFreeModOverallSpeed,
                    'beatmap_od'                => $allStarRoundBeatmapFreeModOverallDifficulty,
                    'beatmap_hp'                => $allStarRoundBeatmapFreeModOverallHealth,
                    'beatmap_pass_count'        => $allStarRoundBeatmapFreeModPassCount
                ];
            }

            foreach ($allStarRoundJsonEasyData as $allStarRoundEasyType => $allStarRoundJsonEasyId) {
                $allStarRoundBeatmapEasyData = getTournamentRoundBeatmapData(
                    beatmap_id: $allStarRoundJsonEasyId,
                    access_token: $_COOKIE['vot_access_token']
                );

                $allStarRoundBeatmapEasyId                 = $allStarRoundBeatmapEasyData['id'];
                $allStarRoundId                               = $allStarRoundAbbreviationName;
                $allStarTournamentId                          = strtoupper(string: $tournament_name);
                $allStarRoundBeatmapEasyType               = $allStarRoundEasyType;
                $allStarRoundBeatmapEasyImage              = $allStarRoundBeatmapEasyData['beatmapset']['covers']['cover'];
                $allStarRoundBeatmapEasyUrl                = $allStarRoundBeatmapEasyData['url'];
                $allStarRoundBeatmapEasyName               = $allStarRoundBeatmapEasyData['beatmapset']['title'];
                $allStarRoundBeatmapEasyDifficultyName     = $allStarRoundBeatmapEasyData['version'];
                $allStarRoundBeatmapEasyFeatureArtist      = $allStarRoundBeatmapEasyData['beatmapset']['artist'];
                $allStarRoundBeatmapEasyMapper             = $allStarRoundBeatmapEasyData['beatmapset']['creator'];
                $allStarRoundBeatmapEasyMapperUrl          = "https://osu.ppy.sh/users/{$allStarRoundBeatmapEasyData['beatmapset']['user_id']}";
                $allStarRoundBeatmapEasyDifficulty         = $allStarRoundBeatmapEasyData['difficulty_rating'];
                $allStarRoundBeatmapEasyLength             = $allStarRoundBeatmapEasyData['total_length'];
                $allStarRoundBeatmapEasyOverallSpeed       = $allStarRoundBeatmapEasyData['beatmapset']['bpm'];
                $allStarRoundBeatmapEasyOverallDifficulty  = $allStarRoundBeatmapEasyData['accuracy'];
                $allStarRoundBeatmapEasyOverallHealth      = $allStarRoundBeatmapEasyData['drain'];
                $allStarRoundBeatmapEasyPassCount          = $allStarRoundBeatmapEasyData['passcount'];

                $tournamentRoundBeatmapEasyData[] = [
                    'beatmap_id'                => $allStarRoundBeatmapEasyId,
                    'beatmap_round_id'          => $allStarRoundId,
                    'beatmap_tournament_id'     => $allStarTournamentId,
                    'beatmap_type'              => $allStarRoundBeatmapEasyType,
                    'beatmap_image'             => $allStarRoundBeatmapEasyImage,
                    'beatmap_url'               => $allStarRoundBeatmapEasyUrl,
                    'beatmap_name'              => $allStarRoundBeatmapEasyName,
                    'beatmap_difficulty_name'   => $allStarRoundBeatmapEasyDifficultyName,
                    'beatmap_fa'                => $allStarRoundBeatmapEasyFeatureArtist,
                    'beatmap_mapper'            => $allStarRoundBeatmapEasyMapper,
                    'beatmap_mapper_url'        => $allStarRoundBeatmapEasyMapperUrl,
                    'beatmap_difficulty'        => $allStarRoundBeatmapEasyDifficulty,
                    'beatmap_length'            => $allStarRoundBeatmapEasyLength,
                    'beatmap_bpm'               => $allStarRoundBeatmapEasyOverallSpeed,
                    'beatmap_od'                => $allStarRoundBeatmapEasyOverallDifficulty,
                    'beatmap_hp'                => $allStarRoundBeatmapEasyOverallHealth,
                    'beatmap_pass_count'        => $allStarRoundBeatmapEasyPassCount
                ];
            }

            foreach ($allStarRoundJsonHiddenHardRockData as $allStarRoundHiddenHardRockType => $allStarRoundJsonHiddenHardRockId) {
                $allStarRoundBeatmapHiddenHardRockData = getTournamentRoundBeatmapData(
                    beatmap_id: $allStarRoundJsonHiddenHardRockId,
                    access_token: $_COOKIE['vot_access_token']
                );

                $allStarRoundBeatmapHiddenHardRockId                 = $allStarRoundBeatmapHiddenHardRockData['id'];
                $allStarRoundId                               = $allStarRoundAbbreviationName;
                $allStarTournamentId                          = strtoupper(string: $tournament_name);
                $allStarRoundBeatmapHiddenHardRockType               = $allStarRoundHiddenHardRockType;
                $allStarRoundBeatmapHiddenHardRockImage              = $allStarRoundBeatmapHiddenHardRockData['beatmapset']['covers']['cover'];
                $allStarRoundBeatmapHiddenHardRockUrl                = $allStarRoundBeatmapHiddenHardRockData['url'];
                $allStarRoundBeatmapHiddenHardRockName               = $allStarRoundBeatmapHiddenHardRockData['beatmapset']['title'];
                $allStarRoundBeatmapHiddenHardRockDifficultyName     = $allStarRoundBeatmapHiddenHardRockData['version'];
                $allStarRoundBeatmapHiddenHardRockFeatureArtist      = $allStarRoundBeatmapHiddenHardRockData['beatmapset']['artist'];
                $allStarRoundBeatmapHiddenHardRockMapper             = $allStarRoundBeatmapHiddenHardRockData['beatmapset']['creator'];
                $allStarRoundBeatmapHiddenHardRockMapperUrl          = "https://osu.ppy.sh/users/{$allStarRoundBeatmapHiddenHardRockData['beatmapset']['user_id']}";
                $allStarRoundBeatmapHiddenHardRockDifficulty         = $allStarRoundBeatmapHiddenHardRockData['difficulty_rating'];
                $allStarRoundBeatmapHiddenHardRockLength             = $allStarRoundBeatmapHiddenHardRockData['total_length'];
                $allStarRoundBeatmapHiddenHardRockOverallSpeed       = $allStarRoundBeatmapHiddenHardRockData['beatmapset']['bpm'];
                $allStarRoundBeatmapHiddenHardRockOverallDifficulty  = $allStarRoundBeatmapHiddenHardRockData['accuracy'];
                $allStarRoundBeatmapHiddenHardRockOverallHealth      = $allStarRoundBeatmapHiddenHardRockData['drain'];
                $allStarRoundBeatmapHiddenHardRockPassCount          = $allStarRoundBeatmapHiddenHardRockData['passcount'];

                $tournamentRoundBeatmapHiddenHardRockData[] = [
                    'beatmap_id'                => $allStarRoundBeatmapHiddenHardRockId,
                    'beatmap_round_id'          => $allStarRoundId,
                    'beatmap_tournament_id'     => $allStarTournamentId,
                    'beatmap_type'              => $allStarRoundBeatmapHiddenHardRockType,
                    'beatmap_image'             => $allStarRoundBeatmapHiddenHardRockImage,
                    'beatmap_url'               => $allStarRoundBeatmapHiddenHardRockUrl,
                    'beatmap_name'              => $allStarRoundBeatmapHiddenHardRockName,
                    'beatmap_difficulty_name'   => $allStarRoundBeatmapHiddenHardRockDifficultyName,
                    'beatmap_fa'                => $allStarRoundBeatmapHiddenHardRockFeatureArtist,
                    'beatmap_mapper'            => $allStarRoundBeatmapHiddenHardRockMapper,
                    'beatmap_mapper_url'        => $allStarRoundBeatmapHiddenHardRockMapperUrl,
                    'beatmap_difficulty'        => $allStarRoundBeatmapHiddenHardRockDifficulty,
                    'beatmap_length'            => $allStarRoundBeatmapHiddenHardRockLength,
                    'beatmap_bpm'               => $allStarRoundBeatmapHiddenHardRockOverallSpeed,
                    'beatmap_od'                => $allStarRoundBeatmapHiddenHardRockOverallDifficulty,
                    'beatmap_hp'                => $allStarRoundBeatmapHiddenHardRockOverallHealth,
                    'beatmap_pass_count'        => $allStarRoundBeatmapHiddenHardRockPassCount
                ];
            }

            foreach ($allStarRoundJsonTiebreakerData as $allStarRoundTiebreakerType => $allStarRoundJsonTiebreakerId) {
                $allStarRoundBeatmapTiebreakerData = getTournamentRoundBeatmapData(
                    beatmap_id: $allStarRoundJsonTiebreakerId,
                    access_token: $_COOKIE['vot_access_token']
                );

                $allStarRoundBeatmapTiebreakerId                 = $allStarRoundBeatmapTiebreakerData['id'];
                $allStarRoundId                               = $allStarRoundAbbreviationName;
                $allStarTournamentId                          = strtoupper(string: $tournament_name);
                $allStarRoundBeatmapTiebreakerType               = $allStarRoundTiebreakerType;
                $allStarRoundBeatmapTiebreakerImage              = $allStarRoundBeatmapTiebreakerData['beatmapset']['covers']['cover'];
                $allStarRoundBeatmapTiebreakerUrl                = $allStarRoundBeatmapTiebreakerData['url'];
                $allStarRoundBeatmapTiebreakerName               = $allStarRoundBeatmapTiebreakerData['beatmapset']['title'];
                $allStarRoundBeatmapTiebreakerDifficultyName     = $allStarRoundBeatmapTiebreakerData['version'];
                $allStarRoundBeatmapTiebreakerFeatureArtist      = $allStarRoundBeatmapTiebreakerData['beatmapset']['artist'];
                $allStarRoundBeatmapTiebreakerMapper             = $allStarRoundBeatmapTiebreakerData['beatmapset']['creator'];
                $allStarRoundBeatmapTiebreakerMapperUrl          = "https://osu.ppy.sh/users/{$allStarRoundBeatmapTiebreakerData['beatmapset']['user_id']}";
                $allStarRoundBeatmapTiebreakerDifficulty         = $allStarRoundBeatmapTiebreakerData['difficulty_rating'];
                $allStarRoundBeatmapTiebreakerLength             = $allStarRoundBeatmapTiebreakerData['total_length'];
                $allStarRoundBeatmapTiebreakerOverallSpeed       = $allStarRoundBeatmapTiebreakerData['beatmapset']['bpm'];
                $allStarRoundBeatmapTiebreakerOverallDifficulty  = $allStarRoundBeatmapTiebreakerData['accuracy'];
                $allStarRoundBeatmapTiebreakerOverallHealth      = $allStarRoundBeatmapTiebreakerData['drain'];
                $allStarRoundBeatmapTiebreakerPassCount          = $allStarRoundBeatmapTiebreakerData['passcount'];

                $tournamentRoundBeatmapTiebreakerData[] = [
                    'beatmap_id'                => $allStarRoundBeatmapTiebreakerId,
                    'beatmap_round_id'          => $allStarRoundId,
                    'beatmap_tournament_id'     => $allStarTournamentId,
                    'beatmap_type'              => $allStarRoundBeatmapTiebreakerType,
                    'beatmap_image'             => $allStarRoundBeatmapTiebreakerImage,
                    'beatmap_url'               => $allStarRoundBeatmapTiebreakerUrl,
                    'beatmap_name'              => $allStarRoundBeatmapTiebreakerName,
                    'beatmap_difficulty_name'   => $allStarRoundBeatmapTiebreakerDifficultyName,
                    'beatmap_fa'                => $allStarRoundBeatmapTiebreakerFeatureArtist,
                    'beatmap_mapper'            => $allStarRoundBeatmapTiebreakerMapper,
                    'beatmap_mapper_url'        => $allStarRoundBeatmapTiebreakerMapperUrl,
                    'beatmap_difficulty'        => $allStarRoundBeatmapTiebreakerDifficulty,
                    'beatmap_length'            => $allStarRoundBeatmapTiebreakerLength,
                    'beatmap_bpm'               => $allStarRoundBeatmapTiebreakerOverallSpeed,
                    'beatmap_od'                => $allStarRoundBeatmapTiebreakerOverallDifficulty,
                    'beatmap_hp'                => $allStarRoundBeatmapTiebreakerOverallHealth,
                    'beatmap_pass_count'        => $allStarRoundBeatmapTiebreakerPassCount
                ];
            }

            // Sent all mod-specific beatmaps data back to the database
            getMappoolData(mappool_data: $tournamentRoundBeatmapNoModData);             // NM
            getMappoolData(mappool_data: $tournamentRoundBeatmapHiddenData);            // HD
            getMappoolData(mappool_data: $tournamentRoundBeatmapHardRockData);          // HR
            getMappoolData(mappool_data: $tournamentRoundBeatmapDoubleTimeData);        // DT
            getMappoolData(mappool_data: $tournamentRoundBeatmapFreeModData);           // FM
            getMappoolData(mappool_data: $tournamentRoundBeatmapEasyData);              // EZ
            getMappoolData(mappool_data: $tournamentRoundBeatmapHiddenHardRockData);    // HDHR
            getMappoolData(mappool_data: $tournamentRoundBeatmapTiebreakerData);        // TB

            break;

        default:
            http_response_code(404);
            break;
    }

    return [
        $tournamentRoundBeatmapNoModData,
        $tournamentRoundBeatmapHiddenData,
        $tournamentRoundBeatmapHardRockData,
        $tournamentRoundBeatmapDoubleTimeData,
        $tournamentRoundBeatmapFreeModData,
        $tournamentRoundBeatmapEasyData,
        $tournamentRoundBeatmapHiddenHardRockData,
        $tournamentRoundBeatmapTiebreakerData
    ];
}

function getTournamentRoundBeatmapData(
    int $beatmap_id,
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
