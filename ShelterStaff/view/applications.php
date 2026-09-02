<?php
require_once __DIR__ . "/../../common/controller/AuthGuard.php";
requireRole("staff");
require_once __DIR__ . "/../controller/StaffController.php";
$controller = new StaffController();
$applications = $controller->getApplications();
$error = $_SESSION["staff_error"] ?? "";
$message = $_SESSION["staff_message"] ?? "";
unset($_SESSION["staff_error"], $_SESSION["staff_message"]);
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Adoption Applications | MeowGhor</title><link rel="stylesheet" href="../../common/view/assets/css/style.css"></head>
<body>
    <?php include __DIR__ . "/shared_nav.php"; ?>
    <main class="page-container"><h1 class="page-title">Adoption Applications</h1><p class="section-subtitle">Review pending applications for available cats.</p>
        <?php if ($error !== ""): ?><p class="intake-feedback intake-feedback-error"><?= htmlspecialchars($error, ENT_QUOTES, "UTF-8") ?></p><?php endif; ?><?php if ($message !== ""): ?><p class="intake-feedback intake-feedback-success"><?= htmlspecialchars($message, ENT_QUOTES, "UTF-8") ?></p><?php endif; ?>
        <div class="table-responsive"><table><thead><tr><th>Adopter</th><th>Cat</th><th>Application</th><th>Applied</th><th>Status</th><th>Review</th></tr></thead><tbody>
            <?php if (count($applications) === 0): ?><tr><td colspan="6">No adoption applications have been submitted.</td></tr><?php else: foreach ($applications as $application): ?><tr>
                <td><strong><?= htmlspecialchars($application["adopter_name"], ENT_QUOTES, "UTF-8") ?></strong><br><small><?= htmlspecialchars($application["adopter_email"], ENT_QUOTES, "UTF-8") ?></small></td><td><?= htmlspecialchars($application["cat_name"], ENT_QUOTES, "UTF-8") ?><br><small>Cat status: <?= htmlspecialchars($application["adoption_status"], ENT_QUOTES, "UTF-8") ?></small></td>
                <td><strong>Reason:</strong> <?= htmlspecialchars($application["reason"], ENT_QUOTES, "UTF-8") ?><br><strong>Living situation:</strong> <?= htmlspecialchars($application["living_situation"], ENT_QUOTES, "UTF-8") ?></td><td><?= htmlspecialchars(date("M j, Y", strtotime($application["applied_at"])), ENT_QUOTES, "UTF-8") ?></td><td><?= htmlspecialchars($application["application_status"], ENT_QUOTES, "UTF-8") ?><?= !empty($application["staff_comment"]) ? "<br><small>" . htmlspecialchars($application["staff_comment"], ENT_QUOTES, "UTF-8") . "</small>" : "" ?></td>
                <td><?php if ($application["application_status"] === "Pending"): ?><form action="../controller/StaffController.php" method="post" class="review-form"><input type="hidden" name="action" value="review_adoption"><input type="hidden" name="application_id" value="<?= (int) $application["application_id"] ?>"><textarea name="staff_comment" rows="2" placeholder="Optional comment"></textarea><button class="btn btn-primary" type="submit" name="status" value="Approved">Approve</button><button class="btn btn-danger" type="submit" name="status" value="Rejected">Reject</button></form><?php else: ?>—<?php endif; ?></td>
            </tr><?php endforeach; endif; ?>
        </tbody></table></div>
    </main>
</body>
</html>
