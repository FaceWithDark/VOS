<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require __DIR__ . '/../Models/Song.php';
require __DIR__ . '/../Configurations/Length.php';


if (!isset($_GET['tournament'])) {
    // Just show the page without any actions
    require __DIR__ . '/../Views/Song/SongVotView.php';
} else {
    // Show the page again after actions have been done
    require __DIR__ . '/../Views/Song/SongVotView.php';
    $votTournamentName = $_GET['tournament'];

    // Regex returns a boolean value so this is the way to do it
    switch (true) {
        // *** ALL TOURNAMENTS SONG DATA ***
        case preg_match(
            pattern: '/^(default|DEFAULT)$/i',
            subject: $votTournamentName
        ):
            /*
             *==================================================================
             * Because filter custom song data by default is basically fetching
             * all custom song information within the database of a specific
             * tournament, so I'll just being a bit hacky here by reading the
             * data for each individual tournament straight away. This will
             * prevent me having to create a dedicated 'default' tournament type
             * in the database table that just basically the sum of all other
             * type, which unesscesary increase the database size.
             *==================================================================
             */


            $abbreviateTournamentNames = [
                'VOT5',
                'VOT4',
                'VOT3',
                'VOT2',
                'VOT1'
            ];

            foreach ($abbreviateTournamentNames as $abbreviateTournamentName) {
                $tournamentSongViewData = readSongData(tournament: $abbreviateTournamentName);

                foreach ($tournamentSongViewData as $tournamentSongData) {
                    $tournamentSongTournament          = htmlspecialchars($tournamentSongData['tournamentName']);
                    $tournamentSongRound               = htmlspecialchars($tournamentSongData['roundName']);
                    $tournamentSongType                = htmlspecialchars($tournamentSongData['beatmapType']);
                    $tournamentSongImage               = htmlspecialchars($tournamentSongData['beatmapImage']);
                    $tournamentSongUrl                 = htmlspecialchars($tournamentSongData['beatmapUrl']);
                    $tournamentSongName                = htmlspecialchars($tournamentSongData['beatmapName']);
                    $tournamentSongDifficultyName      = htmlspecialchars($tournamentSongData['beatmapDifficultyName']);
                    $tournamentSongFeatureArtist       = htmlspecialchars($tournamentSongData['beatmapFeatureArtist']);
                    $tournamentSongMapper              = htmlspecialchars($tournamentSongData['beatmapMapper']);
                    $tournamentSongMapperUrl           = htmlspecialchars($tournamentSongData['beatmapMapperUrl']);
                    $tournamentSongDifficulty          = htmlspecialchars($tournamentSongData['beatmapDifficulty']);
                    $tournamentSongLength              = timeStampFormat(number: $tournamentSongData['beatmapLength']);
                    $tournamentSongOverallSpeed        = sprintf('%.2f', $tournamentSongData['beatmapOverallSpeed']);
                    $tournamentSongOverallDifficulty   = sprintf('%.2f', $tournamentSongData['beatmapOverallDifficulty']);
                    $tournamentSongOverallHealth       = sprintf('%.2f', $tournamentSongData['beatmapOverallHealth']);

                    $songInformationTemplate =
                        <<<EOL
                    <section class="custom-song-vot-page">
                        <div class="box-container">
                            <div class="custom-song-header">
                                <div class="custom-song-tournament">
                                    <h1>- $tournamentSongTournament -</h1>
                                </div>
                                <div class="custom-song-round">
                                    <h1>- $tournamentSongRound -</h1>
                                </div>
                                <div class="custom-song-type">
                                    <h1>- $tournamentSongType -</h1>
                                </div>
                            </div>

                            <div class="custom-song-body">
                                <div class="custom-song-image">
                                    <a href="$tournamentSongUrl">
                                        <img src="$tournamentSongImage" alt="VOT4 Custom Song Image">
                                    </a>
                                </div>
                            </div>

                            <div class="custom-song-footer">
                                <div class="custom-song-name">
                                    <h2>$tournamentSongName [$tournamentSongDifficultyName]</h2>
                                </div>

                                <div class="custom-song-feature-artist">
                                    <h3>Song by $tournamentSongFeatureArtist</h3>
                                </div>

                                <div class="custom-song-mapper">
                                    <h4>Created by <a href="$tournamentSongMapperUrl">$tournamentSongMapper</a></h4>
                                </div>

                                <div class="custom-song-attributes">
                                    <div class="custom-song-star-rating">
                                        <p>SR: $tournamentSongDifficulty</p>
                                    </div>
                                    <div class="custom-song-length">
                                        <p>Length: $tournamentSongLength</p>
                                    </div>
                                    <div class="custom-song-speed">
                                        <p>BPM: $tournamentSongOverallSpeed</p>
                                    </div>
                                </div>

                                <div class="custom-song-attributes">
                                    <div class="custom-song-od">
                                        <p>OD: $tournamentSongOverallDifficulty</p>
                                    </div>
                                    <div class="custom-song-hp">
                                        <p>HP: $tournamentSongOverallHealth</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    EOL;

                    // It would be much more nasty if I tried to output this using
                    // the traditional mixed HTML & PHP codes
                    echo $songInformationTemplate;
                }
            }
            break;

        // *** VOT5 SONG DATA ***
        case preg_match(
            pattern: '/^(vot5|VOT5)$/i',
            subject: $votTournamentName
        ):
            // Database use the abbreviation of each song indicator's name
            $vot5AbbreviateTournamentName = 'VOT5';

            $vot5SongViewData = readSongData(
                tournament: $vot5AbbreviateTournamentName
            );

            foreach ($vot5SongViewData as $vot5SongData) {
                $vot5SongTournament          = htmlspecialchars($vot5SongData['tournamentName']);
                $vot5SongRound               = htmlspecialchars($vot5SongData['roundName']);
                $vot5SongType                = htmlspecialchars($vot5SongData['beatmapType']);
                $vot5SongImage               = htmlspecialchars($vot5SongData['beatmapImage']);
                $vot5SongUrl                 = htmlspecialchars($vot5SongData['beatmapUrl']);
                $vot5SongName                = htmlspecialchars($vot5SongData['beatmapName']);
                $vot5SongDifficultyName      = htmlspecialchars($vot5SongData['beatmapDifficultyName']);
                $vot5SongFeatureArtist       = htmlspecialchars($vot5SongData['beatmapFeatureArtist']);
                $vot5SongMapper              = htmlspecialchars($vot5SongData['beatmapMapper']);
                $vot5SongMapperUrl           = htmlspecialchars($vot5SongData['beatmapMapperUrl']);
                $vot5SongDifficulty          = htmlspecialchars($vot5SongData['beatmapDifficulty']);
                $vot5SongLength              = timeStampFormat(number: $vot5SongData['beatmapLength']);
                $vot5SongOverallSpeed        = sprintf('%.2f', $vot5SongData['beatmapOverallSpeed']);
                $vot5SongOverallDifficulty   = sprintf('%.2f', $vot5SongData['beatmapOverallDifficulty']);
                $vot5SongOverallHealth       = sprintf('%.2f', $vot5SongData['beatmapOverallHealth']);

                $songInformationTemplate =
                    <<<EOL
                    <section class="custom-song-vot-page">
                        <div class="box-container">
                            <div class="custom-song-header">
                                <div class="custom-song-tournament">
                                    <h1>- $vot5SongTournament -</h1>
                                </div>
                                <div class="custom-song-round">
                                    <h1>- $vot5SongRound -</h1>
                                </div>
                                <div class="custom-song-type">
                                    <h1>- $vot5SongType -</h1>
                                </div>
                            </div>

                            <div class="custom-song-body">
                                <div class="custom-song-image">
                                    <a href="$vot5SongUrl">
                                        <img src="$vot5SongImage" alt="VOT4 Custom Song Image">
                                    </a>
                                </div>
                            </div>

                            <div class="custom-song-footer">
                                <div class="custom-song-name">
                                    <h2>$vot5SongName [$vot5SongDifficultyName]</h2>
                                </div>

                                <div class="custom-song-feature-artist">
                                    <h3>Song by $vot5SongFeatureArtist</h3>
                                </div>

                                <div class="custom-song-mapper">
                                    <h4>Created by <a href="$vot5SongMapperUrl">$vot5SongMapper</a></h4>
                                </div>

                                <div class="custom-song-attributes">
                                    <div class="custom-song-star-rating">
                                        <p>SR: $vot5SongDifficulty</p>
                                    </div>
                                    <div class="custom-song-length">
                                        <p>Length: $vot5SongLength</p>
                                    </div>
                                    <div class="custom-song-speed">
                                        <p>BPM: $vot5SongOverallSpeed</p>
                                    </div>
                                </div>

                                <div class="custom-song-attributes">
                                    <div class="custom-song-od">
                                        <p>OD: $vot5SongOverallDifficulty</p>
                                    </div>
                                    <div class="custom-song-hp">
                                        <p>HP: $vot5SongOverallHealth</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    EOL;

                // It would be much more nasty if I tried to output this using
                // the traditional mixed HTML & PHP codes
                echo $songInformationTemplate;
            }
            break;

        // *** VOT4 SONG DATA ***
        case preg_match(
            pattern: '/^(vot4|VOT4)$/i',
            subject: $votTournamentName
        ):
            // Database use the abbreviation of each song indicator's name
            $vot4AbbreviateTournamentName = 'VOT4';

            $vot4SongViewData = readSongData(
                tournament: $vot4AbbreviateTournamentName
            );

            foreach ($vot4SongViewData as $vot4SongData) {
                $vot4SongTournament          = htmlspecialchars($vot4SongData['tournamentName']);
                $vot4SongRound               = htmlspecialchars($vot4SongData['roundName']);
                $vot4SongType                = htmlspecialchars($vot4SongData['beatmapType']);
                $vot4SongImage               = htmlspecialchars($vot4SongData['beatmapImage']);
                $vot4SongUrl                 = htmlspecialchars($vot4SongData['beatmapUrl']);
                $vot4SongName                = htmlspecialchars($vot4SongData['beatmapName']);
                $vot4SongDifficultyName      = htmlspecialchars($vot4SongData['beatmapDifficultyName']);
                $vot4SongFeatureArtist       = htmlspecialchars($vot4SongData['beatmapFeatureArtist']);
                $vot4SongMapper              = htmlspecialchars($vot4SongData['beatmapMapper']);
                $vot4SongMapperUrl           = htmlspecialchars($vot4SongData['beatmapMapperUrl']);
                $vot4SongDifficulty          = htmlspecialchars($vot4SongData['beatmapDifficulty']);
                $vot4SongLength              = timeStampFormat(number: $vot4SongData['beatmapLength']);
                $vot4SongOverallSpeed        = sprintf('%.2f', $vot4SongData['beatmapOverallSpeed']);
                $vot4SongOverallDifficulty   = sprintf('%.2f', $vot4SongData['beatmapOverallDifficulty']);
                $vot4SongOverallHealth       = sprintf('%.2f', $vot4SongData['beatmapOverallHealth']);

                $songInformationTemplate =
                    <<<EOL
                    <section class="custom-song-vot-page">
                        <div class="box-container">
                            <div class="custom-song-header">
                                <div class="custom-song-tournament">
                                    <h1>- $vot4SongTournament -</h1>
                                </div>
                                <div class="custom-song-round">
                                    <h1>- $vot4SongRound -</h1>
                                </div>
                                <div class="custom-song-type">
                                    <h1>- $vot4SongType -</h1>
                                </div>
                            </div>

                            <div class="custom-song-body">
                                <div class="custom-song-image">
                                    <a href="$vot4SongUrl">
                                        <img src="$vot4SongImage" alt="VOT4 Custom Song Image">
                                    </a>
                                </div>
                            </div>

                            <div class="custom-song-footer">
                                <div class="custom-song-name">
                                    <h2>$vot4SongName [$vot4SongDifficultyName]</h2>
                                </div>

                                <div class="custom-song-feature-artist">
                                    <h3>Song by $vot4SongFeatureArtist</h3>
                                </div>

                                <div class="custom-song-mapper">
                                    <h4>Created by <a href="$vot4SongMapperUrl">$vot4SongMapper</a></h4>
                                </div>

                                <div class="custom-song-attributes">
                                    <div class="custom-song-star-rating">
                                        <p>SR: $vot4SongDifficulty</p>
                                    </div>
                                    <div class="custom-song-length">
                                        <p>Length: $vot4SongLength</p>
                                    </div>
                                    <div class="custom-song-speed">
                                        <p>BPM: $vot4SongOverallSpeed</p>
                                    </div>
                                </div>

                                <div class="custom-song-attributes">
                                    <div class="custom-song-od">
                                        <p>OD: $vot4SongOverallDifficulty</p>
                                    </div>
                                    <div class="custom-song-hp">
                                        <p>HP: $vot4SongOverallHealth</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    EOL;

                // It would be much more nasty if I tried to output this using
                // the traditional mixed HTML & PHP codes
                echo $songInformationTemplate;
            }
            break;

        // *** VOT3 SONG DATA ***
        case preg_match(
            pattern: '/^(vot3|VOT3)$/i',
            subject: $votTournamentName
        ):
            echo 'COMING SOON!! (VOT3)';
            break;

        // *** VOT2 SONG DATA ***
        case preg_match(
            pattern: '/^(vot2|VOT2)$/i',
            subject: $votTournamentName
        ):
            echo 'COMING SOON!! (VOT2)';
            break;

        // *** VOT1 SONG DATA ***
        case preg_match(
            pattern: '/^(vot1|VOT1)$/i',
            subject: $votTournamentName
        ):
            echo 'COMING SOON!! (VOT1)';
            break;

        default:
            // TODO: proper handling
            require_once __DIR__ . '/../Configurations/PrettyArray.php';
            echo array_dump(readSongData(tournament: $votTournamentName));
            break;
    }
}
