<?php
    // Sent another request to fetch the user's profile details incl name, avatar, etc. 
    use GuzzleHttp\Client;
    use GuzzleHttp\Exception\RequestException;

    require 'configuration.php';

    // Get user details.

    function getUser() {
        if(empty($_COOKIE['vot_access_token'])) {
            return false;
        }

        $apiUrl = "https://osu.ppy.sh/api/v2/me/osu";

        $client = new Client();

        try {
            $response = $client -> get($apiUrl, [
                'headers' => [
                    'authorization' => 'Bearer ' . $_COOKIE['vot_access_token'],
                    'Accept' => 'application/json',
                ]
            ]);
                
            if($response -> getStatusCode() == 200) {
                return json_decode($response -> getBody() -> getContents());
            }
            return false;
        }
        catch(RequestException $exceptions) {
            return false;
        }
    }

    $user = false;

    $user = getUser();

    // die('<pre>' . print_r($user, true) . '</pre>');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VOT</title>

    <link rel="stylesheet" type="text/css" href="../css/reset.css">
    <link rel="stylesheet" type="text/css" href="../css/main.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <!-- Favicon images compatibility across mobile/desktop. -->
    <link rel="apple-touch-icon" sizes="512x512" href="../android-chrome-512x512.png">
    <link rel="apple-touch-icon" sizes="192x192" href="../android-chrome-192x192.png">
    <link rel="apple-touch-icon" sizes="180x180" href="../apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../favicon-16x16.png">
    <link rel="manifest" href="../site.webmanifest">
</head>
    <body>
        <nav>
            <div class="top-navigation-bar">
                <strong><i class="bx bxs-navigation">VOT</i></strong>
                <i class="bx bx-menu" id="click-button"></i>
            </div>

            <?php if(!empty($user)): ?>
                <div class="user-info">
                    <!-- TODO: Access this information from SQL using PHP (maybe not). -->
                    <img src="<?= htmlspecialchars($user -> avatar_url); ?>" alt="<?= htmlspecialchars($user -> username); ?>" class="user-image">
                    <p><?= htmlspecialchars($user -> username); ?></p>
                </div>
            
                <ul>
                    <li>
                        <a href="../pages/index.php">
                            <i class="bx bxs-grid-alt"></i>
                            <p>Home</p>
                        </a>
                    </li>

                    <li>
                        <a href="../pages/archive.php">
                            <i class="bx bxs-box"></i>
                            <p>Archive<p>
                        </a>
                    </li>

                    <li>
                        <a href="../pages/staff.php">
                            <i class="bx bxs-phone"></i>
                            <p>Staff</p>
                        </a>
                    </li>

                    <li>
                        <a href="../pages/song.php">
                            <i class="bx bxs-music"></i>
                            <p>Song</p>
                        </a>
                    </li>
                    
                    <li>
                        <a href="../modules/user/logout.php">
                            <i class='bx bx-user-minus'></i>
                            <p>Logout</p>
                        </a>
                    </li>

                    <li>
                        <i class='bx bxs-moon' id="dark-mode"></i>
                        <i class='bx bxs-sun' id="light-mode"></i>
                    </li>
                </ul>
                
            <?php else: ?>
                <div class="failed-user-info">
                    <img src="../assets/images/FaliedAuthenticationImage.gif" alt="Sus?" class="authentication-failed-image">
                    <p>Sussy Baka</p>
                </div>

                <ul>
                    <li>
                        <a href="https://osu.ppy.sh/oauth/authorize?client_id=<?= $_ENV['CLIENT_ID'] ?>&redirect_uri=https://phpstack-1257657-4517689.cloudwaysapps.com/modules/authentication/token_callback.php&response_type=code&scope=public+identify&state=randomise">
                            <i class='bx bx-user-plus'></i>
                            <p>Login</p>
                        </a>
                    </li>

                    <li>
                        <a href="../pages/index.php">
                            <i class="bx bxs-grid-alt"></i>
                            <p>Home</p>
                        </a>
                    </li>

                    <li>
                        <a href="../pages/archive.php">
                            <i class="bx bxs-box"></i>
                            <p>Archive</p>
                        </a>
                    </li>

                    <li>
                        <a href="../pages/staff.php">
                            <i class="bx bxs-phone"></i>
                            <p>Staff</p>
                        </a>
                    </li>

                    <li>
                        <a href="../pages/song.php">
                            <i class="bx bxs-music"></i>
                            <p>Song</p>
                        </a>
                    </li>

                    <li>
                        <i class='bx bxs-moon' id="dark-mode"></i>
                        <i class='bx bxs-sun' id="light-mode"></i>
                    </li>
                </ul>
            <?php endif; ?>
        </nav>

        <script src="../js/activeButton.js" type="text/javascript"></script>
    </body>
</html>
