<?php

/* Empty cookies' value and old timestamp. See here:
 *      https://stackoverflow.com/questions/686155/how-to-remove-a-cookie-in-php
 */
if (isset($_COOKIE['vot_access_token'])) {
    unset($_COOKIE['vot_access_token']);

    setcookie('vot_access_token', '', 1, '/');

    // Direct to the main page.
    exit(header('Location: /user/Home.php', true, 302));
}
