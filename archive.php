<?php
    include 'navigation-bar.html';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VOT</title>

    <!-- Stylish ""title" class inside HTML file using CSS inline -->
    <style>
        body { 
            font-family: "Montserrat", sans-serif;
            background-color: #F3EEEA; 
        }

        #title {
            font-size: 30px;
            font-weight: 600;
            text-align: center;
            color: #7C9D96;
            margin: 2rem;
        }

        table {
            border-spacing: 2vh 10px; /* Set spacing for both horizontial and veritcal value respectively. */
            border-collapse: separate;            
        }

        th, td {
            border: 5px solid darkgray; /* 5px is for test debug purpose. 2px would be better. */
            border-radius: 15px;
            align-items: center;
            padding: 1rem;
            background-color: lightcoral;
        }

        th a {
            color: lightgreen;
        }
    </style>
</head>
    <body>
        <form action="archive.php" method="post">
            <!-- Webpage's main title name -->
            <div id="title">Archived Tournament</div>
            <!-- Create a table (Like table function in Excel, MS Word, etc). To create border line, use "border" attribute -->
            <table>
                <!-- Create table rows, to adjust the position then use "align" attribute -->
                <tr>
                    <!-- Create table headers (How many vertical boxes will be needed to be create in the table row tag). To direct VOT main spreadsheets, use <a> tag -->
                    <th><a href="https://docs.google.com/spreadsheets/d/1NTKjjdmax-qjO8L9-V8Iuom8zjcCaz9X-DHFJ4SDSAg/edit#gid=0">Vietnam osu!taiko Tournament 1</a></th>
                    <th><a href="https://docs.google.com/spreadsheets/d/1LJg5ITi8tqUer-C2dKb1xE-MYQSELpi_-_Ur2UV_las/edit#gid=0">Vietnam osu!taiko Tournament 2</a></th>
                    <th><a href="https://docs.google.com/spreadsheets/d/17O6J2lPWZWVvozOhT3OiumRrmFn_9wRlLQrwOHP2B-k/edit#gid=0">Vietnam osu!taiko Tournament 3</a></th>
                    <th><a href="#">Vietnam osu!taiko Tournament 4</a></th>
                </tr>
            </table>
        </form>
    </body>
</html>