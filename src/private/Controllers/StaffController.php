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
            require __DIR__ . '/NavigationBarController.php';
            require __DIR__ . '/../Views/Tournament/StaffVot4View.php';
        } else {
            // Show the page again after actions have been done
            require __DIR__ . '/NavigationBarController.php';
            require __DIR__ . '/../Views/Tournament/StaffVot4View.php';
            $vot4RoleName = $_GET['role'];

            // Regex returns a boolean value so this is the way to do it
            switch (true) {
                // *** VOT4 HOST STAFF DATA ***
                case preg_match(
                    pattern: '/^(host|hst)$/i',
                    subject: $vot4RoleName
                ):
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

                // *** VOT4 MAPPOOLER STAFF DATA ***
                case preg_match(
                    pattern: '/^(mappooler|maplr)$/i',
                    subject: $vot4RoleName
                ):
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

                // *** VOT4 GFX/VFX STAFF DATA ***
                case preg_match(
                    pattern: '/^(gfx_vfx|gnvfx)$/i',
                    subject: $vot4RoleName
                ):
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

                // *** VOT4 MAPPER STAFF DATA ***
                case preg_match(
                    pattern: '/^(mapper|mappr)$/i',
                    subject: $vot4RoleName
                ):
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

                // *** VOT4 PLAY TESTER STAFF DATA ***
                case preg_match(
                    pattern: '/^(play_tester|pltst)$/i',
                    subject: $vot4RoleName
                ):
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

                // *** VOT4 REFEREE STAFF DATA ***
                case preg_match(
                    pattern: '/^(referee|refre)$/i',
                    subject: $vot4RoleName
                ):
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

                // *** VOT4 STREAMER STAFF DATA ***
                case preg_match(
                    pattern: '/^(streamer|strmr)$/i',
                    subject: $vot4RoleName
                ):
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

                // *** VOT4 COMMENTATOR STAFF DATA ***
                case preg_match(
                    pattern: '/^(commentator|cmntr)$/i',
                    subject: $vot4RoleName
                ):
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

                // *** VOT4 STATISTICIAN STAFF DATA ***
                case preg_match(
                    pattern: '/^(statistician|stacn)$/i',
                    subject: $vot4RoleName
                ):
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
