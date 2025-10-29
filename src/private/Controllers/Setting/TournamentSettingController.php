<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);


/* Desired format:
{
    "VOT5": {
        "QLF": {
            "NM1": 12345678,
        }
    }
}
*/
$jsonData = [];

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
        require __DIR__ . '/../../Controllers/NavigationBarController.php';

        $tournamentId   = strtoupper(string: $_GET['tournament']);
        $roundId        = strtoupper(string: $_GET['round']);
        $beatmapType    = strtoupper(string: $_POST['beatmapType']);
        $beatmapId      = (int)$_POST['beatmapId'];

        //TODO: regex case-insensitive for beatmap type AND round ID.
        $votMappoolJsonData = __DIR__ . '/../../Datas/Tournament/VotMappoolData.json';

        if (file_exists(filename: $votMappoolJsonData)) {
            require_once __DIR__ . '/../../Configurations/PrettyArray.php';

            $votMappoolJsonViewableData = file_get_contents(
                filename: $votMappoolJsonData,
                use_include_path: false,
                context: null,
                offset: 0,
                length: null
            );
            $votMappoolJsonUsableData = json_decode(
                json: $votMappoolJsonViewableData,
                associative: true
            );

            echo array_dump(array: $votMappoolJsonUsableData);
        } else {
            $jsonData[$tournamentId][$roundId][$beatmapType] = $beatmapId;

            $appendJsonData = json_encode(
                value: $jsonData,
                flags: 0,
                depth: 512
            );

            file_put_contents(
                filename: $votMappoolJsonData,
                data: $appendJsonData,
                flags: 0,
                context: null
            );
        }
    }
}
