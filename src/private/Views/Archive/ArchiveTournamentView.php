<header>
    <h1>Archived Tournament</h1>
</header>

<section class="archive-page">
    <form action="/mappool" method="get">
        <!-- TODO: Kill the page with HTTP status code immediately if no data found from the database (SQL injection, maybe). -->
        <div class="vot-center-button-container">
            <button type="submit" name="tournament" value="VOT">VOT</button>
            <button type="submit" name="name" value="VTC">VTC</button>
        </div>
    </form>
</section>
