<?php
    /*
        Here, we want to sent anather request to fetch the user's profile details
        name, avatar image.
     */ 

    use GuzzleHttp\Client;
    use GuzzleHttp\Exception\RequestException;
    
    use Dotenv\Dotenv;

    require_once __DIR__ . '/../vendor/autoload.php';

    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv -> load();

    // Get the user's details

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

    // var_dump($user);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VOT</title>

    <link rel="stylesheet" href="../css/reset.css">
    <link rel="stylesheet" href="../css/main.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
    <body>
        <nav>
            <div class="top-navigation-bar">
                <i class="bx bxs-navigation"><strong>Navigate</strong></i>
            </div>

            <i class="bx bx-menu" id="click-button"></i>

            <?php if(!empty($user)): ?>
                <div class="users">
                    <!-- TODO: Access this information from SQL using PHP (maybe not). -->
                    <img src="<?= htmlspecialchars($user -> avatar_url); ?>" alt="<?= htmlspecialchars($user -> username); ?>" class="users-image">
                    <p><?= htmlspecialchars($user -> username); ?></p>
                </div>
            
                <ul>
                    <li>
                        <a href="../pages/index.php">
                            <i class="bx bxs-grid-alt"></i>
                            <span class="navigation-links">Home</span>
                        </a>
                        <span class="small-navigation-links">Home</span>
                    </li>

                    <li>
                        <a href="../pages/archive.php">
                            <i class="bx bxs-box"></i>
                            <span class="navigation-links">Archive</span>
                        </a>
                        <span class="small-navigation-links">Archive</span>
                    </li>

                    <li>
                        <a href="../pages/staff.php">
                            <i class="bx bxs-phone"></i>
                            <span class="navigation-links">Staff</span>
                        </a>
                        <span class="small-navigation-links">Staff</span>
                    </li>

                    <li>
                        <a href="../pages/song.php">
                            <i class="bx bxs-music"></i>
                            <span class="navigation-links">Song</span>
                        </a>
                        <span class="small-navigation-links">Song</span>
                    </li>
                    
                    <li>
                        <a href="../pages/logout.php">
                            <i class='bx bx-user-minus'></i>
                            <span class="navigation-links">Logout</span>
                        </a>
                        <span class="small-navigation-links">Logout</span>
                    </li>
                </ul>
                
            <?php else: ?>
                <div class="failed-users">
                    <img src="../img/Authentication failed.gif" alt="Sus?" class="authentication-failed-image">
                    <p>Sussy Baka</p>
                </div>

                <ul>
                    <li>
                        <a href="https://osu.ppy.sh/oauth/authorize?client_id=<?= $_ENV['CLIENT_ID'] ?>&redirect_uri=http://localhost/VOT/oauth/token_callback.php&response_type=code&scope=public+identify&state=randomise">
                            <i class='bx bx-user-plus'></i>
                            <span class="navigation-links">Login</span>
                        </a>
                        <span class="small-navigation-links">Login</span>
                    </li>

                    <li>
                        <a href="../pages/index.php">
                            <i class="bx bxs-grid-alt"></i>
                            <span class="navigation-links">Home</span>
                        </a>
                        <span class="small-navigation-links">Home</span>
                    </li>

                    <li>
                        <a href="../pages/archive.php">
                            <i class="bx bxs-box"></i>
                            <span class="navigation-links">Archive</span>
                        </a>
                        <span class="small-navigation-links">Archive</span>
                    </li>

                    <li>
                        <a href="../pages/staff.php">
                            <i class="bx bxs-phone"></i>
                            <span class="navigation-links">Staff</span>
                        </a>
                        <span class="small-navigation-links">Staff</span>
                    </li>

                    <li>
                        <a href="../pages/song.php">
                            <i class="bx bxs-music"></i>
                            <span class="navigation-links">Song</span>
                        </a>
                        <span class="small-navigation-links">Song</span>
                    </li>
                </ul>
            <?php endif; ?>
        </nav>

        <script src="../js/activeButton.js" type="text/JavaScript"></script>
    </body>
</html>