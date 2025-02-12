<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

// Sent another request to fetch the user's profile details incl name, avatar, etc. 
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

require __DIR__ . '/../config/Configuration.php';

// Fetch user data from the Osu! API
function getUserDetail()
{
    $accessToken = $_COOKIE['vot_access_token'] ?? null;
    if (!$accessToken) {
        // Access token is not available
        return false;
    }

    $apiUrl = "https://osu.ppy.sh/api/v2/me/osu";
    $client = new Client();

    try {
        $response = $client->get($apiUrl, [
            'headers' => [
                'Authorization' => "Bearer {$accessToken}",
                'Accept' => 'application/json',
            ]
        ]);

        if ($response->getStatusCode() === 200) {
            return json_decode($response->getBody()->getContents());
        }
        // API call did not return a 200 status
        return false;
    } catch (RequestException $exception) {
        error_log($exception->getMessage()); // Log the exception message
        return false;                        // An exception occurred during the API call
    }
}

// Get user data from the API call
$userData = getUserDetail();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VOT</title>

    <link rel="stylesheet" type="text/css" href="/../assets/css/reset.css">
    <link rel="stylesheet" type="text/css" href="/../assets/css/main.css">
    <link rel='stylesheet' type="text/css" href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css'>

    <!-- Favicon images compatibility across mobile/desktop. -->
    <link rel="browser-web-icon" type="image/png" sizes="32x32" href="/../assets/ico/favicon-32x32.png">
    <link rel="browser-web-icon" type="image/png" sizes="16x16" href="/../assets/ico/favicon-16x16.png">
    <link rel="android-web-manifest" type="application/manifest+json" href="/../assets/ico/site.webmanifest">
    <link rel="ios-web-icon" type="image/png" sizes="180x180" href="/../assets/ico/apple-touch-icon.png">
</head>

<body>
    <nav>
        <div class="top-navigation-section">
            <strong><i class="bx bxs-navigation">VOT</i></strong>
            <i class="bx bx-menu" id="click-button"></i>
        </div>

        <?php if ($userData): ?>
            <div class="middle-navigation-first-section">
                <!-- TODO: Access this information from SQL using PHP (maybe not). -->
                <img src="<?= htmlspecialchars($userData->avatar_url); ?>" alt="<?= htmlspecialchars($userData->username); ?>" class="user-image">
                <p><?= htmlspecialchars($userData->username); ?></p>
            </div>

            <div class="middle-navigation-second-section">
                <ul>
                    <li>
                        <a href="/../user/Home.php">
                            <i class="bx bxs-grid-alt"></i>
                            <p>Home</p>
                        </a>
                    </li>

                    <li>
                        <a href="/../user/Archive.php">
                            <i class="bx bxs-box"></i>
                            <p>Archive<p>
                        </a>
                    </li>

                    <li>
                        <a href="/../user/Staff.php">
                            <i class="bx bxs-phone"></i>
                            <p>Staff</p>
                        </a>
                    </li>

                    <li>
                        <a href="/../user/Song.php">
                            <i class="bx bxs-music"></i>
                            <p>Song</p>
                        </a>
                    </li>

                    <li>
                        <a href="/../admin/LogOutInterface.php">
                            <i class='bx bx-user-minus'></i>
                            <p>Logout</p>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="bottom-navigation-section">
                <ul>
                    <li>
                        <i class='bx bxs-moon' id="dark-mode"></i>
                        <i class='bx bxs-sun' id="light-mode"></i>
                    </li>
                </ul>
            </div>

        <?php else: ?>
            <div class="middle-navigation-first-section">
                <img src="/../assets/img/Authentication Failed.webp" alt="Sus?" class="authentication-failed-image">
                <p>Sussy Baka</p>
            </div>

            <div class="middle-navigation-second-section">
                <ul>
                    <li>
                        <a href="https://osu.ppy.sh/oauth/authorize?client_id=<?= $_ENV['CLIENT_ID']; ?>&redirect_uri=<?= $_ENV['CALLBACK_URL']; ?>&response_type=code&scope=public+identify&state=randomise">
                            <i class='bx bx-user-plus'></i>
                            <p>Login</p>
                        </a>
                    </li>

                    <li>
                        <a href="/../user/Home.php">
                            <i class="bx bxs-grid-alt"></i>
                            <p>Home</p>
                        </a>
                    </li>

                    <li>
                        <a href="/../user/Archive.php">
                            <i class="bx bxs-box"></i>
                            <p>Archive</p>
                        </a>
                    </li>

                    <li>
                        <a href="/../user/Staff.php">
                            <i class="bx bxs-phone"></i>
                            <p>Staff</p>
                        </a>
                    </li>

                    <li>
                        <a href="/../user/Song.php">
                            <i class="bx bxs-music"></i>
                            <p>Song</p>
                        </a>
                    </li>
                </ul>
            </div>


            <div class="bottom-navigation-section">
                <ul>
                    <li>
                        <i class='bx bxs-moon' id="dark-mode"></i>
                        <i class='bx bxs-sun' id="light-mode"></i>
                    </li>
                </ul>
            </div>

        <?php endif; ?>
    </nav>

    <script src="/../assets/js/activeButton.js" type="text/javascript"></script>
</body>

</html>
