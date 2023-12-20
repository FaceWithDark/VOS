<?php
    // Start the on-going session throughtout all other webpages (stored within' the server's cookie sessions //TODO: need to implement cookies).
    session_start()
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VOT</title>

    <?php include('navigation_bar.html'); ?>
</head>
<body style="background-color: #008b8b;">
    <form action="archives.php" method="post">
        <!-- Webpage's main title name -->
        <div class="title">Archived Tournament</div>
        <!-- Stylish ""title" class inside HTML file using CSS inline -->
        <style>
            .title {
                font-family: "Montserrat", sans-serif;
                font-size: 30px;
                text-align: center;
                color: orange;
                margin: 15px;
            }
        </style>
    
        <!-- Direct to VOT main spreadsheet -->
        <a href="https://docs.google.com/spreadsheets/d/1NTKjjdmax-qjO8L9-V8Iuom8zjcCaz9X-DHFJ4SDSAg/edit#gid=0">Vietnam osu!taiko Tournament 1 </a><br>
        <a href="https://docs.google.com/spreadsheets/d/1LJg5ITi8tqUer-C2dKb1xE-MYQSELpi_-_Ur2UV_las/edit#gid=0">Vietnam osu!taiko Tournament 2 </a><br>
        <a href="https://docs.google.com/spreadsheets/d/17O6J2lPWZWVvozOhT3OiumRrmFn_9wRlLQrwOHP2B-k/edit#gid=0">Vietnam osu!taiko Tournament 3 </a><br>

        <!-- Return to home button -->
        <input type="submit" value="return" name="return">
    </form>
</body>
</html>

<?php

    // Check if the "return" button is clicked.
    if(isset($_POST["return"])) {
        // End the current session.
        session_destroy();
        // Direct to the set location.
        header("Location: index.php");
    }
?>