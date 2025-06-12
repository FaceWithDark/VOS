<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require __DIR__ . '/../../Configurations/Database.php';
require __DIR__ . '/../../Models/Mappool.php';
require __DIR__ . '/../../Configurations/Length.php';

if (isset($_COOKIE['vot_access_token'])) {
    if (isset($_GET['round'])) {
        // Variable scoping in PHP is a bit weird somehow: https://www.php.net/manual/en/language.variables.scope.php
        $allMappoolData = readMappoolData(
            round_name: strtoupper(string: $_GET['round']),
            tournament_name: trim(
                string: (
                    parse_url(
                        url: $_SERVER['REQUEST_URI'],
                        component: PHP_URL_PATH
                    )
                ),
                characters: '/'
            ),
            database_handle: $GLOBALS['votDatabaseHandle']
        );
        foreach ($allMappoolData as $mappoolData) {
            // echo '<pre>' . print_r($mappoolData, true) . '</pre>';
            $beatmapDisplayType                = htmlspecialchars($mappoolData['beatmapType']);
            $beatmapDisplayImage               = htmlspecialchars($mappoolData['beatmapImage']);
            $beatmapDisplayUrl                 = htmlspecialchars($mappoolData['beatmapUrl']);
            $beatmapDisplayName                = htmlspecialchars($mappoolData['beatmapName']);
            $beatmapDisplayDifficultyName      = htmlspecialchars($mappoolData['beatmapDifficultyName']);
            $beatmapDisplayFeatureArtist       = htmlspecialchars($mappoolData['beatmapFeatureArtist']);
            $beatmapDisplayMapper              = htmlspecialchars($mappoolData['beatmapMapper']);
            $beatmapDisplayMapperUrl           = htmlspecialchars($mappoolData['beatmapMapperUrl']);
            $beatmapDisplayDifficulty          = htmlspecialchars($mappoolData['beatmapDifficulty']);
            $beatmapDisplayLength              = timeStampFormat(number: $mappoolData['beatmapLength']);
            $beatmapDisplayOverallSpeed        = sprintf('%.2f', $mappoolData['beatmapOverallSpeed']);
            $beatmapDisplayOverallDifficulty   = sprintf('%.2f', $mappoolData['beatmapOverallDifficulty']);
            $beatmapDisplayOverallHealth       = sprintf('%.2f', $mappoolData['beatmapOverallHealth']);
            $beatmapDisplayPassCount           = $mappoolData['beatmapPassCount'];

            // It would be much more nasty if I tried to output this using the traditional mixed HTML & PHP codes
            echo <<<EOL
                <div class="box-container">
                    <div class="song-type">
                        <h1>$beatmapDisplayType</h1>
                    </div>

                    <div class="song-image">
                        <a href="$beatmapDisplayUrl">
                            <img src="$beatmapDisplayImage" alt="VOT4 Beatmap Image">
                        </a>
                    </div>

                    <div class="song-name">
                        <h2>$beatmapDisplayName [$beatmapDisplayDifficultyName]</h2>
                    </div>

                    <div class="song-feature-artist">
                        <h3>Song by $beatmapDisplayFeatureArtist</h3>
                    </div>

                    <div class="song-mapper">
                        <h4>Created by <a href="$beatmapDisplayMapperUrl">$beatmapDisplayMapper</a></h4>
                    </div>

                    <div class="song-attributes">
                        <div class="song-star-rating">
                            <p>SR:</p>
                            <p>$beatmapDisplayDifficulty</p>
                        </div>
                        <div class="song-length">
                            <p>Length:</p>
                            <p>$beatmapDisplayLength</p>
                        </div>
                        <div class="song-speed">
                            <p>BPM:</p>
                            <p>$beatmapDisplayOverallSpeed</p>
                        </div>
                    </div>

                    <div class="song-attributes">
                        <div class="song-od">
                            <p>OD:</p>
                            <p>$beatmapDisplayOverallDifficulty</p>
                        </div>
                        <div class="song-hp">
                            <p>HP:</p>
                            <p>$beatmapDisplayOverallHealth</p>
                        </div>
                        <div class="song-pass">
                            <p>Pass:</p>
                            <p>$beatmapDisplayPassCount</p>
                        </div>
                    </div>
                </div>
            EOL;
        }
    }
} else {
    // TODO: This is not working yet. Fix later (if possible).
    http_response_code(400);
}
