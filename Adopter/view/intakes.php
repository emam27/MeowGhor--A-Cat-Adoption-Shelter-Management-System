<?php
require_once __DIR__ . '/../../common/controller/AuthGuard.php';
requireRole("adopter");
require_once __DIR__ . '/../controller/AdopterController.php';

$adopterController = new AdopterController();
$intakeRequests = $adopterController->getIntakeRequests($_SESSION["user_id"]);
$authError = $_SESSION["auth_error"] ?? "";
$authMessage = $_SESSION["auth_message"] ?? "";
unset($_SESSION["auth_error"], $_SESSION["auth_message"]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cat Intake Requests | MeowGhor</title>
    <link rel="stylesheet" href="../../common/view/assets/css/style.css">
</head>
<body class="adopter-dashboard">
    <nav class="adopter-navbar" aria-label="Adopter navigation">
        <a class="adopter-brand" href="dashboard.php">🐾 MeowGhor</a>

        <div class="adopter-nav-links">
            <a href="cats.php">Browse Cats</a>
            <a href="applications.php">My Applications</a>
            <a href="intakes.php">My Intakes</a>

            <?php include __DIR__ . '/../../common/view/profile_menu.php'; ?>
        </div>
    </nav>

    <main class="adopter-dashboard-main intakes-page-main">
        <?php if ($authError !== ""): ?>
            <div class="intake-feedback intake-feedback-error">
                <?= htmlspecialchars($authError, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <?php if ($authMessage !== ""): ?>
            <div class="intake-feedback intake-feedback-success">
                <?= htmlspecialchars($authMessage, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <section class="intakes-page-intro" aria-labelledby="intakes-heading">
            <h1 id="intakes-heading">My Cat Intake Requests</h1>
            <p>Submit a cat intake request and track its status.</p>
        </section>

        <!-- The future controller will validate and persist this request when storage is connected. -->
        <section class="intake-form-card" aria-labelledby="intake-form-heading">
            <h2 id="intake-form-heading">Submit an Intake Request</h2>
            <form class="intake-form" action="../controller/AdopterController.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="submit_intake">
                <div class="intake-form-grid">
                    <div class="intake-form-field">
                        <label for="cat-name">Cat Name</label>
                        <input type="text" id="cat-name" name="cat_name" required>
                    </div>

                    <div class="intake-form-field">
                        <label for="cat-breed">Breed</label>
                        <input type="text" id="cat-breed" name="breed" required>
                    </div>

                    <div class="intake-form-field">
                        <label for="cat-gender">Gender</label>
                        <select id="cat-gender" name="gender" required>
                            <option value="">Select gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>

                    <div class="intake-form-field">
                        <label for="cat-age">Age</label>
                        <input type="text" id="cat-age" name="age" placeholder="For example, 2 years" required>
                    </div>

                    <div class="intake-form-field intake-form-field-wide">
                        <label for="health-status">Health Status</label>
                        <textarea id="health-status" name="health_status" rows="3" required></textarea>
                    </div>

                    <div class="intake-form-field intake-form-field-wide">
                        <label for="cat-description">Description</label>
                        <textarea id="cat-description" name="description" rows="4" required></textarea>
                    </div>

                    <div class="intake-form-field intake-form-field-wide">
                        <label for="intake-reason">Reason for Intake</label>
                        <textarea id="intake-reason" name="reason_for_intake" rows="4" required></textarea>
                    </div>

                    <div class="intake-form-field intake-form-field-wide">
                        <label for="cat-image">Cat Image</label>
                        <input type="file" id="cat-image" name="cat_image" accept="image/*">
                    </div>
                </div>

                <button class="intake-submit-button" type="submit">Submit Request</button>
            </form>
        </section>

        <section class="intake-requests-section" aria-labelledby="intake-requests-heading">
            <h2 id="intake-requests-heading">My Intake Requests</h2>

            <?php if (count($intakeRequests) === 0): ?>
                <div class="intake-empty-state">
                    <p>No intake requests yet.</p>
                </div>
            <?php else: ?>
                <div class="intake-request-list">
                    <div class="intake-request-list-header">
                        <span>Cat</span>
                        <span>Submitted Date</span>
                        <span>Status</span>
                        <span>Action</span>
                    </div>

                    <?php foreach ($intakeRequests as $request): ?>
                        <div class="intake-request-row">
                            <div class="intake-request-cell">
                                <?= htmlspecialchars($request["cat_name"], ENT_QUOTES, 'UTF-8') ?>
                            </div>
                            <div class="intake-request-cell">
                                <?= htmlspecialchars(date("M j, Y", strtotime($request["submitted_at"])), ENT_QUOTES, 'UTF-8') ?>
                            </div>
                            <div class="intake-request-cell intake-request-status">
                                <?= htmlspecialchars($request["request_status"], ENT_QUOTES, 'UTF-8') ?>
                            </div>
                            <div class="intake-request-cell intake-request-action">
                                <?php if ($request["request_status"] === "Pending"): ?>
                                    <form class="intake-cancel-form" action="../controller/AdopterController.php" method="post">
                                        <input type="hidden" name="action" value="cancel_intake">
                                        <input type="hidden" name="request_id" value="<?= (int) $request["request_id"] ?>">
                                        <button class="intake-cancel-button" type="submit">Cancel</button>
                                    </form>
                                <?php else: ?>
                                    <span class="intake-no-action">—</span>
                                <?php endif; ?>
                            </div>

                            <?php if ($request["staff_comment"] !== null && $request["staff_comment"] !== ""): ?>
                                <div class="intake-staff-comment">
                                    <strong>Staff Comment:</strong>
                                    <?= htmlspecialchars($request["staff_comment"], ENT_QUOTES, 'UTF-8') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
