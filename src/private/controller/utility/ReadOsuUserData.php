<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

// Sent another request to fetch the user's profile details incl name, avatar, etc. 
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;


// Fetch user data from the Osu! API
function getUserDetail(): object | bool
{
    if (!isset($_COOKIE['vot_access_token'])) {
        // Prevent manual cookies delete on the website
        $accessToken = null;

        return false;
    } else {
        $accessToken = $_COOKIE['vot_access_token'];

        $apiUrl = "https://osu.ppy.sh/api/v2/me/osu";
        $client = new Client();

        try {
            $response = $client->get($apiUrl, [
                'headers' => [
                    'Authorization' => "Bearer {$accessToken}",
                    'Accept' => 'application/json',
                ]
            ]);

            if ($response->getStatusCode() !== 200) {
                // API call did not return a 200 status
                return false;
            } else {
                $success_code = json_decode($response->getBody()->getContents());
                return $success_code;
            }
        } catch (RequestException $exception) {
            error_log($exception->getMessage()); // Log the exception message
            return false;                        // An exception occurred during the API call
        }
    }
}
