<?php

    /*
        Here, we want to sent anather request to fetch the user's profile details
        name, avatar image.
     */ 

    use GuzzleHttp\Client;
    use GuzzleHttp\Exception\RequestException;
    
    use Dotenv\Dotenv;

    include '../vendor/autoload.php';

    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv -> load();

    // Get the user's details

    function getUser() {
        if(empty($_COOKIE['vot_access_token'])) {
            return false;
        }

        $api_url = "https://osu.ppy.sh/api/v2/me/osu";

        $client = new Client();

        try {
            $response = $client -> get($api_url, [
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

    <link rel="stylesheet" href="../css/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
    <body>
        <nav>
            <div class="top-navigation-bar">
                <i class="bx bxs-navigation"><strong>Navigate</strong></i>
            </div>

            <i class="bx bx-menu" id="click-button"></i>

            <?php if(!empty($user)) : ?>
            <div class="users">
                <!-- 
                    TODO:
                    - Access this information from SQL using PHP.
                    - This will be hidden when user not logged in yet.
                    - Re-appear when logged in and hide the 'Login' button.
                    - Show 'Logout' button at the same time as showing this user info.
                -->
                <img src="<?= htmlspecialchars($user -> avatar_url); ?>" alt="<?= htmlspecialchars($user -> username); ?>" class="users-image">
                <p><?= htmlspecialchars($user -> username); ?></p>

                <?php else : ?>

                    <h1>Take the L.</h1>
                    <a href="home.php">Please Sign-in</a>

            </div>
            <?php endif ; ?>

            <ul>
                <li>
                    <a href="home.php">
                        <i class="bx bxs-grid-alt"></i>
                        <span class="navigation-links">Home</span>
                    </a>
                    <span class="small-navigation-links">Home</span>
                </li>
    
                <li>
                    <a href="archive.php">
                        <i class="bx bxs-box"></i>
                        <span class="navigation-links">Archive</span>
                    </a>
                    <span class="small-navigation-links">Archive</span>
                </li>
                
                <li>
                    <a href="staff.php">
                        <i class="bx bxs-phone"></i>
                        <span class="navigation-links">Staff</span>
                    </a>
                    <span class="small-navigation-links">Staff</span>
                </li>

                <li>
                    <a href="song.php">
                        <i class="bx bxs-music"></i>
                        <span class="navigation-links">Song</span>
                    </a>
                    <span class="small-navigation-links">Song</span>
                </li>
            </ul>
        </nav>

        <script src="../js/activeButton.js" type="text/JavaScript"></script>
    </body>
</html>