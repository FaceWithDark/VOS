<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require_once '../../private/controller/VotController.php';
require_once '../../private/model/crud/SongHandling.php';
?>

<header>
    <h1>Banger Song <i class='bx bxs-hot'></i></h1>
</header>

<section class="song-page">
    <?php if (!empty($customSongDataArray)): ?>
        <!-- Dynamic custom song information display -->
        <?php foreach ($customSongDataArray as $customSongData): ?>
            <div class="song-card-container">
                <h1>
                    <?= htmlspecialchars(isset($customSongData['tournament_title']) ? $customSongData['tournament_title'] : 'NULL'); ?> <?= htmlspecialchars(isset($customSongData['tournament_round']) ? $customSongData['tournament_round'] : 'NULL'); ?> - <?= htmlspecialchars($customSongData['mod_type']) ? $customSongData['mod_type'] : 'NULL'; ?>
                </h1>

                <a href="<?= htmlspecialchars(isset($customSongData['custom_song_url']) ? $customSongData['custom_song_url'] : '#'); ?>">
                    <img src="<?= htmlspecialchars(isset($customSongData['cover_image_url']) ? $customSongData['cover_image_url'] : 'NULL'); ?>" alt="Beatmap Cover">
                </a>

                <h2>
                    <?= htmlspecialchars(isset($customSongData['title_unicode']) ? $customSongData['title_unicode'] : 'NULL'); ?> [<?= htmlspecialchars(isset($customSongData['difficulty']) ? $customSongData['difficulty'] : 'NULL'); ?>]
                </h2>

                <h3>
                    <?= htmlspecialchars(isset($customSongData['artist_unicode']) ? $customSongData['artist_unicode'] : 'NULL'); ?>
                </h3>

                <h4>
                    Mapset by <a href="https://osu.ppy.sh/users/<?= htmlspecialchars(isset($customSongData['mapper']) ? $customSongData['mapper'] : 'NULL'); ?>"><?= htmlspecialchars(isset($customSongData['mapper']) ? $customSongData['mapper'] : 'NULL'); ?></a>
                </h4>

                <div class="beatmap-attribute-row">
                    <p>
                        <i class='bx bx-star'></i>
                        <?= htmlspecialchars(isset($customSongData['difficulty_rating']) ? number_format((float)$customSongData['difficulty_rating'], 2) : 'NULL'); ?>
                    </p>

                    <p>
                        <i class='bx bx-timer'></i>
                        <?= htmlspecialchars(isset($customSongData['total_length']) ? $customSongData['total_length'] : 'NULL'); ?>
                    </p>

                    <p>
                        <i class='bx bx-tachometer'></i>
                        <?= htmlspecialchars(isset($customSongData['map_bpm']) ? number_format((float)$customSongData['map_bpm'], 2) : 'NULL'); ?> BPM
                    </p>
                </div>

                <div class="beatmap-attribute-row">
                    <p>
                        OD: <?= htmlspecialchars(isset($customSongData['overall_difficulty']) ? number_format((float)$customSongData['overall_difficulty'], 2) : 'NULL'); ?>
                    </p>

                    <p>
                        HP: <?= htmlspecialchars(isset($customSongData['health_point']) ? number_format((float)$customSongData['health_point'], 2) : 'NULL'); ?>
                    </p>

                    <p>
                        Passed: <?= isset($customSongData['amount_of_passes']) ? $customSongData['amount_of_passes'] : 'NULL'; ?>
                    </p>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>
