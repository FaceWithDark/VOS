<?php
    use GuzzleHttp\Client;
    use GuzzleHttp\Exception\RequestException;
    use Dotenv\Dotenv;

    require_once __DIR__ . '/../../vendor/autoload.php';

    // Determine the environment
    $environment = getenv('ENV') ?: 'prod';

    // Load the appropriate .env file based on the environment
    $dotenv = Dotenv::createImmutable(__DIR__, ".env.$environment");
    $dotenv->load();

    function exchangeCode(array $payloadData, string $apiURL) {
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

    $authorizationCode = $_GET['code'];

    // Prepare payload data for token exchange
    $payloadData = [
        'client_id' => $_ENV['CLIENT_ID'],
        'client_secret' => $_ENV['CLIENT_SECRET'],
        'code' => $authorizationCode,
        'grant_type' => 'authorization_code',
        'redirect_uri' => $_ENV['CALLBACK_URL'],
    ];

    $apiURL = "https://osu.ppy.sh/oauth/token";

    // Exchange the authorization code for an access token
    $tokenData = exchangeCode($payloadData, $apiURL);

    if ($tokenData === false) {
        exit('Error getting token');
    }

    if (!empty($tokenData->error)) {
        exit($tokenData->error);
    }

    // Set the access token as an HTTP-only cookie
    if (!empty($tokenData->access_token)) {
        setcookie('vot_access_token', $tokenData->access_token, time() + 86400, "/", "", false, true);
        header('Location: ../../pages/index.php');
        exit();
    }
?>
