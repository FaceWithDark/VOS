<?php

declare(strict_types = 1);

require_once __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

require_once '../layouts/navigation_bar.php';
// require '../layouts/configuration.php';

// Fetch staff data from the Osu! API
function fetchStaffData() {
    // Check if the user is authenticated by looking for the access token in cookies
    $accessToken = $_COOKIE['vot_access_token'] ?? null;
    if ($accessToken) {
        // If authenticated, construct the API URL for fetching beatmap data
        $apiUrl = "https://osu.ppy.sh/api/v2/users/19817503/taiko?key=test";
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
            
            // Return the staff data if it is a 200 status 
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


// Store new staff data into database
function storeStaffData($staffData, $phpDataObject) {
    // SQL query to store staff data into the corresponding database table
    $query = "INSERT INTO vot4_staff (staff_id,
                                      staff_username,
                                      staff_avatar_url,
                                      staff_roles,
                                      staff_global_ranking,
                                      staff_local_ranking,
                                      staff_performance_point,
                                      staff_accuracy,
                                      staff_country_name,
                                      staff_country_flag,
                                      staff_current_location,
                                      staff_tournament_badge)
                     VALUES (:staff_id,
                             :staff_username,
                             :staff_avatar_url,
                             :staff_roles,
                             :staff_global_ranking,
                             :staff_local_ranking,
                             :staff_performance_point,
                             :staff_accuracy,
                             :staff_country_name,
                             :staff_country_flag,
                             :staff_current_location,
                             :staff_tournament_badge);";
    
    // Prepare the SQL statement to prevent SQL injection
    $queryStatement = $phpDataObject -> prepare($query);
    
    // Bind the staff data to the prepared statement
    $queryStatement -> bindParam(":staff_id", $staffData -> id);
    $queryStatement -> bindParam(":staff_username", $staffData -> username);
    $queryStatement -> bindParam(":staff_avatar_url", $staffData -> avatar_url);
    $queryStatement -> bindParam(":staff_roles", $staffData -> need_an_array_for_this);                  // TODO: it is as what it is
    $queryStatement -> bindParam(":staff_global_ranking", $staffData -> statistics -> global_rank);
    $queryStatement -> bindParam(":staff_local_ranking", $staffData -> statistics -> country_rank);
    $queryStatement -> bindParam(":staff_performance_point", $staffData -> statistics -> pp);
    $queryStatement -> bindParam(":staff_accuracy", $staffData -> statistics -> hit_accuracy);
    $queryStatement -> bindParam(":staff_country_name", $staffData -> country -> name);
    $queryStatement -> bindParam(":staff_country_flag", $staffData -> many_approach_but_still_thinking); // TODO: it is as what it is
    $queryStatement -> bindParam(":staff_current_location", $staffData -> location);
    $queryStatement -> bindParam(":staff_tournament_badge", $staffData -> badges -> image_url);          // TODO: need use-case for non-badge staff as well
    
    // Execute the statement and insert the data into database
    if ($queryStatement -> execute()) {
        error_log("Insert successful for staff ID: " . $staffData -> id);
        return $queryStatement -> rowCount() > 0;
    } 
    else {
        error_log("Insert failed for staff ID: " . $staffData -> id);
        $errorInfo = $queryStatement -> errorInfo();
        error_log("Error Info: " . implode(", ", $errorInfo));
        return false;
    }
}

$staffData = fetchStaffData();
// die('<pre>' . print_r($staffData, true) . '</pre>');

/*
 * The function above this comment are tested and worked. I will be doing proper data for those TODO's later on
 * 
 * For testing purposes:
===============================================================================================================

        if($staffData) {
            storeStaffData($staffData, $tempData, $phpDataObject);
            die('<pre>' . print_r(storeStaffData($staffData, $tempData, $phpDataObject), true) . '</pre>');
        }

=============================================================================================================== 
 */
?>

<div class="staff-page">
    <header>
        <h1>Our Lovely Staff 💖</h1>
    </header>

    <section>
        <!-- TODO: 
                - Include avatar and other related info in.
                - PHP can handle this. Need to get data from osu! API only.
        -->
        <div class="mappool-card-container">
            <h1><img src="#" alt="#">DeepInDark</h1>
            <img src="#" alt="#">
            <h2>Streamer</h2>            
        </div>
        
        <div class="mappool-card-container">
            <h1><img src="#" alt="#">ダビッド</h1>
            <img src="#" alt="#">
            <h2>Mappooler</h2>            
        </div>
        
        <div class="mappool-card-container">
            <h1><img src="#" alt="#">rin</h1>
            <img src="#" alt="#">
            <h2>Mapper</h2>            
        </div>
    </section>
</div>
