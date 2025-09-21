<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require __DIR__ . '/../../Models/Song.php';
require __DIR__ . '/../../Configurations/Length.php';
?>

<header>
    <h1>Our Banger VOT Song &#128293</h1>
</header>

<section class="custom-song-vot-page">
    <form action="/song/vot" method="get">
        <button type="submit" name="tournament" value="DEFAULT">Default</button>
        <button type="submit" name="tournament" value="VOT5">VOT5</button>
        <button type="submit" name="tournament" value="VOT4">VOT4</button>
        <button type="submit" name="tournament" value="VOT3">VOT3</button>
        <button type="submit" name="tournament" value="VOT2">VOT2</button>
        <button type="submit" name="tournament" value="VOT1">VOT1</button>
    </form>

    <?php
    $votTournamentDatabase = $GLOBALS['votDatabaseHandle'];

    if (!isset($_GET['tournament'])) {
        echo 'Among Us';
    } else {
        $votTournamentName = $_GET['tournament'];

        $votTournamentCustomSongData = readCustomSongData(
            name: $votTournamentName,
            database_handle: $votTournamentDatabase
        );

        foreach ($votTournamentCustomSongData as $votTournamentCustomSongDisplayData) {
            $votTournamentCustomSongTournament          = htmlspecialchars($votTournamentCustomSongDisplayData['tournamentName']);
            $votTournamentCustomSongRound               = htmlspecialchars($votTournamentCustomSongDisplayData['roundName']);
            $votTournamentCustomSongType                = htmlspecialchars($votTournamentCustomSongDisplayData['beatmapType']);
            $votTournamentCustomSongImage               = htmlspecialchars($votTournamentCustomSongDisplayData['beatmapImage']);
            $votTournamentCustomSongUrl                 = htmlspecialchars($votTournamentCustomSongDisplayData['beatmapUrl']);
            $votTournamentCustomSongName                = htmlspecialchars($votTournamentCustomSongDisplayData['beatmapName']);
            $votTournamentCustomSongDifficultyName      = htmlspecialchars($votTournamentCustomSongDisplayData['beatmapDifficultyName']);
            $votTournamentCustomSongFeatureArtist       = htmlspecialchars($votTournamentCustomSongDisplayData['beatmapFeatureArtist']);
            $votTournamentCustomSongMapper              = htmlspecialchars($votTournamentCustomSongDisplayData['beatmapMapper']);
            $votTournamentCustomSongMapperUrl           = htmlspecialchars($votTournamentCustomSongDisplayData['beatmapMapperUrl']);
            $votTournamentCustomSongDifficulty          = htmlspecialchars($votTournamentCustomSongDisplayData['beatmapDifficulty']);
            $votTournamentCustomSongLength              = timeStampFormat(number: $votTournamentCustomSongDisplayData['beatmapLength']);
            $votTournamentCustomSongOverallSpeed        = sprintf('%.2f', $votTournamentCustomSongDisplayData['beatmapOverallSpeed']);
            $votTournamentCustomSongOverallDifficulty   = sprintf('%.2f', $votTournamentCustomSongDisplayData['beatmapOverallDifficulty']);
            $votTournamentCustomSongOverallHealth       = sprintf('%.2f', $votTournamentCustomSongDisplayData['beatmapOverallHealth']);
            $votTournamentCustomSongPassCount           = $votTournamentCustomSongDisplayData['beatmapPassCount'];

            $customSongDisplayTemplate =
                <<<EOL
                <div class="box-container">
                    <div class="custom-song-header">
                        <div class="custom-song-tournament">
                            <h1>- $votTournamentCustomSongTournament -</h1>
                        </div>
                        <div class="custom-song-round">
                            <h1>- $votTournamentCustomSongRound -</h1>
                        </div>
                        <div class="custom-song-type">
                            <h1>- $votTournamentCustomSongType -</h1>
                        </div>
                    </div>

                    <div class="custom-song-body">
                        <div class="custom-song-image">
                            <a href="$votTournamentCustomSongUrl">
                                <img src="$votTournamentCustomSongImage" alt="VOT4 Custom Song Image">
                            </a>
                        </div>
                    </div>

                    <div class="custom-song-footer">
                        <div class="custom-song-name">
                            <h2>$votTournamentCustomSongName [$votTournamentCustomSongDifficultyName]</h2>
                        </div>

                        <div class="custom-song-feature-artist">
                            <h3>Song by $votTournamentCustomSongFeatureArtist</h3>
                        </div>

                        <div class="custom-song-mapper">
                            <h4>Created by <a href="$votTournamentCustomSongMapperUrl">$votTournamentCustomSongMapper</a></h4>
                        </div>

                        <div class="custom-song-attributes">
                            <div class="custom-song-star-rating">
                                <p>SR: $votTournamentCustomSongDifficulty</p>
                            </div>
                            <div class="custom-song-length">
                                <p>Length: $votTournamentCustomSongLength</p>
                            </div>
                            <div class="custom-song-speed">
                                <p>BPM: $votTournamentCustomSongOverallSpeed</p>
                            </div>
                        </div>

                        <div class="custom-song-attributes">
                            <div class="custom-song-od">
                                <p>OD: $votTournamentCustomSongOverallDifficulty</p>
                            </div>
                            <div class="custom-song-hp">
                                <p>HP: $votTournamentCustomSongOverallHealth</p>
                            </div>
                            <div class="custom-song-pass">
                                <p>Pass: $votTournamentCustomSongPassCount</p>
                            </div>
                        </div>
                    </div>
                </div>
                EOL;

            // It would be much more nasty if I tried to output this using the traditional mixed HTML & PHP codes
            echo $customSongDisplayTemplate;
        }
    }
    ?>
</section>
