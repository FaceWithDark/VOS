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
                http_response_code(404);
            }

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

                    foreach ($qualifierMappoolData as $qualifierBeatmapData) {
                        // echo '<pre>' . print_r($qualifierBeatmapData, true) . '</pre>';
                        $qualifierBeatmapDisplayType                = htmlspecialchars($qualifierBeatmapData['beatmapType']);
                        $qualifierBeatmapDisplayImage               = htmlspecialchars($qualifierBeatmapData['beatmapImage']);
                        $qualifierBeatmapDisplayUrl                 = htmlspecialchars($qualifierBeatmapData['beatmapUrl']);
                        $qualifierBeatmapDisplayName                = htmlspecialchars($qualifierBeatmapData['beatmapName']);
                        $qualifierBeatmapDisplayDifficultyName      = htmlspecialchars($qualifierBeatmapData['beatmapDifficultyName']);
                        $qualifierBeatmapDisplayFeatureArtist       = htmlspecialchars($qualifierBeatmapData['beatmapFeatureArtist']);
                        $qualifierBeatmapDisplayMapper              = htmlspecialchars($qualifierBeatmapData['beatmapMapper']);
                        $qualifierBeatmapDisplayMapperUrl           = htmlspecialchars($qualifierBeatmapData['beatmapMapperUrl']);
                        $qualifierBeatmapDisplayDifficulty          = htmlspecialchars($qualifierBeatmapData['beatmapDifficulty']);
                        $qualifierBeatmapDisplayLength              = timeStampFormat(number: $qualifierBeatmapData['beatmapLength']);
                        $qualifierBeatmapDisplayOverallSpeed        = sprintf('%.2f', $qualifierBeatmapData['beatmapOverallSpeed']);
                        $qualifierBeatmapDisplayOverallDifficulty   = sprintf('%.2f', $qualifierBeatmapData['beatmapOverallDifficulty']);
                        $qualifierBeatmapDisplayOverallHealth       = sprintf('%.2f', $qualifierBeatmapData['beatmapOverallHealth']);
                        $qualifierBeatmapDisplayPassCount           = $qualifierBeatmapData['beatmapPassCount'];

                        $qualifierBeatmapDisplayTemplate =
                            <<<EOL
                                <div class="box-container">
                                    <div class="song-type">
                                        <h1>$qualifierBeatmapDisplayType</h1>
                                    </div>

                                    <div class="song-image">
                                        <a href="$qualifierBeatmapDisplayUrl">
                                            <img src="$qualifierBeatmapDisplayImage" alt="VOT4 Beatmap Image">
                                        </a>
                                    </div>

                                    <div class="song-name">
                                        <h2>$qualifierBeatmapDisplayName [$qualifierBeatmapDisplayDifficultyName]</h2>
                                    </div>

                                    <div class="song-feature-artist">
                                        <h3>Song by $qualifierBeatmapDisplayFeatureArtist</h3>
                                    </div>

                                    <div class="song-mapper">
                                        <h4>Created by <a href="$qualifierBeatmapDisplayMapperUrl">$qualifierBeatmapDisplayMapper</a></h4>
                                    </div>

                                    <div class="song-attributes">
                                        <div class="song-star-rating">
                                            <p>SR:</p>
                                            <p>$qualifierBeatmapDisplayDifficulty</p>
                                        </div>
                                        <div class="song-length">
                                            <p>Length:</p>
                                            <p>$qualifierBeatmapDisplayLength</p>
                                        </div>
                                        <div class="song-speed">
                                            <p>BPM:</p>
                                            <p>$qualifierBeatmapDisplayOverallSpeed</p>
                                        </div>
                                    </div>

                                    <div class="song-attributes">
                                        <div class="song-od">
                                            <p>OD:</p>
                                            <p>$qualifierBeatmapDisplayOverallDifficulty</p>
                                        </div>
                                        <div class="song-hp">
                                            <p>HP:</p>
                                            <p>$qualifierBeatmapDisplayOverallHealth</p>
                                        </div>
                                        <div class="song-pass">
                                            <p>Pass:</p>
                                            <p>$qualifierBeatmapDisplayPassCount</p>
                                        </div>
                                    </div>
                                </div>
                                EOL;

                        // It would be much more nasty if I tried to output this using the traditional mixed HTML & PHP codes
                        echo $qualifierBeatmapDisplayTemplate;
                    }
                    break;

                case 'round_of_16':
                    $roundOf16RoundName         = $mappoolRoundAbbreviationNameData[1];
                    $roundOf16TournamentName    = explode(
                        separator: '/',
                        string: trim(
                            string: $_SERVER['REQUEST_URI'],
                            characters: '/'
                        ),
                        limit: PHP_INT_MAX
                    )[0];
                    $roundOf16Database        = $GLOBALS['votDatabaseHandle'];

                    $roundOf16MappoolData = readMappoolData(
                        round_name: $roundOf16RoundName,
                        tournament_name: $roundOf16TournamentName,
                        database_handle: $roundOf16Database
                    );

                    foreach ($roundOf16MappoolData as $roundOf16BeatmapData) {
                        // echo '<pre>' . print_r($roundOf16BeatmapData, true) . '</pre>';
                        $roundOf16BeatmapDisplayType                = htmlspecialchars($roundOf16BeatmapData['beatmapType']);
                        $roundOf16BeatmapDisplayImage               = htmlspecialchars($roundOf16BeatmapData['beatmapImage']);
                        $roundOf16BeatmapDisplayUrl                 = htmlspecialchars($roundOf16BeatmapData['beatmapUrl']);
                        $roundOf16BeatmapDisplayName                = htmlspecialchars($roundOf16BeatmapData['beatmapName']);
                        $roundOf16BeatmapDisplayDifficultyName      = htmlspecialchars($roundOf16BeatmapData['beatmapDifficultyName']);
                        $roundOf16BeatmapDisplayFeatureArtist       = htmlspecialchars($roundOf16BeatmapData['beatmapFeatureArtist']);
                        $roundOf16BeatmapDisplayMapper              = htmlspecialchars($roundOf16BeatmapData['beatmapMapper']);
                        $roundOf16BeatmapDisplayMapperUrl           = htmlspecialchars($roundOf16BeatmapData['beatmapMapperUrl']);
                        $roundOf16BeatmapDisplayDifficulty          = htmlspecialchars($roundOf16BeatmapData['beatmapDifficulty']);
                        $roundOf16BeatmapDisplayLength              = timeStampFormat(number: $roundOf16BeatmapData['beatmapLength']);
                        $roundOf16BeatmapDisplayOverallSpeed        = sprintf('%.2f', $roundOf16BeatmapData['beatmapOverallSpeed']);
                        $roundOf16BeatmapDisplayOverallDifficulty   = sprintf('%.2f', $roundOf16BeatmapData['beatmapOverallDifficulty']);
                        $roundOf16BeatmapDisplayOverallHealth       = sprintf('%.2f', $roundOf16BeatmapData['beatmapOverallHealth']);
                        $roundOf16BeatmapDisplayPassCount           = $roundOf16BeatmapData['beatmapPassCount'];

                        $roundOf16BeatmapDisplayTemplate =
                            <<<EOL
                                <div class="box-container">
                                    <div class="song-type">
                                        <h1>$roundOf16BeatmapDisplayType</h1>
                                    </div>

                                    <div class="song-image">
                                        <a href="$roundOf16BeatmapDisplayUrl">
                                            <img src="$roundOf16BeatmapDisplayImage" alt="VOT4 Beatmap Image">
                                        </a>
                                    </div>

                                    <div class="song-name">
                                        <h2>$roundOf16BeatmapDisplayName [$roundOf16BeatmapDisplayDifficultyName]</h2>
                                    </div>

                                    <div class="song-feature-artist">
                                        <h3>Song by $roundOf16BeatmapDisplayFeatureArtist</h3>
                                    </div>

                                    <div class="song-mapper">
                                        <h4>Created by <a href="$roundOf16BeatmapDisplayMapperUrl">$roundOf16BeatmapDisplayMapper</a></h4>
                                    </div>

                                    <div class="song-attributes">
                                        <div class="song-star-rating">
                                            <p>SR:</p>
                                            <p>$roundOf16BeatmapDisplayDifficulty</p>
                                        </div>
                                        <div class="song-length">
                                            <p>Length:</p>
                                            <p>$roundOf16BeatmapDisplayLength</p>
                                        </div>
                                        <div class="song-speed">
                                            <p>BPM:</p>
                                            <p>$roundOf16BeatmapDisplayOverallSpeed</p>
                                        </div>
                                    </div>

                                    <div class="song-attributes">
                                        <div class="song-od">
                                            <p>OD:</p>
                                            <p>$roundOf16BeatmapDisplayOverallDifficulty</p>
                                        </div>
                                        <div class="song-hp">
                                            <p>HP:</p>
                                            <p>$roundOf16BeatmapDisplayOverallHealth</p>
                                        </div>
                                        <div class="song-pass">
                                            <p>Pass:</p>
                                            <p>$roundOf16BeatmapDisplayPassCount</p>
                                        </div>
                                    </div>
                                </div>
                                EOL;

                        // It would be much more nasty if I tried to output this using the traditional mixed HTML & PHP codes
                        echo $roundOf16BeatmapDisplayTemplate;
                    }
                    break;

                case 'quarterfinals':
                    $quarterFinalRoundName         = $mappoolRoundAbbreviationNameData[2];
                    $quarterFinalTournamentName    = explode(
                        separator: '/',
                        string: trim(
                            string: $_SERVER['REQUEST_URI'],
                            characters: '/'
                        ),
                        limit: PHP_INT_MAX
                    )[0];
                    $quarterFinalDatabase        = $GLOBALS['votDatabaseHandle'];

                    $quarterFinalMappoolData = readMappoolData(
                        round_name: $quarterFinalRoundName,
                        tournament_name: $quarterFinalTournamentName,
                        database_handle: $quarterFinalDatabase
                    );

                    foreach ($quarterFinalMappoolData as $quarterFinalBeatmapData) {
                        // echo '<pre>' . print_r($quarterFinalBeatmapData, true) . '</pre>';
                        $quarterFinalBeatmapDisplayType                = htmlspecialchars($quarterFinalBeatmapData['beatmapType']);
                        $quarterFinalBeatmapDisplayImage               = htmlspecialchars($quarterFinalBeatmapData['beatmapImage']);
                        $quarterFinalBeatmapDisplayUrl                 = htmlspecialchars($quarterFinalBeatmapData['beatmapUrl']);
                        $quarterFinalBeatmapDisplayName                = htmlspecialchars($quarterFinalBeatmapData['beatmapName']);
                        $quarterFinalBeatmapDisplayDifficultyName      = htmlspecialchars($quarterFinalBeatmapData['beatmapDifficultyName']);
                        $quarterFinalBeatmapDisplayFeatureArtist       = htmlspecialchars($quarterFinalBeatmapData['beatmapFeatureArtist']);
                        $quarterFinalBeatmapDisplayMapper              = htmlspecialchars($quarterFinalBeatmapData['beatmapMapper']);
                        $quarterFinalBeatmapDisplayMapperUrl           = htmlspecialchars($quarterFinalBeatmapData['beatmapMapperUrl']);
                        $quarterFinalBeatmapDisplayDifficulty          = htmlspecialchars($quarterFinalBeatmapData['beatmapDifficulty']);
                        $quarterFinalBeatmapDisplayLength              = timeStampFormat(number: $quarterFinalBeatmapData['beatmapLength']);
                        $quarterFinalBeatmapDisplayOverallSpeed        = sprintf('%.2f', $quarterFinalBeatmapData['beatmapOverallSpeed']);
                        $quarterFinalBeatmapDisplayOverallDifficulty   = sprintf('%.2f', $quarterFinalBeatmapData['beatmapOverallDifficulty']);
                        $quarterFinalBeatmapDisplayOverallHealth       = sprintf('%.2f', $quarterFinalBeatmapData['beatmapOverallHealth']);
                        $quarterFinalBeatmapDisplayPassCount           = $quarterFinalBeatmapData['beatmapPassCount'];

                        $quarterFinalBeatmapDisplayTemplate =
                            <<<EOL
                                <div class="box-container">
                                    <div class="song-type">
                                        <h1>$quarterFinalBeatmapDisplayType</h1>
                                    </div>

                                    <div class="song-image">
                                        <a href="$quarterFinalBeatmapDisplayUrl">
                                            <img src="$quarterFinalBeatmapDisplayImage" alt="VOT4 Beatmap Image">
                                        </a>
                                    </div>

                                    <div class="song-name">
                                        <h2>$quarterFinalBeatmapDisplayName [$quarterFinalBeatmapDisplayDifficultyName]</h2>
                                    </div>

                                    <div class="song-feature-artist">
                                        <h3>Song by $quarterFinalBeatmapDisplayFeatureArtist</h3>
                                    </div>

                                    <div class="song-mapper">
                                        <h4>Created by <a href="$quarterFinalBeatmapDisplayMapperUrl">$quarterFinalBeatmapDisplayMapper</a></h4>
                                    </div>

                                    <div class="song-attributes">
                                        <div class="song-star-rating">
                                            <p>SR:</p>
                                            <p>$quarterFinalBeatmapDisplayDifficulty</p>
                                        </div>
                                        <div class="song-length">
                                            <p>Length:</p>
                                            <p>$quarterFinalBeatmapDisplayLength</p>
                                        </div>
                                        <div class="song-speed">
                                            <p>BPM:</p>
                                            <p>$quarterFinalBeatmapDisplayOverallSpeed</p>
                                        </div>
                                    </div>

                                    <div class="song-attributes">
                                        <div class="song-od">
                                            <p>OD:</p>
                                            <p>$quarterFinalBeatmapDisplayOverallDifficulty</p>
                                        </div>
                                        <div class="song-hp">
                                            <p>HP:</p>
                                            <p>$quarterFinalBeatmapDisplayOverallHealth</p>
                                        </div>
                                        <div class="song-pass">
                                            <p>Pass:</p>
                                            <p>$quarterFinalBeatmapDisplayPassCount</p>
                                        </div>
                                    </div>
                                </div>
                                EOL;

                        // It would be much more nasty if I tried to output this using the traditional mixed HTML & PHP codes
                        echo $quarterFinalBeatmapDisplayTemplate;
                    }
                    break;

                case 'semifinals':
                    $finalRoundName         = $mappoolRoundAbbreviationNameData[3];
                    $finalTournamentName    = explode(
                        separator: '/',
                        string: trim(
                            string: $_SERVER['REQUEST_URI'],
                            characters: '/'
                        ),
                        limit: PHP_INT_MAX
                    )[0];
                    $finalDatabase        = $GLOBALS['votDatabaseHandle'];

                    $finalMappoolData = readMappoolData(
                        round_name: $finalRoundName,
                        tournament_name: $finalTournamentName,
                        database_handle: $finalDatabase
                    );

                    foreach ($finalMappoolData as $finalBeatmapData) {
                        // echo '<pre>' . print_r($finalBeatmapData, true) . '</pre>';
                        $finalBeatmapDisplayType                = htmlspecialchars($finalBeatmapData['beatmapType']);
                        $finalBeatmapDisplayImage               = htmlspecialchars($finalBeatmapData['beatmapImage']);
                        $finalBeatmapDisplayUrl                 = htmlspecialchars($finalBeatmapData['beatmapUrl']);
                        $finalBeatmapDisplayName                = htmlspecialchars($finalBeatmapData['beatmapName']);
                        $finalBeatmapDisplayDifficultyName      = htmlspecialchars($finalBeatmapData['beatmapDifficultyName']);
                        $finalBeatmapDisplayFeatureArtist       = htmlspecialchars($finalBeatmapData['beatmapFeatureArtist']);
                        $finalBeatmapDisplayMapper              = htmlspecialchars($finalBeatmapData['beatmapMapper']);
                        $finalBeatmapDisplayMapperUrl           = htmlspecialchars($finalBeatmapData['beatmapMapperUrl']);
                        $finalBeatmapDisplayDifficulty          = htmlspecialchars($finalBeatmapData['beatmapDifficulty']);
                        $finalBeatmapDisplayLength              = timeStampFormat(number: $finalBeatmapData['beatmapLength']);
                        $finalBeatmapDisplayOverallSpeed        = sprintf('%.2f', $finalBeatmapData['beatmapOverallSpeed']);
                        $finalBeatmapDisplayOverallDifficulty   = sprintf('%.2f', $finalBeatmapData['beatmapOverallDifficulty']);
                        $finalBeatmapDisplayOverallHealth       = sprintf('%.2f', $finalBeatmapData['beatmapOverallHealth']);
                        $finalBeatmapDisplayPassCount           = $finalBeatmapData['beatmapPassCount'];

                        $finalBeatmapDisplayTemplate =
                            <<<EOL
                                <div class="box-container">
                                    <div class="song-type">
                                        <h1>$finalBeatmapDisplayType</h1>
                                    </div>

                                    <div class="song-image">
                                        <a href="$finalBeatmapDisplayUrl">
                                            <img src="$finalBeatmapDisplayImage" alt="VOT4 Beatmap Image">
                                        </a>
                                    </div>

                                    <div class="song-name">
                                        <h2>$finalBeatmapDisplayName [$finalBeatmapDisplayDifficultyName]</h2>
                                    </div>

                                    <div class="song-feature-artist">
                                        <h3>Song by $finalBeatmapDisplayFeatureArtist</h3>
                                    </div>

                                    <div class="song-mapper">
                                        <h4>Created by <a href="$finalBeatmapDisplayMapperUrl">$finalBeatmapDisplayMapper</a></h4>
                                    </div>

                                    <div class="song-attributes">
                                        <div class="song-star-rating">
                                            <p>SR:</p>
                                            <p>$finalBeatmapDisplayDifficulty</p>
                                        </div>
                                        <div class="song-length">
                                            <p>Length:</p>
                                            <p>$finalBeatmapDisplayLength</p>
                                        </div>
                                        <div class="song-speed">
                                            <p>BPM:</p>
                                            <p>$finalBeatmapDisplayOverallSpeed</p>
                                        </div>
                                    </div>

                                    <div class="song-attributes">
                                        <div class="song-od">
                                            <p>OD:</p>
                                            <p>$finalBeatmapDisplayOverallDifficulty</p>
                                        </div>
                                        <div class="song-hp">
                                            <p>HP:</p>
                                            <p>$finalBeatmapDisplayOverallHealth</p>
                                        </div>
                                        <div class="song-pass">
                                            <p>Pass:</p>
                                            <p>$finalBeatmapDisplayPassCount</p>
                                        </div>
                                    </div>
                                </div>
                                EOL;

                        // It would be much more nasty if I tried to output this using the traditional mixed HTML & PHP codes
                        echo $finalBeatmapDisplayTemplate;
                    }
                    break;

                case 'finals':
                    $finalRoundName         = $mappoolRoundAbbreviationNameData[4];
                    $finalTournamentName    = explode(
                        separator: '/',
                        string: trim(
                            string: $_SERVER['REQUEST_URI'],
                            characters: '/'
                        ),
                        limit: PHP_INT_MAX
                    )[0];
                    $finalDatabase        = $GLOBALS['votDatabaseHandle'];

                    $finalMappoolData = readMappoolData(
                        round_name: $finalRoundName,
                        tournament_name: $finalTournamentName,
                        database_handle: $finalDatabase
                    );

                    foreach ($finalMappoolData as $finalBeatmapData) {
                        // echo '<pre>' . print_r($finalBeatmapData, true) . '</pre>';
                        $finalBeatmapDisplayType                = htmlspecialchars($finalBeatmapData['beatmapType']);
                        $finalBeatmapDisplayImage               = htmlspecialchars($finalBeatmapData['beatmapImage']);
                        $finalBeatmapDisplayUrl                 = htmlspecialchars($finalBeatmapData['beatmapUrl']);
                        $finalBeatmapDisplayName                = htmlspecialchars($finalBeatmapData['beatmapName']);
                        $finalBeatmapDisplayDifficultyName      = htmlspecialchars($finalBeatmapData['beatmapDifficultyName']);
                        $finalBeatmapDisplayFeatureArtist       = htmlspecialchars($finalBeatmapData['beatmapFeatureArtist']);
                        $finalBeatmapDisplayMapper              = htmlspecialchars($finalBeatmapData['beatmapMapper']);
                        $finalBeatmapDisplayMapperUrl           = htmlspecialchars($finalBeatmapData['beatmapMapperUrl']);
                        $finalBeatmapDisplayDifficulty          = htmlspecialchars($finalBeatmapData['beatmapDifficulty']);
                        $finalBeatmapDisplayLength              = timeStampFormat(number: $finalBeatmapData['beatmapLength']);
                        $finalBeatmapDisplayOverallSpeed        = sprintf('%.2f', $finalBeatmapData['beatmapOverallSpeed']);
                        $finalBeatmapDisplayOverallDifficulty   = sprintf('%.2f', $finalBeatmapData['beatmapOverallDifficulty']);
                        $finalBeatmapDisplayOverallHealth       = sprintf('%.2f', $finalBeatmapData['beatmapOverallHealth']);
                        $finalBeatmapDisplayPassCount           = $finalBeatmapData['beatmapPassCount'];

                        $finalBeatmapDisplayTemplate =
                            <<<EOL
                                <div class="box-container">
                                    <div class="song-type">
                                        <h1>$finalBeatmapDisplayType</h1>
                                    </div>

                                    <div class="song-image">
                                        <a href="$finalBeatmapDisplayUrl">
                                            <img src="$finalBeatmapDisplayImage" alt="VOT4 Beatmap Image">
                                        </a>
                                    </div>

                                    <div class="song-name">
                                        <h2>$finalBeatmapDisplayName [$finalBeatmapDisplayDifficultyName]</h2>
                                    </div>

                                    <div class="song-feature-artist">
                                        <h3>Song by $finalBeatmapDisplayFeatureArtist</h3>
                                    </div>

                                    <div class="song-mapper">
                                        <h4>Created by <a href="$finalBeatmapDisplayMapperUrl">$finalBeatmapDisplayMapper</a></h4>
                                    </div>

                                    <div class="song-attributes">
                                        <div class="song-star-rating">
                                            <p>SR:</p>
                                            <p>$finalBeatmapDisplayDifficulty</p>
                                        </div>
                                        <div class="song-length">
                                            <p>Length:</p>
                                            <p>$finalBeatmapDisplayLength</p>
                                        </div>
                                        <div class="song-speed">
                                            <p>BPM:</p>
                                            <p>$finalBeatmapDisplayOverallSpeed</p>
                                        </div>
                                    </div>

                                    <div class="song-attributes">
                                        <div class="song-od">
                                            <p>OD:</p>
                                            <p>$finalBeatmapDisplayOverallDifficulty</p>
                                        </div>
                                        <div class="song-hp">
                                            <p>HP:</p>
                                            <p>$finalBeatmapDisplayOverallHealth</p>
                                        </div>
                                        <div class="song-pass">
                                            <p>Pass:</p>
                                            <p>$finalBeatmapDisplayPassCount</p>
                                        </div>
                                    </div>
                                </div>
                                EOL;

                        // It would be much more nasty if I tried to output this using the traditional mixed HTML & PHP codes
                        echo $finalBeatmapDisplayTemplate;
                    }
                    break;

                case 'grandfinals':
                    $grandFinalRoundName         = $mappoolRoundAbbreviationNameData[5];
                    $grandFinalTournamentName    = explode(
                        separator: '/',
                        string: trim(
                            string: $_SERVER['REQUEST_URI'],
                            characters: '/'
                        ),
                        limit: PHP_INT_MAX
                    )[0];
                    $grandFinalDatabase        = $GLOBALS['votDatabaseHandle'];

                    $grandFinalMappoolData = readMappoolData(
                        round_name: $grandFinalRoundName,
                        tournament_name: $grandFinalTournamentName,
                        database_handle: $grandFinalDatabase
                    );

                    foreach ($grandFinalMappoolData as $grandFinalBeatmapData) {
                        // echo '<pre>' . print_r($grandFinalBeatmapData, true) . '</pre>';
                        $grandFinalBeatmapDisplayType                = htmlspecialchars($grandFinalBeatmapData['beatmapType']);
                        $grandFinalBeatmapDisplayImage               = htmlspecialchars($grandFinalBeatmapData['beatmapImage']);
                        $grandFinalBeatmapDisplayUrl                 = htmlspecialchars($grandFinalBeatmapData['beatmapUrl']);
                        $grandFinalBeatmapDisplayName                = htmlspecialchars($grandFinalBeatmapData['beatmapName']);
                        $grandFinalBeatmapDisplayDifficultyName      = htmlspecialchars($grandFinalBeatmapData['beatmapDifficultyName']);
                        $grandFinalBeatmapDisplayFeatureArtist       = htmlspecialchars($grandFinalBeatmapData['beatmapFeatureArtist']);
                        $grandFinalBeatmapDisplayMapper              = htmlspecialchars($grandFinalBeatmapData['beatmapMapper']);
                        $grandFinalBeatmapDisplayMapperUrl           = htmlspecialchars($grandFinalBeatmapData['beatmapMapperUrl']);
                        $grandFinalBeatmapDisplayDifficulty          = htmlspecialchars($grandFinalBeatmapData['beatmapDifficulty']);
                        $grandFinalBeatmapDisplayLength              = timeStampFormat(number: $grandFinalBeatmapData['beatmapLength']);
                        $grandFinalBeatmapDisplayOverallSpeed        = sprintf('%.2f', $grandFinalBeatmapData['beatmapOverallSpeed']);
                        $grandFinalBeatmapDisplayOverallDifficulty   = sprintf('%.2f', $grandFinalBeatmapData['beatmapOverallDifficulty']);
                        $grandFinalBeatmapDisplayOverallHealth       = sprintf('%.2f', $grandFinalBeatmapData['beatmapOverallHealth']);
                        $grandFinalBeatmapDisplayPassCount           = $grandFinalBeatmapData['beatmapPassCount'];

                        $grandFinalBeatmapDisplayTemplate =
                            <<<EOL
                                <div class="box-container">
                                    <div class="song-type">
                                        <h1>$grandFinalBeatmapDisplayType</h1>
                                    </div>

                                    <div class="song-image">
                                        <a href="$grandFinalBeatmapDisplayUrl">
                                            <img src="$grandFinalBeatmapDisplayImage" alt="VOT4 Beatmap Image">
                                        </a>
                                    </div>

                                    <div class="song-name">
                                        <h2>$grandFinalBeatmapDisplayName [$grandFinalBeatmapDisplayDifficultyName]</h2>
                                    </div>

                                    <div class="song-feature-artist">
                                        <h3>Song by $grandFinalBeatmapDisplayFeatureArtist</h3>
                                    </div>

                                    <div class="song-mapper">
                                        <h4>Created by <a href="$grandFinalBeatmapDisplayMapperUrl">$grandFinalBeatmapDisplayMapper</a></h4>
                                    </div>

                                    <div class="song-attributes">
                                        <div class="song-star-rating">
                                            <p>SR:</p>
                                            <p>$grandFinalBeatmapDisplayDifficulty</p>
                                        </div>
                                        <div class="song-length">
                                            <p>Length:</p>
                                            <p>$grandFinalBeatmapDisplayLength</p>
                                        </div>
                                        <div class="song-speed">
                                            <p>BPM:</p>
                                            <p>$grandFinalBeatmapDisplayOverallSpeed</p>
                                        </div>
                                    </div>

                                    <div class="song-attributes">
                                        <div class="song-od">
                                            <p>OD:</p>
                                            <p>$grandFinalBeatmapDisplayOverallDifficulty</p>
                                        </div>
                                        <div class="song-hp">
                                            <p>HP:</p>
                                            <p>$grandFinalBeatmapDisplayOverallHealth</p>
                                        </div>
                                        <div class="song-pass">
                                            <p>Pass:</p>
                                            <p>$grandFinalBeatmapDisplayPassCount</p>
                                        </div>
                                    </div>
                                </div>
                                EOL;

                        // It would be much more nasty if I tried to output this using the traditional mixed HTML & PHP codes
                        echo $grandFinalBeatmapDisplayTemplate;
                    }
                    break;

                case 'allstars':
                    $allStarRoundName         = $mappoolRoundAbbreviationNameData[6];
                    $allStarTournamentName    = explode(
                        separator: '/',
                        string: trim(
                            string: $_SERVER['REQUEST_URI'],
                            characters: '/'
                        ),
                        limit: PHP_INT_MAX
                    )[0];
                    $allStarDatabase        = $GLOBALS['votDatabaseHandle'];

                    $allStarMappoolData = readMappoolData(
                        round_name: $allStarRoundName,
                        tournament_name: $allStarTournamentName,
                        database_handle: $allStarDatabase
                    );

                    foreach ($allStarMappoolData as $allStarBeatmapData) {
                        // echo '<pre>' . print_r($allStarBeatmapData, true) . '</pre>';
                        $allStarBeatmapDisplayType                = htmlspecialchars($allStarBeatmapData['beatmapType']);
                        $allStarBeatmapDisplayImage               = htmlspecialchars($allStarBeatmapData['beatmapImage']);
                        $allStarBeatmapDisplayUrl                 = htmlspecialchars($allStarBeatmapData['beatmapUrl']);
                        $allStarBeatmapDisplayName                = htmlspecialchars($allStarBeatmapData['beatmapName']);
                        $allStarBeatmapDisplayDifficultyName      = htmlspecialchars($allStarBeatmapData['beatmapDifficultyName']);
                        $allStarBeatmapDisplayFeatureArtist       = htmlspecialchars($allStarBeatmapData['beatmapFeatureArtist']);
                        $allStarBeatmapDisplayMapper              = htmlspecialchars($allStarBeatmapData['beatmapMapper']);
                        $allStarBeatmapDisplayMapperUrl           = htmlspecialchars($allStarBeatmapData['beatmapMapperUrl']);
                        $allStarBeatmapDisplayDifficulty          = htmlspecialchars($allStarBeatmapData['beatmapDifficulty']);
                        $allStarBeatmapDisplayLength              = timeStampFormat(number: $allStarBeatmapData['beatmapLength']);
                        $allStarBeatmapDisplayOverallSpeed        = sprintf('%.2f', $allStarBeatmapData['beatmapOverallSpeed']);
                        $allStarBeatmapDisplayOverallDifficulty   = sprintf('%.2f', $allStarBeatmapData['beatmapOverallDifficulty']);
                        $allStarBeatmapDisplayOverallHealth       = sprintf('%.2f', $allStarBeatmapData['beatmapOverallHealth']);
                        $allStarBeatmapDisplayPassCount           = $allStarBeatmapData['beatmapPassCount'];

                        $allStarBeatmapDisplayTemplate =
                            <<<EOL
                                <div class="box-container">
                                    <div class="song-type">
                                        <h1>$allStarBeatmapDisplayType</h1>
                                    </div>

                                    <div class="song-image">
                                        <a href="$allStarBeatmapDisplayUrl">
                                            <img src="$allStarBeatmapDisplayImage" alt="VOT4 Beatmap Image">
                                        </a>
                                    </div>

                                    <div class="song-name">
                                        <h2>$allStarBeatmapDisplayName [$allStarBeatmapDisplayDifficultyName]</h2>
                                    </div>

                                    <div class="song-feature-artist">
                                        <h3>Song by $allStarBeatmapDisplayFeatureArtist</h3>
                                    </div>

                                    <div class="song-mapper">
                                        <h4>Created by <a href="$allStarBeatmapDisplayMapperUrl">$allStarBeatmapDisplayMapper</a></h4>
                                    </div>

                                    <div class="song-attributes">
                                        <div class="song-star-rating">
                                            <p>SR:</p>
                                            <p>$allStarBeatmapDisplayDifficulty</p>
                                        </div>
                                        <div class="song-length">
                                            <p>Length:</p>
                                            <p>$allStarBeatmapDisplayLength</p>
                                        </div>
                                        <div class="song-speed">
                                            <p>BPM:</p>
                                            <p>$allStarBeatmapDisplayOverallSpeed</p>
                                        </div>
                                    </div>

                                    <div class="song-attributes">
                                        <div class="song-od">
                                            <p>OD:</p>
                                            <p>$allStarBeatmapDisplayOverallDifficulty</p>
                                        </div>
                                        <div class="song-hp">
                                            <p>HP:</p>
                                            <p>$allStarBeatmapDisplayOverallHealth</p>
                                        </div>
                                        <div class="song-pass">
                                            <p>Pass:</p>
                                            <p>$allStarBeatmapDisplayPassCount</p>
                                        </div>
                                    </div>
                                </div>
                                EOL;

                        // It would be much more nasty if I tried to output this using the traditional mixed HTML & PHP codes
                        echo $allStarBeatmapDisplayTemplate;
                    }
                    break;
            }
        }
    } else {
        // TODO: This is not working yet. Fix later (if possible).
        http_response_code(400);
    }
    ?>
</section>
