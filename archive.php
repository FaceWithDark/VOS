<?php
    include 'navigation_bar.html';
    // Start the on-going session throughtout all other webpages (stored within' the server's cookie sessions //TODO: need to implement cookies).
    session_start()
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VOT</title>
</head>
<!-- Set <body> tag background color -->
<body style="background-color: #4c516d">
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
        
        <!-- Create a table (Like table function in Excel, MS Word, etc).
             To create border line, use "border" attribute -->
        <table class="table">
            <!-- Create table rows, to adjust the position then use "align" attribute -->
            <tr>
                <!-- Create table headers (How many vertical boxes will be needed to be create in the table row tag )
                     To direct VOT main spreadsheets, use <a> tag -->
                <th width="350"><a href="https://docs.google.com/spreadsheets/d/1NTKjjdmax-qjO8L9-V8Iuom8zjcCaz9X-DHFJ4SDSAg/edit#gid=0">Vietnam osu!taiko Tournament 1</a></th>
                <th width="350"><a href="https://docs.google.com/spreadsheets/d/1LJg5ITi8tqUer-C2dKb1xE-MYQSELpi_-_Ur2UV_las/edit#gid=0">Vietnam osu!taiko Tournament 2</a></th>
                <th width="350"><a href="https://docs.google.com/spreadsheets/d/17O6J2lPWZWVvozOhT3OiumRrmFn_9wRlLQrwOHP2B-k/edit#gid=0">Vietnam osu!taiko Tournament 3</a></th>
                <th width="350"><a href="#">Vietnam osu!taiko Tournament 4</a></th>
            </tr>

            <!-- Create table rows, to adjust the position then use "align" attribute -->
            <tr align="center">
                <!-- Create table data (Un-bolded texts) -->
                <td>Winner: crazynt_ngu</td>
                <td>Winner: JackTVN</td>
                <td>Winner: nahieu2005</td>
                <td>Winner: N/A</td>
            </tr>
        </table>
        
        <style>
            .table {
                margin-left: auto;
                margin-right: auto;
                border: 2px solid;
                border-color: black;
            }

            tr {
                align-items: center;
                background-color: grey;
            }
        </style>
    </form>
</body>
</html>

<?php
    // End the current session.
    session_destroy();
?>