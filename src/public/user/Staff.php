<?php
# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require_once '../../private/controller/VotController.php';
require_once '../../private/model/crud/StaffHandling.php';
?>

<header>
    <h1>Our Lovely Staff :3</h1>
</header>

<section class="staff-page">
    <form action="Staff.php" method="get">
        <div class="flex-container">
            <div class="direct-link-container">
                <button type="submit" name="name" value="vot3">VOT3</button>
            </div>

            <div class="direct-link-container">
                <button type="submit" name="name" value="vot4">VOT4</button>
            </div>
        </div>
    </form>

    <!-- Check if there's staff data to show to end-user and any of the tournament names' button is clicked -->
    <?php if (!empty($staffDatas) && isset($tournamentName)): ?>
        <!-- Dynamic staff information display -->
        <?php foreach ($staffDatas as $staffData): ?>
            <div class="staff-card-container">
                <h1>
                    <img src="<?= htmlspecialchars(isset($staffData['staff_country_flag_url']) ? $staffData['staff_country_flag_url'] : "NULL"); ?>" alt="<?= htmlspecialchars(isset($staffData['staff_country_name']) ? $staffData['staff_country_name'] : "NULL"); ?>">
                    <?= htmlspecialchars(isset($staffData['staff_username']) ? $staffData['staff_username'] : "NULL"); ?>
                </h1>

                <img src="<?= htmlspecialchars(isset($staffData['staff_avatar_url']) ? $staffData['staff_avatar_url'] : "NULL"); ?>" alt="<?= htmlspecialchars(isset($staffData['staff_username']) ? $staffData['staff_username'] : "NULL"); ?>'s Avatar">

                <h2><?= htmlspecialchars(isset($staffData['staff_roles']) ? $staffData['staff_roles'] : "NULL") ?></h2>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>
