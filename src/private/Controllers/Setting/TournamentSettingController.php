<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require __DIR__ . '/../../Models/Mappool.php';
require __DIR__ . '/../../Utilities/Length.php';
require __DIR__ . "/../../Utilities/JsonDataLayer.php";


if (
    !isset($_COOKIE['vot_access_id']) &&
    !isset($_COOKIE['vot_access_token'])
) {
    // Simply: "/setting/tournament" --> "<tournament>"
    $tournamentSettingPath = explode(
        separator: '/',
        string: trim(
            string: $_SERVER['REQUEST_URI'],
            characters: '/'
        )
    )[1];

    error_log(
        message: sprintf(
            "Suspicious attempt to access [%s] setting page detected!!",
            strtoupper(string: $tournamentSettingPath)
        ),
        message_type: 0
    );

    exit(header(
        header: 'Location: /home',
        replace: true,
        response_code: 302
    ));
} else {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        require __DIR__ . '/../../Controllers/NavigationBarController.php';
        require __DIR__ . '/../../Views/Setting/TournamentSettingView.php';
    } else {

        /*============================================*
         *  Final JSON output should be like below:   *
         *============================================*
         *  {                                         *
         *      "<tournament>": {                     *
         *          "<round>": {                      *
         *              "<mod>": <id>,                *
         *              <continue>                    *
         *          },                                *
         *          <continue>                        *
         *      }                                     *
         *  }                                         *
         *============================================*
         */

        $tournamentName  = $_GET['tournament'];
        $roundName       = $_GET['round'];
        $beatmapType     = $_POST['beatmapType'];
        $beatmapId       = (int)$_POST['beatmapId'];

        // Trust me, it looks better this way than string concat
        $mappoolJsonFile = sprintf(
            "%s/../../Datas/Tournament/Mappool%sData.json",
            __DIR__,
            ucfirst(string: $tournamentName)
        );
        $mappoolJsonData = [];
        $mappoolAccessToken = $_COOKIE['vot_access_token'];

        // Tournament regex matching handling
        switch (true) {
            // *** VOT5 TOURNAMENT ***
            case preg_match(
                pattern: '/^(vot5|vietnameseosutaikotournament5)$/i',
                subject: $tournamentName
            ):
                // Round regex matching handling
                switch (true) {
                    // *** TOURNAMENT QUALIFIER MAPPOOL DATA ***
                    case preg_match(
                        pattern: '/^(qualifier|qualifiers|qlf|qlfs)$/i',
                        subject: $roundName
                    ):
                        // Database columns use abbrviation names for quick searching
                        $abbreviateRoundName = 'QLF';

                        appendJsonData(
                            json_data: $mappoolJsonData,
                            json_keys: [
                                strtoupper(string: $tournamentName),
                                $abbreviateRoundName,
                                strtoupper(string: $beatmapType)
                            ],
                            json_value: $beatmapId,
                            json_file: $mappoolJsonFile
                        );

                        $mappoolQualifierJsonData = viewJsonData(json_file: $mappoolJsonFile)[strtoupper(string: $tournamentName)][$abbreviateRoundName];
                        foreach ($mappoolQualifierJsonData as $qualifierJsonType => $qualifierJsonId) {
                            $qualifierData = getMappoolData(
                                id: $qualifierJsonId,
                                token: $mappoolAccessToken
                            );

                            $qualifierId                = $qualifierData['id'];
                            $qualifierRoundId           = $abbreviateRoundName;
                            $qualifierTournamentId      = $tournamentName;
                            $qualifierType              = $qualifierJsonType;
                            $qualifierImage             = $qualifierData['beatmapset']['covers']['cover'];
                            $qualifierUrl               = $qualifierData['url'];
                            $qualifierName              = $qualifierData['beatmapset']['title'];
                            $qualifierDifficultyName    = $qualifierData['version'];
                            $qualifierFeatureArtist     = $qualifierData['beatmapset']['artist'];
                            $qualifierMapper            = $qualifierData['beatmapset']['creator'];
                            $qualifierMapperUrl         = "https://osu.ppy.sh/users/{$qualifierData['beatmapset']['user_id']}";
                            $qualifierDifficulty        = $qualifierData['difficulty_rating'];
                            $qualifierLength            = $qualifierData['total_length'];
                            $qualifierOverallSpeed      = $qualifierData['beatmapset']['bpm'];
                            $qualifierOverallDifficulty = $qualifierData['accuracy'];
                            $qualifierOverallHealth     = $qualifierData['drain'];

                            if (
                                !checkMappoolData(
                                    id: $qualifierId,
                                    round: $qualifierRoundId,
                                    tournament: strtoupper(string: $qualifierTournamentId)
                                )
                            ) {
                                createMappoolData(
                                    beatmap_id: $qualifierId,
                                    round_id: $qualifierRoundId,
                                    tournament_id: strtoupper(string: $qualifierTournamentId),
                                    type: $qualifierType,
                                    image: $qualifierImage,
                                    url: $qualifierUrl,
                                    name: $qualifierName,
                                    diff_name: $qualifierDifficultyName,
                                    fa: $qualifierFeatureArtist,
                                    mapper: $qualifierMapper,
                                    mapper_url: $qualifierMapperUrl,
                                    diff: $qualifierDifficulty,
                                    length: $qualifierLength,
                                    bpm: $qualifierOverallSpeed,
                                    od: $qualifierOverallDifficulty,
                                    hp: $qualifierOverallHealth
                                );
                            } else {
                                // TODO: UPDATE query here (change the 'view'
                                // table only, not the actual table if all data
                                // stay the same).
                                error_log(
                                    message: 'Mappool data exist, simply ignoring it...',
                                    message_type: 0
                                );
                            }
                        }
                        break;

                    // *** TOURNAMENT GROUP STAGE (WEEK 1) MAPPOOL DATA ***
                    case preg_match(
                        pattern: '/^(groupstageweek1|groupstagesweek1|gsw1|gssw1)$/i',
                        subject: $roundName
                    ):
                        appendJsonData(
                            json_data: $mappoolJsonData,
                            json_keys: [
                                strtoupper(string: $tournamentName),
                                // Database columns use abbrviation names for quick searching
                                'GSW1',
                                strtoupper(string: $beatmapType)
                            ],
                            json_value: $beatmapId,
                            json_file: $mappoolJsonFile
                        );

                        $mappoolGroupStageWeek1JsonData = viewJsonData(json_file: $mappoolJsonFile)[strtoupper(string: $tournamentName)][$abbreviateRoundName];
                        foreach ($mappoolGroupStageWeek1JsonData as $groupStageWeek1JsonType => $groupStageWeek1JsonId) {
                            $groupStageWeek1Data = getMappoolData(
                                id: $groupStageWeek1JsonId,
                                token: $mappoolAccessToken
                            );

                            $groupStageWeek1Id                = $groupStageWeek1Data['id'];
                            $groupStageWeek1RoundId           = $abbreviateRoundName;
                            $groupStageWeek1TournamentId      = $tournamentName;
                            $groupStageWeek1Type              = $groupStageWeek1JsonType;
                            $groupStageWeek1Image             = $groupStageWeek1Data['beatmapset']['covers']['cover'];
                            $groupStageWeek1Url               = $groupStageWeek1Data['url'];
                            $groupStageWeek1Name              = $groupStageWeek1Data['beatmapset']['title'];
                            $groupStageWeek1DifficultyName    = $groupStageWeek1Data['version'];
                            $groupStageWeek1FeatureArtist     = $groupStageWeek1Data['beatmapset']['artist'];
                            $groupStageWeek1Mapper            = $groupStageWeek1Data['beatmapset']['creator'];
                            $groupStageWeek1MapperUrl         = "https://osu.ppy.sh/users/{$groupStageWeek1Data['beatmapset']['user_id']}";
                            $groupStageWeek1Difficulty        = $groupStageWeek1Data['difficulty_rating'];
                            $groupStageWeek1Length            = $groupStageWeek1Data['total_length'];
                            $groupStageWeek1OverallSpeed      = $groupStageWeek1Data['beatmapset']['bpm'];
                            $groupStageWeek1OverallDifficulty = $groupStageWeek1Data['accuracy'];
                            $groupStageWeek1OverallHealth     = $groupStageWeek1Data['drain'];

                            if (
                                !checkMappoolData(
                                    id: $groupStageWeek1Id,
                                    round: $groupStageWeek1RoundId,
                                    tournament: strtoupper(string: $groupStageWeek1TournamentId)
                                )
                            ) {
                                createMappoolData(
                                    beatmap_id: $groupStageWeek1Id,
                                    round_id: $groupStageWeek1RoundId,
                                    tournament_id: strtoupper(string: $groupStageWeek1TournamentId),
                                    type: $groupStageWeek1Type,
                                    image: $groupStageWeek1Image,
                                    url: $groupStageWeek1Url,
                                    name: $groupStageWeek1Name,
                                    diff_name: $groupStageWeek1DifficultyName,
                                    fa: $groupStageWeek1FeatureArtist,
                                    mapper: $groupStageWeek1Mapper,
                                    mapper_url: $groupStageWeek1MapperUrl,
                                    diff: $groupStageWeek1Difficulty,
                                    length: $groupStageWeek1Length,
                                    bpm: $groupStageWeek1OverallSpeed,
                                    od: $groupStageWeek1OverallDifficulty,
                                    hp: $groupStageWeek1OverallHealth
                                );
                            } else {
                                // TODO: UPDATE query here (change the 'view'
                                // table only, not the actual table if all data
                                // stay the same).
                                error_log(
                                    message: 'Mappool data exist, simply ignoring it...',
                                    message_type: 0
                                );
                            }
                        }
                        break;

                    // *** TOURNAMENT GROUP STAGE (WEEK 2) MAPPOOL DATA ***
                    case preg_match(
                        pattern: '/^(groupstageweek2|groupstagesweek2|gsw2|gssw2)$/i',
                        subject: $roundName
                    ):
                        appendJsonData(
                            json_data: $mappoolJsonData,
                            json_keys: [
                                strtoupper(string: $tournamentName),
                                // Database columns use abbrviation names for quick searching
                                'GSW2',
                                strtoupper(string: $beatmapType)
                            ],
                            json_value: $beatmapId,
                            json_file: $mappoolJsonFile
                        );

                        $mappoolGroupStageWeek2JsonData = viewJsonData(json_file: $mappoolJsonFile)[strtoupper(string: $tournamentName)][$abbreviateRoundName];
                        foreach ($mappoolGroupStageWeek2JsonData as $groupStageWeek2JsonType => $groupStageWeek2JsonId) {
                            $groupStageWeek2Data = getMappoolData(
                                id: $groupStageWeek2JsonId,
                                token: $mappoolAccessToken
                            );

                            $groupStageWeek2Id                = $groupStageWeek2Data['id'];
                            $groupStageWeek2RoundId           = $abbreviateRoundName;
                            $groupStageWeek2TournamentId      = $tournamentName;
                            $groupStageWeek2Type              = $groupStageWeek2JsonType;
                            $groupStageWeek2Image             = $groupStageWeek2Data['beatmapset']['covers']['cover'];
                            $groupStageWeek2Url               = $groupStageWeek2Data['url'];
                            $groupStageWeek2Name              = $groupStageWeek2Data['beatmapset']['title'];
                            $groupStageWeek2DifficultyName    = $groupStageWeek2Data['version'];
                            $groupStageWeek2FeatureArtist     = $groupStageWeek2Data['beatmapset']['artist'];
                            $groupStageWeek2Mapper            = $groupStageWeek2Data['beatmapset']['creator'];
                            $groupStageWeek2MapperUrl         = "https://osu.ppy.sh/users/{$groupStageWeek2Data['beatmapset']['user_id']}";
                            $groupStageWeek2Difficulty        = $groupStageWeek2Data['difficulty_rating'];
                            $groupStageWeek2Length            = $groupStageWeek2Data['total_length'];
                            $groupStageWeek2OverallSpeed      = $groupStageWeek2Data['beatmapset']['bpm'];
                            $groupStageWeek2OverallDifficulty = $groupStageWeek2Data['accuracy'];
                            $groupStageWeek2OverallHealth     = $groupStageWeek2Data['drain'];

                            if (
                                !checkMappoolData(
                                    id: $groupStageWeek2Id,
                                    round: $groupStageWeek2RoundId,
                                    tournament: strtoupper(string: $groupStageWeek2TournamentId)
                                )
                            ) {
                                createMappoolData(
                                    beatmap_id: $groupStageWeek2Id,
                                    round_id: $groupStageWeek2RoundId,
                                    tournament_id: strtoupper(string: $groupStageWeek2TournamentId),
                                    type: $groupStageWeek2Type,
                                    image: $groupStageWeek2Image,
                                    url: $groupStageWeek2Url,
                                    name: $groupStageWeek2Name,
                                    diff_name: $groupStageWeek2DifficultyName,
                                    fa: $groupStageWeek2FeatureArtist,
                                    mapper: $groupStageWeek2Mapper,
                                    mapper_url: $groupStageWeek2MapperUrl,
                                    diff: $groupStageWeek2Difficulty,
                                    length: $groupStageWeek2Length,
                                    bpm: $groupStageWeek2OverallSpeed,
                                    od: $groupStageWeek2OverallDifficulty,
                                    hp: $groupStageWeek2OverallHealth
                                );
                            } else {
                                // TODO: UPDATE query here (change the 'view'
                                // table only, not the actual table if all data
                                // stay the same).
                                error_log(
                                    message: 'Mappool data exist, simply ignoring it...',
                                    message_type: 0
                                );
                            }
                        }
                        break;

                    // *** TOURNAMENT SEMI FINAL MAPPOOL DATA ***
                    case preg_match(
                        pattern: '/^(semifinal|semifinals|sf|sfs)$/i',
                        subject: $roundName
                    ):
                        appendJsonData(
                            json_data: $mappoolJsonData,
                            json_keys: [
                                strtoupper(string: $tournamentName),
                                // Database columns use abbrviation names for quick searching
                                'SF',
                                strtoupper(string: $beatmapType)
                            ],
                            json_value: $beatmapId,
                            json_file: $mappoolJsonFile
                        );

                        $mappoolSemiFinalJsonData = viewJsonData(json_file: $mappoolJsonFile)[strtoupper(string: $tournamentName)][$abbreviateRoundName];
                        foreach ($mappoolSemiFinalJsonData as $semiFinalJsonType => $semiFinalJsonId) {
                            $semiFinalData = getMappoolData(
                                id: $semiFinalJsonId,
                                token: $mappoolAccessToken
                            );

                            $semiFinalId                = $semiFinalData['id'];
                            $semiFinalRoundId           = $abbreviateRoundName;
                            $semiFinalTournamentId      = $tournamentName;
                            $semiFinalType              = $semiFinalJsonType;
                            $semiFinalImage             = $semiFinalData['beatmapset']['covers']['cover'];
                            $semiFinalUrl               = $semiFinalData['url'];
                            $semiFinalName              = $semiFinalData['beatmapset']['title'];
                            $semiFinalDifficultyName    = $semiFinalData['version'];
                            $semiFinalFeatureArtist     = $semiFinalData['beatmapset']['artist'];
                            $semiFinalMapper            = $semiFinalData['beatmapset']['creator'];
                            $semiFinalMapperUrl         = "https://osu.ppy.sh/users/{$semiFinalData['beatmapset']['user_id']}";
                            $semiFinalDifficulty        = $semiFinalData['difficulty_rating'];
                            $semiFinalLength            = $semiFinalData['total_length'];
                            $semiFinalOverallSpeed      = $semiFinalData['beatmapset']['bpm'];
                            $semiFinalOverallDifficulty = $semiFinalData['accuracy'];
                            $semiFinalOverallHealth     = $semiFinalData['drain'];

                            if (
                                !checkMappoolData(
                                    id: $semiFinalId,
                                    round: $semiFinalRoundId,
                                    tournament: strtoupper(string: $semiFinalTournamentId)
                                )
                            ) {
                                createMappoolData(
                                    beatmap_id: $semiFinalId,
                                    round_id: $semiFinalRoundId,
                                    tournament_id: strtoupper(string: $semiFinalTournamentId),
                                    type: $semiFinalType,
                                    image: $semiFinalImage,
                                    url: $semiFinalUrl,
                                    name: $semiFinalName,
                                    diff_name: $semiFinalDifficultyName,
                                    fa: $semiFinalFeatureArtist,
                                    mapper: $semiFinalMapper,
                                    mapper_url: $semiFinalMapperUrl,
                                    diff: $semiFinalDifficulty,
                                    length: $semiFinalLength,
                                    bpm: $semiFinalOverallSpeed,
                                    od: $semiFinalOverallDifficulty,
                                    hp: $semiFinalOverallHealth
                                );
                            } else {
                                // TODO: UPDATE query here (change the 'view'
                                // table only, not the actual table if all data
                                // stay the same).
                                error_log(
                                    message: 'Mappool data exist, simply ignoring it...',
                                    message_type: 0
                                );
                            }
                        }
                        break;

                    // *** TOURNAMENT FINAL MAPPOOL DATA ***
                    case preg_match(
                        pattern: '/^(final|finals|fnl|fnls)$/i',
                        subject: $roundName
                    ):
                        appendJsonData(
                            json_data: $mappoolJsonData,
                            json_keys: [
                                strtoupper(string: $tournamentName),
                                // Database columns use abbrviation names for quick searching
                                'FNL',
                                strtoupper(string: $beatmapType)
                            ],
                            json_value: $beatmapId,
                            json_file: $mappoolJsonFile
                        );

                        $mappoolFinalJsonData = viewJsonData(json_file: $mappoolJsonFile)[strtoupper(string: $tournamentName)][$abbreviateRoundName];
                        foreach ($mappoolFinalJsonData as $finalJsonType => $finalJsonId) {
                            $finalData = getMappoolData(
                                id: $finalJsonId,
                                token: $mappoolAccessToken
                            );

                            $finalId                = $finalData['id'];
                            $finalRoundId           = $abbreviateRoundName;
                            $finalTournamentId      = $tournamentName;
                            $finalType              = $finalJsonType;
                            $finalImage             = $finalData['beatmapset']['covers']['cover'];
                            $finalUrl               = $finalData['url'];
                            $finalName              = $finalData['beatmapset']['title'];
                            $finalDifficultyName    = $finalData['version'];
                            $finalFeatureArtist     = $finalData['beatmapset']['artist'];
                            $finalMapper            = $finalData['beatmapset']['creator'];
                            $finalMapperUrl         = "https://osu.ppy.sh/users/{$finalData['beatmapset']['user_id']}";
                            $finalDifficulty        = $finalData['difficulty_rating'];
                            $finalLength            = $finalData['total_length'];
                            $finalOverallSpeed      = $finalData['beatmapset']['bpm'];
                            $finalOverallDifficulty = $finalData['accuracy'];
                            $finalOverallHealth     = $finalData['drain'];

                            if (
                                !checkMappoolData(
                                    id: $finalId,
                                    round: $finalRoundId,
                                    tournament: strtoupper(string: $finalTournamentId)
                                )
                            ) {
                                createMappoolData(
                                    beatmap_id: $finalId,
                                    round_id: $finalRoundId,
                                    tournament_id: strtoupper(string: $finalTournamentId),
                                    type: $finalType,
                                    image: $finalImage,
                                    url: $finalUrl,
                                    name: $finalName,
                                    diff_name: $finalDifficultyName,
                                    fa: $finalFeatureArtist,
                                    mapper: $finalMapper,
                                    mapper_url: $finalMapperUrl,
                                    diff: $finalDifficulty,
                                    length: $finalLength,
                                    bpm: $finalOverallSpeed,
                                    od: $finalOverallDifficulty,
                                    hp: $finalOverallHealth
                                );
                            } else {
                                // TODO: UPDATE query here (change the 'view'
                                // table only, not the actual table if all data
                                // stay the same).
                                error_log(
                                    message: 'Mappool data exist, simply ignoring it...',
                                    message_type: 0
                                );
                            }
                        }
                        break;

                    // *** TOURNAMENT GRAND FINAL & ALL STAR MAPPOOL DATA ***
                    case preg_match(
                        pattern: '/^(grandfinal|grandfinals|gf|gfs)$/i',
                        subject: $roundName
                    ):
                    case preg_match(
                        pattern: '/^(allstar|allstars|astr|astrs)$/i',
                        subject: $roundName
                    ):

                        /*
                         *==========================================================
                         * Because [All STAR] mappool is basically the same as
                         * [GRAND FINAL] mappool, so I'll just being a bit lazy here
                         * by using the [GRAND FINAL] mappool data directly. This'll
                         * prevent me from adding the same beatmap ID into the JSON
                         * data.
                         *==========================================================
                         */

                        appendJsonData(
                            json_data: $mappoolJsonData,
                            json_keys: [
                                strtoupper(string: $tournamentName),
                                // Database columns use abbrviation names for quick searching
                                'GF',
                                strtoupper(string: $beatmapType)
                            ],
                            json_value: $beatmapId,
                            json_file: $mappoolJsonFile
                        );

                        $mappoolGrandFinalJsonData = viewJsonData(json_file: $mappoolJsonFile)[strtoupper(string: $tournamentName)][$abbreviateRoundName];
                        foreach ($mappoolGrandFinalJsonData as $grandFinalJsonType => $grandFinalJsonId) {
                            $grandFinalData = getMappoolData(
                                id: $grandFinalJsonId,
                                token: $mappoolAccessToken
                            );

                            $grandFinalId                = $grandFinalData['id'];
                            $grandFinalRoundId           = $abbreviateRoundName;
                            $grandFinalTournamentId      = $tournamentName;
                            $grandFinalType              = $grandFinalJsonType;
                            $grandFinalImage             = $grandFinalData['beatmapset']['covers']['cover'];
                            $grandFinalUrl               = $grandFinalData['url'];
                            $grandFinalName              = $grandFinalData['beatmapset']['title'];
                            $grandFinalDifficultyName    = $grandFinalData['version'];
                            $grandFinalFeatureArtist     = $grandFinalData['beatmapset']['artist'];
                            $grandFinalMapper            = $grandFinalData['beatmapset']['creator'];
                            $grandFinalMapperUrl         = "https://osu.ppy.sh/users/{$grandFinalData['beatmapset']['user_id']}";
                            $grandFinalDifficulty        = $grandFinalData['difficulty_rating'];
                            $grandFinalLength            = $grandFinalData['total_length'];
                            $grandFinalOverallSpeed      = $grandFinalData['beatmapset']['bpm'];
                            $grandFinalOverallDifficulty = $grandFinalData['accuracy'];
                            $grandFinalOverallHealth     = $grandFinalData['drain'];

                            if (
                                !checkMappoolData(
                                    id: $grandFinalId,
                                    round: $grandFinalRoundId,
                                    tournament: strtoupper(string: $grandFinalTournamentId)
                                )
                            ) {
                                createMappoolData(
                                    beatmap_id: $grandFinalId,
                                    round_id: $grandFinalRoundId,
                                    tournament_id: strtoupper(string: $grandFinalTournamentId),
                                    type: $grandFinalType,
                                    image: $grandFinalImage,
                                    url: $grandFinalUrl,
                                    name: $grandFinalName,
                                    diff_name: $grandFinalDifficultyName,
                                    fa: $grandFinalFeatureArtist,
                                    mapper: $grandFinalMapper,
                                    mapper_url: $grandFinalMapperUrl,
                                    diff: $grandFinalDifficulty,
                                    length: $grandFinalLength,
                                    bpm: $grandFinalOverallSpeed,
                                    od: $grandFinalOverallDifficulty,
                                    hp: $grandFinalOverallHealth
                                );
                            } else {
                                // TODO: UPDATE query here (change the 'view'
                                // table only, not the actual table if all data
                                // stay the same).
                                error_log(
                                    message: 'Mappool data exist, simply ignoring it...',
                                    message_type: 0
                                );
                            }
                        }
                        break;

                    default:
                        // TODO: proper handling
                        echo sprintf(
                            "There is no such round named [%s]. What are u tryin' to do bro...",
                            $roundName
                        );
                        break;
                }
                break;

            // *** VOT4 TOURNAMENT ***
            case preg_match(
                pattern: '/^(vot4|vietnameseosutaikotournament4)$/i',
                subject: $tournamentName
            ):
                // Round regex matching handling
                switch (true) {
                    // *** TOURNAMENT QUALIFIER MAPPOOL DATA ***
                    case preg_match(
                        pattern: '/^(qualifier|qualifiers|qlf|qlfs)$/i',
                        subject: $roundName
                    ):
                        // Database columns use abbrviation names for quick searching
                        $abbreviateRoundName = 'QLF';

                        appendJsonData(
                            json_data: $mappoolJsonData,
                            json_keys: [
                                strtoupper(string: $tournamentName),
                                $abbreviateRoundName,
                                strtoupper(string: $beatmapType)
                            ],
                            json_value: $beatmapId,
                            json_file: $mappoolJsonFile
                        );

                        $mappoolQualifierJsonData = viewJsonData(json_file: $mappoolJsonFile)[strtoupper(string: $tournamentName)][$abbreviateRoundName];
                        foreach ($mappoolQualifierJsonData as $qualifierJsonType => $qualifierJsonId) {
                            $qualifierData = getMappoolData(
                                id: $qualifierJsonId,
                                token: $mappoolAccessToken
                            );

                            $qualifierId                = $qualifierData['id'];
                            $qualifierRoundId           = $abbreviateRoundName;
                            $qualifierTournamentId      = $tournamentName;
                            $qualifierType              = $qualifierJsonType;
                            $qualifierImage             = $qualifierData['beatmapset']['covers']['cover'];
                            $qualifierUrl               = $qualifierData['url'];
                            $qualifierName              = $qualifierData['beatmapset']['title'];
                            $qualifierDifficultyName    = $qualifierData['version'];
                            $qualifierFeatureArtist     = $qualifierData['beatmapset']['artist'];
                            $qualifierMapper            = $qualifierData['beatmapset']['creator'];
                            $qualifierMapperUrl         = "https://osu.ppy.sh/users/{$qualifierData['beatmapset']['user_id']}";
                            $qualifierDifficulty        = $qualifierData['difficulty_rating'];
                            $qualifierLength            = $qualifierData['total_length'];
                            $qualifierOverallSpeed      = $qualifierData['beatmapset']['bpm'];
                            $qualifierOverallDifficulty = $qualifierData['accuracy'];
                            $qualifierOverallHealth     = $qualifierData['drain'];

                            if (
                                !checkMappoolData(
                                    id: $qualifierId,
                                    round: $qualifierRoundId,
                                    tournament: strtoupper(string: $qualifierTournamentId)
                                )
                            ) {
                                createMappoolData(
                                    beatmap_id: $qualifierId,
                                    round_id: $qualifierRoundId,
                                    tournament_id: strtoupper(string: $qualifierTournamentId),
                                    type: $qualifierType,
                                    image: $qualifierImage,
                                    url: $qualifierUrl,
                                    name: $qualifierName,
                                    diff_name: $qualifierDifficultyName,
                                    fa: $qualifierFeatureArtist,
                                    mapper: $qualifierMapper,
                                    mapper_url: $qualifierMapperUrl,
                                    diff: $qualifierDifficulty,
                                    length: $qualifierLength,
                                    bpm: $qualifierOverallSpeed,
                                    od: $qualifierOverallDifficulty,
                                    hp: $qualifierOverallHealth
                                );
                            } else {
                                // TODO: UPDATE query here (change the 'view'
                                // table only, not the actual table if all data
                                // stay the same).
                                error_log(
                                    message: 'Mappool data exist, simply ignoring it...',
                                    message_type: 0
                                );
                            }
                        }
                        break;

                    // *** TOURNAMENT ROUND OF 16 MAPPOOL DATA ***
                    case preg_match(
                        pattern: '/^(roundof16|roundsof16|ro16|rso16)$/i',
                        subject: $roundName
                    ):
                        appendJsonData(
                            json_data: $mappoolJsonData,
                            json_keys: [
                                strtoupper(string: $tournamentName),
                                // Database columns use abbrviation names for quick searching
                                'RO16',
                                strtoupper(string: $beatmapType)
                            ],
                            json_value: $beatmapId,
                            json_file: $mappoolJsonFile
                        );

                        $mappoolRoundOf16JsonData = viewJsonData(json_file: $mappoolJsonFile)[strtoupper(string: $tournamentName)][$abbreviateRoundName];
                        foreach ($mappoolRoundOf16JsonData as $roundOf16JsonType => $roundOf16JsonId) {
                            $roundOf16Data = getMappoolData(
                                id: $roundOf16JsonId,
                                token: $mappoolAccessToken
                            );

                            $roundOf16Id                = $roundOf16Data['id'];
                            $roundOf16RoundId           = $abbreviateRoundName;
                            $roundOf16TournamentId      = $tournamentName;
                            $roundOf16Type              = $roundOf16JsonType;
                            $roundOf16Image             = $roundOf16Data['beatmapset']['covers']['cover'];
                            $roundOf16Url               = $roundOf16Data['url'];
                            $roundOf16Name              = $roundOf16Data['beatmapset']['title'];
                            $roundOf16DifficultyName    = $roundOf16Data['version'];
                            $roundOf16FeatureArtist     = $roundOf16Data['beatmapset']['artist'];
                            $roundOf16Mapper            = $roundOf16Data['beatmapset']['creator'];
                            $roundOf16MapperUrl         = "https://osu.ppy.sh/users/{$roundOf16Data['beatmapset']['user_id']}";
                            $roundOf16Difficulty        = $roundOf16Data['difficulty_rating'];
                            $roundOf16Length            = $roundOf16Data['total_length'];
                            $roundOf16OverallSpeed      = $roundOf16Data['beatmapset']['bpm'];
                            $roundOf16OverallDifficulty = $roundOf16Data['accuracy'];
                            $roundOf16OverallHealth     = $roundOf16Data['drain'];

                            if (
                                !checkMappoolData(
                                    id: $roundOf16Id,
                                    round: $roundOf16RoundId,
                                    tournament: strtoupper(string: $roundOf16TournamentId)
                                )
                            ) {
                                createMappoolData(
                                    beatmap_id: $roundOf16Id,
                                    round_id: $roundOf16RoundId,
                                    tournament_id: strtoupper(string: $roundOf16TournamentId),
                                    type: $roundOf16Type,
                                    image: $roundOf16Image,
                                    url: $roundOf16Url,
                                    name: $roundOf16Name,
                                    diff_name: $roundOf16DifficultyName,
                                    fa: $roundOf16FeatureArtist,
                                    mapper: $roundOf16Mapper,
                                    mapper_url: $roundOf16MapperUrl,
                                    diff: $roundOf16Difficulty,
                                    length: $roundOf16Length,
                                    bpm: $roundOf16OverallSpeed,
                                    od: $roundOf16OverallDifficulty,
                                    hp: $roundOf16OverallHealth
                                );
                            } else {
                                // TODO: UPDATE query here (change the 'view'
                                // table only, not the actual table if all data
                                // stay the same).
                                error_log(
                                    message: 'Mappool data exist, simply ignoring it...',
                                    message_type: 0
                                );
                            }
                        }
                        break;

                    // *** TOURNAMENT QUARTER FINAL MAPPOOL DATA ***
                    case preg_match(
                        pattern: '/^(quarterfinal|quarterfinals|qf|qfs)$/i',
                        subject: $roundName
                    ):
                        appendJsonData(
                            json_data: $mappoolJsonData,
                            json_keys: [
                                strtoupper(string: $tournamentName),
                                // Database columns use abbrviation names for quick searching
                                'QF',
                                strtoupper(string: $beatmapType)
                            ],
                            json_value: $beatmapId,
                            json_file: $mappoolJsonFile
                        );

                        $mappoolQuarterFinalJsonData = viewJsonData(json_file: $mappoolJsonFile)[strtoupper(string: $tournamentName)][$abbreviateRoundName];
                        foreach ($mappoolQuarterFinalJsonData as $quarterFinalJsonType => $quarterFinalJsonId) {
                            $quarterFinalData = getMappoolData(
                                id: $quarterFinalJsonId,
                                token: $mappoolAccessToken
                            );

                            $quarterFinalId                = $quarterFinalData['id'];
                            $quarterFinalRoundId           = $abbreviateRoundName;
                            $quarterFinalTournamentId      = $tournamentName;
                            $quarterFinalType              = $quarterFinalJsonType;
                            $quarterFinalImage             = $quarterFinalData['beatmapset']['covers']['cover'];
                            $quarterFinalUrl               = $quarterFinalData['url'];
                            $quarterFinalName              = $quarterFinalData['beatmapset']['title'];
                            $quarterFinalDifficultyName    = $quarterFinalData['version'];
                            $quarterFinalFeatureArtist     = $quarterFinalData['beatmapset']['artist'];
                            $quarterFinalMapper            = $quarterFinalData['beatmapset']['creator'];
                            $quarterFinalMapperUrl         = "https://osu.ppy.sh/users/{$quarterFinalData['beatmapset']['user_id']}";
                            $quarterFinalDifficulty        = $quarterFinalData['difficulty_rating'];
                            $quarterFinalLength            = $quarterFinalData['total_length'];
                            $quarterFinalOverallSpeed      = $quarterFinalData['beatmapset']['bpm'];
                            $quarterFinalOverallDifficulty = $quarterFinalData['accuracy'];
                            $quarterFinalOverallHealth     = $quarterFinalData['drain'];

                            if (
                                !checkMappoolData(
                                    id: $quarterFinalId,
                                    round: $quarterFinalRoundId,
                                    tournament: strtoupper(string: $quarterFinalTournamentId)
                                )
                            ) {
                                createMappoolData(
                                    beatmap_id: $quarterFinalId,
                                    round_id: $quarterFinalRoundId,
                                    tournament_id: strtoupper(string: $quarterFinalTournamentId),
                                    type: $quarterFinalType,
                                    image: $quarterFinalImage,
                                    url: $quarterFinalUrl,
                                    name: $quarterFinalName,
                                    diff_name: $quarterFinalDifficultyName,
                                    fa: $quarterFinalFeatureArtist,
                                    mapper: $quarterFinalMapper,
                                    mapper_url: $quarterFinalMapperUrl,
                                    diff: $quarterFinalDifficulty,
                                    length: $quarterFinalLength,
                                    bpm: $quarterFinalOverallSpeed,
                                    od: $quarterFinalOverallDifficulty,
                                    hp: $quarterFinalOverallHealth
                                );
                            } else {
                                // TODO: UPDATE query here (change the 'view'
                                // table only, not the actual table if all data
                                // stay the same).
                                error_log(
                                    message: 'Mappool data exist, simply ignoring it...',
                                    message_type: 0
                                );
                            }
                        }
                        break;

                    // *** TOURNAMENT SEMI FINAL MAPPOOL DATA ***
                    case preg_match(
                        pattern: '/^(semifinal|semifinals|sf|sfs)$/i',
                        subject: $roundName
                    ):
                        appendJsonData(
                            json_data: $mappoolJsonData,
                            json_keys: [
                                strtoupper(string: $tournamentName),
                                // Database columns use abbrviation names for quick searching
                                'SF',
                                strtoupper(string: $beatmapType)
                            ],
                            json_value: $beatmapId,
                            json_file: $mappoolJsonFile
                        );

                        $mappoolSemiFinalJsonData = viewJsonData(json_file: $mappoolJsonFile)[strtoupper(string: $tournamentName)][$abbreviateRoundName];
                        foreach ($mappoolSemiFinalJsonData as $semiFinalJsonType => $semiFinalJsonId) {
                            $semiFinalData = getMappoolData(
                                id: $semiFinalJsonId,
                                token: $mappoolAccessToken
                            );

                            $semiFinalId                = $semiFinalData['id'];
                            $semiFinalRoundId           = $abbreviateRoundName;
                            $semiFinalTournamentId      = $tournamentName;
                            $semiFinalType              = $semiFinalJsonType;
                            $semiFinalImage             = $semiFinalData['beatmapset']['covers']['cover'];
                            $semiFinalUrl               = $semiFinalData['url'];
                            $semiFinalName              = $semiFinalData['beatmapset']['title'];
                            $semiFinalDifficultyName    = $semiFinalData['version'];
                            $semiFinalFeatureArtist     = $semiFinalData['beatmapset']['artist'];
                            $semiFinalMapper            = $semiFinalData['beatmapset']['creator'];
                            $semiFinalMapperUrl         = "https://osu.ppy.sh/users/{$semiFinalData['beatmapset']['user_id']}";
                            $semiFinalDifficulty        = $semiFinalData['difficulty_rating'];
                            $semiFinalLength            = $semiFinalData['total_length'];
                            $semiFinalOverallSpeed      = $semiFinalData['beatmapset']['bpm'];
                            $semiFinalOverallDifficulty = $semiFinalData['accuracy'];
                            $semiFinalOverallHealth     = $semiFinalData['drain'];

                            if (
                                !checkMappoolData(
                                    id: $semiFinalId,
                                    round: $semiFinalRoundId,
                                    tournament: strtoupper(string: $semiFinalTournamentId)
                                )
                            ) {
                                createMappoolData(
                                    beatmap_id: $semiFinalId,
                                    round_id: $semiFinalRoundId,
                                    tournament_id: strtoupper(string: $semiFinalTournamentId),
                                    type: $semiFinalType,
                                    image: $semiFinalImage,
                                    url: $semiFinalUrl,
                                    name: $semiFinalName,
                                    diff_name: $semiFinalDifficultyName,
                                    fa: $semiFinalFeatureArtist,
                                    mapper: $semiFinalMapper,
                                    mapper_url: $semiFinalMapperUrl,
                                    diff: $semiFinalDifficulty,
                                    length: $semiFinalLength,
                                    bpm: $semiFinalOverallSpeed,
                                    od: $semiFinalOverallDifficulty,
                                    hp: $semiFinalOverallHealth
                                );
                            } else {
                                // TODO: UPDATE query here (change the 'view'
                                // table only, not the actual table if all data
                                // stay the same).
                                error_log(
                                    message: 'Mappool data exist, simply ignoring it...',
                                    message_type: 0
                                );
                            }
                        }
                        break;

                    // *** TOURNAMENT FINAL MAPPOOL DATA ***
                    case preg_match(
                        pattern: '/^(final|finals|fnl|fnls)$/i',
                        subject: $roundName
                    ):
                        appendJsonData(
                            json_data: $mappoolJsonData,
                            json_keys: [
                                strtoupper(string: $tournamentName),
                                // Database columns use abbrviation names for quick searching
                                'FNL',
                                strtoupper(string: $beatmapType)
                            ],
                            json_value: $beatmapId,
                            json_file: $mappoolJsonFile
                        );

                        $mappoolFinalJsonData = viewJsonData(json_file: $mappoolJsonFile)[strtoupper(string: $tournamentName)][$abbreviateRoundName];
                        foreach ($mappoolFinalJsonData as $finalJsonType => $finalJsonId) {
                            $finalData = getMappoolData(
                                id: $finalJsonId,
                                token: $mappoolAccessToken
                            );

                            $finalId                = $finalData['id'];
                            $finalRoundId           = $abbreviateRoundName;
                            $finalTournamentId      = $tournamentName;
                            $finalType              = $finalJsonType;
                            $finalImage             = $finalData['beatmapset']['covers']['cover'];
                            $finalUrl               = $finalData['url'];
                            $finalName              = $finalData['beatmapset']['title'];
                            $finalDifficultyName    = $finalData['version'];
                            $finalFeatureArtist     = $finalData['beatmapset']['artist'];
                            $finalMapper            = $finalData['beatmapset']['creator'];
                            $finalMapperUrl         = "https://osu.ppy.sh/users/{$finalData['beatmapset']['user_id']}";
                            $finalDifficulty        = $finalData['difficulty_rating'];
                            $finalLength            = $finalData['total_length'];
                            $finalOverallSpeed      = $finalData['beatmapset']['bpm'];
                            $finalOverallDifficulty = $finalData['accuracy'];
                            $finalOverallHealth     = $finalData['drain'];

                            if (
                                !checkMappoolData(
                                    id: $finalId,
                                    round: $finalRoundId,
                                    tournament: strtoupper(string: $finalTournamentId)
                                )
                            ) {
                                createMappoolData(
                                    beatmap_id: $finalId,
                                    round_id: $finalRoundId,
                                    tournament_id: strtoupper(string: $finalTournamentId),
                                    type: $finalType,
                                    image: $finalImage,
                                    url: $finalUrl,
                                    name: $finalName,
                                    diff_name: $finalDifficultyName,
                                    fa: $finalFeatureArtist,
                                    mapper: $finalMapper,
                                    mapper_url: $finalMapperUrl,
                                    diff: $finalDifficulty,
                                    length: $finalLength,
                                    bpm: $finalOverallSpeed,
                                    od: $finalOverallDifficulty,
                                    hp: $finalOverallHealth
                                );
                            } else {
                                // TODO: UPDATE query here (change the 'view'
                                // table only, not the actual table if all data
                                // stay the same).
                                error_log(
                                    message: 'Mappool data exist, simply ignoring it...',
                                    message_type: 0
                                );
                            }
                        }
                        break;

                    // *** TOURNAMENT GRAND FINAL & ALL STAR MAPPOOL DATA ***
                    case preg_match(
                        pattern: '/^(grandfinal|grandfinals|gf|gfs)$/i',
                        subject: $roundName
                    ):
                    case preg_match(
                        pattern: '/^(allstar|allstars|astr|astrs)$/i',
                        subject: $roundName
                    ):

                        /*
                         *==========================================================
                         * Because [All STAR] mappool is basically the same as
                         * [GRAND FINAL] mappool, so I'll just being a bit lazy here
                         * by using the [GRAND FINAL] mappool data directly. This'll
                         * prevent me from adding the same beatmap ID into the JSON
                         * data.
                         *==========================================================
                         */

                        appendJsonData(
                            json_data: $mappoolJsonData,
                            json_keys: [
                                strtoupper(string: $tournamentName),
                                // Database columns use abbrviation names for quick searching
                                'GF',
                                strtoupper(string: $beatmapType)
                            ],
                            json_value: $beatmapId,
                            json_file: $mappoolJsonFile
                        );

                        $mappoolGrandFinalJsonData = viewJsonData(json_file: $mappoolJsonFile)[strtoupper(string: $tournamentName)][$abbreviateRoundName];
                        foreach ($mappoolGrandFinalJsonData as $grandFinalJsonType => $grandFinalJsonId) {
                            $grandFinalData = getMappoolData(
                                id: $grandFinalJsonId,
                                token: $mappoolAccessToken
                            );

                            $grandFinalId                = $grandFinalData['id'];
                            $grandFinalRoundId           = $abbreviateRoundName;
                            $grandFinalTournamentId      = $tournamentName;
                            $grandFinalType              = $grandFinalJsonType;
                            $grandFinalImage             = $grandFinalData['beatmapset']['covers']['cover'];
                            $grandFinalUrl               = $grandFinalData['url'];
                            $grandFinalName              = $grandFinalData['beatmapset']['title'];
                            $grandFinalDifficultyName    = $grandFinalData['version'];
                            $grandFinalFeatureArtist     = $grandFinalData['beatmapset']['artist'];
                            $grandFinalMapper            = $grandFinalData['beatmapset']['creator'];
                            $grandFinalMapperUrl         = "https://osu.ppy.sh/users/{$grandFinalData['beatmapset']['user_id']}";
                            $grandFinalDifficulty        = $grandFinalData['difficulty_rating'];
                            $grandFinalLength            = $grandFinalData['total_length'];
                            $grandFinalOverallSpeed      = $grandFinalData['beatmapset']['bpm'];
                            $grandFinalOverallDifficulty = $grandFinalData['accuracy'];
                            $grandFinalOverallHealth     = $grandFinalData['drain'];

                            if (
                                !checkMappoolData(
                                    id: $grandFinalId,
                                    round: $grandFinalRoundId,
                                    tournament: strtoupper(string: $grandFinalTournamentId)
                                )
                            ) {
                                createMappoolData(
                                    beatmap_id: $grandFinalId,
                                    round_id: $grandFinalRoundId,
                                    tournament_id: strtoupper(string: $grandFinalTournamentId),
                                    type: $grandFinalType,
                                    image: $grandFinalImage,
                                    url: $grandFinalUrl,
                                    name: $grandFinalName,
                                    diff_name: $grandFinalDifficultyName,
                                    fa: $grandFinalFeatureArtist,
                                    mapper: $grandFinalMapper,
                                    mapper_url: $grandFinalMapperUrl,
                                    diff: $grandFinalDifficulty,
                                    length: $grandFinalLength,
                                    bpm: $grandFinalOverallSpeed,
                                    od: $grandFinalOverallDifficulty,
                                    hp: $grandFinalOverallHealth
                                );
                            } else {
                                // TODO: UPDATE query here (change the 'view'
                                // table only, not the actual table if all data
                                // stay the same).
                                error_log(
                                    message: 'Mappool data exist, simply ignoring it...',
                                    message_type: 0
                                );
                            }
                        }
                        break;

                    default:
                        // TODO: proper handling
                        echo sprintf(
                            "There is no such round named [%s]. What are u tryin' to do bro...",
                            $roundName
                        );
                        break;
                }
                break;

            default:
                // TODO: proper handling
                echo sprintf(
                    "There is no such tournament named [%s]. What are u tryin' to do bro...",
                    $tournamentName
                );
                break;
        }


        // NOTE: maybe find a way the append multiple data in one go?
        echo "<a href='/setting/tournament'>Wanna do it again?</a>";
    }
}
