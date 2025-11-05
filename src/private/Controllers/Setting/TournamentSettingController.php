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

                $mappoolQualiferJsonData = viewJsonData(json_file: $mappoolJsonFile)[strtoupper(string: $tournamentName)][$abbreviateRoundName];
                foreach ($mappoolQualiferJsonData as $qualifierJsonType => $qualifierJsonId) {
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
                break;

            default:
                // TODO: proper handling
                echo sprintf(
                    "There is no such round named [%s]. What are u tryin' to do bro...",
                    $roundName
                );
                break;
        }

        // NOTE: maybe find a way the append multiple data in one go?
        echo "<a href='/setting/tournament'>Wanna do it again?</a>";
    }
}
