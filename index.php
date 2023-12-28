<?php
    include './navigation_bar.html';
    // Start the on-going session throughtout all other webpages (stored within' the server's cookie sessions //TODO: need to implement cookies).
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VOT</title>
</head>
    <!-- Stylish ""title" class inside HTML file using CSS inline -->
    <style>
        * {
            font-family: "Montserrat", sans-serif;
        }

        .container {        
            padding: 2rem;
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .cell {
            background-color: lightgreen;
            padding: 2rem;
            font-weight: bold;
        }
    </style>
    <!-- Set backhround color -->
    <body style="background-color: #367588;">
        <form action="index.php" method="post">
            <div class="container">
                <div class="cell">Welcome to VOT - the most enjoyable, interesting Vietnamese osu!taiko Tournament oyomyomyom !</div>
                <div class="cell">Box 2</div>
                <div class="cell">Box 3</div>
            </div>
        </form>
    </body>
</html>

<?php
    // End the current session.
    session_destroy();
?>