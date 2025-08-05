<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require __DIR__ . '/../../Models/UserData.php';
?>

<!-- XHTML 1.0 compatible -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<!-- XHTML 1.1 compatible -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

<!-- HTML 4.01 compatible -->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<!-- HTML 5 compatible -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />

    <!-- Mobile Browser application compatible -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />

    <!-- Safiri application compatible -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />

    <!-- IOS application compatible -->
    <meta name="apple-mobile-web-app-title" content="VOT" />

    <!-- Internet Explorer application compatible -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="MobileOptimized" content="width=240" />

    <!-- BlackBerryOS application compatible -->
    <meta name="HandheldFriendly" content="true" />

    <title>VOT</title>

    <!-- CSS files -->
    <link rel="stylesheet" type="text/css" href="/assets/css/TagReset.css" />
    <link rel="stylesheet" type="text/css" href="/assets/css/Global.css" />
    <link rel="stylesheet" type="text/css" href="/assets/css/Navigation.css" />
    <link rel="stylesheet" type="text/css" href="/assets/css/ThemeSwitch.css" />

    <!-- Icon file -->
    <link rel='stylesheet' type="text/css" href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' />

    <!-- JS file -->
    <script type="text/javascript" src="/assets/js/collapsibleNavigationBar.js"></script>
    <script type="text/javascript" src="/assets/js/themeSwitchButton.js"></script>

    <!-- Browser compatible -->
    <link rel="icon" type="image/png" sizes="96x96" href="/assets/ico/favicon-96x96.png" />
    <link rel="icon" type="image/svg+xml" href="/assets/ico/favicon.svg" />
    <link rel="shortcut icon" type="image/x-icon" href="/assets/ico/favicon.ico" />

    <!-- IOS compatible (App Icon) -->
    <link rel="apple-touch-icon" type="image/png" href="/assets/ico/apple-touch-icon.png" />
    <link rel="apple-touch-icon" type="image/png" sizes="57x57" href="/assets/ico/apple-touch-icon-57x57.png" />
    <link rel="apple-touch-icon" type="image/png" sizes="72x72" href="/assets/ico/apple-touch-icon-72x72.png" />
    <link rel="apple-touch-icon" type="image/png" sizes="76x76" href="/assets/ico/apple-touch-icon-76x76.png" />
    <link rel="apple-touch-icon" type="image/png" sizes="114x114" href="/assets/ico/apple-touch-icon-114x114.png" />
    <link rel="apple-touch-icon" type="image/png" sizes="120x120" href="/assets/ico/apple-touch-icon-120x120.png" />
    <link rel="apple-touch-icon" type="image/png" sizes="144x144" href="/assets/ico/apple-touch-icon-144x144.png" />
    <link rel="apple-touch-icon" type="image/png" sizes="152x152" href="/assets/ico/apple-touch-icon-152x152.png" />
    <link rel="apple-touch-icon" type="image/png" sizes="180x180" href="/assets/ico/apple-touch-icon-180x180.png" />

    <!-- IOS compatible (Splash Screen) -->
    <link rel="apple-touch-startup-image" type="image/png" media="screen and (device-width: 440px) and (device-height: 956px) and (-webkit-device-pixel-ratio: 3) and (orientation: landscape)" href="/assets/ico/splash_screens/iPhone_16_Pro_Max_landscape.png" />
    <link rel="apple-touch-startup-image" type="image/png" media="screen and (device-width: 402px) and (device-height: 874px) and (-webkit-device-pixel-ratio: 3) and (orientation: landscape)" href="/assets/ico/splash_screens/iPhone_16_Pro_landscape.png" />
    <link rel="apple-touch-startup-image" type="image/png" media="screen and (device-width: 430px) and (device-height: 932px) and (-webkit-device-pixel-ratio: 3) and (orientation: landscape)" href="/assets/ico/splash_screens/iPhone_16_Plus__iPhone_15_Pro_Max__iPhone_15_Plus__iPhone_14_Pro_Max_landscape.png" />
    <link rel="apple-touch-startup-image" type="image/png" media="screen and (device-width: 393px) and (device-height: 852px) and (-webkit-device-pixel-ratio: 3) and (orientation: landscape)" href="/assets/ico/splash_screens/iPhone_16__iPhone_15_Pro__iPhone_15__iPhone_14_Pro_landscape.png" />
    <link rel="apple-touch-startup-image" type="image/png" media="screen and (device-width: 428px) and (device-height: 926px) and (-webkit-device-pixel-ratio: 3) and (orientation: landscape)" href="/assets/ico/splash_screens/iPhone_14_Plus__iPhone_13_Pro_Max__iPhone_12_Pro_Max_landscape.png" />
    <link rel="apple-touch-startup-image" type="image/png" media="screen and (device-width: 390px) and (device-height: 844px) and (-webkit-device-pixel-ratio: 3) and (orientation: landscape)" href="/assets/ico/splash_screens/iPhone_14__iPhone_13_Pro__iPhone_13__iPhone_12_Pro__iPhone_12_landscape.png" />
    <link rel="apple-touch-startup-image" type="image/png" media="screen and (device-width: 375px) and (device-height: 812px) and (-webkit-device-pixel-ratio: 3) and (orientation: landscape)" href="/assets/ico/splash_screens/iPhone_13_mini__iPhone_12_mini__iPhone_11_Pro__iPhone_XS__iPhone_X_landscape.png" />
    <link rel="apple-touch-startup-image" type="image/png" media="screen and (device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 3) and (orientation: landscape)" href="/assets/ico/splash_screens/iPhone_11_Pro_Max__iPhone_XS_Max_landscape.png" />
    <link rel="apple-touch-startup-image" type="image/png" media="screen and (device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 2) and (orientation: landscape)" href="/assets/ico/splash_screens/iPhone_11__iPhone_XR_landscape.png" />
    <link rel="apple-touch-startup-image" type="image/png" media="screen and (device-width: 414px) and (device-height: 736px) and (-webkit-device-pixel-ratio: 3) and (orientation: landscape)" href="/assets/ico/splash_screens/iPhone_8_Plus__iPhone_7_Plus__iPhone_6s_Plus__iPhone_6_Plus_landscape.png" />
    <link rel="apple-touch-startup-image" type="image/png" media="screen and (device-width: 375px) and (device-height: 667px) and (-webkit-device-pixel-ratio: 2) and (orientation: landscape)" href="/assets/ico/splash_screens/iPhone_8__iPhone_7__iPhone_6s__iPhone_6__4.7__iPhone_SE_landscape.png" />
    <link rel="apple-touch-startup-image" type="image/png" media="screen and (device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2) and (orientation: landscape)" href="/assets/ico/splash_screens/4__iPhone_SE__iPod_touch_5th_generation_and_later_landscape.png" />
    <link rel="apple-touch-startup-image" type="image/png" media="screen and (device-width: 1032px) and (device-height: 1376px) and (-webkit-device-pixel-ratio: 2) and (orientation: landscape)" href="/assets/ico/splash_screens/13__iPad_Pro_M4_landscape.png" />
    <link rel="apple-touch-startup-image" type="image/png" media="screen and (device-width: 1024px) and (device-height: 1366px) and (-webkit-device-pixel-ratio: 2) and (orientation: landscape)" href="/assets/ico/splash_screens/12.9__iPad_Pro_landscape.png" />
    <link rel="apple-touch-startup-image" type="image/png" media="screen and (device-width: 834px) and (device-height: 1210px) and (-webkit-device-pixel-ratio: 2) and (orientation: landscape)" href="/assets/ico/splash_screens/11__iPad_Pro_M4_landscape.png" />
    <link rel="apple-touch-startup-image" type="image/png" media="screen and (device-width: 834px) and (device-height: 1194px) and (-webkit-device-pixel-ratio: 2) and (orientation: landscape)" href="/assets/ico/splash_screens/11__iPad_Pro__10.5__iPad_Pro_landscape.png" />
    <link rel="apple-touch-startup-image" type="image/png" media="screen and (device-width: 820px) and (device-height: 1180px) and (-webkit-device-pixel-ratio: 2) and (orientation: landscape)" href="/assets/ico/splash_screens/10.9__iPad_Air_landscape.png" />
    <link rel="apple-touch-startup-image" type="image/png" media="screen and (device-width: 834px) and (device-height: 1112px) and (-webkit-device-pixel-ratio: 2) and (orientation: landscape)" href="/assets/ico/splash_screens/10.5__iPad_Air_landscape.png" />
    <link rel="apple-touch-startup-image" type="image/png" media="screen and (device-width: 810px) and (device-height: 1080px) and (-webkit-device-pixel-ratio: 2) and (orientation: landscape)" href="/assets/ico/splash_screens/10.2__iPad_landscape.png" />
    <link rel="apple-touch-startup-image" type="image/png" media="screen and (device-width: 768px) and (device-height: 1024px) and (-webkit-device-pixel-ratio: 2) and (orientation: landscape)" href="/assets/ico/splash_screens/9.7__iPad_Pro__7.9__iPad_mini__9.7__iPad_Air__9.7__iPad_landscape.png" />
    <link rel="apple-touch-startup-image" type="image/png" media="screen and (device-width: 744px) and (device-height: 1133px) and (-webkit-device-pixel-ratio: 2) and (orientation: landscape)" href="/assets/ico/splash_screens/8.3__iPad_Mini_landscape.png" />
    <link rel="apple-touch-startup-image" type="image/png" media="screen and (device-width: 440px) and (device-height: 956px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)" href="/assets/ico/splash_screens/iPhone_16_Pro_Max_portrait.png" />
    <link rel="apple-touch-startup-image" type="image/png" media="screen and (device-width: 402px) and (device-height: 874px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)" href="/assets/ico/splash_screens/iPhone_16_Pro_portrait.png" />
    <link rel="apple-touch-startup-image" type="image/png" media="screen and (device-width: 430px) and (device-height: 932px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)" href="/assets/ico/splash_screens/iPhone_16_Plus__iPhone_15_Pro_Max__iPhone_15_Plus__iPhone_14_Pro_Max_portrait.png" />
    <link rel="apple-touch-startup-image" type="image/png" media="screen and (device-width: 393px) and (device-height: 852px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)" href="/assets/ico/splash_screens/iPhone_16__iPhone_15_Pro__iPhone_15__iPhone_14_Pro_portrait.png" />
    <link rel="apple-touch-startup-image" type="image/png" media="screen and (device-width: 428px) and (device-height: 926px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)" href="/assets/ico/splash_screens/iPhone_14_Plus__iPhone_13_Pro_Max__iPhone_12_Pro_Max_portrait.png" />
    <link rel="apple-touch-startup-image" type="image/png" media="screen and (device-width: 390px) and (device-height: 844px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)" href="/assets/ico/splash_screens/iPhone_14__iPhone_13_Pro__iPhone_13__iPhone_12_Pro__iPhone_12_portrait.png" />
    <link rel="apple-touch-startup-image" type="image/png" media="screen and (device-width: 375px) and (device-height: 812px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)" href="/assets/ico/splash_screens/iPhone_13_mini__iPhone_12_mini__iPhone_11_Pro__iPhone_XS__iPhone_X_portrait.png" />
    <link rel="apple-touch-startup-image" type="image/png" media="screen and (device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)" href="/assets/ico/splash_screens/iPhone_11_Pro_Max__iPhone_XS_Max_portrait.png" />
    <link rel="apple-touch-startup-image" type="image/png" media="screen and (device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)" href="/assets/ico/splash_screens/iPhone_11__iPhone_XR_portrait.png" />
    <link rel="apple-touch-startup-image" type="image/png" media="screen and (device-width: 414px) and (device-height: 736px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)" href="/assets/ico/splash_screens/iPhone_8_Plus__iPhone_7_Plus__iPhone_6s_Plus__iPhone_6_Plus_portrait.png" />
    <link rel="apple-touch-startup-image" type="image/png" media="screen and (device-width: 375px) and (device-height: 667px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)" href="/assets/ico/splash_screens/iPhone_8__iPhone_7__iPhone_6s__iPhone_6__4.7__iPhone_SE_portrait.png" />
    <link rel="apple-touch-startup-image" type="image/png" media="screen and (device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)" href="/assets/ico/splash_screens/4__iPhone_SE__iPod_touch_5th_generation_and_later_portrait.png" />
    <link rel="apple-touch-startup-image" type="image/png" media="screen and (device-width: 1032px) and (device-height: 1376px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)" href="/assets/ico/splash_screens/13__iPad_Pro_M4_portrait.png" />
    <link rel="apple-touch-startup-image" type="image/png" media="screen and (device-width: 1024px) and (device-height: 1366px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)" href="/assets/ico/splash_screens/12.9__iPad_Pro_portrait.png" />
    <link rel="apple-touch-startup-image" type="image/png" media="screen and (device-width: 834px) and (device-height: 1210px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)" href="/assets/ico/splash_screens/11__iPad_Pro_M4_portrait.png" />
    <link rel="apple-touch-startup-image" type="image/png" media="screen and (device-width: 834px) and (device-height: 1194px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)" href="/assets/ico/splash_screens/11__iPad_Pro__10.5__iPad_Pro_portrait.png" />
    <link rel="apple-touch-startup-image" type="image/png" media="screen and (device-width: 820px) and (device-height: 1180px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)" href="/assets/ico/splash_screens/10.9__iPad_Air_portrait.png" />
    <link rel="apple-touch-startup-image" type="image/png" media="screen and (device-width: 834px) and (device-height: 1112px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)" href="/assets/ico/splash_screens/10.5__iPad_Air_portrait.png" />
    <link rel="apple-touch-startup-image" type="image/png" media="screen and (device-width: 810px) and (device-height: 1080px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)" href="/assets/ico/splash_screens/10.2__iPad_portrait.png" />
    <link rel="apple-touch-startup-image" type="image/png" media="screen and (device-width: 768px) and (device-height: 1024px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)" href="/assets/ico/splash_screens/9.7__iPad_Pro__7.9__iPad_mini__9.7__iPad_Air__9.7__iPad_portrait.png" />
    <link rel="apple-touch-startup-image" type="image/png" media="screen and (device-width: 744px) and (device-height: 1133px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)" href="/assets/ico/splash_screens/8.3__iPad_Mini_portrait.png" />

    <!-- Android compatible -->
    <link rel="manifest" type="application/manifest+json" href="/assets/ico/site.webmanifest" />
</head>

<body>
    <nav class="collapsed">
        <div class="top-navigation-section">
            <i class="bx bx-menu" id="collapsible-icon"></i>
        </div>

        <?php
        $votUserDatabase = $GLOBALS['votDatabaseHandle'];

        $votUserData = readUserData(
            database_handle: $votUserDatabase
        );

        $votUserName    = htmlspecialchars($votUserData['userName']);
        $votUserImage   = htmlspecialchars($votUserData['userImage']);
        $votUserUrl     = htmlspecialchars($votUserData['userUrl']);

        $userDisplayTemplate =
            <<<EOL
            <div class="middle-navigation-first-section">
                <a href="$votUserUrl">
                    <img src="$votUserImage" alt="User Avatar" class="user-image">
                    <p>$votUserName</p>
                </a>
            </div>
            EOL;

        // It would be much more nasty if I tried to output this using the traditional mixed HTML & PHP codes
        echo $userDisplayTemplate;
        ?>

        <div class="middle-navigation-second-section">
            <ul>
                <li>
                    <a href="/logout">
                        <i class='bx bx-user-minus'></i>
                    </a>
                </li>

                <li>
                    <a href="/home">
                        <i class="bx bxs-grid-alt"></i>
                    </a>
                </li>

                <li>
                    <a href="/archive">
                        <i class="bx bxs-box"></i>
                    </a>
                </li>

                <li>
                    <a href="/staff">
                        <i class="bx bxs-phone"></i>
                    </a>
                </li>

                <li>
                    <a href="/song">
                        <i class="bx bxs-music"></i>
                    </a>
                </li>
            </ul>
        </div>

        <div class="bottom-navigation-section">
            <div class="vot-theme-tray">
                <i class='bx bxs-sun' id="light-theme"></i>
                <i class='bx bx-desktop' id="system-theme"></i>
                <i class='bx bxs-moon' id="dark-theme"></i>
            </div>
        </div>
    </nav>
</body>

</html>
