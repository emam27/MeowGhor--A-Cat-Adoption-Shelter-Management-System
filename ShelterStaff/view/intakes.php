<?php
require_once '../controller/StaffController.php';
$ctrl = new StaffController(); $ctrl->processActions(); $intakes = $ctrl->fetchViewData('intakes');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><title>Manage Intakes - MeowGhor</title>
    <link rel="stylesheet" href="../../common/view/assets/css/style.css">
</head>
<body>
    <?php include 'shared_nav.php'; ?>
    <div class="page-container">
        <h1 class="page-title">Cat Intake Queue</h1>
        <p class="section-subtitle">Accept or reject pet surrender requests filed by the community.</p>
        <div class="table-responsive">
            <table>
                <thead><tr><th>ID</th><th>User</th><th>Cat Name</th><th>Details</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php foreach ($intakes as $i): ?>
                    <tr>
                        <td><?= $i['request_id'] ?></td>
                        <td><strong><?= htmlspecialchars($i['user_name']) ?></strong></td>
                        <td><?= htmlspecialchars($i['cat_name']) ?></td>
                        <td>Breed: <?= htmlspecialchars($i['breed']) ?> | Age: <?= htmlspecialchars($i['age']) ?></td>
                        <td><span class="badge badge-<?= strtolower($i['request_status']) ?>"><?= $i['request_status'] ?></span></td>
                        <td>
                            <?php if ($i['request_status'] === 'Pending'): ?>
                            <form action="intakes.php" method="POST" style="display:flex; gap:5px;">
                                <input type="hidden" name="action" value="review_intake">
                                <input type="hidden" name="request_id" value="<?= $i['request_id'] ?>">
                                <input type="hidden" name="cat_name" value="<?= htmlspecialchars($i['cat_name']) ?>"><input type="hidden" name="breed" value="<?= htmlspecialchars($i['breed']) ?>"><input type="hidden" name="gender" value="<?= $i['gender'] ?>"><input type="hidden" name="age" value="<?= htmlspecialchars($i['age']) ?>"><input type="hidden" name="desc" value="<?= htmlspecialchars($i['description']) ?>"><input type="hidden" name="image" value="<?= htmlspecialchars($i['image']) ?>">
                                <input type="text" name="staff_comment" placeholder="Note..." class="form-control" style="width:120px;" required>
                                <button type="submit" name="status" value="Accepted" class="btn btn-primary" style="padding:4px 8px;">Accept</button>
                                <button type="submit" name="status" value="Rejected" class="btn btn-danger" style="padding:4px 8px;">Reject</button>
                            </form>
                            <?php else: echo "<em>Note: " . htmlspecialchars($i['staff_comment']) . "</em>"; endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; if(empty($intakes)) echo "<tr><td colspan='6'>No intake files present.</td></tr>"; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
