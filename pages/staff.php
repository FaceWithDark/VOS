<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

require_once '../layouts/navigation_bar.php';
//require '../layouts/configuration.php';

// Fetch staff data from the Osu! API
function fetchStaffData($staffId, $tournamentName, $phpDataObject)
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
        return getStaffData($staffId, $tournamentName, $phpDataObject);
    }
}


// Store new staff data into database
function storeStaffData($staffData, $staffRole, $tournamentName, $phpDataObject)
{
    // Construct the flag URL using the country code as in lowercase letter
    $countryCode = strtolower($staffData->country_code);
    $countryFlagUrl = "https://flagcdn.com/24x18/$countryCode.webp";

    // Call the correct tournament database table based on the GET request
    $tournamentTable = "{$tournamentName}_staff";

    $query = "INSERT INTO $tournamentTable (staff_id,
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
function checkStaffData($staffId, $tournamentName, $staffRole, $phpDataObject)
{
    // Call the correct tournament database table based on the GET request
    $tournamentTable = "{$tournamentName}_staff";

    $query = "SELECT id FROM $tournamentTable WHERE staff_id = :staff_id AND staff_roles = :staff_roles";
    $queryStatement = $phpDataObject->prepare($query);
    $queryStatement->bindParam(":staff_id", $staffId, PDO::PARAM_INT);
    $queryStatement->bindParam(":staff_roles", $staffRole, PDO::PARAM_STR);
    $queryStatement->execute();

    return $queryStatement->fetchColumn() !== false;
}


// Update existing staff data in the database with new data
function updateStaffData($staffData, $staffRole, $tournamentName, $phpDataObject)
{

    // Construct the flag URL using the country code as in lowercase letter
    $countryCode = strtolower($staffData->country_code);
    $countryFlagUrl = "https://flagcdn.com/24x18/$countryCode.webp";

    // Call the correct tournament database table based on the GET request
    $tournamentTable = "{$tournamentName}_staff";

    $query = "UPDATE $tournamentTable
              SET staff_username = :staff_username,
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
function getStaffData($staffId, $tournamentName, $phpDataObject)
{
    // Call the correct tournament database table based on the GET request
    $tournamentTable = "{$tournamentName}_staff";

    $query = "SELECT * FROM $tournamentTable WHERE staff_id = :staff_id";
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
// Retrieve tournament-related data from GET request
$tournamentName = $_GET['name'] ?? 'NULL';

$staffDatas = [];

// Check for correct GET request for tournament name
if ($tournamentName) {
    // Read staff IDs from JSON file
    $staffJsonData = json_decode(file_get_contents('../data/staff.json'), true);
    $staffJsonDatas = $staffJsonData[$tournamentName] ?? [];

    foreach ($staffJsonDatas as $staffData) {
        $staffId = $staffData['id'];
        $staffRoles = $staffData['roles'];

        // Fetch the staff data from the API or database
        $staffData = fetchStaffData($staffId, $tournamentName, $phpDataObject);

        // Check if the data fetching is successful
        if ($staffData) {
            // Check if the user is authenticated
            $accessToken = $_COOKIE['vot_access_token'] ?? null;

            if ($accessToken) {
                foreach ($staffRoles as $staffRole) {
                    // Check if the staff data already exists in the database
                    if (!checkStaffData($staffData->id, $tournamentName, $staffRole, $phpDataObject)) {
                        // If not, store the staff data in the database
                        storeStaffData($staffData, $staffRole, $tournamentName, $phpDataObject);
                    } else {
                        // If yes, update the existing staff data in the database
                        updateStaffData($staffData, $staffRole, $tournamentName, $phpDataObject);
                        break;
                    }
                }
                // Get the staff data from the database
                $retrievedStaffData = getStaffData($staffId, $tournamentName, $phpDataObject);
            } else {
                // Get the staff data directly from the database without additional checking
                $retrievedStaffData = getStaffData($staffId, $tournamentName, $phpDataObject);
            }

            // If data retrieval is successful, add it to the array
            if ($retrievedStaffData) {
                $staffDatas[] = $retrievedStaffData;
            } else {
                error_log("Failed to retrieve staff data for ID: {$staffId}.");
            }
        } else {
            error_log("Failed to fetch staff data for ID: {$staffId}.");
            break;
        }
    }
} else {
    die('<div style="display: flex; justify-content: center; align-items: center; margin-left: 45rem; font-size: 5rem;">Please choose a valid button!</div>'); // TODO: This is an absolute temporary. Changed later (if I can).
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
        <?php if (!empty($staffDatas)): ?>
            <!-- Dynamic staff information display -->
            <?php foreach ($staffDatas as $staffData): ?>
                <div class="staff-card-container">
                    <h1><img src="<?= htmlspecialchars($staffData['staff_country_flag_url']); ?>" alt="<?= htmlspecialchars($staffData['staff_country_name']); ?>"><?= htmlspecialchars($staffData['staff_username']); ?></h1>
                    <img src="<?= htmlspecialchars($staffData['staff_avatar_url']); ?>" alt="<?= htmlspecialchars($staffData['staff_username']); ?>'s Avatar">
                    <h2><?= htmlspecialchars($staffData['staff_roles']) ?></h2>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>
