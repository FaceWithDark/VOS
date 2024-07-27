<?php

declare(strict_types = 1);

require_once __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

require_once '../layouts/navigation_bar.php';
include_once '../modules/convertion/time_convertion.php';
// require '../layouts/configuration.php';

// Fetch custom song data from the Osu! API
function fetchCustomSongData() {
    // Check if the user is authenticated by looking for the access token in cookies
    $accessToken = $_COOKIE['vot_access_token'] ?? null;
    if ($accessToken) {
        // If authenticated, construct the API URL for fetching beatmap data
        $apiUrl = "https://osu.ppy.sh/api/v2/beatmaps/4235709";
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

$customSongData = fetchCustomSongData();
die('<pre>' . print_r($customSongData, true) . '</pre>');

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
