<header>
    <h1>Banger Song &#128293</h1>
</header>

<section class="song-page">
    <form action="/song" method="get">
        <!-- TODO: Kill the page with HTTP status code immediately if no data found from the database (SQL injection, maybe). -->
        <div class="vot-center-button-container">
            <button type="submit" name="tournament" value="VOT">VOT</button>
            <button type="submit" name="tournament" value="VTC">VTC</button>
        </div>
    </form>
</section>
