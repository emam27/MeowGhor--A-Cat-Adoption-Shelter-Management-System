<?php
require_once __DIR__ . "/../../common/controller/AuthGuard.php";
requireRole("adopter");
require_once __DIR__ . "/../controller/AdopterController.php";
$controller = new AdopterController();
$intakeRequests = $controller->getIntakeRequests((int) $_SESSION["user_id"]);
$error = $_SESSION["auth_error"] ?? "";
$message = $_SESSION["auth_message"] ?? "";
unset($_SESSION["auth_error"], $_SESSION["auth_message"]);
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>My Cat Intake Requests | MeowGhor</title><link rel="stylesheet" href="../../common/view/assets/css/style.css"></head>
<body class="adopter-dashboard">
    <nav class="adopter-navbar"><a class="adopter-brand" href="dashboard.php">🐾 MeowGhor</a><div class="adopter-nav-links"><a href="cats.php">Browse Cats</a><a href="applications.php">My Applications</a><a href="intakes.php">My Intakes</a><?php include __DIR__ . "/../../common/view/profile_menu.php"; ?></div></nav>
    <main class="adopter-dashboard-main intakes-page-main">
        <?php if ($error !== ""): ?><div class="intake-feedback intake-feedback-error"><?= htmlspecialchars($error, ENT_QUOTES, "UTF-8") ?></div><?php endif; ?>
        <?php if ($message !== ""): ?><div class="intake-feedback intake-feedback-success"><?= htmlspecialchars($message, ENT_QUOTES, "UTF-8") ?></div><?php endif; ?>
        <section class="intakes-page-intro"><h1>My Cat Intake Requests</h1><p>Submit a cat intake request and track its status.</p></section>
        <section class="intake-form-card"><h2>Submit an Intake Request</h2>
            <form class="intake-form" action="../controller/AdopterController.php" method="post" enctype="multipart/form-data"><input type="hidden" name="action" value="submit_intake">
                <div class="intake-form-grid">
                    <div class="intake-form-field"><label for="cat-name">Cat Name</label><input id="cat-name" name="cat_name" required></div>
                    <div class="intake-form-field"><label for="cat-breed">Breed</label><input id="cat-breed" name="breed" required></div>
                    <div class="intake-form-field"><label for="cat-gender">Gender</label><select id="cat-gender" name="gender" required><option value="">Select gender</option><option value="Male">Male</option><option value="Female">Female</option></select></div>
                    <div class="intake-form-field"><label for="cat-age">Age</label><input type="number" min="0" step="0.1" id="cat-age" name="age" required></div>
                    <div class="intake-form-field intake-form-field-wide"><label for="health-status">Health Status</label><textarea id="health-status" name="health_status" rows="3" required></textarea></div>
                    <div class="intake-form-field intake-form-field-wide"><label for="cat-description">Description</label><textarea id="cat-description" name="description" rows="4" required></textarea></div>
                    <div class="intake-form-field intake-form-field-wide"><label for="intake-reason">Reason for Intake</label><textarea id="intake-reason" name="reason_for_intake" rows="4" required></textarea></div>
                    <div class="intake-form-field intake-form-field-wide"><label for="cat-image">Cat Image</label><input type="file" id="cat-image" name="cat_image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"></div>
                </div><button class="intake-submit-button" type="submit">Submit Request</button>
            </form>
        </section>
        <section class="intake-requests-section"><h2>My Intake Requests</h2>
            <?php if (count($intakeRequests) === 0): ?><div class="intake-empty-state"><p>No intake requests yet.</p></div>
            <?php else: ?><div class="intake-request-list"><div class="intake-request-list-header"><span>Cat</span><span>Submitted Date</span><span>Status</span><span>Action</span></div>
                <?php foreach ($intakeRequests as $request): ?><div class="intake-request-row"><div class="intake-request-cell"><?= htmlspecialchars($request["cat_name"], ENT_QUOTES, "UTF-8") ?></div><div class="intake-request-cell"><?= htmlspecialchars(date("M j, Y", strtotime($request["submitted_at"])), ENT_QUOTES, "UTF-8") ?></div><div class="intake-request-cell intake-request-status"><?= htmlspecialchars($request["request_status"], ENT_QUOTES, "UTF-8") ?></div><div class="intake-request-cell intake-request-action"><?php if ($request["request_status"] === "Pending"): ?><form class="intake-cancel-form" action="../controller/AdopterController.php" method="post"><input type="hidden" name="action" value="cancel_intake"><input type="hidden" name="request_id" value="<?= (int) $request["request_id"] ?>"><button class="intake-cancel-button" type="submit">Cancel</button></form><?php else: ?>—<?php endif; ?></div><?php if (!empty($request["staff_comment"])): ?><div class="intake-staff-comment"><strong>Staff Comment:</strong> <?= htmlspecialchars($request["staff_comment"], ENT_QUOTES, "UTF-8") ?></div><?php endif; ?></div><?php endforeach; ?>
            </div><?php endif; ?>
        </section>
    </main>
</body>
</html>
