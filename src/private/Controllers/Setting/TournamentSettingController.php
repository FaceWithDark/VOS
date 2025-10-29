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
        require __DIR__ . '/../../Controllers/NavigationBarController.php';

        $beatmapType    = $_POST['beatmapType'];
        $beatmapId      = (int)$_POST['beatmapId'];

        //TODO: regex case-insensitive for beatmap type.
        echo sprintf(
            "Mappool search data: [%s], [%d]",
            $beatmapType,
            $beatmapId
        );

        /* $votMappoolJsonData = __DIR__ . '/../../Datas/Tournament/VotMappoolData.json';

        if (!file_exists(filename: $votMappoolJsonData)) {
            echo "Sus?";
        } else {
            $a = file_get_contents($votMappoolJsonData);
            $b = json_decode($a, true);

            require_once __DIR__ . '/../../Configurations/PrettyArray.php';
            echo array_dump(array: $b);
        } */
    }
}
