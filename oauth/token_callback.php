<?php
    use GuzzleHttp\Client;
    use GuzzleHttp\Exception\RequestException;
    use Dotenv\Dotenv;

    require_once __DIR__ . '/../vendor/autoload.php';

    function exchangeCode($payloadData, $apiURL) {
        $client = new Client();

        try {
            $respone = $client -> post($apiURL, [
                'form_params' => $payloadData, 
                'header' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/x-www-form-urlencoded', 
                ]
            ]);

            if($respone -> getStatusCode() == 200) {
                return json_decode($respone -> getBody() -> getContents());
            }
            return false;
        }
        catch(RequestException $exceptions) {
            return false;
        }
    }

    if(isset($_GET['error']) || !isset($_GET['code'])) {
        exit('Some error occurred');
    }

    $authorizationCode = $_GET['code'];

    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv -> load();

    // Debugging for token received from Osu!
    // var_dump($_GET);

    /**
     * Let's exchange the code for an access token.
     * For that, we need to send a request to GitHub.
     * PHP supports cURL by default, by it's verbose - so let's use Guzzle.
     */

    $payloadData = [
    'client_id' => $_ENV['CLIENT_ID'],
    'client_secret' => $_ENV['CLIENT_SECRET'],
    'code' => $authorizationCode,
    'grant_type' => 'authorization_code',
    'redirect_uri' => 'http://localhost:8080/oauth/token_callback.php',
    ];

    $apiURL = "https://osu.ppy.sh/oauth/token";

    $tokenData = exchangeCode($payloadData, $apiURL);

    if($tokenData == false) {
        exit('Error getting token');
    }

    if(!empty($tokenData -> error)) {
        exit($tokenData -> error);
    }
    
    // Debugging for exchange token received from Osu!
    // var_dump($tokenData);

    if(!empty($tokenData -> access_token)) {
        // The last arguement "true' - sets it as an HTTP-only cookie.
        setcookie('vot_access_token', $tokenData -> access_token, time() + 86400, "/", "", false, true);
        header('Location: ../pages/index.php');
        exit();
    }