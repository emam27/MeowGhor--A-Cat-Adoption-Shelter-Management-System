<?php
require_once '../controller/StaffController.php';
$ctrl = new StaffController();
if (isset($_GET['action']) && $_GET['action'] == 'logout') { session_start(); session_destroy(); header("Location: dashboard.php"); exit(); }
$metrics = $ctrl->fetchViewData('metrics');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Dashboard - MeowGhor</title>
    <link rel="stylesheet" href="../../common/view/assets/css/style.css">
</head>
<body>
    <?php include 'shared_nav.php'; ?>
    <div class="page-container">
        <h1 class="page-title">Welcome, <?= htmlspecialchars($_SESSION['name']) ?></h1>
        <p class="section-subtitle">MeowGhor Administration Center</p>

        <div class="stats-row">
            <div class="card"><div class="card-title">Pending Intakes</div><div class="card-value"><?= $metrics['intakes'] ?></div></div>
            <div class="card"><div class="card-title">Available Cats</div><div class="card-value"><?= $metrics['cats'] ?></div></div>
            <div class="card"><div class="card-title">Pending Applications</div><div class="card-value"><?= $metrics['apps'] ?></div></div>
        </div>
    </div>
</body>
</html>
