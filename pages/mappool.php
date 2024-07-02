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
                'Content-Type' => 'application/json',
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
        error_log($exception -> getMessage());  // Log the exception message
        return false;                           // An exception occurred during the API call
    }
}

// Store new beatmap data into database
function storeBeatmapData($beatmapData, $modType, $phpDataObject) {
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
                                amount_of_passes,
                                mod_type) 
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
                             :amount_of_passes,
                             :mod_type);";
    
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
    $queryStatement -> bindParam(":mod_type", $modType);
    
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

// Check if beatmap data already exists in the database
function checkBeatmapData($beatmapId, $phpDataObject) {
    $query = "SELECT id FROM vot3 WHERE map_id = :map_id";
    $queryStatement = $phpDataObject -> prepare($query);
    $queryStatement -> bindParam(":map_id", $beatmapId, PDO::PARAM_INT);
    $queryStatement -> execute();

    return $queryStatement -> fetchColumn() !== false;
}

// Update existing beatmap data in the database with new data
function updateBeatmapData($beatmapData, $modType, $phpDataObject) {
    // SQL query to update beatmap data in the 'vot3' table
    $query = "UPDATE vot3 
              SET total_length = :total_length, 
                  map_url = :map_url, 
                  cover_image_url = :cover_image_url, 
                  title_unicode = :title_unicode, 
                  artist_unicode = :artist_unicode, 
                  difficulty = :difficulty, 
                  mapper = :mapper, 
                  difficulty_rating = :difficulty_rating, 
                  map_bpm = :map_bpm, 
                  overall_difficulty = :overall_difficulty, 
                  health_point = :health_point, 
                  amount_of_passes = :amount_of_passes,
                  mod_type = :mod_type
              WHERE map_id = :map_id;";
    
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
    $queryStatement -> bindParam(":mod_type", $modType);


    // Execute the statement and update the existen data in the database
    if ($queryStatement -> execute()) {
        error_log("Update successful for beatmap ID: " . $beatmapData -> id);
        return $queryStatement -> rowCount() > 0;
    } 
    else {
        error_log("Update failed for beatmap ID: " . $beatmapData -> id);
        $errorInfo = $queryStatement -> errorInfo();
        error_log("Error Info: " . implode(", ", $errorInfo));
        return false;
    }
}

// Define beatmap IDs for which data will be fetched
$beatmapIds = [3271670, 3524450];

$beatmapDataArray = [];
foreach($beatmapIds as $beatmapId) {
    // Fetch the beatmap data from the API
    $beatmapData = fetchBeatmapData($beatmapId);

    // If beatmap data is fetched successfully
    if($beatmapData) {
        // Check if the beatmap data already exists in the database 
        if(!checkBeatmapData($beatmapData -> id, $phpDataObject)) {
            // Insert new beatmap data
            storeBeatmapData($beatmapData, $modType, $phpDataObject);
        }
        else {
            // Update existing beatmap data
            updateBeatmapData($beatmapData, $modType, $phpDataObject);
        }

        // Retrieve the beatmap data from the database
        $retrievedBeatmapData = getBeatmapData($beatmapId, $phpDataObject);
        // If data retrieval is successful, add it to the array
        if($retrievedBeatmapData) {
            $beatmapDataArray[] = $retrievedBeatmapData;
        } 
        else {
            echo "Failed to retrieve beatmap data for ID: {$beatmapId}.\n";
        }
    }
}

// Get beatmap data from database by beatmap IDs
function getBeatmapData($mapId, $phpDataObject) {
    $query = "SELECT * FROM vot3 WHERE map_id = :map_id";               // SQL query to select specific values form all columns in targeted table.
    $queryStatement = $phpDataObject -> prepare($query);                // Prepare the SQL statement to prevent SQL injection
    $queryStatement -> bindParam(":map_id", $mapId, PDO::PARAM_INT);    // Bind the beatmap data to the prepared statement

    // Execute the statement and get the needed data in the database to display
    if ($queryStatement -> execute()) {
        error_log("Get data successfully for beatmap ID: " . $mapId);
        // Fetch and return the result as an associative array
        return $queryStatement -> fetch(PDO::FETCH_ASSOC);
    } 
    else {
        error_log("Get data failed for beatmap ID: " . $mapId);
        $errorInfo = $queryStatement -> errorInfo();
        error_log("Error Info: " . implode(", ", $errorInfo));
        return false;
    }
}

include_once '../modules/convertion/time_convertion.php';
?>

<section>
    <div class="mappool-page">
        <?php foreach($beatmapDataArray as $beatmapData): ?>
            <!-- Dynamic beatmap display with correct mod type -->
            <div class="mappool-card-container">
                <h1>NM1</h1>
                <br>
                <a href="<?= ($beatmapData['map_url']); ?>"><img src="<?= htmlspecialchars($beatmapData['cover_image_url']); ?>" width="490px" alt="Beatmap Cover"></a>
                <br><br>
                <h2><?= htmlspecialchars($beatmapData['title_unicode']); ?> [<?= ($beatmapData['difficulty']); ?>]</h2>
                <h3><?= htmlspecialchars($beatmapData['artist_unicode']); ?></h3>
                <h4 class="beatmap-creator-row">
                    Mapset by <a href="https://osu.ppy.sh/users/<?= htmlspecialchars($beatmapData['mapper']); ?>"><?= htmlspecialchars($beatmapData['mapper']); ?></a>
                </h4>
                <br>
                <div class="beatmap-attribute-row">
                    <p style="margin-right: 1rem;"><i class='bx bx-star'></i> <?= htmlspecialchars(number_format((float)$beatmapData['difficulty_rating'], 2)); ?></p>
                    <!-- 
                        TODO: 
                        - Take use of the gmdate("i:s", <timestamp>) function to display the actual timestamp for each beatmap IDs called 
                        - Take adventage of the durationToSeconds() function as well
                    -->
                    <p style="margin-right: 1rem;"><i class='bx bx-timer'></i> <?= htmlspecialchars($beatmapData['total_length']); ?></p>
                    <p><i class='bx bx-tachometer'></i> <?= ($beatmapData['map_bpm']); ?>bpm</p>
                </div>
                <br>
                <div class="beatmap-attribute-row">
                    <p style="margin-right: 1rem;">OD: <?= htmlspecialchars(number_format((float)$beatmapData['overall_difficulty'], 2)); ?></p>
                    <p style="margin-right: 1rem;">HP: <?= htmlspecialchars(number_format((float)$beatmapData['health_point'], 2)); ?></p>
                    <p>Passed: <?= ($beatmapData['amount_of_passes']); ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div> 
</section>
