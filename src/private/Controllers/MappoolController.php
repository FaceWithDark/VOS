<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require __DIR__ . '/../Models/Mappool.php';
require __DIR__ . '/../Configurations/Length.php';


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

$votMappoolJsonData = __DIR__ . '/../Datas/Tournament/VotMappoolData.json';
$votMappoolJsonViewableData = file_get_contents(
    filename: $votMappoolJsonData,
    use_include_path: false,
    context: null,
    offset: 0,
    length: null
);
$votMappoolJsonUsableData = json_decode(
    json: $votMappoolJsonViewableData,
    associative: true
);

switch ($votTournamentName) {
    case 'VOT5':
        if (!isset($_GET['round'])) {
            // Just show the page without any actions
            require __DIR__ . '/../Views/Tournament/Vot5MappoolView.php';
            break;
        } else {
            // Show the page again after actions have been done
            require __DIR__ . '/../Views/Tournament/Vot5MappoolView.php';
            $vot5RoundName = $_GET['round'];

            // Regex returns a boolean value so this is the way to do it
            switch (true) {
                // *** VOT5 QUALIFIER MAPPOOL DATA ***
                case preg_match(
                    pattern: '/^(qualifiers|qlf|qlfs)$/i',
                    subject: $vot5RoundName
                ):
                    // Database use the abbreviation of each round's name
                    $vot5AbbreviateRoundName = 'QLF';

                    if (!isset($_COOKIE['vot_access_token'])) {
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
                    } else {
                        // TODO: storing data should only be done by host or
                        // higher role level
                        $votMappoolAccessToken = $_COOKIE['vot_access_token'];

                        $vot5QualifierJsonData = $votMappoolJsonUsableData[$votTournamentName][$vot5AbbreviateRoundName];
                        foreach ($vot5QualifierJsonData as $vot5QualifierJsonType => $vot5QualifierJsonId) {
                            $vot5QualifierData = getMappoolData(
                                id: $vot5QualifierJsonId,
                                token: $votMappoolAccessToken
                            );

                            $vot5QualifierId                = $vot5QualifierData['id'];
                            $vot5QualifierRoundId           = $vot5AbbreviateRoundName;
                            $vot5QualifierTournamentId      = $votTournamentName;
                            $vot5QualifierType              = $vot5QualifierJsonType;
                            $vot5QualifierImage             = $vot5QualifierData['beatmapset']['covers']['cover'];
                            $vot5QualifierUrl               = $vot5QualifierData['url'];
                            $vot5QualifierName              = $vot5QualifierData['beatmapset']['title'];
                            $vot5QualifierDifficultyName    = $vot5QualifierData['version'];
                            $vot5QualifierFeatureArtist     = $vot5QualifierData['beatmapset']['artist'];
                            $vot5QualifierMapper            = $vot5QualifierData['beatmapset']['creator'];
                            $vot5QualifierMapperUrl         = "https://osu.ppy.sh/users/{$vot5QualifierData['beatmapset']['user_id']}";
                            $vot5QualifierDifficulty        = $vot5QualifierData['difficulty_rating'];
                            $vot5QualifierLength            = $vot5QualifierData['total_length'];
                            $vot5QualifierOverallSpeed      = $vot5QualifierData['beatmapset']['bpm'];
                            $vot5QualifierOverallDifficulty = $vot5QualifierData['accuracy'];
                            $vot5QualifierOverallHealth     = $vot5QualifierData['drain'];

                            if (
                                !checkMappoolData(
                                    id: $vot5QualifierId,
                                    round: $vot5QualifierRoundId,
                                    tournament: $vot5QualifierTournamentId
                                )
                            ) {
                                createMappoolData(
                                    beatmap_id: $vot5QualifierId,
                                    round_id: $vot5QualifierRoundId,
                                    tournament_id: $vot5QualifierTournamentId,
                                    type: $vot5QualifierType,
                                    image: $vot5QualifierImage,
                                    url: $vot5QualifierUrl,
                                    name: $vot5QualifierName,
                                    diff_name: $vot5QualifierDifficultyName,
                                    fa: $vot5QualifierFeatureArtist,
                                    mapper: $vot5QualifierMapper,
                                    mapper_url: $vot5QualifierMapperUrl,
                                    diff: $vot5QualifierDifficulty,
                                    length: $vot5QualifierLength,
                                    bpm: $vot5QualifierOverallSpeed,
                                    od: $vot5QualifierOverallDifficulty,
                                    hp: $vot5QualifierOverallHealth
                                );
                            } else {
                                // TODO: UPDATE query here (change the 'view'
                                // table only, not the actual table if all data
                                // stay the same).
                                error_log(
                                    message: 'Mappool data exist, simply ignoring it...',
                                    message_type: 0
                                );
                            }
                        }

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
                    }
                    break;

                // *** VOT5 GROUP STAGE (WEEK 1) MAPPOOL DATA ***
                case preg_match(
                    pattern: '/^(groupstagesweek1|groupstageweek1|GroupStagesWeek1|GroupStageWeek1|GROUPSTAGEWEEK1)$/i',
                    subject: $vot5RoundName
                ):
                    // Database use the abbreviation of each round's name
                    $vot5AbbreviateRoundName = 'GSW1';

                    if (!isset($_COOKIE['vot_access_token'])) {
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
                    } else {
                        // TODO: storing data should only be done by host or
                        // higher role level
                        $votMappoolAccessToken = $_COOKIE['vot_access_token'];

                        $vot5GroupStageWeek1JsonData = $votMappoolJsonUsableData[$votTournamentName][$vot5AbbreviateRoundName];
                        foreach ($vot5GroupStageWeek1JsonData as $vot5GroupStageWeek1JsonType => $vot5GroupStageWeek1JsonId) {
                            $vot5GroupStageWeek1Data = getMappoolData(
                                id: $vot5GroupStageWeek1JsonId,
                                token: $votMappoolAccessToken
                            );

                            $vot5GroupStageWeek1Id                = $vot5GroupStageWeek1Data['id'];
                            $vot5GroupStageWeek1RoundId           = $vot5AbbreviateRoundName;
                            $vot5GroupStageWeek1TournamentId      = $votTournamentName;
                            $vot5GroupStageWeek1Type              = $vot5GroupStageWeek1JsonType;
                            $vot5GroupStageWeek1Image             = $vot5GroupStageWeek1Data['beatmapset']['covers']['cover'];
                            $vot5GroupStageWeek1Url               = $vot5GroupStageWeek1Data['url'];
                            $vot5GroupStageWeek1Name              = $vot5GroupStageWeek1Data['beatmapset']['title'];
                            $vot5GroupStageWeek1DifficultyName    = $vot5GroupStageWeek1Data['version'];
                            $vot5GroupStageWeek1FeatureArtist     = $vot5GroupStageWeek1Data['beatmapset']['artist'];
                            $vot5GroupStageWeek1Mapper            = $vot5GroupStageWeek1Data['beatmapset']['creator'];
                            $vot5GroupStageWeek1MapperUrl         = "https://osu.ppy.sh/users/{$vot5GroupStageWeek1Data['beatmapset']['user_id']}";
                            $vot5GroupStageWeek1Difficulty        = $vot5GroupStageWeek1Data['difficulty_rating'];
                            $vot5GroupStageWeek1Length            = $vot5GroupStageWeek1Data['total_length'];
                            $vot5GroupStageWeek1OverallSpeed      = $vot5GroupStageWeek1Data['beatmapset']['bpm'];
                            $vot5GroupStageWeek1OverallDifficulty = $vot5GroupStageWeek1Data['accuracy'];
                            $vot5GroupStageWeek1OverallHealth     = $vot5GroupStageWeek1Data['drain'];

                            if (
                                !checkMappoolData(
                                    id: $vot5GroupStageWeek1Id,
                                    round: $vot5GroupStageWeek1RoundId,
                                    tournament: $vot5GroupStageWeek1TournamentId
                                )
                            ) {
                                createMappoolData(
                                    beatmap_id: $vot5GroupStageWeek1Id,
                                    round_id: $vot5GroupStageWeek1RoundId,
                                    tournament_id: $vot5GroupStageWeek1TournamentId,
                                    type: $vot5GroupStageWeek1Type,
                                    image: $vot5GroupStageWeek1Image,
                                    url: $vot5GroupStageWeek1Url,
                                    name: $vot5GroupStageWeek1Name,
                                    diff_name: $vot5GroupStageWeek1DifficultyName,
                                    fa: $vot5GroupStageWeek1FeatureArtist,
                                    mapper: $vot5GroupStageWeek1Mapper,
                                    mapper_url: $vot5GroupStageWeek1MapperUrl,
                                    diff: $vot5GroupStageWeek1Difficulty,
                                    length: $vot5GroupStageWeek1Length,
                                    bpm: $vot5GroupStageWeek1OverallSpeed,
                                    od: $vot5GroupStageWeek1OverallDifficulty,
                                    hp: $vot5GroupStageWeek1OverallHealth
                                );
                            } else {
                                // TODO: UPDATE query here (change the 'view'
                                // table only, not the actual table if all data
                                // stay the same).
                                error_log(
                                    message: 'Mappool data exist, simply ignoring it...',
                                    message_type: 0
                                );
                            }
                        }

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
                    }
                    break;

                // *** VOT5 GROUP STAGE (WEEK 2) MAPPOOL DATA ***
                case preg_match(
                    pattern: '/^(groupstagesweek2|groupstageweek2|GroupStagesWeek2|GroupStageWeek2|GROUPSTAGEWEEK2)$/i',
                    subject: $vot5RoundName
                ):
                    // Database use the abbreviation of each round's name
                    $vot5AbbreviateRoundName = 'GSW2';

                    if (!isset($_COOKIE['vot_access_token'])) {
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
                    } else {
                        // TODO: storing data should only be done by host or
                        // higher role level
                        $votMappoolAccessToken = $_COOKIE['vot_access_token'];

                        $vot5GroupStageWeek2JsonData = $votMappoolJsonUsableData[$votTournamentName][$vot5AbbreviateRoundName];
                        foreach ($vot5GroupStageWeek2JsonData as $vot5GroupStageWeek2JsonType => $vot5GroupStageWeek2JsonId) {
                            $vot5GroupStageWeek2Data = getMappoolData(
                                id: $vot5GroupStageWeek2JsonId,
                                token: $votMappoolAccessToken
                            );

                            $vot5GroupStageWeek2Id                = $vot5GroupStageWeek2Data['id'];
                            $vot5GroupStageWeek2RoundId           = $vot5AbbreviateRoundName;
                            $vot5GroupStageWeek2TournamentId      = $votTournamentName;
                            $vot5GroupStageWeek2Type              = $vot5GroupStageWeek2JsonType;
                            $vot5GroupStageWeek2Image             = $vot5GroupStageWeek2Data['beatmapset']['covers']['cover'];
                            $vot5GroupStageWeek2Url               = $vot5GroupStageWeek2Data['url'];
                            $vot5GroupStageWeek2Name              = $vot5GroupStageWeek2Data['beatmapset']['title'];
                            $vot5GroupStageWeek2DifficultyName    = $vot5GroupStageWeek2Data['version'];
                            $vot5GroupStageWeek2FeatureArtist     = $vot5GroupStageWeek2Data['beatmapset']['artist'];
                            $vot5GroupStageWeek2Mapper            = $vot5GroupStageWeek2Data['beatmapset']['creator'];
                            $vot5GroupStageWeek2MapperUrl         = "https://osu.ppy.sh/users/{$vot5GroupStageWeek2Data['beatmapset']['user_id']}";
                            $vot5GroupStageWeek2Difficulty        = $vot5GroupStageWeek2Data['difficulty_rating'];
                            $vot5GroupStageWeek2Length            = $vot5GroupStageWeek2Data['total_length'];
                            $vot5GroupStageWeek2OverallSpeed      = $vot5GroupStageWeek2Data['beatmapset']['bpm'];
                            $vot5GroupStageWeek2OverallDifficulty = $vot5GroupStageWeek2Data['accuracy'];
                            $vot5GroupStageWeek2OverallHealth     = $vot5GroupStageWeek2Data['drain'];

                            if (
                                !checkMappoolData(
                                    id: $vot5GroupStageWeek2Id,
                                    round: $vot5GroupStageWeek2RoundId,
                                    tournament: $vot5GroupStageWeek2TournamentId
                                )
                            ) {
                                createMappoolData(
                                    beatmap_id: $vot5GroupStageWeek2Id,
                                    round_id: $vot5GroupStageWeek2RoundId,
                                    tournament_id: $vot5GroupStageWeek2TournamentId,
                                    type: $vot5GroupStageWeek2Type,
                                    image: $vot5GroupStageWeek2Image,
                                    url: $vot5GroupStageWeek2Url,
                                    name: $vot5GroupStageWeek2Name,
                                    diff_name: $vot5GroupStageWeek2DifficultyName,
                                    fa: $vot5GroupStageWeek2FeatureArtist,
                                    mapper: $vot5GroupStageWeek2Mapper,
                                    mapper_url: $vot5GroupStageWeek2MapperUrl,
                                    diff: $vot5GroupStageWeek2Difficulty,
                                    length: $vot5GroupStageWeek2Length,
                                    bpm: $vot5GroupStageWeek2OverallSpeed,
                                    od: $vot5GroupStageWeek2OverallDifficulty,
                                    hp: $vot5GroupStageWeek2OverallHealth
                                );
                            } else {
                                // TODO: UPDATE query here (change the 'view'
                                // table only, not the actual table if all data
                                // stay the same).
                                error_log(
                                    message: 'Mappool data exist, simply ignoring it...',
                                    message_type: 0
                                );
                            }
                        }

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
                    }
                    break;

                // *** VOT5 SEMI FINAL MAPPOOL DATA ***
                case preg_match(
                    pattern: '/^(semifinals|sf|sfs)$/i',
                    subject: $vot5RoundName
                ):
                    // Database use the abbreviation of each round's name
                    $vot5AbbreviateRoundName = 'SF';

                    if (!isset($_COOKIE['vot_access_token'])) {
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
                    } else {
                        // TODO: storing data should only be done by host or
                        // higher role level
                        $votMappoolAccessToken = $_COOKIE['vot_access_token'];

                        $vot5SemiFinalJsonData = $votMappoolJsonUsableData[$votTournamentName][$vot5AbbreviateRoundName];
                        foreach ($vot5SemiFinalJsonData as $vot5SemiFinalJsonType => $vot5SemiFinalJsonId) {
                            $vot5SemiFinalData = getMappoolData(
                                id: $vot5SemiFinalJsonId,
                                token: $votMappoolAccessToken
                            );

                            $vot5SemiFinalId                = $vot5SemiFinalData['id'];
                            $vot5SemiFinalRoundId           = $vot5AbbreviateRoundName;
                            $vot5SemiFinalTournamentId      = $votTournamentName;
                            $vot5SemiFinalType              = $vot5SemiFinalJsonType;
                            $vot5SemiFinalImage             = $vot5SemiFinalData['beatmapset']['covers']['cover'];
                            $vot5SemiFinalUrl               = $vot5SemiFinalData['url'];
                            $vot5SemiFinalName              = $vot5SemiFinalData['beatmapset']['title'];
                            $vot5SemiFinalDifficultyName    = $vot5SemiFinalData['version'];
                            $vot5SemiFinalFeatureArtist     = $vot5SemiFinalData['beatmapset']['artist'];
                            $vot5SemiFinalMapper            = $vot5SemiFinalData['beatmapset']['creator'];
                            $vot5SemiFinalMapperUrl         = "https://osu.ppy.sh/users/{$vot5SemiFinalData['beatmapset']['user_id']}";
                            $vot5SemiFinalDifficulty        = $vot5SemiFinalData['difficulty_rating'];
                            $vot5SemiFinalLength            = $vot5SemiFinalData['total_length'];
                            $vot5SemiFinalOverallSpeed      = $vot5SemiFinalData['beatmapset']['bpm'];
                            $vot5SemiFinalOverallDifficulty = $vot5SemiFinalData['accuracy'];
                            $vot5SemiFinalOverallHealth     = $vot5SemiFinalData['drain'];

                            if (
                                !checkMappoolData(
                                    id: $vot5SemiFinalId,
                                    round: $vot5SemiFinalRoundId,
                                    tournament: $vot5SemiFinalTournamentId
                                )
                            ) {
                                createMappoolData(
                                    beatmap_id: $vot5SemiFinalId,
                                    round_id: $vot5SemiFinalRoundId,
                                    tournament_id: $vot5SemiFinalTournamentId,
                                    type: $vot5SemiFinalType,
                                    image: $vot5SemiFinalImage,
                                    url: $vot5SemiFinalUrl,
                                    name: $vot5SemiFinalName,
                                    diff_name: $vot5SemiFinalDifficultyName,
                                    fa: $vot5SemiFinalFeatureArtist,
                                    mapper: $vot5SemiFinalMapper,
                                    mapper_url: $vot5SemiFinalMapperUrl,
                                    diff: $vot5SemiFinalDifficulty,
                                    length: $vot5SemiFinalLength,
                                    bpm: $vot5SemiFinalOverallSpeed,
                                    od: $vot5SemiFinalOverallDifficulty,
                                    hp: $vot5SemiFinalOverallHealth
                                );
                            } else {
                                // TODO: UPDATE query here (change the 'view'
                                // table only, not the actual table if all data
                                // stay the same).
                                error_log(
                                    message: 'Mappool data exist, simply ignoring it...',
                                    message_type: 0
                                );
                            }
                        }

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
                    }
                    break;

                // *** VOT5 FINAL MAPPOOL DATA ***
                case preg_match(
                    pattern: '/^(finals|fnl|fnls)$/i',
                    subject: $vot5RoundName
                ):
                    // Database use the abbreviation of each round's name
                    $vot5AbbreviateRoundName = 'FNL';

                    if (!isset($_COOKIE['vot_access_token'])) {
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
                    } else {
                        // TODO: storing data should only be done by host or
                        // higher role level
                        $votMappoolAccessToken = $_COOKIE['vot_access_token'];

                        $vot5FinalJsonData = $votMappoolJsonUsableData[$votTournamentName][$vot5AbbreviateRoundName];
                        foreach ($vot5FinalJsonData as $vot5FinalJsonType => $vot5FinalJsonId) {
                            $vot5FinalData = getMappoolData(
                                id: $vot5FinalJsonId,
                                token: $votMappoolAccessToken
                            );

                            $vot5FinalId                = $vot5FinalData['id'];
                            $vot5FinalRoundId           = $vot5AbbreviateRoundName;
                            $vot5FinalTournamentId      = $votTournamentName;
                            $vot5FinalType              = $vot5FinalJsonType;
                            $vot5FinalImage             = $vot5FinalData['beatmapset']['covers']['cover'];
                            $vot5FinalUrl               = $vot5FinalData['url'];
                            $vot5FinalName              = $vot5FinalData['beatmapset']['title'];
                            $vot5FinalDifficultyName    = $vot5FinalData['version'];
                            $vot5FinalFeatureArtist     = $vot5FinalData['beatmapset']['artist'];
                            $vot5FinalMapper            = $vot5FinalData['beatmapset']['creator'];
                            $vot5FinalMapperUrl         = "https://osu.ppy.sh/users/{$vot5FinalData['beatmapset']['user_id']}";
                            $vot5FinalDifficulty        = $vot5FinalData['difficulty_rating'];
                            $vot5FinalLength            = $vot5FinalData['total_length'];
                            $vot5FinalOverallSpeed      = $vot5FinalData['beatmapset']['bpm'];
                            $vot5FinalOverallDifficulty = $vot5FinalData['accuracy'];
                            $vot5FinalOverallHealth     = $vot5FinalData['drain'];

                            if (
                                !checkMappoolData(
                                    id: $vot5FinalId,
                                    round: $vot5FinalRoundId,
                                    tournament: $vot5FinalTournamentId
                                )
                            ) {
                                createMappoolData(
                                    beatmap_id: $vot5FinalId,
                                    round_id: $vot5FinalRoundId,
                                    tournament_id: $vot5FinalTournamentId,
                                    type: $vot5FinalType,
                                    image: $vot5FinalImage,
                                    url: $vot5FinalUrl,
                                    name: $vot5FinalName,
                                    diff_name: $vot5FinalDifficultyName,
                                    fa: $vot5FinalFeatureArtist,
                                    mapper: $vot5FinalMapper,
                                    mapper_url: $vot5FinalMapperUrl,
                                    diff: $vot5FinalDifficulty,
                                    length: $vot5FinalLength,
                                    bpm: $vot5FinalOverallSpeed,
                                    od: $vot5FinalOverallDifficulty,
                                    hp: $vot5FinalOverallHealth
                                );
                            } else {
                                // TODO: UPDATE query here (change the 'view'
                                // table only, not the actual table if all data
                                // stay the same).
                                error_log(
                                    message: 'Mappool data exist, simply ignoring it...',
                                    message_type: 0
                                );
                            }
                        }

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
                    }
                    break;

                // *** VOT5 GRAND FINAL & ALL STAR MAPPOOL DATA ***
                case preg_match(
                    pattern: '/^(grandfinals|gf|gfs)$/i',
                    subject: $vot5RoundName
                ):
                case preg_match(
                    pattern: '/^(allstars|astr|astrs|alstr|alstrs)$/i',
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

                    if (!isset($_COOKIE['vot_access_token'])) {
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
                    } else {
                        // TODO: storing data should only be done by host or
                        // higher role level
                        $votMappoolAccessToken = $_COOKIE['vot_access_token'];

                        $vot5GrandFinalJsonData = $votMappoolJsonUsableData[$votTournamentName][$vot5AbbreviateRoundName];
                        foreach ($vot5GrandFinalJsonData as $vot5GrandFinalJsonType => $vot5GrandFinalJsonId) {
                            $vot5GrandFinalData = getMappoolData(
                                id: $vot5GrandFinalJsonId,
                                token: $votMappoolAccessToken
                            );

                            $vot5GrandFinalId                = $vot5GrandFinalData['id'];
                            $vot5GrandFinalRoundId           = $vot5AbbreviateRoundName;
                            $vot5GrandFinalTournamentId      = $votTournamentName;
                            $vot5GrandFinalType              = $vot5GrandFinalJsonType;
                            $vot5GrandFinalImage             = $vot5GrandFinalData['beatmapset']['covers']['cover'];
                            $vot5GrandFinalUrl               = $vot5GrandFinalData['url'];
                            $vot5GrandFinalName              = $vot5GrandFinalData['beatmapset']['title'];
                            $vot5GrandFinalDifficultyName    = $vot5GrandFinalData['version'];
                            $vot5GrandFinalFeatureArtist     = $vot5GrandFinalData['beatmapset']['artist'];
                            $vot5GrandFinalMapper            = $vot5GrandFinalData['beatmapset']['creator'];
                            $vot5GrandFinalMapperUrl         = "https://osu.ppy.sh/users/{$vot5GrandFinalData['beatmapset']['user_id']}";
                            $vot5GrandFinalDifficulty        = $vot5GrandFinalData['difficulty_rating'];
                            $vot5GrandFinalLength            = $vot5GrandFinalData['total_length'];
                            $vot5GrandFinalOverallSpeed      = $vot5GrandFinalData['beatmapset']['bpm'];
                            $vot5GrandFinalOverallDifficulty = $vot5GrandFinalData['accuracy'];
                            $vot5GrandFinalOverallHealth     = $vot5GrandFinalData['drain'];

                            if (
                                !checkMappoolData(
                                    id: $vot5GrandFinalId,
                                    round: $vot5GrandFinalRoundId,
                                    tournament: $vot5GrandFinalTournamentId
                                )
                            ) {
                                createMappoolData(
                                    beatmap_id: $vot5GrandFinalId,
                                    round_id: $vot5GrandFinalRoundId,
                                    tournament_id: $vot5GrandFinalTournamentId,
                                    type: $vot5GrandFinalType,
                                    image: $vot5GrandFinalImage,
                                    url: $vot5GrandFinalUrl,
                                    name: $vot5GrandFinalName,
                                    diff_name: $vot5GrandFinalDifficultyName,
                                    fa: $vot5GrandFinalFeatureArtist,
                                    mapper: $vot5GrandFinalMapper,
                                    mapper_url: $vot5GrandFinalMapperUrl,
                                    diff: $vot5GrandFinalDifficulty,
                                    length: $vot5GrandFinalLength,
                                    bpm: $vot5GrandFinalOverallSpeed,
                                    od: $vot5GrandFinalOverallDifficulty,
                                    hp: $vot5GrandFinalOverallHealth
                                );
                            } else {
                                // TODO: UPDATE query here (change the 'view'
                                // table only, not the actual table if all data
                                // stay the same).
                                error_log(
                                    message: 'Mappool data exist, simply ignoring it...',
                                    message_type: 0
                                );
                            }
                        }

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
            require __DIR__ . '/../Views/Tournament/Vot4MappoolView.php';
            break;
        } else {
            // Show the page again after actions have been done
            require __DIR__ . '/../Views/Tournament/Vot4MappoolView.php';
            $vot4RoundName = $_GET['round'];

            // Regex returns a boolean value so this is the way to do it
            switch (true) {
                // *** VOT4 QUALIFIER MAPPOOL DATA ***
                case preg_match(
                    pattern: '/^(qualifiers|qlf|qlfs)$/i',
                    subject: $vot4RoundName
                ):
                    // Database use the abbreviation of each round's name
                    $vot4AbbreviateRoundName = 'QLF';

                    if (!isset($_COOKIE['vot_access_token'])) {
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
                    } else {
                        // TODO: storing data should only be done by host or
                        // higher role level
                        $votMappoolAccessToken = $_COOKIE['vot_access_token'];

                        $vot4QualifierJsonData = $votMappoolJsonUsableData[$votTournamentName][$vot4AbbreviateRoundName];
                        foreach ($vot4QualifierJsonData as $vot4QualifierJsonType => $vot4QualifierJsonId) {
                            $vot4QualifierData = getMappoolData(
                                id: $vot4QualifierJsonId,
                                token: $votMappoolAccessToken
                            );

                            $vot4QualifierId                = $vot4QualifierData['id'];
                            $vot4QualifierRoundId           = $vot4AbbreviateRoundName;
                            $vot4QualifierTournamentId      = $votTournamentName;
                            $vot4QualifierType              = $vot4QualifierJsonType;
                            $vot4QualifierImage             = $vot4QualifierData['beatmapset']['covers']['cover'];
                            $vot4QualifierUrl               = $vot4QualifierData['url'];
                            $vot4QualifierName              = $vot4QualifierData['beatmapset']['title'];
                            $vot4QualifierDifficultyName    = $vot4QualifierData['version'];
                            $vot4QualifierFeatureArtist     = $vot4QualifierData['beatmapset']['artist'];
                            $vot4QualifierMapper            = $vot4QualifierData['beatmapset']['creator'];
                            $vot4QualifierMapperUrl         = "https://osu.ppy.sh/users/{$vot4QualifierData['beatmapset']['user_id']}";
                            $vot4QualifierDifficulty        = $vot4QualifierData['difficulty_rating'];
                            $vot4QualifierLength            = $vot4QualifierData['total_length'];
                            $vot4QualifierOverallSpeed      = $vot4QualifierData['beatmapset']['bpm'];
                            $vot4QualifierOverallDifficulty = $vot4QualifierData['accuracy'];
                            $vot4QualifierOverallHealth     = $vot4QualifierData['drain'];

                            if (
                                !checkMappoolData(
                                    id: $vot4QualifierId,
                                    round: $vot4QualifierRoundId,
                                    tournament: $vot4QualifierTournamentId
                                )
                            ) {
                                createMappoolData(
                                    beatmap_id: $vot4QualifierId,
                                    round_id: $vot4QualifierRoundId,
                                    tournament_id: $vot4QualifierTournamentId,
                                    type: $vot4QualifierType,
                                    image: $vot4QualifierImage,
                                    url: $vot4QualifierUrl,
                                    name: $vot4QualifierName,
                                    diff_name: $vot4QualifierDifficultyName,
                                    fa: $vot4QualifierFeatureArtist,
                                    mapper: $vot4QualifierMapper,
                                    mapper_url: $vot4QualifierMapperUrl,
                                    diff: $vot4QualifierDifficulty,
                                    length: $vot4QualifierLength,
                                    bpm: $vot4QualifierOverallSpeed,
                                    od: $vot4QualifierOverallDifficulty,
                                    hp: $vot4QualifierOverallHealth
                                );
                            } else {
                                // TODO: UPDATE query here (change the 'view'
                                // table only, not the actual table if all data
                                // stay the same).
                                error_log(
                                    message: 'Mappool data exist, simply ignoring it...',
                                    message_type: 0
                                );
                            }
                        }

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
                    }
                    break;

                // *** VOT4 ROUND OF 16 MAPPOOL DATA ***
                case preg_match(
                    pattern: '/^(roundof16|roundof16s|ro16|ro16s)$/i',
                    subject: $vot4RoundName
                ):
                    // Database use the abbreviation of each round's name
                    $vot4AbbreviateRoundName = 'RO16';

                    if (!isset($_COOKIE['vot_access_token'])) {
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
                    } else {
                        // TODO: storing data should only be done by host or
                        // higher role level
                        $votMappoolAccessToken = $_COOKIE['vot_access_token'];

                        $vot4RoundOf16JsonData = $votMappoolJsonUsableData[$votTournamentName][$vot4AbbreviateRoundName];
                        foreach ($vot4RoundOf16JsonData as $vot4RoundOf16JsonType => $vot4RoundOf16JsonId) {
                            $vot4RoundOf16Data = getMappoolData(
                                id: $vot4RoundOf16JsonId,
                                token: $votMappoolAccessToken
                            );

                            $vot4RoundOf16Id                = $vot4RoundOf16Data['id'];
                            $vot4RoundOf16RoundId           = $vot4AbbreviateRoundName;
                            $vot4RoundOf16TournamentId      = $votTournamentName;
                            $vot4RoundOf16Type              = $vot4RoundOf16JsonType;
                            $vot4RoundOf16Image             = $vot4RoundOf16Data['beatmapset']['covers']['cover'];
                            $vot4RoundOf16Url               = $vot4RoundOf16Data['url'];
                            $vot4RoundOf16Name              = $vot4RoundOf16Data['beatmapset']['title'];
                            $vot4RoundOf16DifficultyName    = $vot4RoundOf16Data['version'];
                            $vot4RoundOf16FeatureArtist     = $vot4RoundOf16Data['beatmapset']['artist'];
                            $vot4RoundOf16Mapper            = $vot4RoundOf16Data['beatmapset']['creator'];
                            $vot4RoundOf16MapperUrl         = "https://osu.ppy.sh/users/{$vot4RoundOf16Data['beatmapset']['user_id']}";
                            $vot4RoundOf16Difficulty        = $vot4RoundOf16Data['difficulty_rating'];
                            $vot4RoundOf16Length            = $vot4RoundOf16Data['total_length'];
                            $vot4RoundOf16OverallSpeed      = $vot4RoundOf16Data['beatmapset']['bpm'];
                            $vot4RoundOf16OverallDifficulty = $vot4RoundOf16Data['accuracy'];
                            $vot4RoundOf16OverallHealth     = $vot4RoundOf16Data['drain'];

                            if (
                                !checkMappoolData(
                                    id: $vot4RoundOf16Id,
                                    round: $vot4RoundOf16RoundId,
                                    tournament: $vot4RoundOf16TournamentId
                                )
                            ) {
                                createMappoolData(
                                    beatmap_id: $vot4RoundOf16Id,
                                    round_id: $vot4RoundOf16RoundId,
                                    tournament_id: $vot4RoundOf16TournamentId,
                                    type: $vot4RoundOf16Type,
                                    image: $vot4RoundOf16Image,
                                    url: $vot4RoundOf16Url,
                                    name: $vot4RoundOf16Name,
                                    diff_name: $vot4RoundOf16DifficultyName,
                                    fa: $vot4RoundOf16FeatureArtist,
                                    mapper: $vot4RoundOf16Mapper,
                                    mapper_url: $vot4RoundOf16MapperUrl,
                                    diff: $vot4RoundOf16Difficulty,
                                    length: $vot4RoundOf16Length,
                                    bpm: $vot4RoundOf16OverallSpeed,
                                    od: $vot4RoundOf16OverallDifficulty,
                                    hp: $vot4RoundOf16OverallHealth
                                );
                            } else {
                                // TODO: UPDATE query here (change the 'view'
                                // table only, not the actual table if all data
                                // stay the same).
                                error_log(
                                    message: 'Mappool data exist, simply ignoring it...',
                                    message_type: 0
                                );
                            }
                        }

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
                    }
                    break;

                // *** VOT4 QUARTER FINAL MAPPOOL DATA ***
                case preg_match(
                    pattern: '/^(quarterfinals|qf|qfs)$/i',
                    subject: $vot4RoundName
                ):
                    // Database use the abbreviation of each round's name
                    $vot4AbbreviateRoundName = 'QF';

                    if (!isset($_COOKIE['vot_access_token'])) {
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
                    } else {
                        // TODO: storing data should only be done by host or
                        // higher role level
                        $votMappoolAccessToken = $_COOKIE['vot_access_token'];

                        $vot4QuarterFinalJsonData = $votMappoolJsonUsableData[$votTournamentName][$vot4AbbreviateRoundName];
                        foreach ($vot4QuarterFinalJsonData as $vot4QuarterFinalJsonType => $vot4QuarterFinalJsonId) {
                            $vot4QuarterFinalData = getMappoolData(
                                id: $vot4QuarterFinalJsonId,
                                token: $votMappoolAccessToken
                            );

                            $vot4QuarterFinalId                = $vot4QuarterFinalData['id'];
                            $vot4QuarterFinalRoundId           = $vot4AbbreviateRoundName;
                            $vot4QuarterFinalTournamentId      = $votTournamentName;
                            $vot4QuarterFinalType              = $vot4QuarterFinalJsonType;
                            $vot4QuarterFinalImage             = $vot4QuarterFinalData['beatmapset']['covers']['cover'];
                            $vot4QuarterFinalUrl               = $vot4QuarterFinalData['url'];
                            $vot4QuarterFinalName              = $vot4QuarterFinalData['beatmapset']['title'];
                            $vot4QuarterFinalDifficultyName    = $vot4QuarterFinalData['version'];
                            $vot4QuarterFinalFeatureArtist     = $vot4QuarterFinalData['beatmapset']['artist'];
                            $vot4QuarterFinalMapper            = $vot4QuarterFinalData['beatmapset']['creator'];
                            $vot4QuarterFinalMapperUrl         = "https://osu.ppy.sh/users/{$vot4QuarterFinalData['beatmapset']['user_id']}";
                            $vot4QuarterFinalDifficulty        = $vot4QuarterFinalData['difficulty_rating'];
                            $vot4QuarterFinalLength            = $vot4QuarterFinalData['total_length'];
                            $vot4QuarterFinalOverallSpeed      = $vot4QuarterFinalData['beatmapset']['bpm'];
                            $vot4QuarterFinalOverallDifficulty = $vot4QuarterFinalData['accuracy'];
                            $vot4QuarterFinalOverallHealth     = $vot4QuarterFinalData['drain'];

                            if (
                                !checkMappoolData(
                                    id: $vot4QuarterFinalId,
                                    round: $vot4QuarterFinalRoundId,
                                    tournament: $vot4QuarterFinalTournamentId
                                )
                            ) {
                                createMappoolData(
                                    beatmap_id: $vot4QuarterFinalId,
                                    round_id: $vot4QuarterFinalRoundId,
                                    tournament_id: $vot4QuarterFinalTournamentId,
                                    type: $vot4QuarterFinalType,
                                    image: $vot4QuarterFinalImage,
                                    url: $vot4QuarterFinalUrl,
                                    name: $vot4QuarterFinalName,
                                    diff_name: $vot4QuarterFinalDifficultyName,
                                    fa: $vot4QuarterFinalFeatureArtist,
                                    mapper: $vot4QuarterFinalMapper,
                                    mapper_url: $vot4QuarterFinalMapperUrl,
                                    diff: $vot4QuarterFinalDifficulty,
                                    length: $vot4QuarterFinalLength,
                                    bpm: $vot4QuarterFinalOverallSpeed,
                                    od: $vot4QuarterFinalOverallDifficulty,
                                    hp: $vot4QuarterFinalOverallHealth
                                );
                            } else {
                                // TODO: UPDATE query here (change the 'view'
                                // table only, not the actual table if all data
                                // stay the same).
                                error_log(
                                    message: 'Mappool data exist, simply ignoring it...',
                                    message_type: 0
                                );
                            }
                        }

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
                    }
                    break;

                // *** VOT4 SEMI FINAL MAPPOOL DATA ***
                case preg_match(
                    pattern: '/^(semifinals|sf|sfs)$/i',
                    subject: $vot4RoundName
                ):
                    // Database use the abbreviation of each round's name
                    $vot4AbbreviateRoundName = 'SF';

                    if (!isset($_COOKIE['vot_access_token'])) {
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
                    } else {
                        // TODO: storing data should only be done by host or
                        // higher role level
                        $votMappoolAccessToken = $_COOKIE['vot_access_token'];

                        $vot4SemiFinalJsonData = $votMappoolJsonUsableData[$votTournamentName][$vot4AbbreviateRoundName];
                        foreach ($vot4SemiFinalJsonData as $vot4SemiFinalJsonType => $vot4SemiFinalJsonId) {
                            $vot4SemiFinalData = getMappoolData(
                                id: $vot4SemiFinalJsonId,
                                token: $votMappoolAccessToken
                            );

                            $vot4SemiFinalId                = $vot4SemiFinalData['id'];
                            $vot4SemiFinalRoundId           = $vot4AbbreviateRoundName;
                            $vot4SemiFinalTournamentId      = $votTournamentName;
                            $vot4SemiFinalType              = $vot4SemiFinalJsonType;
                            $vot4SemiFinalImage             = $vot4SemiFinalData['beatmapset']['covers']['cover'];
                            $vot4SemiFinalUrl               = $vot4SemiFinalData['url'];
                            $vot4SemiFinalName              = $vot4SemiFinalData['beatmapset']['title'];
                            $vot4SemiFinalDifficultyName    = $vot4SemiFinalData['version'];
                            $vot4SemiFinalFeatureArtist     = $vot4SemiFinalData['beatmapset']['artist'];
                            $vot4SemiFinalMapper            = $vot4SemiFinalData['beatmapset']['creator'];
                            $vot4SemiFinalMapperUrl         = "https://osu.ppy.sh/users/{$vot4SemiFinalData['beatmapset']['user_id']}";
                            $vot4SemiFinalDifficulty        = $vot4SemiFinalData['difficulty_rating'];
                            $vot4SemiFinalLength            = $vot4SemiFinalData['total_length'];
                            $vot4SemiFinalOverallSpeed      = $vot4SemiFinalData['beatmapset']['bpm'];
                            $vot4SemiFinalOverallDifficulty = $vot4SemiFinalData['accuracy'];
                            $vot4SemiFinalOverallHealth     = $vot4SemiFinalData['drain'];

                            if (
                                !checkMappoolData(
                                    id: $vot4SemiFinalId,
                                    round: $vot4SemiFinalRoundId,
                                    tournament: $vot4SemiFinalTournamentId
                                )
                            ) {
                                createMappoolData(
                                    beatmap_id: $vot4SemiFinalId,
                                    round_id: $vot4SemiFinalRoundId,
                                    tournament_id: $vot4SemiFinalTournamentId,
                                    type: $vot4SemiFinalType,
                                    image: $vot4SemiFinalImage,
                                    url: $vot4SemiFinalUrl,
                                    name: $vot4SemiFinalName,
                                    diff_name: $vot4SemiFinalDifficultyName,
                                    fa: $vot4SemiFinalFeatureArtist,
                                    mapper: $vot4SemiFinalMapper,
                                    mapper_url: $vot4SemiFinalMapperUrl,
                                    diff: $vot4SemiFinalDifficulty,
                                    length: $vot4SemiFinalLength,
                                    bpm: $vot4SemiFinalOverallSpeed,
                                    od: $vot4SemiFinalOverallDifficulty,
                                    hp: $vot4SemiFinalOverallHealth
                                );
                            } else {
                                // TODO: UPDATE query here (change the 'view'
                                // table only, not the actual table if all data
                                // stay the same).
                                error_log(
                                    message: 'Mappool data exist, simply ignoring it...',
                                    message_type: 0
                                );
                            }
                        }

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
                    }
                    break;

                // *** VOT4 FINAL MAPPOOL DATA ***
                case preg_match(
                    pattern: '/^(finals|fnl|fnls)$/i',
                    subject: $vot4RoundName
                ):
                    // Database use the abbreviation of each round's name
                    $vot4AbbreviateRoundName = 'FNL';

                    if (!isset($_COOKIE['vot_access_token'])) {
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
                    } else {
                        // TODO: storing data should only be done by host or
                        // higher role level
                        $votMappoolAccessToken = $_COOKIE['vot_access_token'];

                        $vot4FinalJsonData = $votMappoolJsonUsableData[$votTournamentName][$vot4AbbreviateRoundName];
                        foreach ($vot4FinalJsonData as $vot4FinalJsonType => $vot4FinalJsonId) {
                            $vot4FinalData = getMappoolData(
                                id: $vot4FinalJsonId,
                                token: $votMappoolAccessToken
                            );

                            $vot4FinalId                = $vot4FinalData['id'];
                            $vot4FinalRoundId           = $vot4AbbreviateRoundName;
                            $vot4FinalTournamentId      = $votTournamentName;
                            $vot4FinalType              = $vot4FinalJsonType;
                            $vot4FinalImage             = $vot4FinalData['beatmapset']['covers']['cover'];
                            $vot4FinalUrl               = $vot4FinalData['url'];
                            $vot4FinalName              = $vot4FinalData['beatmapset']['title'];
                            $vot4FinalDifficultyName    = $vot4FinalData['version'];
                            $vot4FinalFeatureArtist     = $vot4FinalData['beatmapset']['artist'];
                            $vot4FinalMapper            = $vot4FinalData['beatmapset']['creator'];
                            $vot4FinalMapperUrl         = "https://osu.ppy.sh/users/{$vot4FinalData['beatmapset']['user_id']}";
                            $vot4FinalDifficulty        = $vot4FinalData['difficulty_rating'];
                            $vot4FinalLength            = $vot4FinalData['total_length'];
                            $vot4FinalOverallSpeed      = $vot4FinalData['beatmapset']['bpm'];
                            $vot4FinalOverallDifficulty = $vot4FinalData['accuracy'];
                            $vot4FinalOverallHealth     = $vot4FinalData['drain'];

                            if (
                                !checkMappoolData(
                                    id: $vot4FinalId,
                                    round: $vot4FinalRoundId,
                                    tournament: $vot4FinalTournamentId
                                )
                            ) {
                                createMappoolData(
                                    beatmap_id: $vot4FinalId,
                                    round_id: $vot4FinalRoundId,
                                    tournament_id: $vot4FinalTournamentId,
                                    type: $vot4FinalType,
                                    image: $vot4FinalImage,
                                    url: $vot4FinalUrl,
                                    name: $vot4FinalName,
                                    diff_name: $vot4FinalDifficultyName,
                                    fa: $vot4FinalFeatureArtist,
                                    mapper: $vot4FinalMapper,
                                    mapper_url: $vot4FinalMapperUrl,
                                    diff: $vot4FinalDifficulty,
                                    length: $vot4FinalLength,
                                    bpm: $vot4FinalOverallSpeed,
                                    od: $vot4FinalOverallDifficulty,
                                    hp: $vot4FinalOverallHealth
                                );
                            } else {
                                // TODO: UPDATE query here (change the 'view'
                                // table only, not the actual table if all data
                                // stay the same).
                                error_log(
                                    message: 'Mappool data exist, simply ignoring it...',
                                    message_type: 0
                                );
                            }
                        }

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
                    }
                    break;

                // *** VOT4 GRAND FINAL & ALL STAR MAPPOOL DATA ***
                case preg_match(
                    pattern: '/^(grandfinals|gf|gfs)$/i',
                    subject: $vot4RoundName
                ):
                case preg_match(
                    pattern: '/^(allstars|astr|astrs|alstr|alstrs)$/i',
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

                    if (!isset($_COOKIE['vot_access_token'])) {
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
                    } else {
                        // TODO: storing data should only be done by host or
                        // higher role level
                        $votMappoolAccessToken = $_COOKIE['vot_access_token'];

                        $vot4GrandFinalJsonData = $votMappoolJsonUsableData[$votTournamentName][$vot4AbbreviateRoundName];
                        foreach ($vot4GrandFinalJsonData as $vot4GrandFinalJsonType => $vot4GrandFinalJsonId) {
                            $vot4GrandFinalData = getMappoolData(
                                id: $vot4GrandFinalJsonId,
                                token: $votMappoolAccessToken
                            );

                            if (isset($vot4GrandFinalData['error'])) {
                                // NOTE: bypass for now. Generally, only host or
                                // higher role level can perform this task.
                                echo 'BEATMAP DELETED!!';
                            } else {
                                $vot4GrandFinalId                = $vot4GrandFinalData['id'];
                                $vot4GrandFinalRoundId           = $vot4AbbreviateRoundName;
                                $vot4GrandFinalTournamentId      = $votTournamentName;
                                $vot4GrandFinalType              = $vot4GrandFinalJsonType;
                                $vot4GrandFinalImage             = $vot4GrandFinalData['beatmapset']['covers']['cover'];
                                $vot4GrandFinalUrl               = $vot4GrandFinalData['url'];
                                $vot4GrandFinalName              = $vot4GrandFinalData['beatmapset']['title'];
                                $vot4GrandFinalDifficultyName    = $vot4GrandFinalData['version'];
                                $vot4GrandFinalFeatureArtist     = $vot4GrandFinalData['beatmapset']['artist'];
                                $vot4GrandFinalMapper            = $vot4GrandFinalData['beatmapset']['creator'];
                                $vot4GrandFinalMapperUrl         = "https://osu.ppy.sh/users/{$vot4GrandFinalData['beatmapset']['user_id']}";
                                $vot4GrandFinalDifficulty        = $vot4GrandFinalData['difficulty_rating'];
                                $vot4GrandFinalLength            = $vot4GrandFinalData['total_length'];
                                $vot4GrandFinalOverallSpeed      = $vot4GrandFinalData['beatmapset']['bpm'];
                                $vot4GrandFinalOverallDifficulty = $vot4GrandFinalData['accuracy'];
                                $vot4GrandFinalOverallHealth     = $vot4GrandFinalData['drain'];

                                if (
                                    !checkMappoolData(
                                        id: $vot4GrandFinalId,
                                        round: $vot4GrandFinalRoundId,
                                        tournament: $vot4GrandFinalTournamentId
                                    )
                                ) {
                                    createMappoolData(
                                        beatmap_id: $vot4GrandFinalId,
                                        round_id: $vot4GrandFinalRoundId,
                                        tournament_id: $vot4GrandFinalTournamentId,
                                        type: $vot4GrandFinalType,
                                        image: $vot4GrandFinalImage,
                                        url: $vot4GrandFinalUrl,
                                        name: $vot4GrandFinalName,
                                        diff_name: $vot4GrandFinalDifficultyName,
                                        fa: $vot4GrandFinalFeatureArtist,
                                        mapper: $vot4GrandFinalMapper,
                                        mapper_url: $vot4GrandFinalMapperUrl,
                                        diff: $vot4GrandFinalDifficulty,
                                        length: $vot4GrandFinalLength,
                                        bpm: $vot4GrandFinalOverallSpeed,
                                        od: $vot4GrandFinalOverallDifficulty,
                                        hp: $vot4GrandFinalOverallHealth
                                    );
                                } else {
                                    // TODO: UPDATE query here (change the 'view'
                                    // table only, not the actual table if all data
                                    // stay the same).
                                    error_log(
                                        message: 'Mappool data exist, simply ignoring it...',
                                        message_type: 0
                                    );
                                }
                            }
                        }

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
