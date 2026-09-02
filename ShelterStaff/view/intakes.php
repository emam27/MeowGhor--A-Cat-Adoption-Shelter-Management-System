<?php
require_once __DIR__ . "/../../common/controller/AuthGuard.php";
requireRole("staff");
require_once __DIR__ . "/../controller/StaffController.php";
$controller = new StaffController();
$intakes = $controller->getIntakes();
$error = $_SESSION["staff_error"] ?? "";
$message = $_SESSION["staff_message"] ?? "";
unset($_SESSION["staff_error"], $_SESSION["staff_message"]);
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Manage Intakes | MeowGhor</title><link rel="stylesheet" href="../../common/view/assets/css/style.css"></head>
<body>
    <?php include __DIR__ . "/shared_nav.php"; ?>
    <main class="page-container"><h1 class="page-title">Cat Intake Queue</h1><p class="section-subtitle">Accept or reject surrender requests filed by adopters.</p>
        <?php if ($error !== ""): ?><p class="intake-feedback intake-feedback-error"><?= htmlspecialchars($error, ENT_QUOTES, "UTF-8") ?></p><?php endif; ?><?php if ($message !== ""): ?><p class="intake-feedback intake-feedback-success"><?= htmlspecialchars($message, ENT_QUOTES, "UTF-8") ?></p><?php endif; ?>
        <div class="table-responsive"><table><thead><tr><th>ID</th><th>Adopter</th><th>Cat</th><th>Details</th><th>Status</th><th>Review</th></tr></thead><tbody>
            <?php if (count($intakes) === 0): ?><tr><td colspan="6">No intake requests have been submitted.</td></tr><?php else: foreach ($intakes as $intake): ?><tr>
                <td><?= (int) $intake["request_id"] ?></td><td><strong><?= htmlspecialchars($intake["user_name"], ENT_QUOTES, "UTF-8") ?></strong><br><small><?= htmlspecialchars($intake["user_email"], ENT_QUOTES, "UTF-8") ?></small></td>
                <td><?= htmlspecialchars($intake["cat_name"], ENT_QUOTES, "UTF-8") ?></td><td>Breed: <?= htmlspecialchars($intake["breed"] ?: "Not specified", ENT_QUOTES, "UTF-8") ?><br>Gender: <?= htmlspecialchars($intake["gender"], ENT_QUOTES, "UTF-8") ?><br>Age: <?= $intake["age"] === null ? "Not specified" : htmlspecialchars($intake["age"], ENT_QUOTES, "UTF-8") ?><br>Health: <?= htmlspecialchars($intake["health_status"] ?: "Not specified", ENT_QUOTES, "UTF-8") ?><br><small><?= htmlspecialchars($intake["reason"], ENT_QUOTES, "UTF-8") ?></small></td>
                <td><?= htmlspecialchars($intake["request_status"], ENT_QUOTES, "UTF-8") ?></td><td><?php if ($intake["request_status"] === "Pending"): ?><form action="../controller/StaffController.php" method="post" class="review-form"><input type="hidden" name="action" value="review_intake"><input type="hidden" name="request_id" value="<?= (int) $intake["request_id"] ?>"><textarea name="staff_comment" rows="2" placeholder="Optional comment"></textarea><button class="btn btn-primary" name="status" value="Accepted" type="submit">Accept</button><button class="btn btn-danger" name="status" value="Rejected" type="submit">Reject</button></form><?php else: ?><?= !empty($intake["staff_comment"]) ? htmlspecialchars($intake["staff_comment"], ENT_QUOTES, "UTF-8") : "—" ?><?php endif; ?></td>
            </tr><?php endforeach; endif; ?>
        </tbody></table></div>
    </main>
</body>
</html>
