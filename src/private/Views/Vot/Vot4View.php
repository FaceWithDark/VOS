<header>
    <h1>VOT4</h1>
</header>

<section class="vot4-page">
    <form action="/vot4" method="get">
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
    <?php require __DIR__ . '/Vot4MappoolView.php'; ?>
</section>
