<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);


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

        $tournamentMappoolJsonData = [];

        $tournamentName  = $_GET['tournament'];
        $roundName       = $_GET['round'];
        $beatmapType     = $_POST['beatmapType'];
        $beatmapId       = (int)$_POST['beatmapId'];

        $tournamentMappoolJsonFile = __DIR__ . '/../../Datas/Tournament/Mappool' . ucfirst(string: $tournamentName) . "Data.json";

        if (!file_exists(filename: $tournamentMappoolJsonFile)) {
            switch (true) {
                // *** TOURNAMENT QUALIFIER MAPPOOL DATA ***
                case preg_match(
                    pattern: '/^(qualifier|qualifiers|qlf|qlfs)$/i',
                    subject: $roundName
                ):
                    // Database use the abbreviation of each round's name
                    $abbreviateRoundName = 'QLF';

                    $tournamentMappoolJsonData[strtoupper(string: $tournamentName)][$abbreviateRoundName][strtoupper(string: $beatmapType)] = $beatmapId;

                    $tournamentMappoolJsonNew = json_encode(
                        value: $tournamentMappoolJsonData,
                        flags: 0,
                        depth: 512
                    );

                    file_put_contents(
                        filename: $tournamentMappoolJsonFile,
                        data: $tournamentMappoolJsonNew,
                        flags: 0,
                        context: null
                    );
                    break;

                default:
                    require __DIR__ . '/../../Controllers/NavigationBarController.php';
                    break;
            }
        } else {
            require_once __DIR__ . '/../../Configurations/PrettyArray.php';

            $tournamentMappoolJsonViewable = file_get_contents(
                filename: $tournamentMappoolJsonFile,
                use_include_path: false,
                context: null,
                offset: 0,
                length: null
            );
            $tournamentMappoolJsonUsable = json_decode(
                json: $tournamentMappoolJsonViewable,
                associative: true
            );

            echo array_dump(array: $tournamentMappoolJsonUsable);
        }
    }
}
