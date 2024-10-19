<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

require_once '../layouts/navigation_bar.php';

// Fetch staff data from the Osu! API
function fetchStaffData($staffId, $phpDataObject)
{
    // Check if the user is authenticated by looking for the access token in cookies
    $accessToken = $_COOKIE['vot_access_token'] ?? null;
    if ($accessToken) {
        // If authenticated, construct the API URL for fetching beatmap data
        $apiUrl = "https://osu.ppy.sh/api/v2/users/{$staffId}/taiko"; // TODO: 'key' parameter as optional
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

            // Return the staff data if it is a 200 status 
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
        return getStaffData($staffId, $phpDataObject);
    }
}


// Store new staff data into database
function storeStaffData($staffData, $staffRole, $phpDataObject)
{

    // Construct the flag URL using the country code as in lowercase letter
    $countryCode = strtolower($staffData->country_code);
    $countryFlagUrl = "https://flagcdn.com/24x18/$countryCode.webp";

    // SQL query to store staff data into the corresponding database table
    $query = "INSERT INTO vot4_staff (staff_id,
                                      staff_username,
                                      staff_avatar_url,
                                      staff_roles,
                                      staff_country_name,
                                      staff_country_flag_url)
                     VALUES (:staff_id,
                             :staff_username,
                             :staff_avatar_url,
                             :staff_roles,
                             :staff_country_name,
                             :staff_country_flag_url)";

    // Prepare the SQL statement to prevent SQL injection
    $queryStatement = $phpDataObject->prepare($query);

    // Bind the staff data to the prepared statement
    $queryStatement->bindParam(":staff_id", $staffData->id);
    $queryStatement->bindParam(":staff_username", $staffData->username);
    $queryStatement->bindParam(":staff_avatar_url", $staffData->avatar_url);
    $queryStatement->bindParam(":staff_roles", $staffRole);
    $queryStatement->bindParam(":staff_country_name", $staffData->country->name);
    $queryStatement->bindParam(":staff_country_flag_url", $countryFlagUrl);

    // Execute the statement and insert the data into database
    if ($queryStatement->execute()) {
        error_log("Insert successful for staff ID: " . $staffData->id);
        return $queryStatement->rowCount() > 0;
    } else {
        error_log("Insert failed for staff ID: " . $staffData->id);
        $errorInfo = $queryStatement->errorInfo();
        error_log("Error Info: " . implode(", ", $errorInfo));
        return false;
    }
}


// Check if staff data already exists in the database
function checkStaffData($staffId, $phpDataObject)
{
    $query = "SELECT id FROM vot4_staff WHERE staff_id = :staff_id";
    $queryStatement = $phpDataObject->prepare($query);
    $queryStatement->bindParam(":staff_id", $staffId, PDO::PARAM_INT);
    $queryStatement->execute();

    return $queryStatement->fetchColumn() !== false;
}


// Update existing staff data in the database with new data
function updateStaffData($staffData, $staffRole, $phpDataObject)
{
    // Construct the flag URL using the country code as in lowercase letter
    $countryCode = strtolower($staffData->country_code);
    $countryFlagUrl = "https://flagcdn.com/24x18/$countryCode.webp";

    // SQL query to update staff data
    $query = "UPDATE vot4_staff SET
                staff_username = :staff_username,
                staff_avatar_url = :staff_avatar_url,
                staff_roles = :staff_roles,
                staff_country_name = :staff_country_name,
                staff_country_flag_url = :staff_country_flag_url
              WHERE staff_id = :staff_id";

    // Prepare the SQL statement to prevent SQL injection
    $queryStatement = $phpDataObject->prepare($query);

    // Bind the staff data to the prepared statement
    $queryStatement->bindParam(":staff_id", $staffData->id);
    $queryStatement->bindParam(":staff_username", $staffData->username);
    $queryStatement->bindParam(":staff_avatar_url", $staffData->avatar_url);
    $queryStatement->bindParam(":staff_roles", $staffRole);
    $queryStatement->bindParam(":staff_country_name", $staffData->country->name);
    $queryStatement->bindParam(":staff_country_flag_url", $countryFlagUrl);


    // Execute the statement and update the existen data in the database
    if ($queryStatement->execute()) {
        error_log("Update successful for staff ID: " . $staffData->id);
        return $queryStatement->rowCount() > 0;
    } else {
        error_log("Update failed for staff ID: " . $staffData->id);
        $errorInfo = $queryStatement->errorInfo();
        error_log("Error Info: " . implode(", ", $errorInfo));
        return false;
    }
}


// Get staff data from database by staff IDs
function getStaffData($staffId, $phpDataObject)
{
    $query = "SELECT * FROM vot4_staff WHERE staff_id = :staff_id";
    $queryStatement = $phpDataObject->prepare($query);
    $queryStatement->bindParam(":staff_id", $staffId, PDO::PARAM_INT);

    // Execute the statement and get the needed data in the database to display
    if ($queryStatement->execute()) {
        error_log("Get data successfully for staff ID: " . $staffId);
        // Fetch and return the result as an associative array
        return $queryStatement->fetch(PDO::FETCH_ASSOC);
    } else {
        error_log("Get data failed for staff ID: " . $staffId);
        $errorInfo = $queryStatement->errorInfo();
        error_log("Error Info: " . implode(", ", $errorInfo));
        return false;
    }
}


// Get staff roles by array index
function getStaffRoleByIndex($arrayIndex, $staffRoles)
{
    foreach ($staffRoles as $staffRole => $arrayIndexes) {
        if (in_array($arrayIndex, $arrayIndexes)) {
            return $staffRole;
        }
    }
    // None of the roles applied if index is not found
    return 'N/A';
}

// Define staff IDs for which data will be fetched
$staffIds = [ // Host
    9623142,
    // Mappooler
    9623142,
    26012543,
    18397349,
    11833538,
    // GFX / VFX
    16039831,
    14083855,
    13981991,
    9912966,
    14001000,
    // Mapper
    9623142,
    16039831,
    17302272,
    7169208,
    22522738,
    22515524,
    3724819,
    8631719,
    2865172,
    3738344,
    14520910,
    17916791,
    12510704,
    2345156,
    11056763,
    1109122,
    26190106,
    // Playtester
    9623142,
    16039831,
    2228401,
    11411697,
    13630137,
    26012543,
    // Referee
    9623142,
    21290592,
    26012543,
    17148657,
    22515524,
    13456818,
    7109317,
    10129901,
    829469,
    19207842,
    // Streamer
    13456818,
    7789926,
    15815791,
    19817503,
    11406987,
    1926383,
    // Commentator
    9623142,
    16039831,
    21290592,
    26012543,
    24042710,
    22069182,
    // Statistician
    10494860,
    13456818
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

// Retrieve tournament-related data from GET request
$tournamentName = $_GET['name'] ?? 'NULL';

// Initialize an empty array to map staff IDs to their roles
$staffIdToRoles = [];

$staffDataArray = [];

// Check for correct GET request for tournament name
if ($tournamentName) {
    // Iterate over an array of staff IDs to populate the empty array mapping
    foreach ($staffIds as $staffIndex => $staffId) {
        // Get the staff role for the current index received
        $staffRole = getStaffRoleByIndex($staffIndex, $staffRoles);

        // If the staff ID received is not set in the mapping, initialize it with an empty array (means nothing)
        if (!isset($staffIdToRoles[$staffId])) {
            $staffIdToRoles[$staffId] = [];
        }

        // Append the staff role to the staff ID's list of roles 
        $staffIdToRoles[$staffId][] = $staffRole;
    }

    // Remove duplicate staff IDs to ensure each ID is processed only once
    $uniqueStaffIds = array_unique($staffIds);

    // Iterate over the unique staff IDs
    foreach ($uniqueStaffIds as $arrayIndex => $staffId) {
        // Combine the roles into a single string separated by commas
        $staffRole = implode(', ', $staffIdToRoles[$staffId]);

        // Fetch the staff data from the API or database
        $staffData = fetchStaffData($staffId, $phpDataObject);
        if ($staffData) {
            // Check if the user is authenticated
            $accessToken = $_COOKIE['vot_access_token'] ?? null;

            if ($accessToken) {
                if (!checkStaffData($staffId, $phpDataObject)) {
                    storeStaffData($staffData, $staffRole, $phpDataObject);
                } else {
                    updateStaffData($staffData, $staffRole, $phpDataObject);
                }
                // Get stored data from the database after storing/updating the current stored data in the databse only in the case of user is authenticated
                $retrievedStaffData = getStaffData($staffId, $phpDataObject);
            } else {
                // Get stored data directly from the databse if user is not authenticated
                $retrievedStaffData = getStaffData($staffId, $phpDataObject);
            }

            // If data retrieval is successful, add it to the array
            if ($retrievedStaffData) {
                $staffDataArray[] = $retrievedStaffData;
            } else {
                error_log("Failed to retrieve staff data for ID: " . $staffId);
            }
        } else {
            error_log("Failed to fetch staff data for ID: " . $staffId);
        }
    }
}
?>

<section>
    <div class="button-container">
        <form action="staff.php" method="get">
            <!-- Sent the corresponding tournament name fields to the website for fetching stored data -->
            <button type="submit" name="name" value="vot3">VOT3</button>
            <button type="submit" name="name" value="vot4">VOT4</button>
        </form>
    </div>

    <div class="staff-page">
        <?php if (!empty($staffDataArray)): ?>
            <!-- Dynamic staff information display -->
            <?php foreach ($staffDataArray as $staffData): ?>
                <div class="staff-card-container">
                    <h1><img src="<?= htmlspecialchars($staffData['staff_country_flag_url']); ?>" alt="<?= htmlspecialchars($staffData['staff_country_name']); ?>"><?= htmlspecialchars($staffData['staff_username']); ?></h1>
                    <img src="<?= htmlspecialchars($staffData['staff_avatar_url']); ?>" alt="<?= htmlspecialchars($staffData['staff_username']); ?>'s Avatar">
                    <h2><?= htmlspecialchars($staffData['staff_roles']) ?></h2>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>
