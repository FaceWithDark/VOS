<?php

declare(strict_types = 1);

require_once __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

require_once '../layouts/navigation_bar.php';
// require '../layouts/configuration.php';

// Fetch staff data from the Osu! API
function fetchStaffData($staffId) {
    // Check if the user is authenticated by looking for the access token in cookies
    $accessToken = $_COOKIE['vot_access_token'] ?? null;
    if ($accessToken) {
        // If authenticated, construct the API URL for fetching beatmap data
        $apiUrl = "https://osu.ppy.sh/api/v2/users/{$staffId}/taiko"; // TODO: 'key' parameter as optional
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
    $query = "INSERT IGNORE INTO vot4_staff (staff_id,
                                             staff_username,
                                             staff_avatar_url,
                                             staff_roles,
                                             staff_country_name,
                                             staff_country_flag)
                     VALUES (:staff_id,
                             :staff_username,
                             :staff_avatar_url,
                             :staff_roles,
                             :staff_country_name,
                             :staff_country_flag)
                     ON DUPLICATE KEY UPDATE
                             staff_id = VALUES(staff_id),
                             staff_username = VALUES(staff_username),
                             staff_avatar_url = VALUES(staff_avatar_url),
                             staff_roles = VALUES(staff_roles),
                             staff_country_name = VALUES(staff_country_name),
                             staff_country_flag = VALUES(staff_country_flag);";
    
    // Prepare the SQL statement to prevent SQL injection
    $queryStatement = $phpDataObject -> prepare($query);
    
    // Bind the staff data to the prepared statement
    $queryStatement -> bindParam(":staff_id", $staffData -> id);
    $queryStatement -> bindParam(":staff_username", $staffData -> username);
    $queryStatement -> bindParam(":staff_avatar_url", $staffData -> avatar_url);
    $queryStatement -> bindParam(":staff_roles", $staffData -> need_an_array_for_this);                  // TODO: it is as what it is
    $queryStatement -> bindParam(":staff_country_name", $staffData -> country -> name);
    $queryStatement -> bindParam(":staff_country_flag", $staffData -> many_approach_but_still_thinking); // TODO: it is as what it is
    
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

$staffIds = [ // Host
              9623142,
              // Mappooler
              9623142, 26012543, 18397349, 11833538,
              // GFX / VFX
              16039831, 14083855, 13981991, 9912966, 14001000,
              // Mapper
              9623142, 16039831, 17302272, 7169208, 22522738, 22515524, 3724819, 8631719, 2865172, 3738344, 14520910, 17916791, 12510704, 2345156, 11056763, 1109122, 26190106,
              // Playtester
              9623142, 16039831, 2228401, 11411697, 13630137, 26012543,
              // Referee
              9623142, 21290592, 26012543, 17148657, 22515524, 13456818, 7109317, 10129901, 829469, 19207842,
              // Streamer
              13456818, 7789926, 15815791, 19817503, 11406987, 1926383,
              // Commentator
              9623142, 16039831, 21290592, 26012543, 24042710, 22069182,
              // Statistician
              10494860, 13456818
];

$uniqueStaffIds = array_unique($staffIds);

foreach($uniqueStaffIds as $staffId) {
    $staffData = fetchStaffData($staffId);
    if($staffData) {
        storeStaffData($staffData, $phpDataObject);
    }
}
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
