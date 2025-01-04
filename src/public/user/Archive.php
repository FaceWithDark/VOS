<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require_once '../../private/controller/VotController.php';
?>

<div class="archived-page">
    <header>
        <h1>Archived Tournament</h1>
    </header>

    <section>
        <form action="Mappool.php" method="get">
            <div class="flex-container">

                <div class="direct-link-container">
                    <!-- <a href="https://docs.google.com/spreadsheets/d/1NTKjjdmax-qjO8L9-V8Iuom8zjcCaz9X-DHFJ4SDSAg/edit#gid=0"> // TODO: Re-create this as the spreadsheet owner somehow delete this. -->
                    <!-- <button type="submit" name="name" value="vot1">Vietnam osu!taiko Tournament 1</button> -->
                    <a href="#">Vietnam osu!taiko Tournament 1</a>
                </div>

                <div class="direct-link-container">
                    <!-- <a href="https://docs.google.com/spreadsheets/d/1LJg5ITi8tqUer-C2dKb1xE-MYQSELpi_-_Ur2UV_las/edit#gid=0"> // TODO: Re-create this as the spreadsheet owner somehow delete this. -->
                    <!-- <button type="submit" name="name" value="vot2">Vietnam osu!taiko Tournament 2</button> -->
                    <a href="#">Vietnam osu!taiko Tournament 2</a>
                </div>

                <div class="direct-link-container">
                    <!-- <a href="https://docs.google.com/spreadsheets/d/17O6J2lPWZWVvozOhT3OiumRrmFn_9wRlLQrwOHP2B-k/edit#gid=0"> -->
                    <button type="submit" name="name" value="vot3">Vietnam osu!taiko Tournament 3</button>
                </div>

                <div class="direct-link-container">
                    <!-- <a href="https://docs.google.com/spreadsheets/d/1H9N4hm1gcnipTtzKNtGpana7mJ2Gh4EbTnq9B19pCtU/edit?gid=1461400646#gid=1461400646"> -->
                    <button type="submit" name="name" value="vot4">Vietnam osu!taiko Tournament 4</button>
                </div>
            </div>
        </form>
    </section>
</div>
