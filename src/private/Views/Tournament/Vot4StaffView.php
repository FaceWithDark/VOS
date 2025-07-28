<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require __DIR__ . '/../../Models/Staff.php';
?>

<header>
    <h1>VOT4 Staff</h1>
</header>

<section class="vot4-staff">
    <form action="/vot4/staff" method="get">
        <button type="submit" name="role" value="DEFAULT">Default</button>
        <button type="submit" name="role" value="HOST">Host</button>
        <button type="submit" name="role" value="MAPPOOLER">Mappooler</button>
        <button type="submit" name="role" value="GFX_VFX">GFX/VFX</button>
        <button type="submit" name="role" value="MAPPER">Mapper</button>
        <button type="submit" name="role" value="PLAY_TESTER">Play Tester</button>
        <button type="submit" name="role" value="REFEREE">Referee</button>
        <button type="submit" name="role" value="STREAMER">Streamer</button>
        <button type="submit" name="role" value="COMMENTATOR">Commentator</button>
        <button type="submit" name="role" value="STATISTICIAN">Statistician</button>
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

    if (!isset($_GET['role'])) {
        echo 'Among Us';
    } else {
        $vot4StaffRole = $_GET['role'];

        $vot4TournamentStaffData = readStaffData(
            role: $vot4StaffRole,
            name: $vot4TournamentName,
            database_handle: $vot4TournamentDatabase
        );

        foreach ($vot4TournamentStaffData as $vot4TournamentStaffDisplayData) {
            $vot4TournamentStaffName        = htmlspecialchars($vot4TournamentStaffDisplayData['userName']);
            $vot4TournamentStaffRole        = htmlspecialchars($vot4TournamentStaffDisplayData['userRole']);
            $vot4TournamentStaffFlag        = htmlspecialchars("https://flagsapi.com/{$vot4TournamentStaffDisplayData['userFlag']}/shiny/24.png");
            $vot4TournamentStaffImage       = htmlspecialchars($vot4TournamentStaffDisplayData['userImage']);
            $vot4TournamentStaffUrl         = htmlspecialchars($vot4TournamentStaffDisplayData['userUrl']);
            $vot4TournamentStaffRank        = htmlspecialchars($vot4TournamentStaffDisplayData['userRank']);
            $vot4TournamentStaffTimeZone    = htmlspecialchars($vot4TournamentStaffDisplayData['userTimeZone']);

            $staffDisplayTemplate =
                <<<EOL
                <div class="box-container">
                    <div class="staff-header">
                        <div class="staff-name">
                            <h1>$vot4TournamentStaffDisplayName</h1>
                        </div>
                        <div class="staff-flag">
                            <img src="$vot4TournamentStaffDisplayFlag" alt="Staff Country Flag">
                        </div>
                    </div>

                    <div class="staff-body">
                        <div class="staff-image">
                            <a href="$vot4TournamentStaffDisplayUrl">
                                <img src="$vot4TournamentStaffDisplayImage" alt="Staff Avatar">
                            </a>
                        </div>
                    </div>

                    <div class="staff-footer">
                        <div class="staff-role">
                            <h2>$vot4TournamentStaffDisplayRole</h2>
                        </div>
                        <div class="staff-rank">
                            <h3>Rank: $vot4TournamentStaffDisplayRank</h3>
                        </div>
                        <div class="staff-time-zone">
                            <h3>Time Zone: $vot4TournamentStaffDisplayTimeZone</h3>
                        </div>
                    </div>
                </div>
                EOL;

            // It would be much more nasty if I tried to output this using the traditional mixed HTML & PHP codes
            echo $staffDisplayTemplate;
        }
    }
    ?>
</section>
