<?php

declare(strict_types = 1);

require_once __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

require_once '../layouts/navigation_bar.php';
include_once '../modules/convertion/time_convertion.php';

// Fetch beatmap data from the Osu! API
function fetchBeatmapData($beatmapId, $tournamentRound, $phpDataObject) {
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
            // Determine the table to query based on the 'round' parameter
            $tournamentTable = "vot4_{$tournamentRound}";

            // Determine the table to query based on the 'round' parameter
            $query =  "SELECT id FROM $tournamentTable WHERE map_id = :map_id";
            $queryStatement = $phpDataObject -> prepare($query);
            $queryStatement -> bindParam(":map_id", $beatmapId, PDO::PARAM_INT);

            // Execute the statement and fetch the data
            if($queryStatement -> execute()) {
                error_log("Successfully retrieved data for beatmap ID: " . $beatmapId);
                return $queryStatement -> fetch(PDO::FETCH_ASSOC);
            }
            else {
                error_log("Failed to retrieve data for beatmap ID: " . $beatmapId);
                $errorInfo = $queryStatement -> errorInfo();
                error_log("Database error: " . implode(", ", $errorInfo));  // Log the exception message
                return false;                                               // An exception occurred during the database call
            }
        }
        catch(RequestException $exception) {
            error_log("Database query failed: " . $exception -> getMessage());
            return false;
        }
    }
}


// Store new beatmap data into database
function storeBeatmapData($beatmapData, $modType, $tournamentRound, $phpDataObject) {
    $formattedTotalLength = integerToTimeFormat($beatmapData -> total_length);

    // Determine the table to unsert data based on the 'round' parameter
    $tournamentTable = "vot4_{$tournamentRound}";

    // SQL query to store beatmap data in the 'vot4_qualifiers' table
    $query = "INSERT INTO $tournamentTable (map_id, 
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
    $queryStatement -> bindParam(":total_length", $formattedTotalLength);
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
function checkBeatmapData($beatmapId, $tournamentRound, $phpDataObject) {
    // Determine the table to unsert data based on the 'round' parameter
    $tournamentTable = "vot4_{$tournamentRound}";

    $query = "SELECT id FROM $tournamentTable WHERE map_id = :map_id";
    $queryStatement = $phpDataObject -> prepare($query);
    $queryStatement -> bindParam(":map_id", $beatmapId, PDO::PARAM_INT);
    $queryStatement -> execute();

    return $queryStatement -> fetchColumn() !== false;
}


// Update existing beatmap data in the database with new data
function updateBeatmapData($beatmapData, $modType, $tournamentRound, $phpDataObject) {
    $formattedTotalLength = integerToTimeFormat($beatmapData -> total_length);

    // Determine the table to unsert data based on the 'round' parameter
    $tournamentTable = "vot4_{$tournamentRound}";

    // SQL query to update beatmap data in the 'vot4_qualifiers' table
    $query = "UPDATE $tournamentTable 
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
    $queryStatement -> bindParam(":total_length", $formattedTotalLength);
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


// Get beatmap data from database by beatmap IDs
function getBeatmapData($mapId, $tournamentRound, $phpDataObject) {
    // Determine the table to unsert data based on the 'round' parameter
    $tournamentTable = "vot4_{$tournamentRound}";

    $query = "SELECT * FROM $tournamentTable WHERE map_id = :map_id";
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


// Get the 'round' parameter from the URL
$tournamentRound = $_GET['round'] ?? 'qualifiers';

$beatmapDataArray = [];

if($tournamentRound) {
    // Define beatmap IDs for which data will be fetched
    $beatmapIds = [ // VOT4 Qualifiers
                    3832435, 3167804, 4670818, 4353546, 3175478, 3412725, 4215511, 4670467, 2337091,
                    // VOT4 RO16
                    4679944, 2564433, 3789813, 4681218, 4681168, 3304046, 4251890, 4004134, 4458839, 3884457, 2417569, 4633213, 4682562, 4039947, 2035883,
                    // VOT4 QF
                    3929726, 2420243, 4692866, 4692975, 4692897, 4690486, 4601035, 4040942, 3933082, 4692933, 4692544, 4692861, 3442056, 4692872, 4692888, 3308614
    ];

    // Define a mapping of index ranges to beatmap mods
    $modTypes = [
    // NM section
    'NM1' => [0, 9, 24],
    'NM2' => [1, 10, 25],
    'NM3' => [2, 11, 26],
    'NM4' => [12, 27],
    'NM5' => [28],
    'NM6' => [],

    // HD section
    'HD1' => [3, 13, 29],
    'HD2' => [4, 14, 30],

    // HR section
    'HR1' => [5, 15, 31],
    'HR2' => [6, 16, 32],

    // DT section
    'DT1' => [7, 17, 33],
    'DT2' => [18, 34],

    // FM section
    'FM1' => [8, 19, 35],
    'FM2' => [20, 36],

    // EZ section
    'EZ' => [21, 37],

    // HDHR section
    'HDHR' => [22, 38],

    // TB section
    'TB' => [23, 39]
    ];

    foreach ($beatmapIds as $arrayIndex => $beatmapId) {
        // Get the beatmap mod type based on index number in an array
        $modType = getModTypeByIndex($arrayIndex, $modTypes);
    
        // Fetch the beatmap data from the API or database
        $beatmapData = fetchBeatmapData($beatmapId, $tournamentRound, $phpDataObject);
    
        if ($beatmapData) {
            /*
             * TODO:
             * - I don't think this is the way to fix the same beatmap duplication bug but for now, it works
             * - Maybe to actually handle this bug, I have to work on the 'null' case properly
             */
            $retrievedBeatmapData = null;

            // Check if the user is authenticated
            $accessToken = $_COOKIE['vot_access_token'] ?? null;
            
            if ($accessToken) {
                switch ($tournamentRound) {
                    case 'qualifiers':
                        if ($arrayIndex <= 8) {
                            // For 'Qualifiers' database table with AUTHENTICATED user's case 
                            if (!checkBeatmapData($beatmapData -> id, $tournamentRound, $phpDataObject)) {
                                storeBeatmapData($beatmapData, $modType, $tournamentRound, $phpDataObject);
                            } else {
                                updateBeatmapData($beatmapData, $modType, $tournamentRound, $phpDataObject);
                            }
                            $retrievedBeatmapData = getBeatmapData($beatmapId, $tournamentRound, $phpDataObject);
                        }
                        break;
                    case 'ro16':
                        if ($arrayIndex > 8 && $arrayIndex < 24) {
                            // For 'RO16' database table with AUTHENTICATED user's case
                            if (!checkBeatmapData($beatmapData -> id, $tournamentRound, $phpDataObject)) {
                                storeBeatmapData($beatmapData, $modType, $tournamentRound, $phpDataObject);
                            } else {
                                updateBeatmapData($beatmapData, $modType, $tournamentRound, $phpDataObject);
                            }
                            $retrievedBeatmapData = getBeatmapData($beatmapId, $tournamentRound, $phpDataObject);
                        }
                        break;
                    case 'quarterfinals':
                        if ($arrayIndex >= 24 && $arrayIndex <= 39) {
                            // For 'Quarterfinals' database table with AUTHENTICATED user's case
                            if (!checkBeatmapData($beatmapData -> id, $tournamentRound, $phpDataObject)) {
                                storeBeatmapData($beatmapData, $modType, $tournamentRound, $phpDataObject);
                            } else {
                                updateBeatmapData($beatmapData, $modType, $tournamentRound, $phpDataObject);
                            }
                            $retrievedBeatmapData = getBeatmapData($beatmapId, $tournamentRound, $phpDataObject);
                        }
                        break;
                }
            } else {
                switch ($tournamentRound) {
                    case 'qualifiers':
                        if ($arrayIndex <= 8) {
                            // For 'Qualifier' database table with UNAUTHENTICATED user's case    
                            $retrievedBeatmapData = getBeatmapData($beatmapId, $tournamentRound, $phpDataObject);
                        }
                        break;
                    case 'ro16':
                        if ($arrayIndex > 8 && $arrayIndex < 24) {
                            // For 'RO16' database table with UNAUTHENTICATED user's case    
                            $retrievedBeatmapData = getBeatmapData($beatmapId, $tournamentRound, $phpDataObject);
                        }
                        break;
                    case 'quarterfinals':
                        if ($arrayIndex >= 24 && $arrayIndex <= 39) {
                            // For 'Quarterfinals' database table with UNAUTHENTICATED user's case    
                            $retrievedBeatmapData = getBeatmapData($beatmapId, $tournamentRound, $phpDataObject);
                        }
                        break;
                }
            }
    
            // If data retrieval is successful, add it to the array
            if ($retrievedBeatmapData) {
                $beatmapDataArray[] = $retrievedBeatmapData;
            } 
            else {
                error_log("Failed to retrieve beatmap data for ID: {$beatmapId}.");
            }
        }
        else {
            error_log("Failed to fetch beatmap data for ID: {$beatmapId}.");
        }
    }
}
else {
    die('<div class="warning-message" style="display: flex; justify-content: center; align-items: center; margin-left: 45rem; font-size: 5rem;">Please choose a valid button!</div>'); // TODO: This is an absolute temporary. Changed later (if I can).
}
?>

<section>
    <div class="button-container">
        <form action="mappool.php" method="get">
            <button type="submit" name="round" value="qualifiers">Qualifiers</button>
            <button type="submit" name="round" value="ro16">RO16</button>
            <button type="submit" name="round" value="quarterfinals">QF</button>
        </form>
    </div>

    <div class="mappool-page">
        <?php if(!empty($beatmapDataArray)): ?>
        <!-- Dynamic beatmap display with correct mod type -->
            <?php foreach($beatmapDataArray as $beatmapData): ?>
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
        <?php endif; ?>
    </div> 
</section>
