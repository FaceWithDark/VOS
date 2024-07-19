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
function storeStaffData($staffData, $staffRole, $phpDataObject) {
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
    $queryStatement -> bindParam(":staff_roles", $staffRole);
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


// Get staff roles by array index
function getStaffRoleByIndex($arrayIndex, $staffRoles) {
    foreach($staffRoles as $staffRole => $arrayIndexes) {
        if(in_array($arrayIndex, $arrayIndexes)) {
            return $staffRole;
        }
    }
    // None of the roles applied if index is not found
    return 'N/A';
}


function fetchCountryFlagData() {
    $apiUrl = "https://cdn.simplelocalize.io/public/v1/countries";
    $client = new Client();

    try {
        $response = $client -> get($apiUrl, [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ]);

        if($response -> getStatusCode() === 200) {
            $flagData = $response -> getBody() -> getContents();
            return $flagData;
        }

        return false;
    }
    catch(RequestException $exception) {
        error_log("API request failed: " . $exception -> getMessage());
        return false;
    }
}

// Define staff IDs for which data will be fetched
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

// Define a mapping of index ranges to staff roles
$staffRoles = [
    'Host' => [0],
    'Mappooler' => [1, 2, 3, 4],
    'GFX / VFX' => [5, 6, 7, 8, 9],
    'Mapper' => [10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26],
    'Playtester' => [27, 28, 29, 30, 31, 32],
    'Referee' => [33, 34, 35, 36, 37, 38, 39, 40, 41, 42],
    'Streamer' => [43, 44, 45, 46, 47, 48],
    'Commentator' => [49, 50, 51, 52, 53, 54],
    'Statistician' => [55, 56]
];

// Initialize an empty array to map staff IDs to their roles
$staffIdToRoles = [];

// Iterate over an array of staff IDs to populate the empty array mapping
foreach($staffIds as $staffIndex => $staffId) {
    // Get the staff role for the current index received
    $staffRole = getStaffRoleByIndex($staffIndex, $staffRoles);

    // If the staff ID received is not set in the mapping, initialize it with an empty array (means nothing)
    if(!isset($staffIdToRoles[$staffId])) {
        $staffIdToRoles[$staffId] = [];
    }

    // Append the staff role to the staff ID's list of roles 
    $staffIdToRoles[$staffId][] = $staffRole;
}

// Remove duplicate staff IDs to ensure each ID is processed only once
$uniqueStaffIds = array_unique($staffIds);

// Iterate over the unique staff IDs
foreach($uniqueStaffIds as $arrayIndex => $staffId) {
    // Combine the roles into a single string separated by commas
    $staffRole = implode(', ', $staffIdToRoles[$staffId]);    

    // Fetch the staff data from the API or database
    $staffData = fetchStaffData($staffId);
    if($staffData) {
        storeStaffData($staffData, $staffRole, $phpDataObject);
    }
}

$flagData = fetchCountryFlagData();
// die('<pre>' . print_r($flagData, true) . '</pre>');
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
