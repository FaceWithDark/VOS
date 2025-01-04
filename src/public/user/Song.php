<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require_once '../../private/controller/VotController.php';
require_once '../../private/model/crud/SongHandling.php';
?>

<section>
    <div class="header-container">
        <h1>Banger Song <i class='bx bxs-hot'></i></h1>
    </div>

    <div class="song-page">
        <?php if (!empty($customSongDataArray)): ?>
            <!-- Dynamic custom song information display -->
            <?php foreach ($customSongDataArray as $customSongData): ?>
                <div class="song-card-container">
                    <h1><?= htmlspecialchars($customSongData['tournament_title']); ?> - <?= htmlspecialchars($customSongData['tournament_round']); ?> - <?= htmlspecialchars($customSongData['mod_type']); ?></h1>
                    <br>
                    <a href="<?= htmlspecialchars($customSongData['custom_song_url']); ?>"><img src="<?= htmlspecialchars($customSongData['cover_image_url']); ?>" width="490px" alt="Beatmap Cover"></a>
                    <br><br>
                    <h2><?= htmlspecialchars($customSongData['title_unicode']); ?> [<?= ($customSongData['difficulty']); ?>]</h2>
                    <h3><?= htmlspecialchars($customSongData['artist_unicode']); ?></h3>
                    <h4 class="beatmap-creator-row">
                        Mapset by <a href="https://osu.ppy.sh/users/<?= htmlspecialchars($customSongData['mapper']); ?>"><?= htmlspecialchars($customSongData['mapper']); ?></a>
                    </h4>
                    <br>
                    <div class="beatmap-attribute-row">
                        <p style="margin-right: 1rem;"><i class='bx bx-star'></i> <?= htmlspecialchars(number_format((float)$customSongData['difficulty_rating'], 2)); ?></p>
                        <p style="margin-right: 1rem;"><i class='bx bx-timer'></i> <?= htmlspecialchars($customSongData['total_length']); ?></p>
                        <p><i class='bx bx-tachometer'></i> <?= htmlspecialchars(number_format((float)$customSongData['map_bpm'], 2)); ?>bpm</p>
                    </div>
                    <br>
                    <div class="beatmap-attribute-row">
                        <p style="margin-right: 1rem;">OD: <?= htmlspecialchars(number_format((float)$customSongData['overall_difficulty'], 2)); ?></p>
                        <p style="margin-right: 1rem;">HP: <?= htmlspecialchars(number_format((float)$customSongData['health_point'], 2)); ?></p>
                        <p>Passed: <?= ($customSongData['amount_of_passes']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>
