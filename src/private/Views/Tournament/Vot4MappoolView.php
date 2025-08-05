<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require __DIR__ . '/../../Models/Mappool.php';
require __DIR__ . '/../../Configurations/Length.php';
?>

<header>
    <h1>VOT4 Mappool</h1>
</header>

<section class="vot4-mappool">
    <form action="/vot4/mappool" method="get">
        <button type="submit" name="round" value="QLF">Qualifiers</button>
        <button type="submit" name="round" value="RO16">Round Of 16</button>
        <button type="submit" name="round" value="QF">Quarter Finals</button>
        <button type="submit" name="round" value="SF">Semi Finals</button>
        <button type="submit" name="round" value="FNL">Finals</button>
        <button type="submit" name="round" value="GF">Grand Finals</button>
        <button type="submit" name="round" value="ASTR">All Stars</button>
    </form>

    <?php
    $vot4TournamentName = explode(
        separator: '/',
        string: trim(
            string: $_SERVER['REQUEST_URI'],
            characters: '/'
        ),
        limit: PHP_INT_MAX
    )[0];
    $vot4TournamentDatabase = $GLOBALS['votDatabaseHandle'];

    if (!isset($_GET['round'])) {
        echo 'Among Us';
    } else {
        $vot4TournamentRound = $_GET['round'];

        $vot4TournamentMappoolData = readBeatmapData(
            round_name: $vot4TournamentRound,
            tournament_name: $vot4TournamentName,
            database_handle: $vot4TournamentDatabase
        );

        foreach ($vot4TournamentMappoolData as $vot4TournamentBeatmapData) {
            $vot4TournamentBeatmapType                = htmlspecialchars($vot4TournamentBeatmapData['beatmapType']);
            $vot4TournamentBeatmapImage               = htmlspecialchars($vot4TournamentBeatmapData['beatmapImage']);
            $vot4TournamentBeatmapUrl                 = htmlspecialchars($vot4TournamentBeatmapData['beatmapUrl']);
            $vot4TournamentBeatmapName                = htmlspecialchars($vot4TournamentBeatmapData['beatmapName']);
            $vot4TournamentBeatmapDifficultyName      = htmlspecialchars($vot4TournamentBeatmapData['beatmapDifficultyName']);
            $vot4TournamentBeatmapFeatureArtist       = htmlspecialchars($vot4TournamentBeatmapData['beatmapFeatureArtist']);
            $vot4TournamentBeatmapMapper              = htmlspecialchars($vot4TournamentBeatmapData['beatmapMapper']);
            $vot4TournamentBeatmapMapperUrl           = htmlspecialchars($vot4TournamentBeatmapData['beatmapMapperUrl']);
            $vot4TournamentBeatmapDifficulty          = htmlspecialchars($vot4TournamentBeatmapData['beatmapDifficulty']);
            $vot4TournamentBeatmapLength              = timeStampFormat(number: $vot4TournamentBeatmapData['beatmapLength']);
            $vot4TournamentBeatmapOverallSpeed        = sprintf('%.2f', $vot4TournamentBeatmapData['beatmapOverallSpeed']);
            $vot4TournamentBeatmapOverallDifficulty   = sprintf('%.2f', $vot4TournamentBeatmapData['beatmapOverallDifficulty']);
            $vot4TournamentBeatmapOverallHealth       = sprintf('%.2f', $vot4TournamentBeatmapData['beatmapOverallHealth']);
            $vot4TournamentBeatmapPassCount           = $vot4TournamentBeatmapData['beatmapPassCount'];

            $beatmapDisplayTemplate =
                <<<EOL
                <div class="box-container">
                    <div class="song-type">
                        <h1>$vot4TournamentBeatmapType</h1>
                    </div>

                    <div class="song-image">
                        <a href="$vot4TournamentBeatmapUrl">
                            <img src="$vot4TournamentBeatmapImage" alt="VOT4 Beatmap Image">
                        </a>
                    </div>

                    <div class="song-name">
                        <h2>$vot4TournamentBeatmapName [$vot4TournamentBeatmapDifficultyName]</h2>
                    </div>

                    <div class="song-feature-artist">
                        <h3>Song by $vot4TournamentBeatmapFeatureArtist</h3>
                    </div>

                    <div class="song-mapper">
                        <h4>Created by <a href="$vot4TournamentBeatmapMapperUrl">$vot4TournamentBeatmapMapper</a></h4>
                    </div>

                    <div class="song-attributes">
                        <div class="song-star-rating">
                            <p>SR:</p>
                            <p>$vot4TournamentBeatmapDifficulty</p>
                        </div>
                        <div class="song-length">
                            <p>Length:</p>
                            <p>$vot4TournamentBeatmapLength</p>
                        </div>
                        <div class="song-speed">
                            <p>BPM:</p>
                            <p>$vot4TournamentBeatmapOverallSpeed</p>
                        </div>
                    </div>

                    <div class="song-attributes">
                        <div class="song-od">
                            <p>OD:</p>
                            <p>$vot4TournamentBeatmapOverallDifficulty</p>
                        </div>
                        <div class="song-hp">
                            <p>HP:</p>
                            <p>$vot4TournamentBeatmapOverallHealth</p>
                        </div>
                        <div class="song-pass">
                            <p>Pass:</p>
                            <p>$vot4TournamentBeatmapPassCount</p>
                        </div>
                    </div>
                </div>
                EOL;

            // It would be much more nasty if I tried to output this using the traditional mixed HTML & PHP codes
            echo $beatmapDisplayTemplate;
        }
    }
    ?>
</section>
