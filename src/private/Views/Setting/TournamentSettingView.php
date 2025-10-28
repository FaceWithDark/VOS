<header>
    <h1>Tournament Setting</h1>
</header>

<section class="tournament-setting-page">
    <div class="tournament-add-mappool-panel">
        <h2>Add mappool data for specific round and tournament</h2>

        <form action="/setting/tournament" target="_self" method="post">
            <label for="beatmapId">Beatmap ID</label>
            <input type="text" name="beatmapId" placeholder="E.G: 2264801" autofocus required />
            <br />

            <label for="roundId">Round ID</label>
            <input type="text" name="roundId" placeholder="E.G: QLF" autofocus required />
            <br />

            <label for="tournamentId">Tournament ID</label>
            <input type="text" name="tournamentId" placeholder="E.G: VOT5" autofocus required />
            <br />

            <label for="beatmapType">Beatmap Type</label>
            <input type="text" name="beatmapType" placeholder="E.G: NM1" autofocus required />
            <br />

            <input type="submit" value="Add Mappool Data">
        </form>
    </div>

    <div class="tournament-view-data-panel">
        <h2>View Mappool Data</h2>
        <p>COMING SOON!!</p>
    </div>
</section>
