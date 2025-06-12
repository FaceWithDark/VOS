<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require __DIR__ . '/../../Configurations/Database.php';
require __DIR__ . '/../../Models/Mappool.php';
?>

<header>
    <h1>VOT3</h1>
</header>

<section class="vot3-page">
    <form action="/vot3" method="get">
        <div class="vot-right-button-container">
            <button type="submit" name="round" value="qualifiers">Qualifiers</button>
            <button type="submit" name="round" value="round_of_16">Round Of 16</button>
            <button type="submit" name="round" value="quarterfinals">Quarter Finals</button>
            <button type="submit" name="round" value="semifinals">Semi Finals</button>
            <button type="submit" name="round" value="finals">Finals</button>
            <button type="submit" name="round" value="grandfinals">Grand Finals</button>
        </div>

        <! TODO: Display data fetched from the database here ---->
    </form>
</section>
