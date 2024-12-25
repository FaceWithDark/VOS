<?php

    if (isset($_COOKIE['vot_access_token'])) {
        unset($_COOKIE['vot_access_token']);
        
        setcookie('vot_access_token', '', time() - 3600, '/'); // empty value and old timestamp

        // Direct to the main page.
        header("Location: ../../pages/index.php");
        // Terminate all functions.
        die();
    }
