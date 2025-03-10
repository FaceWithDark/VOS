<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

include __DIR__ . '/../utility/ReadEnvironmentFile.php';

// TODO: defined as constant variables in a global-used file and include back to here
$clientIdValue
    = getenv(name: 'CLIENT_ID', local_only: true)
    ?: getenv(name: 'CLIENT_ID');

$clientSecretValue
    = getenv(name: 'CLIENT_SECRET', local_only: true)
    ?: getenv(name: 'CLIENT_SECRET');

$clientAuthorisationCodeValue
    = $_GET['code'];

$clintAuthorisationGrantValue
    = 'authorization_code';

$clientRedirectValue
    = getenv(name: 'CALLBACK_URL', local_only: true)
    ?: getenv(name: 'CALLBACK_URL');


function exchangeCode(array $payloadData, string $apiURL)
{

    $client = new Client();

    try {
        $response = $client->post($apiURL, [
            'form_params' => $payloadData,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/x-www-form-urlencoded',
            ]
        ]);

        if ($response->getStatusCode() == 200) {
            return json_decode($response->getBody()->getContents());
        }
        return false;
    } catch (RequestException $exception) {
        // Log the exception message for debugging
        error_log($exception->getMessage());
        return false;
    }
}

// Check for errors in the query parameters
if (isset($_GET['error']) || !isset($_GET['code'])) {
    exit('Some error occurred');
}

// Prepare payload data for token exchange
$payloadData = [
    'client_id'     => $clientIdValue,
    'client_secret' => $clientSecretValue,
    'code'          => $clientAuthorisationCodeValue,
    'grant_type'    => $clintAuthorisationGrantValue,
    'redirect_uri'  => $clientRedirectValue
];

$apiURL = "https://osu.ppy.sh/oauth/token";

// Exchange the authorization code for an access token
$tokenData = exchangeCode($payloadData, $apiURL);

if ($tokenData !== false) {
    // echo ('Token received');
    // Set the access token as an HTTP-only cookie
    if (!empty($tokenData->access_token)) {
        setcookie('vot_access_token', $tokenData->access_token, time() + 86400, '/', '', false, true);
        exit(header('Location: /user/Home.php', true, 302));
    }
} else {
    exit('Error getting token');
}

if (!empty($tokenData->error)) {
    exit($tokenData->error);
}
