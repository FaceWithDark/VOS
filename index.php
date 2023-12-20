<?php
    // Start the on-going session throughtout all other webpages (stored within' the server's cookie sessions //TODO: need to implement cookies).
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VOT</title>

    <?php include 'navigation_bar.html'; ?>
</head>
<!-- Set backhround color -->
<body style="background-color: #367588;">
    <form action="index.php" method="post">
        <!-- Website title name -->
        <div class="title">Welcome to VOT (Vietnamese Osu!Taiko Tournament) webisite !</div>
        <!-- Stylish ""title" class inside HTML file using CSS inline -->
        <style>
            .title {
                font-family: "Montserrat", sans-serif;
                font-size: 30px;
                text-align: center;
                height: calc(100vh - 60px);
                display: flex;
                flex-direction: column;
                justify-content: center;
                color: orange;
            }
        </style>        
    </form>
</body>
</html>

<?php
    // End the current session.
    session_destroy();
?>