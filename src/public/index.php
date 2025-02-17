<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

ob_start();
?>

<!-- TODO: This is a very 'hacky' way of doing this. Proper handling later on -->
<?php if (isset($_COOKIE['vot_access_token'])): ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>VOT</title>

        <link rel="stylesheet" type="text/css" href="/../assets/css/reset.css">

        <!-- Favicon images compatibility across mobile/desktop. -->
        <link rel="browser-web-icon" type="image/png" sizes="32x32" href="/../assets/ico/favicon-32x32.png">
        <link rel="browser-web-icon" type="image/png" sizes="16x16" href="/../assets/ico/favicon-16x16.png">
        <link rel="android-web-manifest" type="application/manifest+json" href="/../assets/ico/site.webmanifest">
        <link rel="ios-web-icon" type="image/png" sizes="180x180" href="/../assets/ico/apple-touch-icon.png">
    </head>

    <body>
        <h1 style="display: flex; justify-content: center; align-items: center; color: coral;">Hello there, your website is working!</h1>
        <p style="display: flex; justify-content: end; align-items: end; font-weight: bold;"><a href="user/Home.php">Home Page</a></p>
    </body>
    <?php phpinfo(); ?>

<?php else: ?>
    <?php
    // Reference: https://www.php.net/manual/en/function.ob-end-flush.php
    while (ob_get_level() > 0) {
        exit(header('Location: /user/Home.php', true, 302));
        ob_end_flush();
    }
    ?>
<?php endif; ?>
