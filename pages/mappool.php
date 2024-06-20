<?php    
    // Sent another request to fetch the user's profile details incl name, avatar, etc. 
    use GuzzleHttp\Client;
    use GuzzleHttp\Exception\RequestException;

    require_once '../layouts/navigation_bar.php';

    // Get user details.
    function getFirstBeatmap() {
        if(empty($_COOKIE['vot_access_token'])) {
            return false;
        }

        $firstNoModBeatmapApiUrl = "https://osu.ppy.sh/api/v2/beatmaps/3271670";

        $client = new Client();

        try {
            $response = $client -> get($firstNoModBeatmapApiUrl, [
                'headers' => [
                    'authorization' => 'Bearer ' . $_COOKIE['vot_access_token'],
                    'Accept' => 'application/json',
                ]
            ]);
                
            if($response -> getStatusCode() == 200) {
                return json_decode($response -> getBody() -> getContents());
            }
            return false;
        }
        catch(RequestException $exceptions) {
            return false;
        }
    }

    function getSecondBeatmap() {
        if(empty($_COOKIE['vot_access_token'])) {
            return false;
        }

        $secondNoModBeatmapApiUrl = "https://osu.ppy.sh/api/v2/beatmaps/3524450";

        $client = new Client();

        try {
            $response = $client -> get($secondNoModBeatmapApiUrl, [
                'headers' => [
                    'authorization' => 'Bearer ' . $_COOKIE['vot_access_token'],
                    'Accept' => 'application/json',
                ]
            ]);
                
            if($response -> getStatusCode() == 200) {
                return json_decode($response -> getBody() -> getContents());
            }
            return false;
        }
        catch(RequestException $exceptions) {
            return false;
        }
    }

    $firstBeatmap = false;
    $secondBeatmap = false;

    $firstBeatmap = getFirstBeatmap();
    $secondBeatmap = getsecondBeatmap();

    // die('<pre>' . print_r($firstBeatmap, true) . '</pre>');
    // die('<pre>' . print_r($secondBeatmap, true) . '</pre>');
?>

<section>
    <div class="mappool-page">
        <div class="mappool-card-container">
            <h1>NM1</h1>
            
            <br>

            <a href="<?= htmlspecialchars($firstBeatmap -> url); ?>"><img src="<?= htmlspecialchars($firstBeatmap -> beatmapset -> covers -> cover); ?>" width="490px" alt="Beatmap Cover"></a>
            
            <br><br>

            <h2><?= htmlspecialchars($firstBeatmap -> beatmapset -> title_unicode); ?> [<?= htmlspecialchars($firstBeatmap -> version); ?>]</h2>
            <h3><?= htmlspecialchars($firstBeatmap -> beatmapset -> artist_unicode); ?></h3>
            <h4 class="beatmap-creator-row">
                Mapset by <a href="https://osu.ppy.sh/users/5938161"><?= htmlspecialchars($firstBeatmap -> beatmapset -> creator); ?></a>
            </h4>
            
            <br>

            <div class="beatmap-attribute-row">
                <p style="margin-right: 1rem;"><i class='bx bx-star'></i> <?= htmlspecialchars($firstBeatmap -> difficulty_rating); ?></p>
                <p style="margin-right: 1rem;"><i class='bx bx-timer'></i> <?php echo "1:48"; ?></p>                        
                <p><i class='bx bx-tachometer'></i> <?= htmlspecialchars($firstBeatmap -> bpm); ?>bpm</p>
            </div>

            <br>

            <div class="beatmap-attribute-row">
                <p style="margin-right: 1rem;">OD: <?= htmlspecialchars($firstBeatmap -> accuracy); ?></p>
                <p style="margin-right: 1rem;">HP: <?= htmlspecialchars($firstBeatmap -> drain); ?></p>
                <p>Passed: <?= htmlspecialchars($firstBeatmap -> passcount); ?></p>
            </div>
        </div>

        <div class="mappool-card-container">
            <h1>NM2</h1>
            
            <br>

            <a href="<?= htmlspecialchars($secondBeatmap -> url); ?>"><img src="<?= htmlspecialchars($secondBeatmap -> beatmapset -> covers -> cover); ?>" width="490px" alt="Beatmap Cover"></a>
            
            <br><br>

            <h2><?= htmlspecialchars($secondBeatmap -> beatmapset -> title_unicode); ?> [<?= htmlspecialchars($secondBeatmap -> version); ?>]</h2>
            <h3><?= htmlspecialchars($secondBeatmap -> beatmapset -> artist_unicode); ?></h3>
            <h4 class="beatmap-creator-row">
                Mapset by <a href="https://osu.ppy.sh/users/5938161"><?= htmlspecialchars($secondBeatmap -> beatmapset -> creator); ?></a>
            </h4>
            
            <br>

            <div class="beatmap-attribute-row">
                <p style="margin-right: 1rem;"><i class='bx bx-star'></i> <?= htmlspecialchars($secondBeatmap -> difficulty_rating); ?></p>
                <p style="margin-right: 1rem;"><i class='bx bx-timer'></i> <?php echo "1:48"; ?></p>                        
                <p><i class='bx bx-tachometer'></i> <?= htmlspecialchars($secondBeatmap -> bpm); ?>bpm</p>
            </div>

            <br>
            
            <div class="beatmap-attribute-row">
                <p style="margin-right: 1rem;">OD: <?= htmlspecialchars($secondBeatmap -> accuracy); ?></p>
                <p style="margin-right: 1rem;">HP: <?= htmlspecialchars($secondBeatmap -> drain); ?></p>
                <p>Passed: <?= htmlspecialchars($secondBeatmap -> passcount); ?></p>
            </div>
        </div>
</section>
