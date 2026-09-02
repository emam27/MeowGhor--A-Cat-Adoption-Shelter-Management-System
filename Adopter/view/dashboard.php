<?php
require_once __DIR__ . "/../../common/controller/AuthGuard.php";
requireRole("adopter");
require_once __DIR__ . "/../controller/AdopterController.php";
$controller = new AdopterController();
$counts = $controller->getDashboardCounts((int) $_SESSION["user_id"]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adopter Dashboard | MeowGhor</title>
    <link rel="stylesheet" href="../../common/view/assets/css/style.css">
</head>
<body class="adopter-dashboard">
    <nav class="adopter-navbar" aria-label="Adopter navigation">
        <a class="adopter-brand" href="dashboard.php">🐾 MeowGhor</a>
        <div class="adopter-nav-links">
            <a href="cats.php">Browse Cats</a><a href="applications.php">My Applications</a><a href="intakes.php">My Intakes</a>
            <?php include __DIR__ . "/../../common/view/profile_menu.php"; ?>
        </div>
    </nav>
    <main class="adopter-dashboard-main">
        <section class="adopter-dashboard-intro"><h1>Welcome to MeowGhor</h1><p>Manage your adoption and cat intake activities.</p></section>
        <section class="adopter-summary-cards" aria-label="Dashboard summary">
            <article class="adopter-summary-card"><h2>Available Cats</h2><p><?= (int) $counts["available_cats"] ?></p></article>
            <article class="adopter-summary-card"><h2>My Applications</h2><p><?= (int) $counts["applications"] ?></p></article>
            <article class="adopter-summary-card"><h2>My Intake Requests</h2><p><?= (int) $counts["intakes"] ?></p></article>
        </section>
        <section class="adopter-recent-activity"><h2>Recent Activity</h2><p>Use My Applications and My Intakes to see your latest updates.</p></section>
    </main>
</body>
</html>
