<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require __DIR__ . '/../../Models/Mappool.php';
require __DIR__ . '/../../Configurations/Length.php';
?>

<header>
    <h1>VOT5 Mappool</h1>
</header>

<section class="vot5-mappool">
    <form action="/vot5/mappool" method="get">
        <button type="submit" name="round" value="QLF">Qualifiers</button>
        <button type="submit" name="round" value="RO16">Round Of 16</button>
        <button type="submit" name="round" value="QF">Quarter Finals</button>
        <button type="submit" name="round" value="SF">Semi Finals</button>
        <button type="submit" name="round" value="FNL">Finals</button>
        <button type="submit" name="round" value="GF">Grand Finals</button>
        <button type="submit" name="round" value="ASTR">All Stars</button>
    </form>

    <?php
    $vot5TournamentName = explode(
        separator: '/',
        string: trim(
            string: $_SERVER['REQUEST_URI'],
            characters: '/'
        ),
        limit: PHP_INT_MAX
    )[0];
    $vot5TournamentDatabase = $GLOBALS['votDatabaseHandle'];

    if (!isset($_GET['round'])) {
        echo 'Among Us';
    } else {
        $vot5TournamentRound = $_GET['round'];

        $vot5TournamentMappoolData = readBeatmapData(
            round_name: $vot5TournamentRound,
            tournament_name: $vot5TournamentName,
            database_handle: $vot5TournamentDatabase
        );

        foreach ($vot5TournamentMappoolData as $vot5TournamentBeatmapData) {
            $vot5TournamentBeatmapType                = htmlspecialchars($vot5TournamentBeatmapData['beatmapType']);
            $vot5TournamentBeatmapImage               = htmlspecialchars($vot5TournamentBeatmapData['beatmapImage']);
            $vot5TournamentBeatmapUrl                 = htmlspecialchars($vot5TournamentBeatmapData['beatmapUrl']);
            $vot5TournamentBeatmapName                = htmlspecialchars($vot5TournamentBeatmapData['beatmapName']);
            $vot5TournamentBeatmapDifficultyName      = htmlspecialchars($vot5TournamentBeatmapData['beatmapDifficultyName']);
            $vot5TournamentBeatmapFeatureArtist       = htmlspecialchars($vot5TournamentBeatmapData['beatmapFeatureArtist']);
            $vot5TournamentBeatmapMapper              = htmlspecialchars($vot5TournamentBeatmapData['beatmapMapper']);
            $vot5TournamentBeatmapMapperUrl           = htmlspecialchars($vot5TournamentBeatmapData['beatmapMapperUrl']);
            $vot5TournamentBeatmapDifficulty          = htmlspecialchars($vot5TournamentBeatmapData['beatmapDifficulty']);
            $vot5TournamentBeatmapLength              = timeStampFormat(number: $vot5TournamentBeatmapData['beatmapLength']);
            $vot5TournamentBeatmapOverallSpeed        = sprintf('%.2f', $vot5TournamentBeatmapData['beatmapOverallSpeed']);
            $vot5TournamentBeatmapOverallDifficulty   = sprintf('%.2f', $vot5TournamentBeatmapData['beatmapOverallDifficulty']);
            $vot5TournamentBeatmapOverallHealth       = sprintf('%.2f', $vot5TournamentBeatmapData['beatmapOverallHealth']);
            $vot5TournamentBeatmapPassCount           = $vot5TournamentBeatmapData['beatmapPassCount'];

            $beatmapDisplayTemplate =
                <<<EOL
                <div class="box-container">
                    <div class="song-type">
                        <h1>$vot5TournamentBeatmapType</h1>
                    </div>

                    <div class="song-image">
                        <a href="$vot5TournamentBeatmapUrl">
                            <img src="$vot5TournamentBeatmapImage" alt="vot5 Beatmap Image">
                        </a>
                    </div>

                    <div class="song-name">
                        <h2>$vot5TournamentBeatmapName [$vot5TournamentBeatmapDifficultyName]</h2>
                    </div>

                    <div class="song-feature-artist">
                        <h3>Song by $vot5TournamentBeatmapFeatureArtist</h3>
                    </div>

                    <div class="song-mapper">
                        <h4>Created by <a href="$vot5TournamentBeatmapMapperUrl">$vot5TournamentBeatmapMapper</a></h4>
                    </div>

                    <div class="song-attributes">
                        <div class="song-star-rating">
                            <p>SR:</p>
                            <p>$vot5TournamentBeatmapDifficulty</p>
                        </div>
                        <div class="song-length">
                            <p>Length:</p>
                            <p>$vot5TournamentBeatmapLength</p>
                        </div>
                        <div class="song-speed">
                            <p>BPM:</p>
                            <p>$vot5TournamentBeatmapOverallSpeed</p>
                        </div>
                    </div>

                    <div class="song-attributes">
                        <div class="song-od">
                            <p>OD:</p>
                            <p>$vot5TournamentBeatmapOverallDifficulty</p>
                        </div>
                        <div class="song-hp">
                            <p>HP:</p>
                            <p>$vot5TournamentBeatmapOverallHealth</p>
                        </div>
                        <div class="song-pass">
                            <p>Pass:</p>
                            <p>$vot5TournamentBeatmapPassCount</p>
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
