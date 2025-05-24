<header>
    <h1>VOT3</h1>
</header>

<section class="vot3-page">
    <form action="/vot3" method="get">
        <div class="vot-right-button-container">
            <!-- Sent the corresponding tournament round fields to the website for fetching stored data -->
            <button type="submit" name="round" value="qualifiers">Qualifiers</button>
            <button type="submit" name="round" value="ro16">RO16</button>
            <button type="submit" name="round" value="quarterfinals">QF</button>
            <button type="submit" name="round" value="semifinals">SF</button>
            <button type="submit" name="round" value="finals">Finals</button>
            <button type="submit" name="round" value="grandfinals">GF</button>
        </div>
    </form>

    <!-- TODO: Pull mappool data and display it from here -->
    <?php require __DIR__ . '/Vot3MappoolView.php'; ?>
</section>
