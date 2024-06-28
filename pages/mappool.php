<?php

declare(strict_types = 1);

require_once __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

require_once '../layouts/navigation_bar.php';

// Fetch beatmap data from the Osu! API
function fetchBeatmapData($beatmapId) {
    $accessToken = $_COOKIE['vot_access_token'] ?? null;
    if (!$accessToken) {
        // Access token is not available
        return false;
    }

    $apiUrl = "https://osu.ppy.sh/api/v2/beatmaps/{$beatmapId}";
    $client = new Client();

    try {
        $response = $client->get($apiUrl, [
            'headers' => [
                'Authorization' => "Bearer {$accessToken}",
                'Accept' => 'application/json',
            ]
        ]);

        if ($response->getStatusCode() === 200) {
            return json_decode($response -> getBody() -> getContents());
        }
        // API call did not return a 200 status
        return false;
    } 
    catch (RequestException $exception) {
        error_log($exception -> getMessage());    // Log the exception message
        return false;                           // An exception occurred during the API call
    }
}

// Store beatmap data into database
function storeBeatmapData($beatmapData, $phpDataObject) {
    // SQL query to store beatmap data in the 'vot3' table
    $query = "INSERT INTO vot3 (map_id, 
                                total_length, 
                                map_url, 
                                cover_image_url, 
                                title_unicode, 
                                artist_unicode, 
                                difficulty, 
                                mapper, 
                                difficulty_rating, 
                                map_bpm, 
                                overall_difficulty, 
                                health_point, 
                                amount_of_passes) 
                     VALUES (:map_id, 
                             :total_length, 
                             :map_url, 
                             :cover_image_url, 
                             :title_unicode, 
                             :artist_unicode, 
                             :difficulty, 
                             :mapper, 
                             :difficulty_rating, 
                             :map_bpm, 
                             :overall_difficulty, 
                             :health_point, 
                             :amount_of_passes);";
    
    // Prepare the SQL statement to prevent SQL injection
    $queryStatement = $phpDataObject -> prepare($query);
    
    // Bind the beatmap data to the prepared statement
    //TODO: auto-call mods param.
    $queryStatement -> bindParam(":map_id", $beatmapData -> id);
    $queryStatement -> bindParam(":total_length", $beatmapData -> total_length);
    $queryStatement -> bindParam(":map_url", $beatmapData -> url);
    $queryStatement -> bindParam(":cover_image_url", $beatmapData -> beatmapset -> covers -> cover);
    $queryStatement -> bindParam(":title_unicode", $beatmapData -> beatmapset -> title_unicode);
    $queryStatement -> bindParam(":artist_unicode", $beatmapData -> beatmapset -> artist_unicode);
    $queryStatement -> bindParam(":difficulty", $beatmapData -> version);
    $queryStatement -> bindParam(":mapper", $beatmapData -> beatmapset -> creator);
    $queryStatement -> bindParam(":difficulty_rating", $beatmapData -> difficulty_rating);
    $queryStatement -> bindParam(":map_bpm", $beatmapData -> bpm);
    $queryStatement -> bindParam(":overall_difficulty", $beatmapData -> accuracy);
    $queryStatement -> bindParam(":health_point", $beatmapData -> drain);
    $queryStatement -> bindParam(":amount_of_passes", $beatmapData -> passcount);
    
    // Execute the statement and insert the data into database
    if ($queryStatement -> execute()) {
        error_log("Insert successful for beatmap ID: " . $beatmapData -> id);
        return $queryStatement -> rowCount() > 0;
    } 
    else {
        error_log("Insert failed for beatmap ID: " . $beatmapData -> id);
        $errorInfo = $queryStatement -> errorInfo();
        error_log("Error Info: " . implode(", ", $errorInfo));
        return false;
    }
}

// Check for existen beatmap data inside database
function checkBeatmapData($beatmapId, $phpDataObject) {
    $query = "SELECT id FROM vot3 WHERE map_id = :map_id";
    $queryStatement = $phpDataObject -> prepare($query);
    $queryStatement -> bindParam(":map_id", $beatmapId, PDO::PARAM_INT);
    $queryStatement -> execute();

    return $queryStatement -> fetchColumn() !== false;
}

// Define beatmap IDs for which data will be fetched
$beatmapId1 = 3271670;
$beatmapId2 = 3524450;

// Fetch and store beatmap API data for specified beatmap IDs
$beatmapData1 = fetchBeatmapData($beatmapId1);
$beatmapData2 = fetchBeatmapData($beatmapId2);

// Get beatmap data from database by beatmap IDs
function getBeatmapData($mapId, $phpDataObject) {
    $query = "SELECT * FROM beatmap WHERE map_id = :map_id";            // SQL query to select specific values form all columns in targeted table.
    $queryStatement = $phpDataObject -> prepare($query);                // Prepare the SQL statement to prevent SQL injection
    $queryStatement -> bindParam(":map_id", $mapId, PDO::PARAM_INT);    // Bind the beatmap data to the prepared statement
    $queryStatement -> execute();                                       // Execute the statement and insert the data into database
    return $queryStatement -> fetch(PDO::FETCH_ASSOC);                  // Fetch and return the result as an associative array
}

// Get and store beatmap data for specific beatmap IDs from database
$beatmapData1 = getBeatmapData(3271670, $phpDataObject);
$beatmapData2 = getBeatmapData(3524450, $phpDataObject);
?>

<section>
    <div class="mappool-page">
        <div class="mappool-card-container">
            <h1>NM1</h1>
            
            <br>

            <a href="<?= htmlspecialchars($beatmapData1 -> url); ?>"><img src="<?= htmlspecialchars($beatmapData1 -> beatmapset -> covers -> cover); ?>" width="490px" alt="Beatmap Cover"></a>
            
            <br><br>

            <h2><?= htmlspecialchars($beatmapData1 -> beatmapset -> title_unicode); ?> [<?= htmlspecialchars($beatmapData1 -> version); ?>]</h2>
            <h3><?= htmlspecialchars($beatmapData1 -> beatmapset -> artist_unicode); ?></h3>
            <h4 class="beatmap-creator-row">
                Mapset by <a href="https://osu.ppy.sh/users/5938161"><?= htmlspecialchars($beatmapData1 -> beatmapset -> creator); ?></a>
            </h4>
            
            <br>

            <div class="beatmap-attribute-row">
                <p style="margin-right: 1rem;"><i class='bx bx-star'></i> <?= htmlspecialchars($beatmapData1 -> difficulty_rating); ?></p>
                <p style="margin-right: 1rem;"><i class='bx bx-timer'></i> <?php echo "1:48"; ?></p>                        
                <p><i class='bx bx-tachometer'></i> <?= htmlspecialchars($beatmapData1 -> bpm); ?>bpm</p>
            </div>

            <br>

            <div class="beatmap-attribute-row">
                <p style="margin-right: 1rem;">OD: <?= htmlspecialchars($beatmapData1 -> accuracy); ?></p>
                <p style="margin-right: 1rem;">HP: <?= htmlspecialchars($beatmapData1 -> drain); ?></p>
                <p>Passed: <?= htmlspecialchars($beatmapData1 -> passcount); ?></p>
            </div>
        </div>

        <div class="mappool-card-container">
            <h1>NM2</h1>
            
            <br>

            <a href="<?= htmlspecialchars($beatmapData2 -> url); ?>"><img src="<?= htmlspecialchars($beatmapData2 -> beatmapset -> covers -> cover); ?>" width="490px" alt="Beatmap Cover"></a>
            
            <br><br>

            <h2><?= htmlspecialchars($beatmapData2 -> beatmapset -> title_unicode); ?> [<?= htmlspecialchars($beatmapData2 -> version); ?>]</h2>
            <h3><?= htmlspecialchars($beatmapData2 -> beatmapset -> artist_unicode); ?></h3>
            <h4 class="beatmap-creator-row">
                Mapset by <a href="https://osu.ppy.sh/users/5938161"><?= htmlspecialchars($beatmapData2 -> beatmapset -> creator); ?></a>
            </h4>
            
            <br>

            <div class="beatmap-attribute-row">
                <p style="margin-right: 1rem;"><i class='bx bx-star'></i> <?= htmlspecialchars($beatmapData2 -> difficulty_rating); ?></p>
                <p style="margin-right: 1rem;"><i class='bx bx-timer'></i> <?php echo "1:48"; ?></p>                        
                <p><i class='bx bx-tachometer'></i> <?= htmlspecialchars($beatmapData2 -> bpm); ?>bpm</p>
            </div>

            <br>
            
            <div class="beatmap-attribute-row">
                <p style="margin-right: 1rem;">OD: <?= htmlspecialchars($beatmapData2 -> accuracy); ?></p>
                <p style="margin-right: 1rem;">HP: <?= htmlspecialchars($beatmapData2 -> drain); ?></p>
                <p>Passed: <?= htmlspecialchars($beatmapData2 -> passcount); ?></p>
            </div>
        </div>
</section>
