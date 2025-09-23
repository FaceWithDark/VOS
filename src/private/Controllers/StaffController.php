<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require __DIR__ . '/../Models/Staff.php';


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
    case 'VOT4':
        if (!isset($_GET['role'])) {
            // Just show the page without any actions
            require __DIR__ . '/../Views/Tournament/Vot4StaffView.php';
        } else {
            // Show the page again after actions have been done
            require __DIR__ . '/../Views/Tournament/Vot4StaffView.php';
            $vot4RoleName = $_GET['role'];

            switch ($vot4RoleName) {
                case 'host':
                case 'Host':
                case 'HOST':
                    // Database use the abbreviation of each role's name
                    $vot4AbbreviateRoleName = 'HST';

                    $vot4StaffViewData = readStaffData(
                        role: $vot4AbbreviateRoleName,
                        tournament: $votTournamentName
                    );

                    foreach ($vot4StaffViewData as $vot4StaffData) {
                        $vot4StaffName      = htmlspecialchars($vot4StaffData['userName']);
                        $vot4StaffFlag      = htmlspecialchars("https://flagsapi.com/{$vot4StaffData['userFlag']}/shiny/24.png");
                        $vot4StaffImage     = htmlspecialchars($vot4StaffData['userImage']);
                        $vot4StaffUrl       = htmlspecialchars($vot4StaffData['userUrl']);
                        $vot4StaffRank
                            = ($vot4StaffData['userRank'] !== 0)
                            ? ($vot4StaffData['userRank'])
                            : (htmlspecialchars('NO DATA')); // That one staff not even playin' a single taiko map...
                        $vot4StaffTimeZone  = htmlspecialchars($vot4StaffData['userTimeZone']);

                        $staffInformationTemplate =
                            <<<EOL
                            <section class="vot4-staff">
                                <div class="box-container">
                                    <div class="staff-header">
                                        <div class="staff-name">
                                            <h1>$vot4StaffName</h1>
                                        </div>
                                        <div class="staff-flag">
                                            <img src="$vot4StaffFlag" alt="Staff Country Flag">
                                        </div>
                                    </div>

                                    <div class="staff-body">
                                        <div class="staff-image">
                                            <a href="$vot4StaffUrl">
                                                <img src="$vot4StaffImage" alt="Staff Avatar">
                                            </a>
                                        </div>
                                    </div>

                                    <div class="staff-footer">
                                        <div class="staff-rank">
                                            <h3>Rank: $vot4StaffRank</h3>
                                        </div>
                                        <div class="staff-time-zone">
                                            <h3>Time Zone: $vot4StaffTimeZone</h3>
                                        </div>
                                    </div>
                                </div>
                            </section>
                            EOL;

                        // It would be much more nasty if I tried to output this using the traditional mixed HTML & PHP codes
                        echo $staffInformationTemplate;
                    }
                    break;

                case 'mappooler':
                case 'Mappooler':
                case 'MAPPOOLER':
                    // Database use the abbreviation of each role's name
                    $vot4AbbreviateRoleName = 'MAPLR';

                    $vot4StaffViewData = readStaffData(
                        role: $vot4AbbreviateRoleName,
                        tournament: $votTournamentName
                    );

                    foreach ($vot4StaffViewData as $vot4StaffData) {
                        $vot4StaffName      = htmlspecialchars($vot4StaffData['userName']);
                        $vot4StaffFlag      = htmlspecialchars("https://flagsapi.com/{$vot4StaffData['userFlag']}/shiny/24.png");
                        $vot4StaffImage     = htmlspecialchars($vot4StaffData['userImage']);
                        $vot4StaffUrl       = htmlspecialchars($vot4StaffData['userUrl']);
                        $vot4StaffRank
                            = ($vot4StaffData['userRank'] !== 0)
                            ? ($vot4StaffData['userRank'])
                            : (htmlspecialchars('NO DATA')); // That one staff not even playin' a single taiko map...
                        $vot4StaffTimeZone  = htmlspecialchars($vot4StaffData['userTimeZone']);

                        $staffInformationTemplate =
                            <<<EOL
                            <section class="vot4-staff">
                                <div class="box-container">
                                    <div class="staff-header">
                                        <div class="staff-name">
                                            <h1>$vot4StaffName</h1>
                                        </div>
                                        <div class="staff-flag">
                                            <img src="$vot4StaffFlag" alt="Staff Country Flag">
                                        </div>
                                    </div>

                                    <div class="staff-body">
                                        <div class="staff-image">
                                            <a href="$vot4StaffUrl">
                                                <img src="$vot4StaffImage" alt="Staff Avatar">
                                            </a>
                                        </div>
                                    </div>

                                    <div class="staff-footer">
                                        <div class="staff-rank">
                                            <h3>Rank: $vot4StaffRank</h3>
                                        </div>
                                        <div class="staff-time-zone">
                                            <h3>Time Zone: $vot4StaffTimeZone</h3>
                                        </div>
                                    </div>
                                </div>
                            </section>
                            EOL;

                        // It would be much more nasty if I tried to output this using the traditional mixed HTML & PHP codes
                        echo $staffInformationTemplate;
                    }
                    break;

                case 'gfx_vfx':
                case 'Gfx_vfx':
                case 'gfx_Vfx':
                case 'Gfx_Vfx':
                case 'GFX_VFX':
                    // Database use the abbreviation of each role's name
                    $vot4AbbreviateRoleName = 'GNVFX';

                    $vot4StaffViewData = readStaffData(
                        role: $vot4AbbreviateRoleName,
                        tournament: $votTournamentName
                    );

                    foreach ($vot4StaffViewData as $vot4StaffData) {
                        $vot4StaffName      = htmlspecialchars($vot4StaffData['userName']);
                        $vot4StaffFlag      = htmlspecialchars("https://flagsapi.com/{$vot4StaffData['userFlag']}/shiny/24.png");
                        $vot4StaffImage     = htmlspecialchars($vot4StaffData['userImage']);
                        $vot4StaffUrl       = htmlspecialchars($vot4StaffData['userUrl']);
                        $vot4StaffRank
                            = ($vot4StaffData['userRank'] !== 0)
                            ? ($vot4StaffData['userRank'])
                            : (htmlspecialchars('NO DATA')); // That one staff not even playin' a single taiko map...
                        $vot4StaffTimeZone  = htmlspecialchars($vot4StaffData['userTimeZone']);

                        $staffInformationTemplate =
                            <<<EOL
                            <section class="vot4-staff">
                                <div class="box-container">
                                    <div class="staff-header">
                                        <div class="staff-name">
                                            <h1>$vot4StaffName</h1>
                                        </div>
                                        <div class="staff-flag">
                                            <img src="$vot4StaffFlag" alt="Staff Country Flag">
                                        </div>
                                    </div>

                                    <div class="staff-body">
                                        <div class="staff-image">
                                            <a href="$vot4StaffUrl">
                                                <img src="$vot4StaffImage" alt="Staff Avatar">
                                            </a>
                                        </div>
                                    </div>

                                    <div class="staff-footer">
                                        <div class="staff-rank">
                                            <h3>Rank: $vot4StaffRank</h3>
                                        </div>
                                        <div class="staff-time-zone">
                                            <h3>Time Zone: $vot4StaffTimeZone</h3>
                                        </div>
                                    </div>
                                </div>
                            </section>
                            EOL;

                        // It would be much more nasty if I tried to output this using the traditional mixed HTML & PHP codes
                        echo $staffInformationTemplate;
                    }
                    break;

                case 'mapper':
                case 'Mapper':
                case 'MAPPER':
                    // Database use the abbreviation of each role's name
                    $vot4AbbreviateRoleName = 'MAPPR';

                    $vot4StaffViewData = readStaffData(
                        role: $vot4AbbreviateRoleName,
                        tournament: $votTournamentName
                    );

                    foreach ($vot4StaffViewData as $vot4StaffData) {
                        $vot4StaffName      = htmlspecialchars($vot4StaffData['userName']);
                        $vot4StaffFlag      = htmlspecialchars("https://flagsapi.com/{$vot4StaffData['userFlag']}/shiny/24.png");
                        $vot4StaffImage     = htmlspecialchars($vot4StaffData['userImage']);
                        $vot4StaffUrl       = htmlspecialchars($vot4StaffData['userUrl']);
                        $vot4StaffRank
                            = ($vot4StaffData['userRank'] !== 0)
                            ? ($vot4StaffData['userRank'])
                            : (htmlspecialchars('NO DATA')); // That one staff not even playin' a single taiko map...
                        $vot4StaffTimeZone  = htmlspecialchars($vot4StaffData['userTimeZone']);

                        $staffInformationTemplate =
                            <<<EOL
                            <section class="vot4-staff">
                                <div class="box-container">
                                    <div class="staff-header">
                                        <div class="staff-name">
                                            <h1>$vot4StaffName</h1>
                                        </div>
                                        <div class="staff-flag">
                                            <img src="$vot4StaffFlag" alt="Staff Country Flag">
                                        </div>
                                    </div>

                                    <div class="staff-body">
                                        <div class="staff-image">
                                            <a href="$vot4StaffUrl">
                                                <img src="$vot4StaffImage" alt="Staff Avatar">
                                            </a>
                                        </div>
                                    </div>

                                    <div class="staff-footer">
                                        <div class="staff-rank">
                                            <h3>Rank: $vot4StaffRank</h3>
                                        </div>
                                        <div class="staff-time-zone">
                                            <h3>Time Zone: $vot4StaffTimeZone</h3>
                                        </div>
                                    </div>
                                </div>
                            </section>
                            EOL;

                        // It would be much more nasty if I tried to output this using the traditional mixed HTML & PHP codes
                        echo $staffInformationTemplate;
                    }
                    break;

                case 'play_tester':
                case 'Play_tester':
                case 'Play_Tester':
                case 'PLAY_TESTER':
                    // Database use the abbreviation of each role's name
                    $vot4AbbreviateRoleName = 'PLTST';

                    $vot4StaffViewData = readStaffData(
                        role: $vot4AbbreviateRoleName,
                        tournament: $votTournamentName
                    );

                    foreach ($vot4StaffViewData as $vot4StaffData) {
                        $vot4StaffName      = htmlspecialchars($vot4StaffData['userName']);
                        $vot4StaffFlag      = htmlspecialchars("https://flagsapi.com/{$vot4StaffData['userFlag']}/shiny/24.png");
                        $vot4StaffImage     = htmlspecialchars($vot4StaffData['userImage']);
                        $vot4StaffUrl       = htmlspecialchars($vot4StaffData['userUrl']);
                        $vot4StaffRank
                            = ($vot4StaffData['userRank'] !== 0)
                            ? ($vot4StaffData['userRank'])
                            : (htmlspecialchars('NO DATA')); // That one staff not even playin' a single taiko map...
                        $vot4StaffTimeZone  = htmlspecialchars($vot4StaffData['userTimeZone']);

                        $staffInformationTemplate =
                            <<<EOL
                            <section class="vot4-staff">
                                <div class="box-container">
                                    <div class="staff-header">
                                        <div class="staff-name">
                                            <h1>$vot4StaffName</h1>
                                        </div>
                                        <div class="staff-flag">
                                            <img src="$vot4StaffFlag" alt="Staff Country Flag">
                                        </div>
                                    </div>

                                    <div class="staff-body">
                                        <div class="staff-image">
                                            <a href="$vot4StaffUrl">
                                                <img src="$vot4StaffImage" alt="Staff Avatar">
                                            </a>
                                        </div>
                                    </div>

                                    <div class="staff-footer">
                                        <div class="staff-rank">
                                            <h3>Rank: $vot4StaffRank</h3>
                                        </div>
                                        <div class="staff-time-zone">
                                            <h3>Time Zone: $vot4StaffTimeZone</h3>
                                        </div>
                                    </div>
                                </div>
                            </section>
                            EOL;

                        // It would be much more nasty if I tried to output this using the traditional mixed HTML & PHP codes
                        echo $staffInformationTemplate;
                    }
                    break;

                case 'referee':
                case 'Referee':
                case 'REFEREE':
                    // Database use the abbreviation of each role's name
                    $vot4AbbreviateRoleName = 'REFRE';

                    $vot4StaffViewData = readStaffData(
                        role: $vot4AbbreviateRoleName,
                        tournament: $votTournamentName
                    );

                    foreach ($vot4StaffViewData as $vot4StaffData) {
                        $vot4StaffName      = htmlspecialchars($vot4StaffData['userName']);
                        $vot4StaffFlag      = htmlspecialchars("https://flagsapi.com/{$vot4StaffData['userFlag']}/shiny/24.png");
                        $vot4StaffImage     = htmlspecialchars($vot4StaffData['userImage']);
                        $vot4StaffUrl       = htmlspecialchars($vot4StaffData['userUrl']);
                        $vot4StaffRank
                            = ($vot4StaffData['userRank'] !== 0)
                            ? ($vot4StaffData['userRank'])
                            : (htmlspecialchars('NO DATA')); // That one staff not even playin' a single taiko map...
                        $vot4StaffTimeZone  = htmlspecialchars($vot4StaffData['userTimeZone']);

                        $staffInformationTemplate =
                            <<<EOL
                            <section class="vot4-staff">
                                <div class="box-container">
                                    <div class="staff-header">
                                        <div class="staff-name">
                                            <h1>$vot4StaffName</h1>
                                        </div>
                                        <div class="staff-flag">
                                            <img src="$vot4StaffFlag" alt="Staff Country Flag">
                                        </div>
                                    </div>

                                    <div class="staff-body">
                                        <div class="staff-image">
                                            <a href="$vot4StaffUrl">
                                                <img src="$vot4StaffImage" alt="Staff Avatar">
                                            </a>
                                        </div>
                                    </div>

                                    <div class="staff-footer">
                                        <div class="staff-rank">
                                            <h3>Rank: $vot4StaffRank</h3>
                                        </div>
                                        <div class="staff-time-zone">
                                            <h3>Time Zone: $vot4StaffTimeZone</h3>
                                        </div>
                                    </div>
                                </div>
                            </section>
                            EOL;

                        // It would be much more nasty if I tried to output this using the traditional mixed HTML & PHP codes
                        echo $staffInformationTemplate;
                    }
                    break;

                case 'streamer':
                case 'Streamer':
                case 'STREAMER':
                    // Database use the abbreviation of each role's name
                    $vot4AbbreviateRoleName = 'STRMR';

                    $vot4StaffViewData = readStaffData(
                        role: $vot4AbbreviateRoleName,
                        tournament: $votTournamentName
                    );

                    foreach ($vot4StaffViewData as $vot4StaffData) {
                        $vot4StaffName      = htmlspecialchars($vot4StaffData['userName']);
                        $vot4StaffFlag      = htmlspecialchars("https://flagsapi.com/{$vot4StaffData['userFlag']}/shiny/24.png");
                        $vot4StaffImage     = htmlspecialchars($vot4StaffData['userImage']);
                        $vot4StaffUrl       = htmlspecialchars($vot4StaffData['userUrl']);
                        $vot4StaffRank
                            = ($vot4StaffData['userRank'] !== 0)
                            ? ($vot4StaffData['userRank'])
                            : (htmlspecialchars('NO DATA')); // That one staff not even playin' a single taiko map...
                        $vot4StaffTimeZone  = htmlspecialchars($vot4StaffData['userTimeZone']);

                        $staffInformationTemplate =
                            <<<EOL
                            <section class="vot4-staff">
                                <div class="box-container">
                                    <div class="staff-header">
                                        <div class="staff-name">
                                            <h1>$vot4StaffName</h1>
                                        </div>
                                        <div class="staff-flag">
                                            <img src="$vot4StaffFlag" alt="Staff Country Flag">
                                        </div>
                                    </div>

                                    <div class="staff-body">
                                        <div class="staff-image">
                                            <a href="$vot4StaffUrl">
                                                <img src="$vot4StaffImage" alt="Staff Avatar">
                                            </a>
                                        </div>
                                    </div>

                                    <div class="staff-footer">
                                        <div class="staff-rank">
                                            <h3>Rank: $vot4StaffRank</h3>
                                        </div>
                                        <div class="staff-time-zone">
                                            <h3>Time Zone: $vot4StaffTimeZone</h3>
                                        </div>
                                    </div>
                                </div>
                            </section>
                            EOL;

                        // It would be much more nasty if I tried to output this using the traditional mixed HTML & PHP codes
                        echo $staffInformationTemplate;
                    }
                    break;

                case 'commentator':
                case 'Commentator':
                case 'COMMENTATOR':
                    // Database use the abbreviation of each role's name
                    $vot4AbbreviateRoleName = 'CMNTR';

                    $vot4StaffViewData = readStaffData(
                        role: $vot4AbbreviateRoleName,
                        tournament: $votTournamentName
                    );

                    foreach ($vot4StaffViewData as $vot4StaffData) {
                        $vot4StaffName      = htmlspecialchars($vot4StaffData['userName']);
                        $vot4StaffFlag      = htmlspecialchars("https://flagsapi.com/{$vot4StaffData['userFlag']}/shiny/24.png");
                        $vot4StaffImage     = htmlspecialchars($vot4StaffData['userImage']);
                        $vot4StaffUrl       = htmlspecialchars($vot4StaffData['userUrl']);
                        $vot4StaffRank
                            = ($vot4StaffData['userRank'] !== 0)
                            ? ($vot4StaffData['userRank'])
                            : (htmlspecialchars('NO DATA')); // That one staff not even playin' a single taiko map...
                        $vot4StaffTimeZone  = htmlspecialchars($vot4StaffData['userTimeZone']);

                        $staffInformationTemplate =
                            <<<EOL
                            <section class="vot4-staff">
                                <div class="box-container">
                                    <div class="staff-header">
                                        <div class="staff-name">
                                            <h1>$vot4StaffName</h1>
                                        </div>
                                        <div class="staff-flag">
                                            <img src="$vot4StaffFlag" alt="Staff Country Flag">
                                        </div>
                                    </div>

                                    <div class="staff-body">
                                        <div class="staff-image">
                                            <a href="$vot4StaffUrl">
                                                <img src="$vot4StaffImage" alt="Staff Avatar">
                                            </a>
                                        </div>
                                    </div>

                                    <div class="staff-footer">
                                        <div class="staff-rank">
                                            <h3>Rank: $vot4StaffRank</h3>
                                        </div>
                                        <div class="staff-time-zone">
                                            <h3>Time Zone: $vot4StaffTimeZone</h3>
                                        </div>
                                    </div>
                                </div>
                            </section>
                            EOL;

                        // It would be much more nasty if I tried to output this using the traditional mixed HTML & PHP codes
                        echo $staffInformationTemplate;
                    }
                    break;

                case 'statistician':
                case 'Statistician':
                case 'STATISTICIAN':
                    // Database use the abbreviation of each role's name
                    $vot4AbbreviateRoleName = 'STACN';

                    $vot4StaffViewData = readStaffData(
                        role: $vot4AbbreviateRoleName,
                        tournament: $votTournamentName
                    );

                    foreach ($vot4StaffViewData as $vot4StaffData) {
                        $vot4StaffName      = htmlspecialchars($vot4StaffData['userName']);
                        $vot4StaffFlag      = htmlspecialchars("https://flagsapi.com/{$vot4StaffData['userFlag']}/shiny/24.png");
                        $vot4StaffImage     = htmlspecialchars($vot4StaffData['userImage']);
                        $vot4StaffUrl       = htmlspecialchars($vot4StaffData['userUrl']);
                        $vot4StaffRank
                            = ($vot4StaffData['userRank'] !== 0)
                            ? ($vot4StaffData['userRank'])
                            : (htmlspecialchars('NO DATA')); // That one staff not even playin' a single taiko map...
                        $vot4StaffTimeZone  = htmlspecialchars($vot4StaffData['userTimeZone']);

                        $staffInformationTemplate =
                            <<<EOL
                            <section class="vot4-staff">
                                <div class="box-container">
                                    <div class="staff-header">
                                        <div class="staff-name">
                                            <h1>$vot4StaffName</h1>
                                        </div>
                                        <div class="staff-flag">
                                            <img src="$vot4StaffFlag" alt="Staff Country Flag">
                                        </div>
                                    </div>

                                    <div class="staff-body">
                                        <div class="staff-image">
                                            <a href="$vot4StaffUrl">
                                                <img src="$vot4StaffImage" alt="Staff Avatar">
                                            </a>
                                        </div>
                                    </div>

                                    <div class="staff-footer">
                                        <div class="staff-rank">
                                            <h3>Rank: $vot4StaffRank</h3>
                                        </div>
                                        <div class="staff-time-zone">
                                            <h3>Time Zone: $vot4StaffTimeZone</h3>
                                        </div>
                                    </div>
                                </div>
                            </section>
                            EOL;

                        // It would be much more nasty if I tried to output this using the traditional mixed HTML & PHP codes
                        echo $staffInformationTemplate;
                    }
                    break;

                default:
                    // TODO: proper handling
                    echo sprintf(
                        "There is no such role named [%s]. What are u tryin' to do bro...",
                        $vot4RoleName
                    );
                    break;
            }
        }
        break;

    default:
        // TODO: proper handling
        echo sprintf(
            "There is no such tournament named [%s]. What are u tryin' to do bro...",
            $votTournamentName
        );
        break;
}
