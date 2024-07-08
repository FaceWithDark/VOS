<?php

declare(strict_types = 1);

require_once __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

require_once '../layouts/navigation_bar.php';

// Fetch beatmap data from the Osu! API
function fetchBeatmapData($beatmapId) {
    // Check if the user is authenticated by looking for the access token in cookies
    $accessToken = $_COOKIE['vot_access_token'] ?? null;
    if ($accessToken) {
        // If authenticated, construct the API URL for fetching beatmap data
        $apiUrl = "https://osu.ppy.sh/api/v2/beatmaps/{$beatmapId}";
        $client = new Client();
    
        try {
            // Make a GET request to the Osu! API with the access token
            $response = $client -> get($apiUrl, [
                'headers' => [
                    'Authorization' => "Bearer {$accessToken}",
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ]
            ]);
            
            // Return the beatmap data if it is a 200 status 
            if ($response->getStatusCode() === 200) {
                $apiData = json_decode($response -> getBody() -> getContents());
                return $apiData;
            }
            // API call did not return a 200 status
            return false;
        } 
        catch (RequestException $exception) {
            error_log("API request failed: " . $exception -> getMessage());  // Log the exception message
            return false;                                                    // An exception occurred during the API call
        }
    }
    else {
        // If not authenticated, fetch data from the database directly
        try {
            global $phpDataObject;
            
            // Check if the beatmap data exists in the database
            if(checkBeatmapData($beatmapId, $phpDataObject)) {
                $query = "SELECT id FROM vot4 WHERE map_id = :map_id";
                $queryStatement = $phpDataObject -> prepare($query);
                $queryStatement -> bindParam(":map_id", $beatmapId, PDO::PARAM_INT);

                // Execute the statement and fetch the data
                if ($queryStatement->execute()) {
                    error_log("Successfully retrieved data for beatmap ID: " . $beatmapId);
                    // Fetch and return the result as an associative array
                    return $queryStatement -> fetch(PDO::FETCH_ASSOC);
                } 
                else {
                    error_log("Failed to retrieve data for beatmap ID: " . $beatmapId);
                    $errorInfo = $queryStatement -> errorInfo();
                    error_log("Database error: " . implode(", ", $errorInfo));
                    return false;
                }
            }
            return false; // Beatmap data not found in the database
        }
        catch (RequestException $exception) {
            error_log("Database query failed: " . $exception -> getMessage());  // Log the exception message
            return false;                                                       // An exception occurred during the API call
        }
    }
}

// Store new beatmap data into database
function storeBeatmapData($beatmapData, $modType, $phpDataObject) {
    // SQL query to store beatmap data in the 'vot4' table
    $query = "INSERT INTO vot4 (map_id, 
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
    $query = "SELECT id FROM vot4 WHERE map_id = :map_id";
    $queryStatement = $phpDataObject -> prepare($query);
    $queryStatement -> bindParam(":map_id", $beatmapId, PDO::PARAM_INT);
    $queryStatement -> execute();

    return $queryStatement -> fetchColumn() !== false;
}

// Update existing beatmap data in the database with new data
function updateBeatmapData($beatmapData, $modType, $phpDataObject) {
    // SQL query to update beatmap data in the 'vot4' table
    $query = "UPDATE vot4 
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
$beatmapIds = [ // VOT4 Qualifiers
                3832435, 3167804, 4670818, 4353546, 3175478, 3412725, 4215511, 4670467, 2337091
              ];

$beatmapDataArray = [];

// Define a mapping of index ranges to beatmap mods
$modTypes = [
    // NM section
    'NM1' => [0],
    'NM2' => [1],
    'NM3' => [2],
    'NM4' => [],
    'NM5' => [],
    'NM6' => [],

    // HD section
    'HD1' => [3],
    'HD2' => [4],

    // HR section
    'HR1' => [5],
    'HR2' => [6],

    // DT section
    'DT1' => [7],
    'DT2' => [],

    // FM section
    'FM1' => [8],
    'FM2' => [],

    // EZ section
    'EZ' => [],

    // TB section
    'TB' => []
];

// Get beatmap mod by array index
function getModTypeByIndex($arrayIndex, $modTypes) {
    foreach($modTypes as $modType => $arrayIndexes) {
        if(in_array($arrayIndex, $arrayIndexes)) {
            return $modType;
        }
    }
    // None of the mod applied if index is not found
    return 'N/A';
}

foreach ($beatmapIds as $arrayIndex => $beatmapId) {
    // Get the beatmap mod type based on index number in an array
    $modType = getModTypeByIndex($arrayIndex, $modTypes);

    // Fetch the beatmap data from the API or database
    $beatmapData = fetchBeatmapData($beatmapId);

    // If beatmap data is fetched successfully
    if ($beatmapData) {
        // Check if the user is authenticated
        $accessToken = $_COOKIE['vot_access_token'] ?? null;
        if ($accessToken) {
            // Check if the beatmap data already exists in the database
            if (!checkBeatmapData($beatmapData -> id, $phpDataObject)) {
                // Insert new beatmap data
                storeBeatmapData($beatmapData, $modType, $phpDataObject);
            } else {
                // Update existing beatmap data
                updateBeatmapData($beatmapData, $modType, $phpDataObject);
            }

            // Retrieve the beatmap data from the database
            $retrievedBeatmapData = getBeatmapData($beatmapId, $phpDataObject);

            // die('<pre>' . print_r($retrievedBeatmapData, true) . '</pre>');
        } 
        else {
            // For unauthenticated users, directly retrieve the data from the database
            $retrievedBeatmapData = getBeatmapData($beatmapId, $phpDataObject);

            // die('<pre>' . print_r($retrievedBeatmapData, true) . '</pre>');
        }

        // If data retrieval is successful, add it to the array
        if ($retrievedBeatmapData) {
            $beatmapDataArray[] = $retrievedBeatmapData;

            // die('<pre>' . print_r($beatmapDataArray, true) . '</pre>');
        } 
        else {
            echo "Failed to retrieve beatmap data for ID: {$beatmapId}.\n";
        }
    }
}

// Get beatmap data from database by beatmap IDs
function getBeatmapData($mapId, $phpDataObject) {
    $query = "SELECT * FROM vot4 WHERE map_id = :map_id";
    $queryStatement = $phpDataObject -> prepare($query);
    $queryStatement -> bindParam(":map_id", $mapId, PDO::PARAM_INT);

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
                <h1><?= htmlspecialchars($beatmapData['mod_type']) ?></h1>
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
