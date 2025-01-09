<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

// START: strictly needed code lines to enabling Composer package usages
require_once __DIR__ . '/../../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
// END: strictly needed code lines to enabling Composer package usages

$userTable = "VotUser";
$userFlagSize = "24x18";
$userFlagFormat = "webp";


// Fetch user data from the Osu! API
function fetchUserData(int $userId, string $userTable, object $phpDataObject): object | bool | array
{
    // Check if the user is authenticated by looking for the access token in cookies
    $accessToken = $_COOKIE['vot_access_token'] ?? null;
    if ($accessToken) {
        // If authenticated, construct the API URL for fetching user data
        $apiUrl = "https://osu.ppy.sh/api/v2/users/{$userId}/taiko";
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

            // Return the user data if it is a 200 status 
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
        $fetchUserData = getUserData($userId, $userTable, $phpDataObject);
        return $fetchUserData;
    }
}


// Store new staff data into database
function storeUserData(
    object $userData,
    string $userFlagSize,
    string $userFlagFormat,
    string $userTable,
    string $userRole,
    object $phpDataObject
): bool {
    // Construct the flag URL using the country code as in lowercase letter
    $userCountryCode = strtolower($userData->country_code);
    $userCountryFlag = "https://flagcdn.com/$userFlagSize/$userCountryCode.$userFlagFormat";

    $query = "INSERT INTO $userTable (userId,
                                      userName,
                                      userImage,
                                      userCountryName,
                                      userCountryFlag,
                                      userRole)
              VALUES (:userId,
                      :userName,
                      :userImage,
                      :userCountryName,
                      :userCountryFlag,
                      :userRole)";

    // Prepare the SQL statement to prevent SQL injection
    $queryStatement = $phpDataObject->prepare($query);

    // Bind the staff data to the prepared statement
    $queryStatement->bindParam(":userId",          $userData->id,            PDO::PARAM_INT);
    $queryStatement->bindParam(":userName",        $userData->username,      PDO::PARAM_STR, 100);
    $queryStatement->bindParam(":userImage",       $userData->avatar_url,    PDO::PARAM_STR, 255);
    $queryStatement->bindParam(":userCountryName", $userData->country->name, PDO::PARAM_STR, 100);
    $queryStatement->bindParam(":userCountryFlag", $userCountryFlag,         PDO::PARAM_STR, 255);
    $queryStatement->bindParam(":userRole",        $userRole,                PDO::PARAM_STR, 10);

    // Execute the statement and insert the data into database
    if ($queryStatement->execute()) {
        error_log("Insert successful for user ID: " . $userData->id);
        return $queryStatement->rowCount() > 0;
    } else {
        error_log("Insert failed for user ID: " . $userData->id);
        $errorInfo = $queryStatement->errorInfo();
        error_log("Error Info: " . implode(", ", $errorInfo));
        return false;
    }
}


// Check if user data already exists in the database
function checkUserData(string $userId, string $userTable, object $phpDataObject): bool
{
    $query = "SELECT userId FROM $userTable WHERE userId = :userId";
    $queryStatement = $phpDataObject->prepare($query);
    $queryStatement->bindParam(":userId", $userId, PDO::PARAM_INT);
    $queryStatement->execute();

    return $queryStatement->fetchColumn() !== false;
}


// Update existing user data in the database with new data
// TODO: Switch this to VIEW query so I don't accidentally damage the stored data
function updateUserData(
    object $userData,
    string $userFlagSize,
    string $userFlagFormat,
    string $userTable,
    string $userRole,
    object $phpDataObject
): bool {
    // Construct the flag URL using the country code as in lowercase letter
    $userCountryCode = strtolower($userData->country_code);
    $userCountryFlag = "https://flagcdn.com/$userFlagSize/$userCountryCode.$userFlagFormat";

    $query = "UPDATE $userTable
              SET userName        = :userName,
                  userImage       = :userImage,
                  userRole        = :userRole,
                  userCountryName = :userCountryName,
                  userCountryFlag = :userCountryFlag
              WHERE userId = :userId";

    // Prepare the SQL statement to prevent SQL injection
    $queryStatement = $phpDataObject->prepare($query);


    // Bind the staff data to the prepared statement
    $queryStatement->bindParam(":userId",          $userData->id,            PDO::PARAM_INT);
    $queryStatement->bindParam(":userName",        $userData->username,      PDO::PARAM_STR, 100);
    $queryStatement->bindParam(":userImage",       $userData->avatar_url,    PDO::PARAM_STR, 255);
    $queryStatement->bindParam(":userCountryName", $userData->country->name, PDO::PARAM_STR, 100);
    $queryStatement->bindParam(":userCountryFlag", $userCountryFlag,         PDO::PARAM_STR, 255);
    $queryStatement->bindParam(":userRole",        $userRole,                PDO::PARAM_STR, 10);

    // Execute the statement and update the existen data in the database
    if ($queryStatement->execute()) {
        error_log("Update successful for user ID: " . $userData->id);
        return $queryStatement->rowCount() > 0;
    } else {
        error_log("Update failed for user ID: " . $userData->id);
        $errorInfo = $queryStatement->errorInfo();
        error_log("Error Info: " . implode(", ", $errorInfo));
        return false;
    }
}


// Get user data from database by user IDs
function getUserData(int $userId, string $userTable, object $phpDataObject): array
{
    $query = "SELECT * FROM $userTable WHERE userId = :userId";
    $queryStatement = $phpDataObject->prepare($query);
    $queryStatement->bindParam(":userId", $userId, PDO::PARAM_INT);

    // Execute the statement and get the needed data in the database to display
    if ($queryStatement->execute()) {
        error_log("Get data successfully for user ID: " . $userId);
        // Fetch and return the result as an associative array
        return $queryStatement->fetch(PDO::FETCH_ASSOC);
    } else {
        error_log("Get data failed for user ID: " . $userId);
        $errorInfo = $queryStatement->errorInfo();
        error_log("Error Info: " . implode(", ", $errorInfo));
        return false;
    }
}

/*
 * You can get around things this way but don't do it, it looks bad:
 *
 *      $userJsonData = json_decode(file_get_contents(__DIR__ . '/../../resource/User.json'));
 *
 *      $userId = $userJsonData->id;
 *      echo $userId;
 *      $userRole = $userJsonData->role;
 *      echo $userRole;
 */

// Read user IDs from JSON file
$userJsonData = json_decode(file_get_contents(__DIR__ . '/../../resource/User.json'), true);

$userId = $userJsonData['id'];
$userRole = $userJsonData['role'];

$userData = fetchUserData($userId, $userTable, $phpDataObject);

if ($userData == true) {
    // Make sure user's cookie is there for use
    $userAccessToken = $_COOKIE['vot_access_token'] ?? null;

    if ($userAccessToken == true) {
        (!checkUserData($userId, $userTable, $phpDataObject))
            ? storeUserData($userData, $userFlagSize, $userFlagFormat, $userTable, $userRole, $phpDataObject)
            : updateUserData($userData, $userFlagSize, $userFlagFormat, $userTable, $userRole, $phpDataObject);

        // Fetch newly stored user data from the database (a.k.a live data)
        $fetchUserData = getUserData($userId, $userTable, $phpDataObject);
    } else {
        // Fetch previously stored user data from the database (a.k.a local data)
        $fetchUserData = getUserData($userId, $userTable, $phpDataObject);
        //echo $fetchUserData['userId'];
    }
} else {
    error_log("Fetch data failed for user ID: " . $userId);
    exit("Error 404: Data Not Found");
}
