<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require_once '../../private/controller/VotController.php';
require_once '../../private/model/crud/MappoolHandling.php';
?>

<section>
    <div class="button-container">
        <form action="Mappool.php" method="get">
            <!-- This allows the URL to have more than 1 field showed at the same time -->
            <input type="hidden" name="name" value="<?= $tournamentName; ?>">
            <!-- Sent the corresponding tournament round fields to the website for fetching stored data -->
            <button type="submit" name="round" value="qualifiers">Qualifiers</button>
            <button type="submit" name="round" value="ro16">RO16</button>
            <button type="submit" name="round" value="quarterfinals">QF</button>
            <button type="submit" name="round" value="semifinals">SF</button>
            <button type="submit" name="round" value="finals">Finals</button>
            <button type="submit" name="round" value="grandfinals">GF</button>
        </form>
    </div>

    <div class="mappool-page">
        <!-- Check if there's beatmap data to show to end-user and any of the tournament rounds' button is clicked -->
        <?php if (!empty($beatmapDatas) && isset($tournamentRound)): ?>
            <!-- Dynamic beatmap display with correct mod type -->
            <?php foreach ($beatmapDatas as $beatmapData): ?>
                <div class="mappool-card-container">
                    <h1>
                        <?= htmlspecialchars(isset($beatmapData['mod_type']) ? $beatmapData['mod_type'] : 'NULL'); ?>
                    </h1>

                    <a href="<?= isset($beatmapData['map_url']) ? $beatmapData['map_url'] : '#'; ?>">
                        <img src="<?= htmlspecialchars(isset($beatmapData['cover_image_url']) ? $beatmapData['cover_image_url'] : 'NULL'); ?>" alt="Beatmap Cover">
                    </a>

                    <h2>
                        <?= htmlspecialchars(isset($beatmapData['title_unicode']) ? $beatmapData['title_unicode'] : 'NULL'); ?> [<?= htmlspecialchars(isset($beatmapData['difficulty']) ? $beatmapData['difficulty'] : 'NULL'); ?>]
                    </h2>

                    <h3>
                        <?= htmlspecialchars(isset($beatmapData['artist_unicode']) ? $beatmapData['artist_unicode'] : 'NULL'); ?>
                    </h3>

                    <h4>
                        Mapset by <a href="https://osu.ppy.sh/users/<?= htmlspecialchars(isset($beatmapData['mapper']) ? $beatmapData['mapper'] : '#'); ?>"><?= htmlspecialchars(isset($beatmapData['mapper']) ? $beatmapData['mapper'] : 'NULL'); ?></a>
                    </h4>

                    <div class="beatmap-attribute-row">
                        <p>
                            <i class='bx bx-star'></i>
                            <?= htmlspecialchars(isset($beatmapData['difficulty_rating']) ? number_format((float)$beatmapData['difficulty_rating'], 2) : 'NULL'); ?>
                        </p>

                        <p>
                            <i class='bx bx-timer'></i>
                            <?= htmlspecialchars(isset($beatmapData['total_length']) ? $beatmapData['total_length'] : 'NULL'); ?>
                        </p>

                        <p>
                            <i class='bx bx-tachometer'></i>
                            <?= htmlspecialchars(isset($beatmapData['map_bpm']) ? number_format((float)$beatmapData['map_bpm'], 2) : 'NULL'); ?> BPM
                        </p>
                    </div>

                    <div class="beatmap-attribute-row">
                        <p>
                            OD: <?= htmlspecialchars(isset($beatmapData['overall_difficulty']) ? number_format((float)$beatmapData['overall_difficulty'], 2) : 'NULL'); ?>
                        </p>

                        <p>
                            HP: <?= htmlspecialchars(isset($beatmapData['health_point']) ? number_format((float)$beatmapData['health_point'], 2) : 'NULL'); ?>
                        </p>

                        <p>
                            Passed: <?= isset($beatmapData['amount_of_passes']) ? $beatmapData['amount_of_passes'] : 'NULL'; ?>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>
