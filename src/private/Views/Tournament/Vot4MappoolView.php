<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require __DIR__ . '/../../Configurations/Database.php';
require __DIR__ . '/../../Models/Mappool.php';
require __DIR__ . '/../../Configurations/Length.php';

$mappoolRoundAbbreviationNames = [
    'qualifiers'        => 'QLF',
    'round_of_16'       => 'RO16',
    'quarterfinals'     => 'QF',
    'semifinals'        => 'SF',
    'finals'            => 'FNL',
    'grandfinals'       => 'GF',
    'allstars'          => 'ASTR',
];

$mappoolRoundAbbreviationNameData = [];
?>


<header>
    <h1>VOT4 Mappool</h1>
</header>

<section class="vot4-mappool">
    <form action="/vot4/mappool" method="get">
        <button type="submit" name="round" value="qualifiers">Qualifiers</button>
        <button type="submit" name="round" value="round_of_16">Round Of 16</button>
        <button type="submit" name="round" value="quarterfinals">Quarter Finals</button>
        <button type="submit" name="round" value="semifinals">Semi Finals</button>
        <button type="submit" name="round" value="finals">Finals</button>
        <button type="submit" name="round" value="grandfinals">Grand Finals</button>
        <button type="submit" name="round" value="allstars">All Stars</button>
    </form>

    <?php
    if (isset($_COOKIE['vot_access_token'])) {
        if (isset($_GET['round'])) {
            $mappoolRoundName = $_GET['round'];

            if (array_key_exists(key: $mappoolRoundName, array: $mappoolRoundAbbreviationNames)) {
                foreach ($mappoolRoundAbbreviationNames as $mappoolRoundAbbreviationName) {
                    $mappoolRoundAbbreviationNameData[] = $mappoolRoundAbbreviationName;
                }
            } else {
                echo 'Sus';
            }

            // echo '<pre>' . print_r($mappoolRoundAbbreviationNameData, true) . '</pre>';

            switch ($mappoolRoundName) {
                case 'qualifiers':
                    $qualifierRoundName         = $mappoolRoundAbbreviationNameData[0];
                    $qualifierTournamentName    = explode(
                        separator: '/',
                        string: trim(
                            string: $_SERVER['REQUEST_URI'],
                            characters: '/'
                        ),
                        limit: PHP_INT_MAX
                    )[0];
                    $qualifierDatabase        = $GLOBALS['votDatabaseHandle'];

                    $qualifierMappoolData = readMappoolData(
                        round_name: $qualifierRoundName,
                        tournament_name: $qualifierTournamentName,
                        database_handle: $qualifierDatabase
                    );

                    foreach ($qualifierMappoolData as $mappoolData) {
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

                        $beatmapDisplayTemplate =
                            <<<EOL
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

                        // It would be much more nasty if I tried to output this using the traditional mixed HTML & PHP codes
                        echo $beatmapDisplayTemplate;
                    }
            }
        }
    } else {
        // TODO: This is not working yet. Fix later (if possible).
        http_response_code(400);
    }
    ?>
</section>
