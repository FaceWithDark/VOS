<?php

    use Dotenv\Dotenv;
    include './vendor/autoload.php';

    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv -> load();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VOT</title>

    <link rel="stylesheet" href="css/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
    <body>
        <nav>
            <div class="top-navigation-bar">
                <i class="bx bxs-navigation"><strong>Navigate</strong></i>
            </div>

            <i class="bx bx-menu" id="click-button"></i>

            <div class="users">
                <!-- 
                    TODO:
                    - Access this information from SQL using PHP.
                    - This will be hidden when user not logged in yet.
                    - Re-appear when logged in and hide the 'Login' button.
                    - Show 'Logout' button at the same time as showing this user info.
                -->
                <img src="#" alt="me" class="users-image">
                <p>Insert name here</p>
            </div>

            <ul>
                <li>
                    <!-- 
                        TODO: 
                        - OAuth Login Format here. 
                        - Disappear this part when logged in.
                        - 'Logout' button will be implementing after the 'Song' icon.
                    -->
                    <a href="https://osu.ppy.sh/oauth/authorize?client_id=<?= $_ENV['CLIENT_ID'] ?>&redirect_uri=http://localhost/VOT/oauth/token_callback.php&response_type=code&scope=public+identify&state=randomise">
                        <i class='bx bx-user-plus'></i>
                        <span class="navigation-links">Login</span>
                    </a>
                    <span class="small-navigation-links">Login</span>
                </li>

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

        <script src="js/activeButton.js" type="text/JavaScript"></script>
    </body>
</html>