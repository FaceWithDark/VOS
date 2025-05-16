<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VOT</title>

    <link rel="stylesheet" type="text/css" href="/assets/css/TagReset.css">

    <!-- Favicon images compatibility across mobile/desktop. -->
    <link rel="browser-web-icon" type="image/png" sizes="32x32" href="/assets/ico/favicon-32x32.png">
    <link rel="browser-web-icon" type="image/png" sizes="16x16" href="/assets/ico/favicon-16x16.png">
    <link rel="android-web-manifest" type="application/manifest+json" href="/assets/ico/site.webmanifest">
    <link rel="ios-web-icon" type="image/png" sizes="180x180" href="/assets/ico/apple-touch-icon.png">

    <style>
        h1 {
            display: flex;
            flex-direction: row;
            justify-content: center;
            font-weight: bold;
            font-size: 1.25rem;
            color: coral;
        }

        p {
            display: flex;
            flex-direction: row;
            justify-content: flex-start;
            padding: 0.5rem 0rem;
        }

        a {
            display: flex;
            flex-direction: row;
            justify-content: flex-end;
            font-weight: bold;
        }

        a:hover {
            color: darkcyan;
        }
    </style>
</head>

<body>
    <h1>Authorise granted, you may leave this page now!</h1>
    <p>Your access token: <?= $_COOKIE['vot_access_token']; ?></p>
    <a href="/home">Back to Home page</a>
</body>
