<?php
require_once __DIR__ . "/../../common/controller/AuthGuard.php";
requireRole("staff");
require_once __DIR__ . "/../controller/StaffController.php";
$controller = new StaffController();
$metrics = $controller->getDashboardMetrics();
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Staff Dashboard | MeowGhor</title><link rel="stylesheet" href="../../common/view/assets/css/style.css"></head>
<body>
    <?php include __DIR__ . "/shared_nav.php"; ?>
    <main class="page-container"><h1 class="page-title">Welcome, <?= htmlspecialchars($_SESSION["name"], ENT_QUOTES, "UTF-8") ?></h1><p class="section-subtitle">MeowGhor Administration Center</p>
        <section class="stats-row">
            <article class="card"><div class="card-title">Total Cats</div><div class="card-value"><?= (int) $metrics["total_cats"] ?></div></article>
            <article class="card"><div class="card-title">Available Cats</div><div class="card-value"><?= (int) $metrics["available_cats"] ?></div></article>
            <article class="card"><div class="card-title">Pending Intakes</div><div class="card-value"><?= (int) $metrics["pending_intakes"] ?></div></article>
            <article class="card"><div class="card-title">Pending Applications</div><div class="card-value"><?= (int) $metrics["pending_applications"] ?></div></article>
        </section>
    </main>
</body>
</html>
