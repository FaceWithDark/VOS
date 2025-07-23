<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require __DIR__ . '/../../Configurations/Database.php';
require __DIR__ . '/../../Models/Song.php';
require __DIR__ . '/../../Configurations/Length.php';
?>

<header>
    <h1>Our Banger VOT Song &#128293</h1>
</header>

<section class="custom-song-vot-page">
    <form action="/song/vot" method="get">
        <button type="submit" name="tournament" value="default">Default</button>
        <button type="submit" name="tournament" value="vot4">VOT4</button>
        <button type="submit" name="tournament" value="vot3">VOT3</button>
        <button type="submit" name="tournament" value="vot2">VOT2</button>
        <button type="submit" name="tournament" value="vot1">VOT1</button>
    </form>

    <?php
    require __DIR__ . '/../../Configurations/PrettyArray.php';
    if (isset($_COOKIE['vot_access_token'])) {
        if (isset($_GET['tournament'])) {
            $votCustomSongLocation = $_GET['tournament'];
            $votCustomSongDatabase = $GLOBALS['votDatabaseHandle'];

            switch ($votCustomSongLocation) {
                case 'vot4':
                    $customSongFetchedData = readCustomSongData(
                        tournament_name: $votCustomSongLocation,
                        database_handle: $votCustomSongDatabase
                    );

                    foreach ($customSongFetchedData as $customSongDisplayData) {
                        // echo array_dump(array: $customSongDisplayData);
                        $customSongDisplayTournament          = htmlspecialchars($customSongDisplayData['tournamentName']);
                        $customSongDisplayRound               = htmlspecialchars($customSongDisplayData['roundName']);
                        $customSongDisplayType                = htmlspecialchars($customSongDisplayData['beatmapType']);
                        $customSongDisplayImage               = htmlspecialchars($customSongDisplayData['beatmapImage']);
                        $customSongDisplayUrl                 = htmlspecialchars($customSongDisplayData['beatmapUrl']);
                        $customSongDisplayName                = htmlspecialchars($customSongDisplayData['beatmapName']);
                        $customSongDisplayDifficultyName      = htmlspecialchars($customSongDisplayData['beatmapDifficultyName']);
                        $customSongDisplayFeatureArtist       = htmlspecialchars($customSongDisplayData['beatmapFeatureArtist']);
                        $customSongDisplayMapper              = htmlspecialchars($customSongDisplayData['beatmapMapper']);
                        $customSongDisplayMapperUrl           = htmlspecialchars($customSongDisplayData['beatmapMapperUrl']);
                        $customSongDisplayDifficulty          = htmlspecialchars($customSongDisplayData['beatmapDifficulty']);
                        $customSongDisplayLength              = timeStampFormat(number: $customSongDisplayData['beatmapLength']);
                        $customSongDisplayOverallSpeed        = sprintf('%.2f', $customSongDisplayData['beatmapOverallSpeed']);
                        $customSongDisplayOverallDifficulty   = sprintf('%.2f', $customSongDisplayData['beatmapOverallDifficulty']);
                        $customSongDisplayOverallHealth       = sprintf('%.2f', $customSongDisplayData['beatmapOverallHealth']);
                        $customSongDisplayPassCount           = $customSongDisplayData['beatmapPassCount'];

                        $customSongDisplayTemplate =
                            <<<EOL
                                <div class="box-container">
                                    <div class="custom-song-header">
                                        <div class="custom-song-tournament">
                                            <h1>- $customSongDisplayTournament -</h1>
                                        </div>
                                        <div class="custom-song-round">
                                            <h1>- $customSongDisplayRound -</h1>
                                        </div>
                                        <div class="custom-song-type">
                                            <h1>- $customSongDisplayType -</h1>
                                        </div>
                                    </div>

                                    <div class="custom-song-body">
                                        <div class="custom-song-image">
                                            <a href="$customSongDisplayUrl">
                                                <img src="$customSongDisplayImage" alt="VOT4 Custom Song Image">
                                            </a>
                                        </div>
                                    </div>

                                    <div class="custom-song-footer">
                                        <div class="custom-song-name">
                                            <h2>$customSongDisplayName [$customSongDisplayDifficultyName]</h2>
                                        </div>

                                        <div class="custom-song-feature-artist">
                                            <h3>Song by $customSongDisplayFeatureArtist</h3>
                                        </div>

                                        <div class="custom-song-mapper">
                                            <h4>Created by <a href="$customSongDisplayMapperUrl">$customSongDisplayMapper</a></h4>
                                        </div>

                                        <div class="custom-song-attributes">
                                            <div class="custom-song-star-rating">
                                                <p>SR: $customSongDisplayDifficulty</p>
                                            </div>
                                            <div class="custom-song-length">
                                                <p>Length: $customSongDisplayLength</p>
                                            </div>
                                            <div class="custom-song-speed">
                                                <p>BPM: $customSongDisplayOverallSpeed</p>
                                            </div>
                                        </div>

                                        <div class="custom-song-attributes">
                                            <div class="custom-song-od">
                                                <p>OD: $customSongDisplayOverallDifficulty</p>
                                            </div>
                                            <div class="custom-song-hp">
                                                <p>HP: $customSongDisplayOverallHealth</p>
                                            </div>
                                            <div class="custom-song-pass">
                                                <p>Pass: $customSongDisplayPassCount</p>
                                            </div>
                                        </div>
                                    </div>

                                EOL;

                        // It would be much more nasty if I tried to output this using the traditional mixed HTML & PHP codes
                        echo $customSongDisplayTemplate;
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
