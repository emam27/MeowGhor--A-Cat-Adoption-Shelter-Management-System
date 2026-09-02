<?php
require_once __DIR__ . "/../../common/controller/AuthGuard.php";
requireRole("adopter");
require_once __DIR__ . "/../controller/AdopterController.php";
$filters = [
    "search" => trim($_GET["search"] ?? ""),
    "gender" => in_array($_GET["gender"] ?? "", ["Male", "Female"], true) ? $_GET["gender"] : "",
    "age" => in_array($_GET["age"] ?? "", ["kitten", "young", "adult", "senior"], true) ? $_GET["age"] : ""
];
$controller = new AdopterController();
$cats = $controller->getCats($filters);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Cats | MeowGhor</title><link rel="stylesheet" href="../../common/view/assets/css/style.css">
</head>
<body class="adopter-dashboard">
    <nav class="adopter-navbar"><a class="adopter-brand" href="dashboard.php">🐾 MeowGhor</a><div class="adopter-nav-links"><a href="cats.php">Browse Cats</a><a href="applications.php">My Applications</a><a href="intakes.php">My Intakes</a><?php include __DIR__ . "/../../common/view/profile_menu.php"; ?></div></nav>
    <main class="adopter-dashboard-main cats-page-main">
        <section class="cats-page-intro"><h1>Available Cats</h1><p>Find a cat that could be your new companion.</p></section>
        <form class="cat-filters" action="cats.php" method="get">
            <div class="cat-filter-field"><label for="cat-search">Search</label><input type="search" id="cat-search" name="search" value="<?= htmlspecialchars($filters["search"], ENT_QUOTES, "UTF-8") ?>" placeholder="Cat name or breed"></div>
            <div class="cat-filter-field"><label for="cat-gender">Gender</label><select id="cat-gender" name="gender"><option value="">All</option><option value="Male" <?= $filters["gender"] === "Male" ? "selected" : "" ?>>Male</option><option value="Female" <?= $filters["gender"] === "Female" ? "selected" : "" ?>>Female</option></select></div>
            <div class="cat-filter-field"><label for="cat-age">Age</label><select id="cat-age" name="age"><option value="">All Ages</option><option value="kitten" <?= $filters["age"] === "kitten" ? "selected" : "" ?>>Kitten (&lt; 1)</option><option value="young" <?= $filters["age"] === "young" ? "selected" : "" ?>>Young (1–3)</option><option value="adult" <?= $filters["age"] === "adult" ? "selected" : "" ?>>Adult (4–7)</option><option value="senior" <?= $filters["age"] === "senior" ? "selected" : "" ?>>Senior (8+)</option></select></div>
            <button class="cat-filter-button" type="submit">Apply Filters</button><a class="cat-reset-button" href="cats.php">Reset</a>
        </form>
        <section class="cat-listings" aria-label="Cat listings">
            <?php if (count($cats) === 0): ?>
                <div class="cat-empty-state"><h2>No cats are available yet.</h2><p>Try changing your filters or check back soon.</p></div>
            <?php else: foreach ($cats as $cat): $imageValue = trim($cat["image"] ?? ""); $imageUrl = $imageValue === "" ? "" : "/MeowGhor/" . (strpos($imageValue, "/") === false ? "uploads/cats/" . $imageValue : ltrim($imageValue, "/")); ?>
                <article class="cat-card">
                    <?php if ($imageUrl !== ""): ?><img class="cat-card-image" src="<?= htmlspecialchars($imageUrl, ENT_QUOTES, "UTF-8") ?>" alt="<?= htmlspecialchars($cat["name"], ENT_QUOTES, "UTF-8") ?>"><?php else: ?><div class="cat-card-image cat-image-placeholder">No image available</div><?php endif; ?>
                    <div class="cat-card-details"><h2 class="cat-card-name"><?= htmlspecialchars($cat["name"], ENT_QUOTES, "UTF-8") ?></h2><p class="cat-card-breed"><?= htmlspecialchars($cat["breed"] ?: "Breed not specified", ENT_QUOTES, "UTF-8") ?></p><p class="cat-card-gender"><?= htmlspecialchars($cat["gender"], ENT_QUOTES, "UTF-8") ?></p><p class="cat-card-age"><?= $cat["age"] === null ? "Age not specified" : htmlspecialchars($cat["age"], ENT_QUOTES, "UTF-8") . " years" ?></p><p class="cat-card-status">Available</p><a class="cat-card-button" href="cat_details.php?cat_id=<?= (int) $cat["cat_id"] ?>">View Details</a></div>
                </article>
            <?php endforeach; endif; ?>
        </section>
    </main>
</body>
</html>
