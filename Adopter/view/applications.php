<?php
require_once __DIR__ . "/../../common/controller/AuthGuard.php";
requireRole("adopter");
require_once __DIR__ . "/../controller/AdopterController.php";
$controller = new AdopterController();
$selectedCatId = filter_var($_GET["cat_id"] ?? null, FILTER_VALIDATE_INT);
$selectedCat = $selectedCatId ? $controller->getAvailableCat($selectedCatId) : null;
$applications = $controller->getApplications((int) $_SESSION["user_id"]);
$error = $_SESSION["auth_error"] ?? "";
$message = $_SESSION["auth_message"] ?? "";
unset($_SESSION["auth_error"], $_SESSION["auth_message"]);
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>My Adoption Applications | MeowGhor</title><link rel="stylesheet" href="../../common/view/assets/css/style.css"></head>
<body class="adopter-dashboard">
    <nav class="adopter-navbar"><a class="adopter-brand" href="dashboard.php">🐾 MeowGhor</a><div class="adopter-nav-links"><a href="cats.php">Browse Cats</a><a href="applications.php">My Applications</a><a href="intakes.php">My Intakes</a><?php include __DIR__ . "/../../common/view/profile_menu.php"; ?></div></nav>
    <main class="adopter-dashboard-main applications-page-main">
        <section class="applications-page-intro"><h1>My Adoption Applications</h1><p>Track your adoption applications and their current status.</p></section>
        <?php if ($error !== ""): ?><p class="intake-feedback intake-feedback-error"><?= htmlspecialchars($error, ENT_QUOTES, "UTF-8") ?></p><?php endif; ?>
        <?php if ($message !== ""): ?><p class="intake-feedback intake-feedback-success"><?= htmlspecialchars($message, ENT_QUOTES, "UTF-8") ?></p><?php endif; ?>
        <?php if ($selectedCat !== null): ?>
            <section class="adoption-application-form" aria-labelledby="application-form-heading"><h2 id="application-form-heading">Apply for <?= htmlspecialchars($selectedCat["name"], ENT_QUOTES, "UTF-8") ?></h2>
                <form action="../controller/AdopterController.php" method="post"><input type="hidden" name="action" value="submit_application"><input type="hidden" name="cat_id" value="<?= (int) $selectedCat["cat_id"] ?>">
                    <div class="application-form-field"><label>Selected Cat</label><input value="<?= htmlspecialchars($selectedCat["name"], ENT_QUOTES, "UTF-8") ?><?= $selectedCat["breed"] ? " — " . htmlspecialchars($selectedCat["breed"], ENT_QUOTES, "UTF-8") : "" ?>" readonly></div>
                    <div class="application-form-field"><label for="adoption-reason">Reason for Adoption</label><textarea id="adoption-reason" name="reason" rows="4" required></textarea></div>
                    <div class="application-form-field"><label for="living-situation">Living Situation</label><textarea id="living-situation" name="living_situation" rows="4" required></textarea></div>
                    <button class="application-submit-button" type="submit">Submit Application</button>
                </form>
            </section>
        <?php elseif ($selectedCatId): ?>
            <section class="applications-empty-state"><h2>This cat is no longer available.</h2><p>Please browse the available cats to choose another companion.</p><a class="applications-browse-button" href="cats.php">Browse Cats</a></section>
        <?php endif; ?>
        <section class="application-list" aria-labelledby="application-list-heading"><h2 id="application-list-heading">Applications</h2>
            <?php if (count($applications) === 0): ?><div class="applications-empty-state"><h2>No adoption applications yet.</h2><p>Browse available cats and choose a cat to start an adoption application.</p><a class="applications-browse-button" href="cats.php">Browse Cats</a></div>
            <?php else: ?><div class="application-list-header"><span>Cat</span><span>Applied Date</span><span>Status</span><span>Action</span></div>
                <?php foreach ($applications as $application): ?><div class="application-list-row"><span><?= htmlspecialchars($application["cat_name"], ENT_QUOTES, "UTF-8") ?><?= $application["cat_breed"] ? " — " . htmlspecialchars($application["cat_breed"], ENT_QUOTES, "UTF-8") : "" ?><?php if (!empty($application["staff_comment"])): ?><small class="application-comment">Staff: <?= htmlspecialchars($application["staff_comment"], ENT_QUOTES, "UTF-8") ?></small><?php endif; ?></span><span><?= htmlspecialchars(date("M j, Y", strtotime($application["applied_at"])), ENT_QUOTES, "UTF-8") ?></span><span class="application-status"><?= htmlspecialchars($application["application_status"], ENT_QUOTES, "UTF-8") ?></span><span class="application-action"><?php if ($application["application_status"] === "Pending"): ?><form action="../controller/AdopterController.php" method="post"><input type="hidden" name="action" value="withdraw_application"><input type="hidden" name="application_id" value="<?= (int) $application["application_id"] ?>"><button class="intake-cancel-button" type="submit">Withdraw</button></form><?php else: ?>—<?php endif; ?></span></div><?php endforeach; ?>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
