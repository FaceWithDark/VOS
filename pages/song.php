<?php

declare(strict_types = 1);

require_once __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

require_once '../layouts/navigation_bar.php';
include_once '../modules/convertion/time_convertion.php';
// require '../layouts/configuration.php';

// Fetch custom song data from the Osu! API
function fetchCustomSongData($customSongId, $phpDataObject) {
    // Check if the user is authenticated by looking for the access token in cookies
    $accessToken = $_COOKIE['vot_access_token'] ?? null;
    if ($accessToken) {
        // If authenticated, construct the API URL for fetching beatmap data
        $apiUrl = "https://osu.ppy.sh/api/v2/beatmaps/{$customSongId}";
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
        try {
            // Determine the table to query based on the 'round' parameter
            $query = "SELECT id FROM vot4_custom_song WHERE custom_song_id = :custom_song_id";
            $queryStatement = $phpDataObject -> prepare($query);
            $queryStatement -> bindParam(":custom_song_id", $customSongId, PDO::PARAM_INT);

            // Execute the statement and fetch the data
            if($queryStatement -> execute()) {
                error_log("Successfully retrieved data for custom song ID: " . $customSongId);
                return $queryStatement -> fetch(PDO::FETCH_ASSOC);
            }
            else {
                error_log("Failed to retrieve data for custom song ID: " . $customSongId);
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


// Store new custom song data into database
function storeCustomSongData($customSongData, $tournamentTitle, $tournamentRound, $modType, $phpDataObject) {
    $formattedTotalLength = integerToTimeFormat($customSongData -> total_length);
    // Add the tournament title, round, and mod type as custom parameters to the fetched data 
    $customSongData -> tournament_title = $tournamentTitle;
    $customSongData -> tournament_round = $tournamentRound;
    $customSongData -> mod_type = $modType;

    // SQL query to store custom song data in the 'vot4_custom_song' table
    $query = "INSERT INTO vot4_custom_song (tournament_title, 
                                            tournament_round, 
                                            mod_type, 
                                            custom_song_id, 
                                            custom_song_url,
                                            total_length, 
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
                     VALUES (:tournament_title, 
                             :tournament_round, 
                             :mod_type,
                             :custom_song_id,
                             :custom_song_url,
                             :total_length,
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
    $queryStatement -> bindParam(":tournament_title", $tournamentTitle);
    $queryStatement -> bindParam(":tournament_round", $tournamentRound);
    $queryStatement -> bindParam(":mod_type", $modType);
    $queryStatement -> bindParam(":custom_song_id", $customSongData -> id);
    $queryStatement -> bindParam(":custom_song_url", $customSongData -> url);    
    $queryStatement -> bindParam(":total_length", $formattedTotalLength);
    $queryStatement -> bindParam(":cover_image_url", $customSongData -> beatmapset -> covers -> cover);
    $queryStatement -> bindParam(":title_unicode", $customSongData -> beatmapset -> title_unicode);
    $queryStatement -> bindParam(":artist_unicode", $customSongData -> beatmapset -> artist_unicode);
    $queryStatement -> bindParam(":difficulty", $customSongData -> version);
    $queryStatement -> bindParam(":mapper", $customSongData -> beatmapset -> creator);
    $queryStatement -> bindParam(":difficulty_rating", $customSongData -> difficulty_rating);
    $queryStatement -> bindParam(":map_bpm", $customSongData -> bpm);
    $queryStatement -> bindParam(":overall_difficulty", $customSongData -> accuracy);
    $queryStatement -> bindParam(":health_point", $customSongData -> drain);
    $queryStatement -> bindParam(":amount_of_passes", $customSongData -> passcount);

    // Execute the statement and insert the data into database
    if ($queryStatement -> execute()) {
        error_log("Insert successful for custom song ID: " . $customSongData -> id);
        return $queryStatement -> rowCount() > 0;
    } 
    else {
        error_log("Insert failed for custom song ID: " . $customSongData -> id);
        $errorInfo = $queryStatement -> errorInfo();
        error_log("Error Info: " . implode(", ", $errorInfo));
        return false;
    }
}


// Check if custom song data already exists in the database
function checkCustomSongData($customSongId, $phpDataObject) {
    $query = "SELECT id FROM vot4_custom_song WHERE custom_song_id = :custom_song_id";
    $queryStatement = $phpDataObject -> prepare($query);
    $queryStatement -> bindParam(":custom_song_id", $customSongId, PDO::PARAM_INT);
    $queryStatement -> execute();

    return $queryStatement -> fetchColumn() !== false;
}


// Update existing custom song data in the database with new data
function updateCustomSongData($customSongData, $tournamentTitle, $tournamentRound, $modType, $phpDataObject) {
    $formattedTotalLength = integerToTimeFormat($customSongData -> total_length);
    // Add the tournament title, round, and mod type as custom parameters to the fetched data 
    $customSongData -> tournament_title = $tournamentTitle;
    $customSongData -> tournament_round = $tournamentRound;
    $customSongData -> mod_type = $modType;

    // SQL query to update custom song data in the 'vot4_custom_song' table
    $query = "UPDATE vot4_custom_song 
              SET tournament_title = :tournament_title, 
                  tournament_round = :tournament_round, 
                  mod_type = :mod_type,
                  custom_song_url = :custom_song_url,
                  total_length = :total_length,
                  cover_image_url = :cover_image_url, 
                  title_unicode = :title_unicode, 
                  artist_unicode = :artist_unicode, 
                  difficulty = :difficulty, 
                  mapper = :mapper, 
                  difficulty_rating = :difficulty_rating, 
                  map_bpm = :map_bpm, 
                  overall_difficulty = :overall_difficulty, 
                  health_point = :health_point, 
                  amount_of_passes = :amount_of_passes
              WHERE custom_song_id = :custom_song_id;";
    
    // Prepare the SQL statement to prevent SQL injection
    $queryStatement = $phpDataObject -> prepare($query);
    
    // Bind the beatmap data to the prepared statement
    $queryStatement -> bindParam(":tournament_title", $tournamentTitle);
    $queryStatement -> bindParam(":tournament_round", $tournamentRound);
    $queryStatement -> bindParam(":mod_type", $modType);
    $queryStatement -> bindParam(":custom_song_id", $customSongData -> id);
    $queryStatement -> bindParam(":custom_song_url", $customSongData -> url);    
    $queryStatement -> bindParam(":total_length", $formattedTotalLength);
    $queryStatement -> bindParam(":cover_image_url", $customSongData -> beatmapset -> covers -> cover);
    $queryStatement -> bindParam(":title_unicode", $customSongData -> beatmapset -> title_unicode);
    $queryStatement -> bindParam(":artist_unicode", $customSongData -> beatmapset -> artist_unicode);
    $queryStatement -> bindParam(":difficulty", $customSongData -> version);
    $queryStatement -> bindParam(":mapper", $customSongData -> beatmapset -> creator);
    $queryStatement -> bindParam(":difficulty_rating", $customSongData -> difficulty_rating);
    $queryStatement -> bindParam(":map_bpm", $customSongData -> bpm);
    $queryStatement -> bindParam(":overall_difficulty", $customSongData -> accuracy);
    $queryStatement -> bindParam(":health_point", $customSongData -> drain);
    $queryStatement -> bindParam(":amount_of_passes", $customSongData -> passcount);


    // Execute the statement and update the existen data in the database
    if ($queryStatement -> execute()) {
        error_log("Update successful for custom song ID: " . $customSongData -> id);
        return $queryStatement -> rowCount() > 0;
    } 
    else {
        error_log("Update failed for custom song ID: " . $customSongData -> id);
        $errorInfo = $queryStatement -> errorInfo();
        error_log("Error Info: " . implode(", ", $errorInfo));
        return false;
    }
}

// Get custom song data from database by beatmap IDs
function getCustomSongData($customSongId, $phpDataObject) {
    $query = "SELECT * FROM vot4_custom_song WHERE custom_song_id = :custom_song_id";
    $queryStatement = $phpDataObject -> prepare($query);
    $queryStatement -> bindParam(":custom_song_id", $customSongId, PDO::PARAM_INT);

    // Execute the statement and get the needed data in the database to display
    if ($queryStatement -> execute()) {
        error_log("Get data successfully for custom song ID: " . $customSongId);
        // Fetch and return the result as an associative array
        return $queryStatement -> fetch(PDO::FETCH_ASSOC);
    } 
    else {
        error_log("Get data failed for custom song ID: " . $customSongId);
        $errorInfo = $queryStatement -> errorInfo();
        error_log("Error Info: " . implode(", ", $errorInfo));
        return false;
    }
}

$customSongIds = [ // VOT3  // VOT4
                   4235709, 4692888
                 ];

$tournamentTitles = [ 'VOT3', 'VOT4' ];
$tournamentRounds = ['Qualifiers', 'RO16', 'Quarterfinals', 'Semifinals', 'Finals', 'Grandfinals'];
$modTypes = ['NM', 'HD', 'HR', 'DT', 'FM', 'EZ', 'HDHR', 'TB'];

$customSongDataArray = [];

foreach($customSongIds as $customSongId) {
    $customSongData = fetchCustomSongData($customSongId, $phpDataObject);
    // die('<pre>' . print_r($customSongData, true) . '</pre>');

    if($customSongData) {
        // Define a variable for storing tournament title, round, and mod type correspondingly
        $tournamentTitle = '';
        $tournamentRound = '';
        $modType = '';

        // Loop thorugh the tournament titles to assign the corresponding values based on the conditions
        foreach($tournamentTitles as $tournamentTitle) {
            // TODO: this's purely hard-coded. Will optimise later on (maybe)
            if($tournamentTitle === 'VOT3' && $customSongId == '4235709') {
                $tournamentTitle = 'VOT3';
                $tournamentRound = 'Semifinals';
                $modType = 'HD1';
                break;
            }
            else if($tournamentTitle === 'VOT4' && $customSongId == '4692888') {
                $tournamentTitle = 'VOT4';
                $tournamentRound = 'Quarterfinals';
                $modType = 'HDHR';
                break;
            }
        }

        // Stored the fetched data in the database with the tournament title, round, and mod type
        if($tournamentTitle && $tournamentRound && $modType) {
            // Check if the user is authenticated
            $accessToken = $_COOKIE['vot_access_token'] ?? null;

            if($accessToken) {
                if(!checkCustomSongData($customSongId, $phpDataObject)) {
                    storeCustomSongData($customSongData, $tournamentTitle, $tournamentRound, $modType, $phpDataObject);
                }
                else {
                    updateCustomSongData($customSongData, $tournamentTitle, $tournamentRound, $modType, $phpDataObject);
                }
                // Get stored data from the database after storing/updating the current stored data in the databse only in the case of user is authenticated
                $retrievedCustomSongData = getCustomSongData($customSongId, $phpDataObject);
            }
            else {
                // Get stored data directly from the databse if user is not authenticated
                $retrievedCustomSongData = getCustomSongData($customSongId, $phpDataObject);
            }
            
            // If data retrieval is successful, add it to the array
            if($retrievedCustomSongData) {
                $customSongDataArray[] = $retrievedCustomSongData;
            }
            else {
                error_log("Failed to retrieve custom song data for ID: " . $customSongId);
            }
        }
        else {
            error_log("Failed to fetch custom song data for ID: " . $customSongId);
        }
    }
}
?>

<section>
    <div class="header-container">
        <h1>Banger Song <i class='bx bxs-hot'></i></h1>
    </div>

    <div class="song-page">
        <?php if(!empty($customSongDataArray)): ?>
            <!-- Dynamic custom song information display -->
            <?php foreach($customSongDataArray as $customSongData): ?>              
                <div class="song-card-container">
                    <h1><?= htmlspecialchars($customSongData['tournament_title']); ?> - <?= htmlspecialchars($customSongData['tournament_round']); ?> - <?= htmlspecialchars($customSongData['mod_type']); ?></h1>
                    <br>
                    <a href="<?= htmlspecialchars($customSongData['custom_song_url']); ?>"><img src="<?= htmlspecialchars($customSongData['cover_image_url']); ?>" width="490px" alt="Beatmap Cover"></a>
                    <br><br>
                    <h2><?= htmlspecialchars($customSongData['title_unicode']); ?> [<?= ($customSongData['difficulty']); ?>]</h2>
                    <h3><?=  htmlspecialchars($customSongData['artist_unicode']); ?></h3>
                    <h4 class="beatmap-creator-row">
                        Mapset by <a href="https://osu.ppy.sh/users/<?= htmlspecialchars($customSongData['mapper']); ?>"><?= htmlspecialchars($customSongData['mapper']); ?></a>
                    </h4>
                    <br>
                    <div class="beatmap-attribute-row">
                        <p style="margin-right: 1rem;"><i class='bx bx-star'></i> <?= htmlspecialchars(number_format((float)$customSongData['difficulty_rating'], 2)); ?></p>
                        <p style="margin-right: 1rem;"><i class='bx bx-timer'></i> <?= htmlspecialchars($customSongData['total_length']); ?></p>
                        <p><i class='bx bx-tachometer'></i> <?= ($customSongData['map_bpm']); ?>bpm</p>
                    </div>
                    <br>
                    <div class="beatmap-attribute-row">
                        <p style="margin-right: 1rem;">OD: <?= htmlspecialchars(number_format((float)$customSongData['overall_difficulty'], 2)); ?></p>
                        <p style="margin-right: 1rem;">HP: <?= htmlspecialchars(number_format((float)$customSongData['health_point'], 2)); ?></p>
                        <p>Passed: <?= ($customSongData['amount_of_passes']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>
