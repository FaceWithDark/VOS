<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require __DIR__ . '/../Models/Mappool.php';
require __DIR__ . '/../Utilities/Length.php';


// Simply: "/<tournament-name>/staff" --> "<TOURNAMENT-NAME>"
$votTournamentName = strtoupper(
    string: explode(
        separator: '/',
        string: trim(
            string: $_SERVER['REQUEST_URI'],
            characters: '/'
        ),
        limit: PHP_INT_MAX
    )[0]
);

switch ($votTournamentName) {
    case 'VOT5':
        if (!isset($_GET['round'])) {
            // Just show the page without any actions
            require __DIR__ . '/NavigationBarController.php';
            require __DIR__ . '/../Views/Tournament/MappoolVot5View.php';
            break;
        } else {
            // Show the page again after actions have been done
            require __DIR__ . '/NavigationBarController.php';
            require __DIR__ . '/../Views/Tournament/MappoolVot5View.php';

            $vot5RoundName = $_GET['round'];

            switch (true) {
                // *** VOT5 QUALIFIER MAPPOOL DATA ***
                case preg_match(
                    pattern: '/^(qualifier|qualifiers|qlf|qlfs)$/i',
                    subject: $vot5RoundName
                ):
                    // Database use the abbreviation of each round's name
                    $vot5AbbreviateRoundName = 'QLF';

                    $vot5QualifierMappoolViewData = readMappoolData(
                        round: $vot5AbbreviateRoundName,
                        tournament: $votTournamentName
                    );

                    foreach ($vot5QualifierMappoolViewData as $vot5QualifierMappoolData) {
                        $vot5QualifierMappoolType                = htmlspecialchars($vot5QualifierMappoolData['beatmapType']);
                        $vot5QualifierMappoolImage               = htmlspecialchars($vot5QualifierMappoolData['beatmapImage']);
                        $vot5QualifierMappoolUrl                 = htmlspecialchars($vot5QualifierMappoolData['beatmapUrl']);
                        $vot5QualifierMappoolName                = htmlspecialchars($vot5QualifierMappoolData['beatmapName']);
                        $vot5QualifierMappoolDifficultyName      = htmlspecialchars($vot5QualifierMappoolData['beatmapDifficultyName']);
                        $vot5QualifierMappoolFeatureArtist       = htmlspecialchars($vot5QualifierMappoolData['beatmapFeatureArtist']);
                        $vot5QualifierMappoolMapper              = htmlspecialchars($vot5QualifierMappoolData['beatmapMapper']);
                        $vot5QualifierMappoolMapperUrl           = htmlspecialchars($vot5QualifierMappoolData['beatmapMapperUrl']);
                        $vot5QualifierMappoolDifficulty          = htmlspecialchars($vot5QualifierMappoolData['beatmapDifficulty']);
                        $vot5QualifierMappoolLength              = timeStampFormat(number: $vot5QualifierMappoolData['beatmapLength']);
                        $vot5QualifierMappoolOverallSpeed        = sprintf('%.2f', $vot5QualifierMappoolData['beatmapOverallSpeed']);
                        $vot5QualifierMappoolOverallDifficulty   = sprintf('%.2f', $vot5QualifierMappoolData['beatmapOverallDifficulty']);
                        $vot5QualifierMappoolOverallHealth       = sprintf('%.2f', $vot5QualifierMappoolData['beatmapOverallHealth']);

                        $beatmapInformationTemplate =
                            <<<EOL
                            <section class="vot5-mappool">
                                <div class="box-container">
                                    <div class="song-type">
                                        <h1>$vot5QualifierMappoolType</h1>
                                    </div>

                                    <div class="song-image">
                                        <a href="$vot5QualifierMappoolUrl">
                                            <img src="$vot5QualifierMappoolImage" alt="{$votTournamentName} Beatmap Image">
                                        </a>
                                    </div>

                                    <div class="song-name">
                                        <h2>$vot5QualifierMappoolName [$vot5QualifierMappoolDifficultyName]</h2>
                                    </div>

                                    <div class="song-feature-artist">
                                        <h3>Song by $vot5QualifierMappoolFeatureArtist</h3>
                                    </div>

                                    <div class="song-mapper">
                                        <h4>Created by <a href="$vot5QualifierMappoolMapperUrl">$vot5QualifierMappoolMapper</a></h4>
                                    </div>

                                    <div class="song-attributes">
                                        <div class="song-star-rating">
                                            <p>SR:</p>
                                            <p>$vot5QualifierMappoolDifficulty</p>
                                        </div>
                                        <div class="song-length">
                                            <p>Length:</p>
                                            <p>$vot5QualifierMappoolLength</p>
                                        </div>
                                        <div class="song-speed">
                                            <p>BPM:</p>
                                            <p>$vot5QualifierMappoolOverallSpeed</p>
                                        </div>
                                    </div>

                                    <div class="song-attributes">
                                        <div class="song-od">
                                            <p>OD:</p>
                                            <p>$vot5QualifierMappoolOverallDifficulty</p>
                                        </div>
                                        <div class="song-hp">
                                            <p>HP:</p>
                                            <p>$vot5QualifierMappoolOverallHealth</p>
                                        </div>
                                    </div>
                                </div>
                            </section>
                            EOL;

                        // It would be much more nasty if I tried to output
                        // this using the traditional mixed HTML & PHP codes
                        echo $beatmapInformationTemplate;
                    }
                    break;

                // *** VOT5 GROUP STAGE (WEEK 1) MAPPOOL DATA ***
                case preg_match(
                    pattern: '/^(groupstageweek1|groupstagesweek1|gsw1|gssw1)$/i',
                    subject: $vot5RoundName
                ):
                    // Database use the abbreviation of each round's name
                    $vot5AbbreviateRoundName = 'GSW1';

                    $vot5GroupStageWeek1MappoolViewData = readMappoolData(
                        round: $vot5AbbreviateRoundName,
                        tournament: $votTournamentName
                    );

                    foreach ($vot5GroupStageWeek1MappoolViewData as $vot5GroupStageWeek1MappoolData) {
                        $vot5GroupStageWeek1MappoolType                = htmlspecialchars($vot5GroupStageWeek1MappoolData['beatmapType']);
                        $vot5GroupStageWeek1MappoolImage               = htmlspecialchars($vot5GroupStageWeek1MappoolData['beatmapImage']);
                        $vot5GroupStageWeek1MappoolUrl                 = htmlspecialchars($vot5GroupStageWeek1MappoolData['beatmapUrl']);
                        $vot5GroupStageWeek1MappoolName                = htmlspecialchars($vot5GroupStageWeek1MappoolData['beatmapName']);
                        $vot5GroupStageWeek1MappoolDifficultyName      = htmlspecialchars($vot5GroupStageWeek1MappoolData['beatmapDifficultyName']);
                        $vot5GroupStageWeek1MappoolFeatureArtist       = htmlspecialchars($vot5GroupStageWeek1MappoolData['beatmapFeatureArtist']);
                        $vot5GroupStageWeek1MappoolMapper              = htmlspecialchars($vot5GroupStageWeek1MappoolData['beatmapMapper']);
                        $vot5GroupStageWeek1MappoolMapperUrl           = htmlspecialchars($vot5GroupStageWeek1MappoolData['beatmapMapperUrl']);
                        $vot5GroupStageWeek1MappoolDifficulty          = htmlspecialchars($vot5GroupStageWeek1MappoolData['beatmapDifficulty']);
                        $vot5GroupStageWeek1MappoolLength              = timeStampFormat(number: $vot5GroupStageWeek1MappoolData['beatmapLength']);
                        $vot5GroupStageWeek1MappoolOverallSpeed        = sprintf('%.2f', $vot5GroupStageWeek1MappoolData['beatmapOverallSpeed']);
                        $vot5GroupStageWeek1MappoolOverallDifficulty   = sprintf('%.2f', $vot5GroupStageWeek1MappoolData['beatmapOverallDifficulty']);
                        $vot5GroupStageWeek1MappoolOverallHealth       = sprintf('%.2f', $vot5GroupStageWeek1MappoolData['beatmapOverallHealth']);

                        $beatmapInformationTemplate =
                            <<<EOL
                            <section class="vot5-mappool">
                                <div class="box-container">
                                    <div class="song-type">
                                        <h1>$vot5GroupStageWeek1MappoolType</h1>
                                    </div>

                                    <div class="song-image">
                                        <a href="$vot5GroupStageWeek1MappoolUrl">
                                            <img src="$vot5GroupStageWeek1MappoolImage" alt="{$votTournamentName} Beatmap Image">
                                        </a>
                                    </div>

                                    <div class="song-name">
                                        <h2>$vot5GroupStageWeek1MappoolName [$vot5GroupStageWeek1MappoolDifficultyName]</h2>
                                    </div>

                                    <div class="song-feature-artist">
                                        <h3>Song by $vot5GroupStageWeek1MappoolFeatureArtist</h3>
                                    </div>

                                    <div class="song-mapper">
                                        <h4>Created by <a href="$vot5GroupStageWeek1MappoolMapperUrl">$vot5GroupStageWeek1MappoolMapper</a></h4>
                                    </div>

                                    <div class="song-attributes">
                                        <div class="song-star-rating">
                                            <p>SR:</p>
                                            <p>$vot5GroupStageWeek1MappoolDifficulty</p>
                                        </div>
                                        <div class="song-length">
                                            <p>Length:</p>
                                            <p>$vot5GroupStageWeek1MappoolLength</p>
                                        </div>
                                        <div class="song-speed">
                                            <p>BPM:</p>
                                            <p>$vot5GroupStageWeek1MappoolOverallSpeed</p>
                                        </div>
                                    </div>

                                    <div class="song-attributes">
                                        <div class="song-od">
                                            <p>OD:</p>
                                            <p>$vot5GroupStageWeek1MappoolOverallDifficulty</p>
                                        </div>
                                        <div class="song-hp">
                                            <p>HP:</p>
                                            <p>$vot5GroupStageWeek1MappoolOverallHealth</p>
                                        </div>
                                    </div>
                                </div>
                            </section>
                            EOL;

                        // It would be much more nasty if I tried to output
                        // this using the traditional mixed HTML & PHP codes
                        echo $beatmapInformationTemplate;
                    }
                    break;

                // *** VOT5 GROUP STAGE (WEEK 2) MAPPOOL DATA ***
                case preg_match(
                    pattern: '/^(groupstageweek2|groupstagesweek2|gsw2|gssw2)$/i',
                    subject: $vot5RoundName
                ):
                    // Database use the abbreviation of each round's name
                    $vot5AbbreviateRoundName = 'GSW2';

                    $vot5GroupStageWeek2MappoolViewData = readMappoolData(
                        round: $vot5AbbreviateRoundName,
                        tournament: $votTournamentName
                    );

                    foreach ($vot5GroupStageWeek2MappoolViewData as $vot5GroupStageWeek2MappoolData) {
                        $vot5GroupStageWeek2MappoolType                = htmlspecialchars($vot5GroupStageWeek2MappoolData['beatmapType']);
                        $vot5GroupStageWeek2MappoolImage               = htmlspecialchars($vot5GroupStageWeek2MappoolData['beatmapImage']);
                        $vot5GroupStageWeek2MappoolUrl                 = htmlspecialchars($vot5GroupStageWeek2MappoolData['beatmapUrl']);
                        $vot5GroupStageWeek2MappoolName                = htmlspecialchars($vot5GroupStageWeek2MappoolData['beatmapName']);
                        $vot5GroupStageWeek2MappoolDifficultyName      = htmlspecialchars($vot5GroupStageWeek2MappoolData['beatmapDifficultyName']);
                        $vot5GroupStageWeek2MappoolFeatureArtist       = htmlspecialchars($vot5GroupStageWeek2MappoolData['beatmapFeatureArtist']);
                        $vot5GroupStageWeek2MappoolMapper              = htmlspecialchars($vot5GroupStageWeek2MappoolData['beatmapMapper']);
                        $vot5GroupStageWeek2MappoolMapperUrl           = htmlspecialchars($vot5GroupStageWeek2MappoolData['beatmapMapperUrl']);
                        $vot5GroupStageWeek2MappoolDifficulty          = htmlspecialchars($vot5GroupStageWeek2MappoolData['beatmapDifficulty']);
                        $vot5GroupStageWeek2MappoolLength              = timeStampFormat(number: $vot5GroupStageWeek2MappoolData['beatmapLength']);
                        $vot5GroupStageWeek2MappoolOverallSpeed        = sprintf('%.2f', $vot5GroupStageWeek2MappoolData['beatmapOverallSpeed']);
                        $vot5GroupStageWeek2MappoolOverallDifficulty   = sprintf('%.2f', $vot5GroupStageWeek2MappoolData['beatmapOverallDifficulty']);
                        $vot5GroupStageWeek2MappoolOverallHealth       = sprintf('%.2f', $vot5GroupStageWeek2MappoolData['beatmapOverallHealth']);

                        $beatmapInformationTemplate =
                            <<<EOL
                            <section class="vot5-mappool">
                                <div class="box-container">
                                    <div class="song-type">
                                        <h1>$vot5GroupStageWeek2MappoolType</h1>
                                    </div>

                                    <div class="song-image">
                                        <a href="$vot5GroupStageWeek2MappoolUrl">
                                            <img src="$vot5GroupStageWeek2MappoolImage" alt="{$votTournamentName} Beatmap Image">
                                        </a>
                                    </div>

                                    <div class="song-name">
                                        <h2>$vot5GroupStageWeek2MappoolName [$vot5GroupStageWeek2MappoolDifficultyName]</h2>
                                    </div>

                                    <div class="song-feature-artist">
                                        <h3>Song by $vot5GroupStageWeek2MappoolFeatureArtist</h3>
                                    </div>

                                    <div class="song-mapper">
                                        <h4>Created by <a href="$vot5GroupStageWeek2MappoolMapperUrl">$vot5GroupStageWeek2MappoolMapper</a></h4>
                                    </div>

                                    <div class="song-attributes">
                                        <div class="song-star-rating">
                                            <p>SR:</p>
                                            <p>$vot5GroupStageWeek2MappoolDifficulty</p>
                                        </div>
                                        <div class="song-length">
                                            <p>Length:</p>
                                            <p>$vot5GroupStageWeek2MappoolLength</p>
                                        </div>
                                        <div class="song-speed">
                                            <p>BPM:</p>
                                            <p>$vot5GroupStageWeek2MappoolOverallSpeed</p>
                                        </div>
                                    </div>

                                    <div class="song-attributes">
                                        <div class="song-od">
                                            <p>OD:</p>
                                            <p>$vot5GroupStageWeek2MappoolOverallDifficulty</p>
                                        </div>
                                        <div class="song-hp">
                                            <p>HP:</p>
                                            <p>$vot5GroupStageWeek2MappoolOverallHealth</p>
                                        </div>
                                    </div>
                                </div>
                            </section>
                            EOL;

                        // It would be much more nasty if I tried to output
                        // this using the traditional mixed HTML & PHP codes
                        echo $beatmapInformationTemplate;
                    }
                    break;

                // *** VOT5 SEMI FINAL MAPPOOL DATA ***
                case preg_match(
                    pattern: '/^(semifinal|semifinals|sf|sfs)$/i',
                    subject: $vot5RoundName
                ):
                    // Database use the abbreviation of each round's name
                    $vot5AbbreviateRoundName = 'SF';

                    $vot5SemiFinalMappoolViewData = readMappoolData(
                        round: $vot5AbbreviateRoundName,
                        tournament: $votTournamentName
                    );

                    foreach ($vot5SemiFinalMappoolViewData as $vot5SemiFinalMappoolData) {
                        $vot5SemiFinalMappoolType                = htmlspecialchars($vot5SemiFinalMappoolData['beatmapType']);
                        $vot5SemiFinalMappoolImage               = htmlspecialchars($vot5SemiFinalMappoolData['beatmapImage']);
                        $vot5SemiFinalMappoolUrl                 = htmlspecialchars($vot5SemiFinalMappoolData['beatmapUrl']);
                        $vot5SemiFinalMappoolName                = htmlspecialchars($vot5SemiFinalMappoolData['beatmapName']);
                        $vot5SemiFinalMappoolDifficultyName      = htmlspecialchars($vot5SemiFinalMappoolData['beatmapDifficultyName']);
                        $vot5SemiFinalMappoolFeatureArtist       = htmlspecialchars($vot5SemiFinalMappoolData['beatmapFeatureArtist']);
                        $vot5SemiFinalMappoolMapper              = htmlspecialchars($vot5SemiFinalMappoolData['beatmapMapper']);
                        $vot5SemiFinalMappoolMapperUrl           = htmlspecialchars($vot5SemiFinalMappoolData['beatmapMapperUrl']);
                        $vot5SemiFinalMappoolDifficulty          = htmlspecialchars($vot5SemiFinalMappoolData['beatmapDifficulty']);
                        $vot5SemiFinalMappoolLength              = timeStampFormat(number: $vot5SemiFinalMappoolData['beatmapLength']);
                        $vot5SemiFinalMappoolOverallSpeed        = sprintf('%.2f', $vot5SemiFinalMappoolData['beatmapOverallSpeed']);
                        $vot5SemiFinalMappoolOverallDifficulty   = sprintf('%.2f', $vot5SemiFinalMappoolData['beatmapOverallDifficulty']);
                        $vot5SemiFinalMappoolOverallHealth       = sprintf('%.2f', $vot5SemiFinalMappoolData['beatmapOverallHealth']);

                        $beatmapInformationTemplate =
                            <<<EOL
                            <section class="vot5-mappool">
                                <div class="box-container">
                                    <div class="song-type">
                                        <h1>$vot5SemiFinalMappoolType</h1>
                                    </div>

                                    <div class="song-image">
                                        <a href="$vot5SemiFinalMappoolUrl">
                                            <img src="$vot5SemiFinalMappoolImage" alt="{$votTournamentName} Beatmap Image">
                                        </a>
                                    </div>

                                    <div class="song-name">
                                        <h2>$vot5SemiFinalMappoolName [$vot5SemiFinalMappoolDifficultyName]</h2>
                                    </div>

                                    <div class="song-feature-artist">
                                        <h3>Song by $vot5SemiFinalMappoolFeatureArtist</h3>
                                    </div>

                                    <div class="song-mapper">
                                        <h4>Created by <a href="$vot5SemiFinalMappoolMapperUrl">$vot5SemiFinalMappoolMapper</a></h4>
                                    </div>

                                    <div class="song-attributes">
                                        <div class="song-star-rating">
                                            <p>SR:</p>
                                            <p>$vot5SemiFinalMappoolDifficulty</p>
                                        </div>
                                        <div class="song-length">
                                            <p>Length:</p>
                                            <p>$vot5SemiFinalMappoolLength</p>
                                        </div>
                                        <div class="song-speed">
                                            <p>BPM:</p>
                                            <p>$vot5SemiFinalMappoolOverallSpeed</p>
                                        </div>
                                    </div>

                                    <div class="song-attributes">
                                        <div class="song-od">
                                            <p>OD:</p>
                                            <p>$vot5SemiFinalMappoolOverallDifficulty</p>
                                        </div>
                                        <div class="song-hp">
                                            <p>HP:</p>
                                            <p>$vot5SemiFinalMappoolOverallHealth</p>
                                        </div>
                                    </div>
                                </div>
                            </section>
                            EOL;

                        // It would be much more nasty if I tried to output
                        // this using the traditional mixed HTML & PHP codes
                        echo $beatmapInformationTemplate;
                    }
                    break;

                // *** VOT5 FINAL MAPPOOL DATA ***
                case preg_match(
                    pattern: '/^(final|finals|fnl|fnls)$/i',
                    subject: $vot5RoundName
                ):
                    // Database use the abbreviation of each round's name
                    $vot5AbbreviateRoundName = 'FNL';

                    $vot5FinalMappoolViewData = readMappoolData(
                        round: $vot5AbbreviateRoundName,
                        tournament: $votTournamentName
                    );

                    foreach ($vot5FinalMappoolViewData as $vot5FinalMappoolData) {
                        $vot5FinalMappoolType                = htmlspecialchars($vot5FinalMappoolData['beatmapType']);
                        $vot5FinalMappoolImage               = htmlspecialchars($vot5FinalMappoolData['beatmapImage']);
                        $vot5FinalMappoolUrl                 = htmlspecialchars($vot5FinalMappoolData['beatmapUrl']);
                        $vot5FinalMappoolName                = htmlspecialchars($vot5FinalMappoolData['beatmapName']);
                        $vot5FinalMappoolDifficultyName      = htmlspecialchars($vot5FinalMappoolData['beatmapDifficultyName']);
                        $vot5FinalMappoolFeatureArtist       = htmlspecialchars($vot5FinalMappoolData['beatmapFeatureArtist']);
                        $vot5FinalMappoolMapper              = htmlspecialchars($vot5FinalMappoolData['beatmapMapper']);
                        $vot5FinalMappoolMapperUrl           = htmlspecialchars($vot5FinalMappoolData['beatmapMapperUrl']);
                        $vot5FinalMappoolDifficulty          = htmlspecialchars($vot5FinalMappoolData['beatmapDifficulty']);
                        $vot5FinalMappoolLength              = timeStampFormat(number: $vot5FinalMappoolData['beatmapLength']);
                        $vot5FinalMappoolOverallSpeed        = sprintf('%.2f', $vot5FinalMappoolData['beatmapOverallSpeed']);
                        $vot5FinalMappoolOverallDifficulty   = sprintf('%.2f', $vot5FinalMappoolData['beatmapOverallDifficulty']);
                        $vot5FinalMappoolOverallHealth       = sprintf('%.2f', $vot5FinalMappoolData['beatmapOverallHealth']);

                        $beatmapInformationTemplate =
                            <<<EOL
                            <section class="vot5-mappool">
                                <div class="box-container">
                                    <div class="song-type">
                                        <h1>$vot5FinalMappoolType</h1>
                                    </div>

                                    <div class="song-image">
                                        <a href="$vot5FinalMappoolUrl">
                                            <img src="$vot5FinalMappoolImage" alt="{$votTournamentName} Beatmap Image">
                                        </a>
                                    </div>

                                    <div class="song-name">
                                        <h2>$vot5FinalMappoolName [$vot5FinalMappoolDifficultyName]</h2>
                                    </div>

                                    <div class="song-feature-artist">
                                        <h3>Song by $vot5FinalMappoolFeatureArtist</h3>
                                    </div>

                                    <div class="song-mapper">
                                        <h4>Created by <a href="$vot5FinalMappoolMapperUrl">$vot5FinalMappoolMapper</a></h4>
                                    </div>

                                    <div class="song-attributes">
                                        <div class="song-star-rating">
                                            <p>SR:</p>
                                            <p>$vot5FinalMappoolDifficulty</p>
                                        </div>
                                        <div class="song-length">
                                            <p>Length:</p>
                                            <p>$vot5FinalMappoolLength</p>
                                        </div>
                                        <div class="song-speed">
                                            <p>BPM:</p>
                                            <p>$vot5FinalMappoolOverallSpeed</p>
                                        </div>
                                    </div>

                                    <div class="song-attributes">
                                        <div class="song-od">
                                            <p>OD:</p>
                                            <p>$vot5FinalMappoolOverallDifficulty</p>
                                        </div>
                                        <div class="song-hp">
                                            <p>HP:</p>
                                            <p>$vot5FinalMappoolOverallHealth</p>
                                        </div>
                                    </div>
                                </div>
                            </section>
                            EOL;

                        // It would be much more nasty if I tried to output
                        // this using the traditional mixed HTML & PHP codes
                        echo $beatmapInformationTemplate;
                    }
                    break;

                // *** VOT5 GRAND FINAL & ALL STAR MAPPOOL DATA ***
                case preg_match(
                    pattern: '/^(grandfinal|grandfinals|gf|gfs)$/i',
                    subject: $vot5RoundName
                ):
                case preg_match(
                    pattern: '/^(allstar|allstars|astr|astrs)$/i',
                    subject: $vot5RoundName
                ):

                    /*
                     *==========================================================
                     * Because [All STAR] mappool is basically the same as
                     * [GRAND FINAL] mappool, so I'll just being a bit lazy here
                     * by using the [GRAND FINAL] mappool data directly. This'll
                     * prevent me from adding the same beatmap ID into the
                     * database, which violate the uniqueness of primary key
                     * (beatmap ID) attribute.
                     *==========================================================
                     */

                    // Database use the abbreviation of each round's name
                    $vot5AbbreviateRoundName = 'GF';

                    $vot5GrandFinalMappoolViewData = readMappoolData(
                        round: $vot5AbbreviateRoundName,
                        tournament: $votTournamentName
                    );

                    foreach ($vot5GrandFinalMappoolViewData as $vot5GrandFinalMappoolData) {
                        $vot5GrandFinalMappoolType                = htmlspecialchars($vot5GrandFinalMappoolData['beatmapType']);
                        $vot5GrandFinalMappoolImage               = htmlspecialchars($vot5GrandFinalMappoolData['beatmapImage']);
                        $vot5GrandFinalMappoolUrl                 = htmlspecialchars($vot5GrandFinalMappoolData['beatmapUrl']);
                        $vot5GrandFinalMappoolName                = htmlspecialchars($vot5GrandFinalMappoolData['beatmapName']);
                        $vot5GrandFinalMappoolDifficultyName      = htmlspecialchars($vot5GrandFinalMappoolData['beatmapDifficultyName']);
                        $vot5GrandFinalMappoolFeatureArtist       = htmlspecialchars($vot5GrandFinalMappoolData['beatmapFeatureArtist']);
                        $vot5GrandFinalMappoolMapper              = htmlspecialchars($vot5GrandFinalMappoolData['beatmapMapper']);
                        $vot5GrandFinalMappoolMapperUrl           = htmlspecialchars($vot5GrandFinalMappoolData['beatmapMapperUrl']);
                        $vot5GrandFinalMappoolDifficulty          = htmlspecialchars($vot5GrandFinalMappoolData['beatmapDifficulty']);
                        $vot5GrandFinalMappoolLength              = timeStampFormat(number: $vot5GrandFinalMappoolData['beatmapLength']);
                        $vot5GrandFinalMappoolOverallSpeed        = sprintf('%.2f', $vot5GrandFinalMappoolData['beatmapOverallSpeed']);
                        $vot5GrandFinalMappoolOverallDifficulty   = sprintf('%.2f', $vot5GrandFinalMappoolData['beatmapOverallDifficulty']);
                        $vot5GrandFinalMappoolOverallHealth       = sprintf('%.2f', $vot5GrandFinalMappoolData['beatmapOverallHealth']);

                        $beatmapInformationTemplate =
                            <<<EOL
                            <section class="vot5-mappool">
                                <div class="box-container">
                                    <div class="song-type">
                                        <h1>$vot5GrandFinalMappoolType</h1>
                                    </div>

                                    <div class="song-image">
                                        <a href="$vot5GrandFinalMappoolUrl">
                                            <img src="$vot5GrandFinalMappoolImage" alt="{$votTournamentName} Beatmap Image">
                                        </a>
                                    </div>

                                    <div class="song-name">
                                        <h2>$vot5GrandFinalMappoolName [$vot5GrandFinalMappoolDifficultyName]</h2>
                                    </div>

                                    <div class="song-feature-artist">
                                        <h3>Song by $vot5GrandFinalMappoolFeatureArtist</h3>
                                    </div>

                                    <div class="song-mapper">
                                        <h4>Created by <a href="$vot5GrandFinalMappoolMapperUrl">$vot5GrandFinalMappoolMapper</a></h4>
                                    </div>

                                    <div class="song-attributes">
                                        <div class="song-star-rating">
                                            <p>SR:</p>
                                            <p>$vot5GrandFinalMappoolDifficulty</p>
                                        </div>
                                        <div class="song-length">
                                            <p>Length:</p>
                                            <p>$vot5GrandFinalMappoolLength</p>
                                        </div>
                                        <div class="song-speed">
                                            <p>BPM:</p>
                                            <p>$vot5GrandFinalMappoolOverallSpeed</p>
                                        </div>
                                    </div>

                                    <div class="song-attributes">
                                        <div class="song-od">
                                            <p>OD:</p>
                                            <p>$vot5GrandFinalMappoolOverallDifficulty</p>
                                        </div>
                                        <div class="song-hp">
                                            <p>HP:</p>
                                            <p>$vot5GrandFinalMappoolOverallHealth</p>
                                        </div>
                                    </div>
                                </div>
                            </section>
                            EOL;

                        // It would be much more nasty if I tried to output
                        // this using the traditional mixed HTML & PHP codes
                        echo $beatmapInformationTemplate;
                    }
                    break;

                default:
                    // TODO: proper handling
                    echo sprintf(
                        "There is no such round named [%s]. What are u tryin' to do bro...",
                        $vot4RoundName
                    );
                    break;
            }
            break;
        }

    case 'VOT4':
        if (!isset($_GET['round'])) {
            // Just show the page without any actions
            require __DIR__ . '/NavigationBarController.php';
            require __DIR__ . '/../Views/Tournament/MappoolVot4View.php';
            break;
        } else {
            // Show the page again after actions have been done
            require __DIR__ . '/NavigationBarController.php';
            require __DIR__ . '/../Views/Tournament/MappoolVot4View.php';

            $vot4RoundName = $_GET['round'];

            // Regex returns a boolean value so this is the way to do it
            switch (true) {
                // *** VOT4 QUALIFIER MAPPOOL DATA ***
                case preg_match(
                    pattern: '/^(qualifier|qualifiers|qlf|qlfs)$/i',
                    subject: $vot4RoundName
                ):
                    // Database use the abbreviation of each round's name
                    $vot4AbbreviateRoundName = 'QLF';

                    $vot4QualifierMappoolViewData = readMappoolData(
                        round: $vot4AbbreviateRoundName,
                        tournament: $votTournamentName
                    );

                    foreach ($vot4QualifierMappoolViewData as $vot4QualifierMappoolData) {
                        $vot4QualifierMappoolType                = htmlspecialchars($vot4QualifierMappoolData['beatmapType']);
                        $vot4QualifierMappoolImage               = htmlspecialchars($vot4QualifierMappoolData['beatmapImage']);
                        $vot4QualifierMappoolUrl                 = htmlspecialchars($vot4QualifierMappoolData['beatmapUrl']);
                        $vot4QualifierMappoolName                = htmlspecialchars($vot4QualifierMappoolData['beatmapName']);
                        $vot4QualifierMappoolDifficultyName      = htmlspecialchars($vot4QualifierMappoolData['beatmapDifficultyName']);
                        $vot4QualifierMappoolFeatureArtist       = htmlspecialchars($vot4QualifierMappoolData['beatmapFeatureArtist']);
                        $vot4QualifierMappoolMapper              = htmlspecialchars($vot4QualifierMappoolData['beatmapMapper']);
                        $vot4QualifierMappoolMapperUrl           = htmlspecialchars($vot4QualifierMappoolData['beatmapMapperUrl']);
                        $vot4QualifierMappoolDifficulty          = htmlspecialchars($vot4QualifierMappoolData['beatmapDifficulty']);
                        $vot4QualifierMappoolLength              = timeStampFormat(number: $vot4QualifierMappoolData['beatmapLength']);
                        $vot4QualifierMappoolOverallSpeed        = sprintf('%.2f', $vot4QualifierMappoolData['beatmapOverallSpeed']);
                        $vot4QualifierMappoolOverallDifficulty   = sprintf('%.2f', $vot4QualifierMappoolData['beatmapOverallDifficulty']);
                        $vot4QualifierMappoolOverallHealth       = sprintf('%.2f', $vot4QualifierMappoolData['beatmapOverallHealth']);

                        $beatmapInformationTemplate =
                            <<<EOL
                            <section class="vot4-mappool">
                                <div class="box-container">
                                    <div class="song-type">
                                        <h1>$vot4QualifierMappoolType</h1>
                                    </div>

                                    <div class="song-image">
                                        <a href="$vot4QualifierMappoolUrl">
                                            <img src="$vot4QualifierMappoolImage" alt="{$votTournamentName} Beatmap Image">
                                        </a>
                                    </div>

                                    <div class="song-name">
                                        <h2>$vot4QualifierMappoolName [$vot4QualifierMappoolDifficultyName]</h2>
                                    </div>

                                    <div class="song-feature-artist">
                                        <h3>Song by $vot4QualifierMappoolFeatureArtist</h3>
                                    </div>

                                    <div class="song-mapper">
                                        <h4>Created by <a href="$vot4QualifierMappoolMapperUrl">$vot4QualifierMappoolMapper</a></h4>
                                    </div>

                                    <div class="song-attributes">
                                        <div class="song-star-rating">
                                            <p>SR:</p>
                                            <p>$vot4QualifierMappoolDifficulty</p>
                                        </div>
                                        <div class="song-length">
                                            <p>Length:</p>
                                            <p>$vot4QualifierMappoolLength</p>
                                        </div>
                                        <div class="song-speed">
                                            <p>BPM:</p>
                                            <p>$vot4QualifierMappoolOverallSpeed</p>
                                        </div>
                                    </div>

                                    <div class="song-attributes">
                                        <div class="song-od">
                                            <p>OD:</p>
                                            <p>$vot4QualifierMappoolOverallDifficulty</p>
                                        </div>
                                        <div class="song-hp">
                                            <p>HP:</p>
                                            <p>$vot4QualifierMappoolOverallHealth</p>
                                        </div>
                                    </div>
                                </div>
                            </section>
                            EOL;

                        // It would be much more nasty if I tried to output
                        // this using the traditional mixed HTML & PHP codes
                        echo $beatmapInformationTemplate;
                    }
                    break;

                // *** VOT4 ROUND OF 16 MAPPOOL DATA ***
                case preg_match(
                    pattern: '/^(roundof16|roundsof16|ro16|rso16)$/i',
                    subject: $vot4RoundName
                ):
                    // Database use the abbreviation of each round's name
                    $vot4AbbreviateRoundName = 'RO16';

                    $vot4RoundOf16MappoolViewData = readMappoolData(
                        round: $vot4AbbreviateRoundName,
                        tournament: $votTournamentName
                    );

                    foreach ($vot4RoundOf16MappoolViewData as $vot4RoundOf16MappoolData) {
                        $vot4RoundOf16MappoolType                = htmlspecialchars($vot4RoundOf16MappoolData['beatmapType']);
                        $vot4RoundOf16MappoolImage               = htmlspecialchars($vot4RoundOf16MappoolData['beatmapImage']);
                        $vot4RoundOf16MappoolUrl                 = htmlspecialchars($vot4RoundOf16MappoolData['beatmapUrl']);
                        $vot4RoundOf16MappoolName                = htmlspecialchars($vot4RoundOf16MappoolData['beatmapName']);
                        $vot4RoundOf16MappoolDifficultyName      = htmlspecialchars($vot4RoundOf16MappoolData['beatmapDifficultyName']);
                        $vot4RoundOf16MappoolFeatureArtist       = htmlspecialchars($vot4RoundOf16MappoolData['beatmapFeatureArtist']);
                        $vot4RoundOf16MappoolMapper              = htmlspecialchars($vot4RoundOf16MappoolData['beatmapMapper']);
                        $vot4RoundOf16MappoolMapperUrl           = htmlspecialchars($vot4RoundOf16MappoolData['beatmapMapperUrl']);
                        $vot4RoundOf16MappoolDifficulty          = htmlspecialchars($vot4RoundOf16MappoolData['beatmapDifficulty']);
                        $vot4RoundOf16MappoolLength              = timeStampFormat(number: $vot4RoundOf16MappoolData['beatmapLength']);
                        $vot4RoundOf16MappoolOverallSpeed        = sprintf('%.2f', $vot4RoundOf16MappoolData['beatmapOverallSpeed']);
                        $vot4RoundOf16MappoolOverallDifficulty   = sprintf('%.2f', $vot4RoundOf16MappoolData['beatmapOverallDifficulty']);
                        $vot4RoundOf16MappoolOverallHealth       = sprintf('%.2f', $vot4RoundOf16MappoolData['beatmapOverallHealth']);

                        $beatmapInformationTemplate =
                            <<<EOL
                            <section class="vot4-mappool">
                                <div class="box-container">
                                    <div class="song-type">
                                        <h1>$vot4RoundOf16MappoolType</h1>
                                    </div>

                                    <div class="song-image">
                                        <a href="$vot4RoundOf16MappoolUrl">
                                            <img src="$vot4RoundOf16MappoolImage" alt="{$votTournamentName} Beatmap Image">
                                        </a>
                                    </div>

                                    <div class="song-name">
                                        <h2>$vot4RoundOf16MappoolName [$vot4RoundOf16MappoolDifficultyName]</h2>
                                    </div>

                                    <div class="song-feature-artist">
                                        <h3>Song by $vot4RoundOf16MappoolFeatureArtist</h3>
                                    </div>

                                    <div class="song-mapper">
                                        <h4>Created by <a href="$vot4RoundOf16MappoolMapperUrl">$vot4RoundOf16MappoolMapper</a></h4>
                                    </div>

                                    <div class="song-attributes">
                                        <div class="song-star-rating">
                                            <p>SR:</p>
                                            <p>$vot4RoundOf16MappoolDifficulty</p>
                                        </div>
                                        <div class="song-length">
                                            <p>Length:</p>
                                            <p>$vot4RoundOf16MappoolLength</p>
                                        </div>
                                        <div class="song-speed">
                                            <p>BPM:</p>
                                            <p>$vot4RoundOf16MappoolOverallSpeed</p>
                                        </div>
                                    </div>

                                    <div class="song-attributes">
                                        <div class="song-od">
                                            <p>OD:</p>
                                            <p>$vot4RoundOf16MappoolOverallDifficulty</p>
                                        </div>
                                        <div class="song-hp">
                                            <p>HP:</p>
                                            <p>$vot4RoundOf16MappoolOverallHealth</p>
                                        </div>
                                    </div>
                                </div>
                            </section>
                            EOL;

                        // It would be much more nasty if I tried to output
                        // this using the traditional mixed HTML & PHP codes
                        echo $beatmapInformationTemplate;
                    }
                    break;

                // *** VOT4 QUARTER FINAL MAPPOOL DATA ***
                case preg_match(
                    pattern: '/^(quarterfinal|quarterfinals|qf|qfs)$/i',
                    subject: $vot4RoundName
                ):
                    // Database use the abbreviation of each round's name
                    $vot4AbbreviateRoundName = 'QF';

                    $vot4QuarterFinalMappoolViewData = readMappoolData(
                        round: $vot4AbbreviateRoundName,
                        tournament: $votTournamentName
                    );

                    foreach ($vot4QuarterFinalMappoolViewData as $vot4QuarterFinalMappoolData) {
                        $vot4QuarterFinalMappoolType                = htmlspecialchars($vot4QuarterFinalMappoolData['beatmapType']);
                        $vot4QuarterFinalMappoolImage               = htmlspecialchars($vot4QuarterFinalMappoolData['beatmapImage']);
                        $vot4QuarterFinalMappoolUrl                 = htmlspecialchars($vot4QuarterFinalMappoolData['beatmapUrl']);
                        $vot4QuarterFinalMappoolName                = htmlspecialchars($vot4QuarterFinalMappoolData['beatmapName']);
                        $vot4QuarterFinalMappoolDifficultyName      = htmlspecialchars($vot4QuarterFinalMappoolData['beatmapDifficultyName']);
                        $vot4QuarterFinalMappoolFeatureArtist       = htmlspecialchars($vot4QuarterFinalMappoolData['beatmapFeatureArtist']);
                        $vot4QuarterFinalMappoolMapper              = htmlspecialchars($vot4QuarterFinalMappoolData['beatmapMapper']);
                        $vot4QuarterFinalMappoolMapperUrl           = htmlspecialchars($vot4QuarterFinalMappoolData['beatmapMapperUrl']);
                        $vot4QuarterFinalMappoolDifficulty          = htmlspecialchars($vot4QuarterFinalMappoolData['beatmapDifficulty']);
                        $vot4QuarterFinalMappoolLength              = timeStampFormat(number: $vot4QuarterFinalMappoolData['beatmapLength']);
                        $vot4QuarterFinalMappoolOverallSpeed        = sprintf('%.2f', $vot4QuarterFinalMappoolData['beatmapOverallSpeed']);
                        $vot4QuarterFinalMappoolOverallDifficulty   = sprintf('%.2f', $vot4QuarterFinalMappoolData['beatmapOverallDifficulty']);
                        $vot4QuarterFinalMappoolOverallHealth       = sprintf('%.2f', $vot4QuarterFinalMappoolData['beatmapOverallHealth']);

                        $beatmapInformationTemplate =
                            <<<EOL
                            <section class="vot4-mappool">
                                <div class="box-container">
                                    <div class="song-type">
                                        <h1>$vot4QuarterFinalMappoolType</h1>
                                    </div>

                                    <div class="song-image">
                                        <a href="$vot4QuarterFinalMappoolUrl">
                                            <img src="$vot4QuarterFinalMappoolImage" alt="{$votTournamentName} Beatmap Image">
                                        </a>
                                    </div>

                                    <div class="song-name">
                                        <h2>$vot4QuarterFinalMappoolName [$vot4QuarterFinalMappoolDifficultyName]</h2>
                                    </div>

                                    <div class="song-feature-artist">
                                        <h3>Song by $vot4QuarterFinalMappoolFeatureArtist</h3>
                                    </div>

                                    <div class="song-mapper">
                                        <h4>Created by <a href="$vot4QuarterFinalMappoolMapperUrl">$vot4QuarterFinalMappoolMapper</a></h4>
                                    </div>

                                    <div class="song-attributes">
                                        <div class="song-star-rating">
                                            <p>SR:</p>
                                            <p>$vot4QuarterFinalMappoolDifficulty</p>
                                        </div>
                                        <div class="song-length">
                                            <p>Length:</p>
                                            <p>$vot4QuarterFinalMappoolLength</p>
                                        </div>
                                        <div class="song-speed">
                                            <p>BPM:</p>
                                            <p>$vot4QuarterFinalMappoolOverallSpeed</p>
                                        </div>
                                    </div>

                                    <div class="song-attributes">
                                        <div class="song-od">
                                            <p>OD:</p>
                                            <p>$vot4QuarterFinalMappoolOverallDifficulty</p>
                                        </div>
                                        <div class="song-hp">
                                            <p>HP:</p>
                                            <p>$vot4QuarterFinalMappoolOverallHealth</p>
                                        </div>
                                    </div>
                                </div>
                            </section>
                            EOL;

                        // It would be much more nasty if I tried to output
                        // this using the traditional mixed HTML & PHP codes
                        echo $beatmapInformationTemplate;
                    }
                    break;

                // *** VOT4 SEMI FINAL MAPPOOL DATA ***
                case preg_match(
                    pattern: '/^(semifinal|semifinals|sf|sfs)$/i',
                    subject: $vot4RoundName
                ):
                    // Database use the abbreviation of each round's name
                    $vot4AbbreviateRoundName = 'SF';

                    $vot4SemiFinalMappoolViewData = readMappoolData(
                        round: $vot4AbbreviateRoundName,
                        tournament: $votTournamentName
                    );

                    foreach ($vot4SemiFinalMappoolViewData as $vot4SemiFinalMappoolData) {
                        $vot4SemiFinalMappoolType                = htmlspecialchars($vot4SemiFinalMappoolData['beatmapType']);
                        $vot4SemiFinalMappoolImage               = htmlspecialchars($vot4SemiFinalMappoolData['beatmapImage']);
                        $vot4SemiFinalMappoolUrl                 = htmlspecialchars($vot4SemiFinalMappoolData['beatmapUrl']);
                        $vot4SemiFinalMappoolName                = htmlspecialchars($vot4SemiFinalMappoolData['beatmapName']);
                        $vot4SemiFinalMappoolDifficultyName      = htmlspecialchars($vot4SemiFinalMappoolData['beatmapDifficultyName']);
                        $vot4SemiFinalMappoolFeatureArtist       = htmlspecialchars($vot4SemiFinalMappoolData['beatmapFeatureArtist']);
                        $vot4SemiFinalMappoolMapper              = htmlspecialchars($vot4SemiFinalMappoolData['beatmapMapper']);
                        $vot4SemiFinalMappoolMapperUrl           = htmlspecialchars($vot4SemiFinalMappoolData['beatmapMapperUrl']);
                        $vot4SemiFinalMappoolDifficulty          = htmlspecialchars($vot4SemiFinalMappoolData['beatmapDifficulty']);
                        $vot4SemiFinalMappoolLength              = timeStampFormat(number: $vot4SemiFinalMappoolData['beatmapLength']);
                        $vot4SemiFinalMappoolOverallSpeed        = sprintf('%.2f', $vot4SemiFinalMappoolData['beatmapOverallSpeed']);
                        $vot4SemiFinalMappoolOverallDifficulty   = sprintf('%.2f', $vot4SemiFinalMappoolData['beatmapOverallDifficulty']);
                        $vot4SemiFinalMappoolOverallHealth       = sprintf('%.2f', $vot4SemiFinalMappoolData['beatmapOverallHealth']);

                        $beatmapInformationTemplate =
                            <<<EOL
                            <section class="vot4-mappool">
                                <div class="box-container">
                                    <div class="song-type">
                                        <h1>$vot4SemiFinalMappoolType</h1>
                                    </div>

                                    <div class="song-image">
                                        <a href="$vot4SemiFinalMappoolUrl">
                                            <img src="$vot4SemiFinalMappoolImage" alt="{$votTournamentName} Beatmap Image">
                                        </a>
                                    </div>

                                    <div class="song-name">
                                        <h2>$vot4SemiFinalMappoolName [$vot4SemiFinalMappoolDifficultyName]</h2>
                                    </div>

                                    <div class="song-feature-artist">
                                        <h3>Song by $vot4SemiFinalMappoolFeatureArtist</h3>
                                    </div>

                                    <div class="song-mapper">
                                        <h4>Created by <a href="$vot4SemiFinalMappoolMapperUrl">$vot4SemiFinalMappoolMapper</a></h4>
                                    </div>

                                    <div class="song-attributes">
                                        <div class="song-star-rating">
                                            <p>SR:</p>
                                            <p>$vot4SemiFinalMappoolDifficulty</p>
                                        </div>
                                        <div class="song-length">
                                            <p>Length:</p>
                                            <p>$vot4SemiFinalMappoolLength</p>
                                        </div>
                                        <div class="song-speed">
                                            <p>BPM:</p>
                                            <p>$vot4SemiFinalMappoolOverallSpeed</p>
                                        </div>
                                    </div>

                                    <div class="song-attributes">
                                        <div class="song-od">
                                            <p>OD:</p>
                                            <p>$vot4SemiFinalMappoolOverallDifficulty</p>
                                        </div>
                                        <div class="song-hp">
                                            <p>HP:</p>
                                            <p>$vot4SemiFinalMappoolOverallHealth</p>
                                        </div>
                                    </div>
                                </div>
                            </section>
                            EOL;

                        // It would be much more nasty if I tried to output
                        // this using the traditional mixed HTML & PHP codes
                        echo $beatmapInformationTemplate;
                    }
                    break;

                // *** VOT4 FINAL MAPPOOL DATA ***
                case preg_match(
                    pattern: '/^(final|finals|fnl|fnls)$/i',
                    subject: $vot4RoundName
                ):
                    // Database use the abbreviation of each round's name
                    $vot4AbbreviateRoundName = 'FNL';

                    $vot4FinalMappoolViewData = readMappoolData(
                        round: $vot4AbbreviateRoundName,
                        tournament: $votTournamentName
                    );

                    foreach ($vot4FinalMappoolViewData as $vot4FinalMappoolData) {
                        $vot4FinalMappoolType                = htmlspecialchars($vot4FinalMappoolData['beatmapType']);
                        $vot4FinalMappoolImage               = htmlspecialchars($vot4FinalMappoolData['beatmapImage']);
                        $vot4FinalMappoolUrl                 = htmlspecialchars($vot4FinalMappoolData['beatmapUrl']);
                        $vot4FinalMappoolName                = htmlspecialchars($vot4FinalMappoolData['beatmapName']);
                        $vot4FinalMappoolDifficultyName      = htmlspecialchars($vot4FinalMappoolData['beatmapDifficultyName']);
                        $vot4FinalMappoolFeatureArtist       = htmlspecialchars($vot4FinalMappoolData['beatmapFeatureArtist']);
                        $vot4FinalMappoolMapper              = htmlspecialchars($vot4FinalMappoolData['beatmapMapper']);
                        $vot4FinalMappoolMapperUrl           = htmlspecialchars($vot4FinalMappoolData['beatmapMapperUrl']);
                        $vot4FinalMappoolDifficulty          = htmlspecialchars($vot4FinalMappoolData['beatmapDifficulty']);
                        $vot4FinalMappoolLength              = timeStampFormat(number: $vot4FinalMappoolData['beatmapLength']);
                        $vot4FinalMappoolOverallSpeed        = sprintf('%.2f', $vot4FinalMappoolData['beatmapOverallSpeed']);
                        $vot4FinalMappoolOverallDifficulty   = sprintf('%.2f', $vot4FinalMappoolData['beatmapOverallDifficulty']);
                        $vot4FinalMappoolOverallHealth       = sprintf('%.2f', $vot4FinalMappoolData['beatmapOverallHealth']);

                        $beatmapInformationTemplate =
                            <<<EOL
                            <section class="vot4-mappool">
                                <div class="box-container">
                                    <div class="song-type">
                                        <h1>$vot4FinalMappoolType</h1>
                                    </div>

                                    <div class="song-image">
                                        <a href="$vot4FinalMappoolUrl">
                                            <img src="$vot4FinalMappoolImage" alt="{$votTournamentName} Beatmap Image">
                                        </a>
                                    </div>

                                    <div class="song-name">
                                        <h2>$vot4FinalMappoolName [$vot4FinalMappoolDifficultyName]</h2>
                                    </div>

                                    <div class="song-feature-artist">
                                        <h3>Song by $vot4FinalMappoolFeatureArtist</h3>
                                    </div>

                                    <div class="song-mapper">
                                        <h4>Created by <a href="$vot4FinalMappoolMapperUrl">$vot4FinalMappoolMapper</a></h4>
                                    </div>

                                    <div class="song-attributes">
                                        <div class="song-star-rating">
                                            <p>SR:</p>
                                            <p>$vot4FinalMappoolDifficulty</p>
                                        </div>
                                        <div class="song-length">
                                            <p>Length:</p>
                                            <p>$vot4FinalMappoolLength</p>
                                        </div>
                                        <div class="song-speed">
                                            <p>BPM:</p>
                                            <p>$vot4FinalMappoolOverallSpeed</p>
                                        </div>
                                    </div>

                                    <div class="song-attributes">
                                        <div class="song-od">
                                            <p>OD:</p>
                                            <p>$vot4FinalMappoolOverallDifficulty</p>
                                        </div>
                                        <div class="song-hp">
                                            <p>HP:</p>
                                            <p>$vot4FinalMappoolOverallHealth</p>
                                        </div>
                                    </div>
                                </div>
                            </section>
                            EOL;

                        // It would be much more nasty if I tried to output
                        // this using the traditional mixed HTML & PHP codes
                        echo $beatmapInformationTemplate;
                    }
                    break;

                // *** VOT4 GRAND FINAL & ALL STAR MAPPOOL DATA ***
                case preg_match(
                    pattern: '/^(grandfinal|grandfinals|gf|gfs)$/i',
                    subject: $vot4RoundName
                ):
                case preg_match(
                    pattern: '/^(allstar|allstars|astr|astrs)$/i',
                    subject: $vot4RoundName
                ):

                    /*
                     *==========================================================
                     * Because [All STAR] mappool is basically the same as
                     * [GRAND FINAL] mappool, so I'll just being a bit lazy here
                     * by using the [GRAND FINAL] mappool data directly. This'll
                     * prevent me from adding the same beatmap ID into the
                     * database, which violate the uniqueness of primary key
                     * (beatmap ID) attribute.
                     *==========================================================
                     */

                    // Database use the abbreviation of each round's name
                    $vot4AbbreviateRoundName = 'GF';

                    $vot4GrandFinalMappoolViewData = readMappoolData(
                        round: $vot4AbbreviateRoundName,
                        tournament: $votTournamentName
                    );

                    foreach ($vot4GrandFinalMappoolViewData as $vot4GrandFinalMappoolData) {
                        $vot4GrandFinalMappoolType                = htmlspecialchars($vot4GrandFinalMappoolData['beatmapType']);
                        $vot4GrandFinalMappoolImage               = htmlspecialchars($vot4GrandFinalMappoolData['beatmapImage']);
                        $vot4GrandFinalMappoolUrl                 = htmlspecialchars($vot4GrandFinalMappoolData['beatmapUrl']);
                        $vot4GrandFinalMappoolName                = htmlspecialchars($vot4GrandFinalMappoolData['beatmapName']);
                        $vot4GrandFinalMappoolDifficultyName      = htmlspecialchars($vot4GrandFinalMappoolData['beatmapDifficultyName']);
                        $vot4GrandFinalMappoolFeatureArtist       = htmlspecialchars($vot4GrandFinalMappoolData['beatmapFeatureArtist']);
                        $vot4GrandFinalMappoolMapper              = htmlspecialchars($vot4GrandFinalMappoolData['beatmapMapper']);
                        $vot4GrandFinalMappoolMapperUrl           = htmlspecialchars($vot4GrandFinalMappoolData['beatmapMapperUrl']);
                        $vot4GrandFinalMappoolDifficulty          = htmlspecialchars($vot4GrandFinalMappoolData['beatmapDifficulty']);
                        $vot4GrandFinalMappoolLength              = timeStampFormat(number: $vot4GrandFinalMappoolData['beatmapLength']);
                        $vot4GrandFinalMappoolOverallSpeed        = sprintf('%.2f', $vot4GrandFinalMappoolData['beatmapOverallSpeed']);
                        $vot4GrandFinalMappoolOverallDifficulty   = sprintf('%.2f', $vot4GrandFinalMappoolData['beatmapOverallDifficulty']);
                        $vot4GrandFinalMappoolOverallHealth       = sprintf('%.2f', $vot4GrandFinalMappoolData['beatmapOverallHealth']);

                        $beatmapInformationTemplate =
                            <<<EOL
                            <section class="vot4-mappool">
                                <div class="box-container">
                                    <div class="song-type">
                                        <h1>$vot4GrandFinalMappoolType</h1>
                                    </div>

                                    <div class="song-image">
                                        <a href="$vot4GrandFinalMappoolUrl">
                                            <img src="$vot4GrandFinalMappoolImage" alt="{$votTournamentName} Beatmap Image">
                                        </a>
                                    </div>

                                    <div class="song-name">
                                        <h2>$vot4GrandFinalMappoolName [$vot4GrandFinalMappoolDifficultyName]</h2>
                                    </div>

                                    <div class="song-feature-artist">
                                        <h3>Song by $vot4GrandFinalMappoolFeatureArtist</h3>
                                    </div>

                                    <div class="song-mapper">
                                        <h4>Created by <a href="$vot4GrandFinalMappoolMapperUrl">$vot4GrandFinalMappoolMapper</a></h4>
                                    </div>

                                    <div class="song-attributes">
                                        <div class="song-star-rating">
                                            <p>SR:</p>
                                            <p>$vot4GrandFinalMappoolDifficulty</p>
                                        </div>
                                        <div class="song-length">
                                            <p>Length:</p>
                                            <p>$vot4GrandFinalMappoolLength</p>
                                        </div>
                                        <div class="song-speed">
                                            <p>BPM:</p>
                                            <p>$vot4GrandFinalMappoolOverallSpeed</p>
                                        </div>
                                    </div>

                                    <div class="song-attributes">
                                        <div class="song-od">
                                            <p>OD:</p>
                                            <p>$vot4GrandFinalMappoolOverallDifficulty</p>
                                        </div>
                                        <div class="song-hp">
                                            <p>HP:</p>
                                            <p>$vot4GrandFinalMappoolOverallHealth</p>
                                        </div>
                                    </div>
                                </div>
                            </section>
                            EOL;

                        // It would be much more nasty if I tried to output
                        // this using the traditional mixed HTML & PHP codes
                        echo $beatmapInformationTemplate;
                    }
                    break;

                default:
                    // TODO: proper handling
                    echo sprintf(
                        "There is no such round named [%s]. What are u tryin' to do bro...",
                        $vot4RoundName
                    );
                    break;
            }
            break;
        }

    default:
        // TODO: proper handling
        echo sprintf(
            "There is no such tournament named [%s]. What are u tryin' to do bro...",
            $votTournamentName
        );
        break;
}
