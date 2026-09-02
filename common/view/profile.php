<?php
require_once __DIR__ . "/../controller/AuthGuard.php";
requireLogin();
require_once __DIR__ . "/../controller/ProfileController.php";

$controller = new ProfileController();
$profile = $controller->getProfile((int) $_SESSION["user_id"]);
if ($profile === null) {
    session_unset();
    session_destroy();
    header("Location: /MeowGhor/common/view/login.php");
    exit();
}
$error = $_SESSION["profile_error"] ?? "";
$message = $_SESSION["profile_message"] ?? "";
unset($_SESSION["profile_error"], $_SESSION["profile_message"]);
$dashboardUrl = $_SESSION["user_type"] === "staff"
    ? "/MeowGhor/ShelterStaff/view/dashboard.php"
    : "/MeowGhor/Adopter/view/dashboard.php";
$isStaff = $_SESSION["user_type"] === "staff";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | MeowGhor</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="adopter-dashboard">
    <nav class="adopter-navbar">
        <a class="adopter-brand" href="<?= htmlspecialchars($dashboardUrl, ENT_QUOTES, "UTF-8") ?>">🐾 MeowGhor</a>
        <div class="adopter-nav-links"><a href="<?= htmlspecialchars($dashboardUrl, ENT_QUOTES, "UTF-8") ?>">Dashboard</a><?php include __DIR__ . "/profile_menu.php"; ?></div>
    </nav>
    <main class="adopter-dashboard-main profile-page-main">
        <section class="profile-card">
            <h1>My Profile</h1>
            <?php if ($error !== ""): ?><p class="error-message"><?= htmlspecialchars($error, ENT_QUOTES, "UTF-8") ?></p><?php endif; ?>
            <?php if ($message !== ""): ?><p class="info-message"><?= htmlspecialchars($message, ENT_QUOTES, "UTF-8") ?></p><?php endif; ?>
            <form class="profile-form" action="../controller/ProfileController.php" method="post">
                <input type="hidden" name="action" value="update_profile">
                <div class="form-group"><label for="name">Name</label><input id="name" name="name" value="<?= htmlspecialchars($profile["name"], ENT_QUOTES, "UTF-8") ?>" required></div>
                <?php if ($isStaff): ?>
                    <div class="form-group"><label for="staff-email">Email (read only)</label><input type="email" id="staff-email" value="<?= htmlspecialchars($profile["email"], ENT_QUOTES, "UTF-8") ?>" readonly aria-readonly="true"></div>
                <?php else: ?>
                    <div class="form-group"><label for="email">Email</label><input type="email" id="email" name="email" value="<?= htmlspecialchars($profile["email"], ENT_QUOTES, "UTF-8") ?>" required></div>
                    <div class="form-group"><label for="phone">Phone</label><input id="phone" name="phone" value="<?= htmlspecialchars($profile["phone"] ?? "", ENT_QUOTES, "UTF-8") ?>"></div>
                    <div class="form-group"><label for="address">Address</label><textarea id="address" name="address" rows="3"><?= htmlspecialchars($profile["address"] ?? "", ENT_QUOTES, "UTF-8") ?></textarea></div>
                <?php endif; ?>
                <button class="auth-btn" type="submit"><?= $isStaff ? "Save Name" : "Save Profile" ?></button>
            </form>
        </section>
        <section class="profile-card">
            <h2>Change Password</h2>
            <form class="profile-form" action="../controller/ProfileController.php" method="post">
                <input type="hidden" name="action" value="change_password">
                <div class="form-group"><label for="current-password">Current Password</label><input type="password" id="current-password" name="current_password" required></div>
                <div class="form-group"><label for="new-password">New Password</label><input type="password" id="new-password" name="new_password" required></div>
                <div class="form-group"><label for="confirm-new-password">Confirm New Password</label><input type="password" id="confirm-new-password" name="confirm_new_password" required></div>
                <button class="auth-btn" type="submit">Change Password</button>
            </form>
        </section>
    </main>
</body>
</html>
