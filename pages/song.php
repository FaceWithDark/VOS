<?php

declare(strict_types = 1);

require_once __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

require_once '../layouts/navigation_bar.php';
include_once '../modules/convertion/time_convertion.php';
// require '../layouts/configuration.php';

// Fetch custom song data from the Osu! API
function fetchCustomSongData($customSongId) {
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
}


// Store new custom song data into database
function storeCustomSongData($customSongData, $phpDataObject) {
    $formattedTotalLength = integerToTimeFormat($customSongData -> total_length);

    // SQL query to store custom song data in the 'vot4_custom_song' table
    $query = "INSERT IGNORE INTO vot4_custom_song (tournament_title, 
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
    $queryStatement -> bindParam(":tournament_title", $customSongData -> need_array_or_so_for_this); // TODO: it is as what it is
    $queryStatement -> bindParam(":tournament_round", $customSongData -> need_array_or_so_for_this); // TODO: it is as what it is
    $queryStatement -> bindParam(":mod_type", $customSongData -> need_array_or_so_for_this);         // TODO: it is as what it is
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


$customSongIds = [ // VOT3  // VOT4
                   4235709, 4692888
                 ];

foreach($customSongIds as $customSongId) {
    $customSongData = fetchCustomSongData($customSongId);
    // die('<pre>' . print_r($customSongData, true) . '</pre>');
    
    if($customSongData) {
        $storedCustomSongData = storeCustomSongData($customSongData, $phpDataObject);
    }
}
?>

<section>
    <div class="header-container">
        <h1>Banger Song <i class='bx bxs-hot'></i></h1>
    </div>

    <div class="song-page">
        <!-- TODO: 
            - Include song's image and other related info in.
            - PHP can handle this. Need to get data from osu! API only.
            - Direct link will be change to Soundclound link (if there is one).
        -->                
        <div class="song-card-container">
            <h1>VOT3 - Semifinals - HD1</h1>
            <br>
            <a href="https://osu.ppy.sh/beatmapsets/2032103#taiko/4235709"><img src="" width="490px" alt="Beatmap Cover"></a>
            <br><br>
            <h2>ILFK [davidminh & njshift's Terrestrial Oni]</h2>
            <h3>SectorJack & Edolmary</h3>
            <h4 class="beatmap-creator-row">
                Mapset by <a href="https://osu.ppy.sh/users/9623142">davidminh0111</a>
            </h4>
            <br>
            <div class="beatmap-attribute-row">
                <p style="margin-right: 1rem;"><i class='bx bx-star'></i>abc</p>
                <p style="margin-right: 1rem;"><i class='bx bx-timer'></i>abc</p>
                <p><i class='bx bx-tachometer'></i>abc bpm</p>
            </div>
            <br>
            <div class="beatmap-attribute-row">
                <p style="margin-right: 1rem;">OD: abc</p>
                <p style="margin-right: 1rem;">HP: abc</p>
                <p>Passed: abc</p>
            </div>
        </div>

        <div class="song-card-container">
            <h1>abc</h1>
            <br>
            <a href="#"><img src="" width="490px" alt="Beatmap Cover"></a>
            <br><br>
            <h2>abc</h2>
            <h3>abc</h3>
            <h4 class="beatmap-creator-row">
                Mapset by <a href="#">abc</a>
            </h4>
            <br>
            <div class="beatmap-attribute-row">
                <p style="margin-right: 1rem;"><i class='bx bx-star'></i>abc</p>
                <p style="margin-right: 1rem;"><i class='bx bx-timer'></i>abc</p>
                <p><i class='bx bx-tachometer'></i>abc bpm</p>
            </div>
            <br>
            <div class="beatmap-attribute-row">
                <p style="margin-right: 1rem;">OD: abc</p>
                <p style="margin-right: 1rem;">HP: abc</p>
                <p>Passed: abc</p>
            </div>
        </div>

        <div class="song-card-container">
            <h1>abc</h1>
            <br>
            <a href="#"><img src="" width="490px" alt="Beatmap Cover"></a>
            <br><br>
            <h2>abc</h2>
            <h3>abc</h3>
            <h4 class="beatmap-creator-row">
                Mapset by <a href="#">abc</a>
            </h4>
            <br>
            <div class="beatmap-attribute-row">
                <p style="margin-right: 1rem;"><i class='bx bx-star'></i>abc</p>
                <p style="margin-right: 1rem;"><i class='bx bx-timer'></i>abc</p>
                <p><i class='bx bx-tachometer'></i>abc bpm</p>
            </div>
            <br>
            <div class="beatmap-attribute-row">
                <p style="margin-right: 1rem;">OD: abc</p>
                <p style="margin-right: 1rem;">HP: abc</p>
                <p>Passed: abc</p>
            </div>
        </div>
    </div>
</section>
