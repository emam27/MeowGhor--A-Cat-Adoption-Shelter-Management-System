<?php
require_once __DIR__ . "/../../common/controller/AuthGuard.php";
requireRole("staff");
$current = basename($_SERVER["PHP_SELF"]);
?>
<div class="nav-bar">
    <a href="/MeowGhor/ShelterStaff/view/dashboard.php" class="nav-brand">🐾 MeowGhor</a>
    <div class="nav-links">
        <a href="/MeowGhor/ShelterStaff/view/dashboard.php" class="<?= $current === "dashboard.php" ? "active" : "" ?>">Dashboard</a>
        <a href="/MeowGhor/ShelterStaff/view/intakes.php" class="<?= $current === "intakes.php" ? "active" : "" ?>">Intake Requests</a>
        <a href="/MeowGhor/ShelterStaff/view/view_cats.php" class="<?= in_array($current, ["cats.php", "view_cats.php"], true) ? "active" : "" ?>">Cats</a>
        <a href="/MeowGhor/ShelterStaff/view/applications.php" class="<?= $current === "applications.php" ? "active" : "" ?>">Applications</a>
        <?php include __DIR__ . "/profile_menu.php"; ?>
    </div>
</div>
