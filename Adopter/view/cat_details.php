<?php
require_once __DIR__ . "/../../common/controller/AuthGuard.php";
requireRole("adopter");
require_once __DIR__ . "/../controller/AdopterController.php";
$catId = filter_var($_GET["cat_id"] ?? null, FILTER_VALIDATE_INT);
$controller = new AdopterController();
$cat = $catId ? $controller->getCat($catId) : null;
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Cat Details | MeowGhor</title><link rel="stylesheet" href="../../common/view/assets/css/style.css"></head>
<body class="adopter-dashboard">
    <nav class="adopter-navbar"><a class="adopter-brand" href="dashboard.php">🐾 MeowGhor</a><div class="adopter-nav-links"><a href="cats.php">Browse Cats</a><a href="applications.php">My Applications</a><a href="intakes.php">My Intakes</a><?php include __DIR__ . "/../../common/view/profile_menu.php"; ?></div></nav>
    <main class="adopter-dashboard-main cat-details-page-main">
        <?php if ($cat === null): ?>
            <section class="cat-details-empty-state"><h1>Cat not found.</h1><p>The selected cat does not exist or the link is invalid.</p><a class="cat-back-button" href="cats.php">Back to Browse Cats</a></section>
        <?php else: $imageValue = trim($cat["image"] ?? ""); $imageUrl = $imageValue === "" ? "" : "/MeowGhor/" . (strpos($imageValue, "/") === false ? "uploads/cats/" . $imageValue : ltrim($imageValue, "/")); ?>
            <section class="cat-details-card">
                <div class="cat-details-image-wrap"><?php if ($imageUrl !== ""): ?><img class="cat-details-image" src="<?= htmlspecialchars($imageUrl, ENT_QUOTES, "UTF-8") ?>" alt="<?= htmlspecialchars($cat["name"], ENT_QUOTES, "UTF-8") ?>"><?php else: ?><div class="cat-details-image cat-image-placeholder">No image available</div><?php endif; ?></div>
                <div class="cat-details-content"><h1><?= htmlspecialchars($cat["name"], ENT_QUOTES, "UTF-8") ?></h1><p class="cat-details-breed"><?= htmlspecialchars($cat["breed"] ?: "Breed not specified", ENT_QUOTES, "UTF-8") ?></p>
                    <dl class="cat-details-facts"><div><dt>Gender</dt><dd><?= htmlspecialchars($cat["gender"], ENT_QUOTES, "UTF-8") ?></dd></div><div><dt>Age</dt><dd><?= $cat["age"] === null ? "Not specified" : htmlspecialchars($cat["age"], ENT_QUOTES, "UTF-8") . " years" ?></dd></div><div><dt>Color</dt><dd><?= htmlspecialchars($cat["color"] ?: "Not specified", ENT_QUOTES, "UTF-8") ?></dd></div><div><dt>Health Status</dt><dd><?= htmlspecialchars($cat["health_status"] ?: "Not specified", ENT_QUOTES, "UTF-8") ?></dd></div><div><dt>Adoption Status</dt><dd><?= htmlspecialchars($cat["adoption_status"], ENT_QUOTES, "UTF-8") ?></dd></div></dl>
                    <div class="cat-details-description"><h2>Description</h2><p class="cat-details-description-text"><?= nl2br(htmlspecialchars($cat["description"] ?: "No description provided.", ENT_QUOTES, "UTF-8")) ?></p></div>
                    <?php if ($cat["adoption_status"] === "Available"): ?><a class="cat-details-apply-button" href="applications.php?cat_id=<?= (int) $cat["cat_id"] ?>">Apply for Adoption</a><?php endif; ?>
                </div>
            </section>
        <?php endif; ?>
    </main>
</body>
</html>
