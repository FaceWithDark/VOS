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
    <form action="archive.php" method="post">
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
        <!-- Create navigation class -->
        <nav>
            <!-- Create listing element where order of the items inside not matter -->
            <ul>
                <!-- Direct to VOT main spreadsheet -->
                <li><a href="https://docs.google.com/spreadsheets/d/1NTKjjdmax-qjO8L9-V8Iuom8zjcCaz9X-DHFJ4SDSAg/edit#gid=0">Vietnam osu!taiko Tournament 1 </a></li>
                <li><a href="https://docs.google.com/spreadsheets/d/1LJg5ITi8tqUer-C2dKb1xE-MYQSELpi_-_Ur2UV_las/edit#gid=0">Vietnam osu!taiko Tournament 2 </a></li>
                <li><a href="https://docs.google.com/spreadsheets/d/17O6J2lPWZWVvozOhT3OiumRrmFn_9wRlLQrwOHP2B-k/edit#gid=0">Vietnam osu!taiko Tournament 3 </a></li>
            </ul>
        </nav>
    </form>
</body>
</html>

<?php
    // End the current session.
    session_destroy();
?>