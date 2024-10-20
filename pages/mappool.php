<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

require_once '../layouts/navigation_bar.php';
include_once '../modules/convertion/time_convertion.php';


// Fetch beatmap data from the Osu! API
function fetchBeatmapData($beatmapId, $tournamentName, $tournamentRound, $phpDataObject)
{
    // Check if the user is authenticated by looking for the access token in cookies
    $accessToken = $_COOKIE['vot_access_token'] ?? null;
    if ($accessToken) {
        // If authenticated, construct the API URL for fetching beatmap data
        $apiUrl = "https://osu.ppy.sh/api/v2/beatmaps/{$beatmapId}";
        $client = new Client();

        try {
            // Make a GET request to the Osu! API with the access token
            $response = $client->get($apiUrl, [
                'headers' => [
                    'Authorization' => "Bearer {$accessToken}",
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ]
            ]);

            // Return the beatmap data if it is a 200 status 
            if ($response->getStatusCode() === 200) {
                $apiData = json_decode($response->getBody()->getContents());
                return $apiData;
            }
            // API call did not return a 200 status
            return false;
        } catch (RequestException $exception) {
            error_log("API request failed: " . $exception->getMessage());
            return false;
        }
    } else {
        // If not authenticated, fetch data from the database directly
        return getBeatmapData($beatmapId, $tournamentName, $tournamentRound, $phpDataObject);
    }
}


// Store new beatmap data into database
function storeBeatmapData($beatmapData, $modType, $tournamentName, $tournamentRound, $phpDataObject)
{
    $formattedTotalLength = integerToTimeFormat($beatmapData->total_length);

    // Call the correct tournament database table based on the GET request
    $tournamentTable = "{$tournamentName}_{$tournamentRound}";

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
    $queryStatement = $phpDataObject->prepare($query);

    // Bind the beatmap data to the prepared statement
    $queryStatement->bindParam(":map_id", $beatmapData->id);
    $queryStatement->bindParam(":total_length", $formattedTotalLength);
    $queryStatement->bindParam(":map_url", $beatmapData->url);
    $queryStatement->bindParam(":cover_image_url", $beatmapData->beatmapset->covers->cover);
    $queryStatement->bindParam(":title_unicode", $beatmapData->beatmapset->title_unicode);
    $queryStatement->bindParam(":artist_unicode", $beatmapData->beatmapset->artist_unicode);
    $queryStatement->bindParam(":difficulty", $beatmapData->version);
    $queryStatement->bindParam(":mapper", $beatmapData->beatmapset->creator);
    $queryStatement->bindParam(":difficulty_rating", $beatmapData->difficulty_rating);
    $queryStatement->bindParam(":map_bpm", $beatmapData->bpm);
    $queryStatement->bindParam(":overall_difficulty", $beatmapData->accuracy);
    $queryStatement->bindParam(":health_point", $beatmapData->drain);
    $queryStatement->bindParam(":amount_of_passes", $beatmapData->passcount);
    $queryStatement->bindParam(":mod_type", $modType);

    // Execute the statement and insert the data into database
    if ($queryStatement->execute()) {
        error_log("Insert successful for beatmap ID: " . $beatmapData->id);
        return $queryStatement->rowCount() > 0;
    } else {
        error_log("Insert failed for beatmap ID: " . $beatmapData->id);
        $errorInfo = $queryStatement->errorInfo();
        error_log("Error Info: " . implode(", ", $errorInfo));
        return false;
    }
}


// Check if beatmap data already exists in the database
function checkBeatmapData($beatmapId, $tournamentName, $tournamentRound, $phpDataObject)
{
    // Call the correct tournament database table based on the GET request
    $tournamentTable = "{$tournamentName}_{$tournamentRound}";

    $query = "SELECT id FROM $tournamentTable WHERE map_id = :map_id";
    $queryStatement = $phpDataObject->prepare($query);
    $queryStatement->bindParam(":map_id", $beatmapId, PDO::PARAM_INT);
    $queryStatement->execute();

    return $queryStatement->fetchColumn() !== false;
}


// Update existing beatmap data in the database with new data
function updateBeatmapData($beatmapData, $modType, $tournamentName, $tournamentRound, $phpDataObject)
{
    $formattedTotalLength = integerToTimeFormat($beatmapData->total_length);

    // Call the correct tournament database table based on the GET request
    $tournamentTable = "{$tournamentName}_{$tournamentRound}";

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
    $queryStatement = $phpDataObject->prepare($query);

    // Bind the beatmap data to the prepared statement
    $queryStatement->bindParam(":map_id", $beatmapData->id);
    $queryStatement->bindParam(":total_length", $formattedTotalLength);
    $queryStatement->bindParam(":map_url", $beatmapData->url);
    $queryStatement->bindParam(":cover_image_url", $beatmapData->beatmapset->covers->cover);
    $queryStatement->bindParam(":title_unicode", $beatmapData->beatmapset->title_unicode);
    $queryStatement->bindParam(":artist_unicode", $beatmapData->beatmapset->artist_unicode);
    $queryStatement->bindParam(":difficulty", $beatmapData->version);
    $queryStatement->bindParam(":mapper", $beatmapData->beatmapset->creator);
    $queryStatement->bindParam(":difficulty_rating", $beatmapData->difficulty_rating);
    $queryStatement->bindParam(":map_bpm", $beatmapData->bpm);
    $queryStatement->bindParam(":overall_difficulty", $beatmapData->accuracy);
    $queryStatement->bindParam(":health_point", $beatmapData->drain);
    $queryStatement->bindParam(":amount_of_passes", $beatmapData->passcount);
    $queryStatement->bindParam(":mod_type", $modType);


    // Execute the statement and update the existen data in the database
    if ($queryStatement->execute()) {
        error_log("Update successful for beatmap ID: " . $beatmapData->id);
        return $queryStatement->rowCount() > 0;
    } else {
        error_log("Update failed for beatmap ID: " . $beatmapData->id);
        $errorInfo = $queryStatement->errorInfo();
        error_log("Error Info: " . implode(", ", $errorInfo));
        return false;
    }
}


// Get beatmap data from database by beatmap IDs
function getBeatmapData($mapId, $tournamentName, $tournamentRound, $phpDataObject)
{
    // Call the correct tournament database table based on the GET request
    $tournamentTable = "{$tournamentName}_{$tournamentRound}";

    $query = "SELECT * FROM $tournamentTable WHERE map_id = :map_id";
    $queryStatement = $phpDataObject->prepare($query);
    $queryStatement->bindParam(":map_id", $mapId, PDO::PARAM_INT);

    // Execute the statement and get the needed data in the database to display
    if ($queryStatement->execute()) {
        error_log("Get data successfully for beatmap ID: " . $mapId);
        // Fetch and return the result as an associative array
        return $queryStatement->fetch(PDO::FETCH_ASSOC);
    } else {
        error_log("Get data failed for beatmap ID: " . $mapId);
        $errorInfo = $queryStatement->errorInfo();
        error_log("Error Info: " . implode(", ", $errorInfo));
        return false;
    }
}


// Fetch deleted beatmap data from JSON file
function fetchDeletedBeatmapData($tournamentName, $tournamentRound)
{
    // Read deleted beatmap data from JSON file
    $deletedBeatmapJsonData = json_decode(file_get_contents('../data/deleted_beatmap.json'), true);
    $deletedBeatmapJsonDatas = $deletedBeatmapJsonData[$tournamentName][$tournamentRound] ?? [];

    return $deletedBeatmapJsonDatas;
}


// Store new deleted beatmap data into database
function storeDeletedBeatmapData($beatmapData, $tournamentName, $tournamentRound, $phpDataObject)
{
    // Call the correct tournament database table based on the GET request
    $tournamentTable = "{$tournamentName}_{$tournamentRound}";

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
    $queryStatement = $phpDataObject->prepare($query);

    // Bind the beatmap data to the prepared statement
    $queryStatement->bindParam(":map_id", $beatmapData['map_id']);
    $queryStatement->bindParam(":total_length", $beatmapData['total_length']);
    $queryStatement->bindParam(":map_url", $beatmapData['map_url']);
    $queryStatement->bindParam(":cover_image_url", $beatmapData['cover_image_url']);
    $queryStatement->bindParam(":title_unicode", $beatmapData['title_unicode']);
    $queryStatement->bindParam(":artist_unicode", $beatmapData['artist_unicode']);
    $queryStatement->bindParam(":difficulty", $beatmapData['difficulty']);
    $queryStatement->bindParam(":mapper", $beatmapData['mapper']);
    $queryStatement->bindParam(":difficulty_rating", $beatmapData['difficulty_rating']);
    $queryStatement->bindParam(":map_bpm", $beatmapData['map_bpm']);
    $queryStatement->bindParam(":overall_difficulty", $beatmapData['overall_difficulty']);
    $queryStatement->bindParam(":health_point", $beatmapData['health_point']);
    $queryStatement->bindParam(":amount_of_passes", $beatmapData['amount_of_passes']);
    $queryStatement->bindParam(":mod_type", $beatmapData['mod_type']);

    // Execute the statement and insert the data into database
    if ($queryStatement->execute()) {
        error_log("Insert successful for deleted beatmap ID: " . $beatmapData['map_id']);
        return $queryStatement->rowCount() > 0;
    } else {
        error_log("Insert failed for deleted beatmap ID: " . $beatmapData['map_id']);
        $errorInfo = $queryStatement->errorInfo();
        error_log("Error Info: " . implode(", ", $errorInfo));
        return false;
    }
}


// Check if deleted beatmap data already exists in the database
function checkDeletedBeatmapData($beatmapId, $tournamentName, $tournamentRound, $phpDataObject)
{
    // Call the correct tournament database table based on the GET request
    $tournamentTable = "{$tournamentName}_{$tournamentRound}";

    $query = "SELECT id FROM $tournamentTable WHERE map_id = :map_id";
    $queryStatement = $phpDataObject->prepare($query);
    $queryStatement->bindParam(":map_id", $beatmapId, PDO::PARAM_INT);
    $queryStatement->execute();

    return $queryStatement->fetchColumn() !== false;
}


// Update existing deleted beatmap data in the database with new data
function updateDeletedBeatmapData($beatmapData, $tournamentName, $tournamentRound, $phpDataObject)
{
    // Call the correct tournament database table based on the GET request
    $tournamentTable = "{$tournamentName}_{$tournamentRound}";

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
    $queryStatement = $phpDataObject->prepare($query);

    // Bind the beatmap data to the prepared statement
    $queryStatement->bindParam(":map_id", $beatmapData['map_id']);
    $queryStatement->bindParam(":total_length", $beatmapData['total_length']);
    $queryStatement->bindParam(":map_url", $beatmapData['map_url']);
    $queryStatement->bindParam(":cover_image_url", $beatmapData['cover_image_url']);
    $queryStatement->bindParam(":title_unicode", $beatmapData['title_unicode']);
    $queryStatement->bindParam(":artist_unicode", $beatmapData['artist_unicode']);
    $queryStatement->bindParam(":difficulty", $beatmapData['difficulty']);
    $queryStatement->bindParam(":mapper", $beatmapData['mapper']);
    $queryStatement->bindParam(":difficulty_rating", $beatmapData['difficulty_rating']);
    $queryStatement->bindParam(":map_bpm", $beatmapData['map_bpm']);
    $queryStatement->bindParam(":overall_difficulty", $beatmapData['overall_difficulty']);
    $queryStatement->bindParam(":health_point", $beatmapData['health_point']);
    $queryStatement->bindParam(":amount_of_passes", $beatmapData['amount_of_passes']);
    $queryStatement->bindParam(":mod_type", $beatmapData['mod_type']);

    // Execute the statement and update the existen data in the database
    if ($queryStatement->execute()) {
        error_log("Update successful for deleted beatmap ID: " . $beatmapData['map_id']);
        return $queryStatement->rowCount() > 0;
    } else {
        error_log("Update failed for deleted beatmap ID: " . $beatmapData['map_id']);
        $errorInfo = $queryStatement->errorInfo();
        error_log("Error Info: " . implode(", ", $errorInfo));
        return false;
    }
}


// Get deleted beatmap data from database by beatmap IDs
function getDeletedBeatmapData($mapId, $tournamentName, $tournamentRound, $phpDataObject)
{
    // Call the correct tournament database table based on the GET request
    $tournamentTable = "{$tournamentName}_{$tournamentRound}";

    $query = "SELECT * FROM $tournamentTable WHERE map_id = :map_id";
    $queryStatement = $phpDataObject->prepare($query);
    $queryStatement->bindParam(":map_id", $mapId, PDO::PARAM_INT);

    // Execute the statement and get the needed data in the database to display
    if ($queryStatement->execute()) {
        error_log("Get data successfully for deleted beatmap ID: " . $mapId);
        // Fetch and return the result as an associative array
        return $queryStatement->fetch(PDO::FETCH_ASSOC);
    } else {
        error_log("Get data failed for deleted beatmap ID: " . $mapId);
        $errorInfo = $queryStatement->errorInfo();
        error_log("Error Info: " . implode(", ", $errorInfo));
        return false;
    }
}

// Retrieve tournament-related data from GET request
$tournamentName = $_GET['name'] ?? 'NULL';
$tournamentRound = $_GET['round'] ?? 'NULL';

$beatmapDatas = [];

// Check for correct GET request for tournament name
if ($tournamentName) {
    // Check for correct GET request for tournament round
    if ($tournamentRound) {
        // Read beatmap IDs and mod types from JSON file
        $beatmapJsonData = json_decode(file_get_contents('../data/beatmap.json'), true);
        $beatmapJsonDatas = $beatmapJsonData[$tournamentName][$tournamentRound] ?? [];

        foreach ($beatmapJsonDatas as $beatmapData) {
            $beatmapId = $beatmapData['id'];
            $modType = $beatmapData['mod'];

            // Fetch the beatmap data from the API or database
            $beatmapData = fetchBeatmapData($beatmapId, $tournamentName, $tournamentRound, $phpDataObject);

            // Check if the data fetching is successful
            if ($beatmapData) {
                // Check if the user is authenticated
                $accessToken = $_COOKIE['vot_access_token'] ?? null;

                if ($accessToken) {
                    // Check if the beatmap data already exists in the database
                    if (!checkBeatmapData($beatmapData->id, $tournamentName, $tournamentRound, $phpDataObject)) {
                        // If not, store the beatmap data in the database
                        storeBeatmapData($beatmapData, $modType, $tournamentName, $tournamentRound, $phpDataObject);
                    } else {
                        // If yes, update the existing beatmap data in the database
                        updateBeatmapData($beatmapData, $modType, $tournamentName, $tournamentRound, $phpDataObject);
                    }
                    // Get the beatmap data from the database
                    $retrievedBeatmapData = getBeatmapData($beatmapId, $tournamentName, $tournamentRound, $phpDataObject);
                } else {
                    // Get the beatmap data directly from the database without additional checking
                    $retrievedBeatmapData = getBeatmapData($beatmapId, $tournamentName, $tournamentRound, $phpDataObject);
                }

                // If data retrieval is successful, add it to the array
                if ($retrievedBeatmapData) {
                    $beatmapDatas[] = $retrievedBeatmapData;
                } else {
                    error_log("Failed to retrieve beatmap data for ID: {$beatmapId}.");
                }
            } else {
                // If data fetching is not successful, try to fetch deleted beatmap data from JSON file
                $deletedBeatmapDatas = fetchDeletedBeatmapData($tournamentName, $tournamentRound);

                foreach ($deletedBeatmapDatas as $deletedBeatmapData) {
                    // Check if deleted beatmap IDs in JSON file for deleted ones are the same as deleted beatmap IDs in JSON file for working ones.
                    if ($deletedBeatmapData['map_id'] == $beatmapId) {
                        // Check if the deleted beatmap data already exists in the database
                        if (!checkDeletedBeatmapData($deletedBeatmapData['map_id'], $tournamentName, $tournamentRound, $phpDataObject)) {
                            // If not, store the deleted beatmap data in the database
                            storeDeletedBeatmapData($deletedBeatmapData, $tournamentName, $tournamentRound, $phpDataObject);
                        } else {
                            // If yes, update the existing deleted beatmap data in the database
                            updateDeletedBeatmapData($deletedBeatmapData, $tournamentName, $tournamentRound, $phpDataObject);
                        }
                        // Get the deleted beatmap data from the database
                        $retrievedDeletedBeatmapData = getDeletedBeatmapData($deletedBeatmapData['map_id'], $tournamentName, $tournamentRound, $phpDataObject);

                        // If data retrieval is successful, add it to the array
                        if ($retrievedDeletedBeatmapData) {
                            $beatmapDatas[] = $retrievedDeletedBeatmapData;
                        } else {
                            error_log("Failed to retrieve deleted beatmap data for ID: {$beatmapId}.");
                        }
                        break;
                    }
                }
            }
        }
    } else {
        die('<div style="display: flex; justify-content: center; align-items: center; margin-left: 45rem; font-size: 5rem;">Please choose a valid button!</div>'); // TODO: This is an absolute temporary. Changed later (if I can).
    }
} else {
    die('<div style="display: flex; justify-content: center; align-items: center; margin-left: 45rem; font-size: 5rem;">Please choose a valid button!</div>'); // TODO: This is an absolute temporary. Changed later (if I can).
}
?>

<section>
    <div class="button-container">
        <form action="mappool.php" method="get">
            <!-- This allows the URL to have more than 1 field showed at the same time -->
            <input type="hidden" name="name" value="<?= $tournamentName; ?>">
            <!-- Sent the corresponding tournament round fields to the website for fetching stored data -->
            <button type="submit" name="round" value="qualifiers">Qualifiers</button>
            <button type="submit" name="round" value="ro16">RO16</button>
            <button type="submit" name="round" value="quarterfinals">QF</button>
            <button type="submit" name="round" value="semifinals">SF</button>
            <button type="submit" name="round" value="finals">Finals</button>
            <button type="submit" name="round" value="grandfinals">GF</button>
        </form>
    </div>

    <div class="mappool-page">
        <!-- Check if there's beatmap data to show to end-user and any of the tournament rounds' button is clicked -->
        <?php if (!empty($beatmapDatas) && isset($tournamentRound)): ?>
            <!-- Dynamic beatmap display with correct mod type -->
            <?php foreach ($beatmapDatas as $beatmapData): ?>
                <div class="mappool-card-container">
                    <h1><?= htmlspecialchars(isset($beatmapData['mod_type']) ? $beatmapData['mod_type'] : 'NULL'); ?></h1>

                    <br>

                    <a href="<?= isset($beatmapData['map_url']) ? $beatmapData['map_url'] : '#'; ?>">
                        <img src="<?= htmlspecialchars(isset($beatmapData['cover_image_url']) ? $beatmapData['cover_image_url'] : 'NULL'); ?>" width="490px" alt="Beatmap Cover">
                    </a>

                    <br><br>

                    <h2><?= htmlspecialchars(isset($beatmapData['title_unicode']) ? $beatmapData['title_unicode'] : 'NULL'); ?> [<?= isset($beatmapData['difficulty']) ? $beatmapData['difficulty'] : 'NULL'; ?>]</h2>
                    <h3><?= htmlspecialchars(isset($beatmapData['artist_unicode']) ? $beatmapData['artist_unicode'] : 'NULL'); ?></h3>
                    <h4 class="beatmap-creator-row">
                        Mapset by <a href="https://osu.ppy.sh/users/<?= htmlspecialchars(isset($beatmapData['mapper']) ? $beatmapData['mapper'] : '#'); ?>"><?= htmlspecialchars(isset($beatmapData['mapper']) ? $beatmapData['mapper'] : 'NULL'); ?></a>
                    </h4>

                    <br>

                    <div class="beatmap-attribute-row">
                        <p style="margin-right: 1rem;"><i class='bx bx-star'></i> <?= htmlspecialchars(isset($beatmapData['difficulty_rating']) ? number_format((float)$beatmapData['difficulty_rating'], 2) : 'NULL'); ?></p>
                        <p style="margin-right: 1rem;"><i class='bx bx-timer'></i> <?= htmlspecialchars(isset($beatmapData['total_length']) ? $beatmapData['total_length'] : 'NULL'); ?></p>
                        <p><i class='bx bx-tachometer'></i> <?= htmlspecialchars(isset($beatmapData['map_bpm']) ? number_format((float)$beatmapData['map_bpm'], 2) : 'NULL'); ?>bpm</p>
                    </div>

                    <br>

                    <div class="beatmap-attribute-row">
                        <p style="margin-right: 1rem;">OD: <?= htmlspecialchars(isset($beatmapData['overall_difficulty']) ? number_format((float)$beatmapData['overall_difficulty'], 2) : 'NULL'); ?></p>
                        <p style="margin-right: 1rem;">HP: <?= htmlspecialchars(isset($beatmapData['health_point']) ? number_format((float)$beatmapData['health_point'], 2) : 'NULL'); ?></p>
                        <p>Passed: <?= isset($beatmapData['amount_of_passes']) ? $beatmapData['amount_of_passes'] : 'NULL'; ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>
