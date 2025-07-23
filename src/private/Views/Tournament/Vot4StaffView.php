<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require __DIR__ . '/../../Configurations/Database.php';
require __DIR__ . '/../../Models/Staff.php';
?>


<header>
    <h1>VOT4 Staff</h1>
</header>

<section class="vot4-staff">
    <form action="/vot4/staff" method="get">
        <button type="submit" name="role" value="default">Default</button>
        <button type="submit" name="role" value="host">Host</button>
        <button type="submit" name="role" value="mappooler">Mappooler</button>
        <button type="submit" name="role" value="gfx_vfx">GFX/VFX</button>
        <button type="submit" name="role" value="mapper">Mapper</button>
        <button type="submit" name="role" value="play_tester">Play Tester</button>
        <button type="submit" name="role" value="referee">Referee</button>
        <button type="submit" name="role" value="streamer">Streamer</button>
        <button type="submit" name="role" value="commentator">Commentator</button>
        <button type="submit" name="role" value="statistician">Statistician</button>
    </form>

    <?php
    if (isset($_COOKIE['vot_access_token'])) {
        if (isset($_GET['role'])) {
            $staffRoleName = $_GET['role'];

            switch ($staffRoleName) {
                case 'default':
                    $staffJsonData = __DIR__ . '/../../Datas/Staff/VotStaffData.json';
                    $staffDatabase = $GLOBALS['votDatabaseHandle'];
                    $staffViewableJsonData = file_get_contents(
                        filename: $staffJsonData,
                        use_include_path: false,
                        context: null,
                        offset: 0,
                        length: null
                    );

                    $staffReadableJsonData = json_decode(
                        json: $staffViewableJsonData,
                        associative: true
                    );

                    foreach ($staffReadableJsonData as $staffRoleJsonData) {
                        foreach ($staffRoleJsonData['host_role'] as $staffRoleSpecificIdJsonData) {
                            $staffFetchedData = readStaffData(
                                staff_id: $staffRoleSpecificIdJsonData,
                                database_handle: $staffDatabase
                            );

                            foreach ($staffFetchedData as $staffDisplayData) {
                                // echo '<pre>' . print_r($staffDisplayData, true) . '</pre>';
                                $staffDisplayName       = htmlspecialchars($staffDisplayData['userName']);
                                $staffDisplayRole       = htmlspecialchars($staffDisplayData['userRole']);
                                $staffDisplayFlag       = htmlspecialchars("https://flagsapi.com/{$staffDisplayData['userFlag']}/shiny/24.png");
                                $staffDisplayImage      = htmlspecialchars($staffDisplayData['userImage']);
                                $staffDisplayUrl        = htmlspecialchars($staffDisplayData['userUrl']);
                                $staffDisplayRank       = htmlspecialchars($staffDisplayData['userRank']);
                                $staffDisplayTimeZone   = htmlspecialchars($staffDisplayData['userTimeZone']);


                                $staffDisplayTemplate   =
                                    <<<EOL
                                        <div class="box-container">
                                            <div class="staff-header">
                                                <div class="staff-name">
                                                    <h1>$staffDisplayName</h1>
                                                </div>
                                                <div class="staff-flag">
                                                    <img src="$staffDisplayFlag" alt="Staff Country Flag">
                                                </div>
                                            </div>

                                            <div class="staff-body">
                                                <div class="staff-image">
                                                    <a href="$staffDisplayUrl">
                                                        <img src="$staffDisplayImage" alt="Staff Avatar">
                                                    </a>
                                                </div>
                                            </div>

                                            <div class="staff-footer">
                                                <div class="staff-role">
                                                    <h2>$staffDisplayRole</h2>
                                                </div>
                                                <div class="staff-rank">
                                                    <h3>Rank: $staffDisplayRank</h3>
                                                </div>
                                                <div class="staff-time-zone">
                                                    <h3>Time Zone: $staffDisplayTimeZone</h3>
                                                </div>
                                            </div>
                                        </div>
                                    EOL;

                                // It would be much more nasty if I tried to output this using the traditional mixed HTML & PHP codes
                                echo $staffDisplayTemplate;
                            }
                        }
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
