<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['name'] = 'Jane Staff';
    $_SESSION['user_type'] = 'staff';
}
$current = basename($_SERVER['PHP_SELF']);
?>
<div class="nav-bar">
    <a href="dashboard.php" class="nav-brand">🐾 MeowGhor</a>
    <div class="nav-links" style="display: flex; align-items: center;">
        <a href="dashboard.php" class="<?= $current == 'dashboard.php' ? 'active' : '' ?>">Dashboard</a>
        <a href="intakes.php" class="<?= $current == 'intakes.php' ? 'active' : '' ?>">Intake Requests</a>
        <a href="view_cats.php" class="<?= $current == 'view_cats.php' ? 'active' : '' ?>">View Cats</a>
        
        <!-- FIX: Added '../../' to step out of 'shelter_staff/view' and into the root folder -->
        <a href="../../common/view/profile.php" class="<?= $current == 'profile.php' ? 'active' : '' ?>">My Profile</a>
        
        <!-- Direct Logout Link (Matches the same directory escape path) -->
        <a href="../../common/controller/AuthController.php?action=logout">Logout</a>
    </div>
</div>

