<?php
require_once '../controller/StaffController.php';
$ctrl = new StaffController(); $ctrl->processActions();
if (isset($_GET['delete_id'])) {
    $model = new StaffModel(); $model->deleteCatListing($_GET['delete_id']);
    header("Location: cats.php?msg=deleted"); exit();
}
$cats = $ctrl->fetchViewData('cats');
$edit = isset($_GET['edit_id']) ? $ctrl->fetchViewData('cat_single', $_GET['edit_id']) : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><title>Manage Cats - MeowGhor</title>
    <link rel="stylesheet" href="../../common/view/assets/css/style.css">
</head>
<body>
    <?php include 'shared_nav.php'; ?>
    <div class="page-container" style="display:flex; gap:30px;">
        <div class="form-box" style="width:350px; margin:0;">
            <h3><?= $edit ? "Edit Listing" : "Add New Cat" ?></h3>
            <form action="cats.php" method="POST" enctype="multipart/form-data" style="margin-top:15px;">
                <input type="hidden" name="action" value="<?= $edit ? 'edit_cat' : 'add_cat' ?>">
                <?php if ($edit): ?><input type="hidden" name="cat_id" value="<?= $edit['cat_id'] ?>"><?php endif; ?>
                <div class="form-group"><label>Cat Name</label><input type="text" name="name" class="form-control" value="<?= $edit['name']??'' ?>" required></div>
                <div class="form-group"><label>Breed</label><input type="text" name="breed" class="form-control" value="<?= $edit['breed']??'' ?>" required></div>
                <div class="form-group"><label>Gender</label><select name="gender" class="form-control"><option value="Male" <?= isset($edit)&&$edit['gender']=='Male'?'selected':'' ?>>Male</option><option value="Female" <?= isset($edit)&&$edit['gender']=='Female'?'selected':'' ?>>Female</option></select></div>
                <div class="form-group"><label>Age</label><input type="text" name="age" class="form-control" value="<?= $edit['age']??'' ?>" required></div>
                <div class="form-group"><label>Color</label><input type="text" name="color" class="form-control" value="<?= $edit['color']??'Mixed' ?>" required></div>
                <div class="form-group"><label>Health Status</label><input type="text" name="health_status" class="form-control" value="<?= $edit['health_status']??'Healthy' ?>" required></div>
                <div class="form-group"><label>Description</label><textarea name="description" class="form-control" required><?= $edit['description']??'' ?></textarea></div>
                <div class="form-group"><label>Image Upload</label><input type="file" name="image" class="form-control"></div>
                <?php if ($edit): ?>
                <div class="form-group"><label>Status</label><select name="adoption_status" class="form-control"><option value="Available" <?= $edit['adoption_status']=='Available'?'selected':'' ?>>Available</option><option value="Adopted" <?= $edit['adoption_status']=='Adopted'?'selected':'' ?>>Adopted</option><option value="Unavailable" <?= $edit['adoption_status']=='Unavailable'?'selected':'' ?>>Unavailable</option></select></div>
                <?php endif; ?>
                <button type="submit" class="btn btn-primary" style="width:100%;"><?= $edit ? "Save Changes" : "Publish Listing" ?></button>
