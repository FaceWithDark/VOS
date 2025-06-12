<header>
    <h1>VOT4</h1>
</header>

<section class="vot4-page">
    <form action="/vot4" method="get">
        <div class="vot-right-button-container">
            <button type="submit" name="round" value="qlf">Qualifiers</button>
            <button type="submit" name="round" value="ro16">Round Of 16</button>
            <button type="submit" name="round" value="qf">Quarter Finals</button>
            <button type="submit" name="round" value="sf">Semi Finals</button>
            <button type="submit" name="round" value="fnl">Finals</button>
            <button type="submit" name="round" value="gf">Grand Finals</button>
        </div>

        <?php require __DIR__ . '/Vot4MappoolView.php'; ?>
    </form>
</section>
