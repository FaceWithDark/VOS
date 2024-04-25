<?php
    require_once '../oauth/navigation_bar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VOT</title>
</head>
    <body>
        <div class="song-page">
            <header>
                <h1>Banger Song 🔥</h1>
            </header>

            <section>
                <!-- TODO: 
                        - Include song's image and other related info in.
                        - PHP can handle this. Need to get data from osu! API only.
                        - Direct link will be change to Soundclound link (if there is one).
                 -->                
                <div class="flex-container">
                    <div class="box-of-content">
                        <a href="https://osu.ppy.sh/beatmapsets/2032103#taiko/4235709">
                            ILFK (SectorJack & Edolmary)
                        </a>
                    </div>

                    <div class="box-of-content">
                        <a href="#">Song 2</a>
                    </div>

                    <div class="box-of-content">
                        <a href="#">Song 3</a>
                    </div>
            </section>
        </div>    
    </body>
</html>