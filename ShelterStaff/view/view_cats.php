<?php
require_once __DIR__ . "/../../common/controller/AuthGuard.php";
requireRole("staff");
require_once __DIR__ . "/../controller/StaffController.php";
$controller = new StaffController();
$cats = $controller->getCats();
$error = $_SESSION["staff_error"] ?? "";
$message = $_SESSION["staff_message"] ?? "";
unset($_SESSION["staff_error"], $_SESSION["staff_message"]);
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Cat Catalog | MeowGhor</title><link rel="stylesheet" href="../../common/view/assets/css/style.css"></head>
<body>
    <?php include __DIR__ . "/shared_nav.php"; ?>
    <main class="page-container"><div class="page-heading-row"><div><h1 class="page-title">Cat Catalog</h1><p class="section-subtitle">All registered shelter cat listings.</p></div><a class="btn btn-primary" href="cats.php">Add Cat</a></div>
        <?php if ($error !== ""): ?><p class="intake-feedback intake-feedback-error"><?= htmlspecialchars($error, ENT_QUOTES, "UTF-8") ?></p><?php endif; ?><?php if ($message !== ""): ?><p class="intake-feedback intake-feedback-success"><?= htmlspecialchars($message, ENT_QUOTES, "UTF-8") ?></p><?php endif; ?>
        <section class="catalog-grid">
            <?php if (count($cats) === 0): ?><p class="catalog-empty">No cats have been added yet.</p><?php else: foreach ($cats as $cat): $imageValue = trim($cat["image"] ?? ""); $imageUrl = $imageValue === "" ? "" : "/MeowGhor/" . (strpos($imageValue, "/") === false ? "uploads/cats/" . $imageValue : ltrim($imageValue, "/")); ?>
                <article class="staff-cat-card"><?php if ($imageUrl !== ""): ?><img src="<?= htmlspecialchars($imageUrl, ENT_QUOTES, "UTF-8") ?>" alt="<?= htmlspecialchars($cat["name"], ENT_QUOTES, "UTF-8") ?>"><?php else: ?><div class="staff-image-placeholder">No image available</div><?php endif; ?><div class="staff-cat-card-body"><h2><?= htmlspecialchars($cat["name"], ENT_QUOTES, "UTF-8") ?></h2><p><strong>Status:</strong> <?= htmlspecialchars($cat["adoption_status"], ENT_QUOTES, "UTF-8") ?></p><p><strong>Breed:</strong> <?= htmlspecialchars($cat["breed"] ?: "Not specified", ENT_QUOTES, "UTF-8") ?></p><p><strong>Gender/Age:</strong> <?= htmlspecialchars($cat["gender"], ENT_QUOTES, "UTF-8") ?> / <?= $cat["age"] === null ? "Not specified" : htmlspecialchars($cat["age"], ENT_QUOTES, "UTF-8") ?></p><p><strong>Added by:</strong> <?= htmlspecialchars($cat["staff_name"], ENT_QUOTES, "UTF-8") ?></p><div class="card-actions"><a class="btn btn-secondary" href="cats.php?edit_id=<?= (int) $cat["cat_id"] ?>">Edit</a><?php if ($cat["adoption_status"] === "Available"): ?><form action="../controller/StaffController.php" method="post"><input type="hidden" name="action" value="archive_cat"><input type="hidden" name="cat_id" value="<?= (int) $cat["cat_id"] ?>"><button class="btn btn-danger" type="submit">Archive</button></form><?php endif; ?></div></div></article>
            <?php endforeach; endif; ?>
        </section>
    </main>
</body>
</html>
