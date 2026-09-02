<?php
require_once '../controller/StaffController.php';
$ctrl = new StaffController();

// Intercept inline delete actions before rendering data rows to maintain workflow speed
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['inline_action']) && $_POST['inline_action'] === 'delete_card') {
    $cat_id = filter_input(INPUT_POST, 'cat_id', FILTER_VALIDATE_INT);
    if ($cat_id) {
        $model = new StaffModel();
        $model->deleteCatListing($cat_id);
        header("Location: view_cats.php?msg=deleted");
        exit();
    }
}

// Fetch up-to-date catalog inventory listings from your database matrix
$cats = $ctrl->fetchViewData('cats');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Listed Cats - MeowGhor</title>
    <link rel="stylesheet" href="../../common/view/assets/css/style.css">
    <style>
        .catalog-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 25px; margin-top: 20px; }
        .cat-card { background: #ffffff; border-radius: 12px; border: 1px solid #e8e0da; overflow: hidden; display: flex; flex-direction: column; transition: transform 0.2s; }
        .cat-card:hover { transform: translateY(-3px); box-shadow: 0 4px 10px rgba(231,125,69,0.08); }
        .cat-img-wrapper { width: 100%; height: 200px; background: #e8e0da; position: relative; }
        .cat-card-img { width: 100%; height: 100%; object-fit: cover; }
        .cat-card-body { padding: 18px; flex-grow: 1; display: flex; flex-direction: column; }
        .cat-card-title { font-size: 20px; font-weight: bold; color: #333333; margin-bottom: 8px; display: flex; justify-content: space-between; align-items: center; }
        .cat-meta { font-size: 13px; color: #777777; margin-bottom: 5px; }
        .cat-card-desc { font-size: 14px; color: #555555; margin-top: 10px; margin-bottom: 15px; flex-grow: 1; font-style: italic; line-height: 1.4; }
        .card-actions { border-top: 1px solid #fff8f2; padding-top: 12px; display: flex; justify-content: space-between; align-items: center; gap: 8px; }
    </style>
</head>
<body>
    <?php include 'shared_nav.php'; ?>
    
    <div class="page-container">
        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
            <div style="background: #fff5f5; color: #c92a2a; padding: 12px; border-radius: 6px; border: 1px solid #ffc9c9; margin-bottom: 20px; font-weight: bold; font-size: 14px;">
                ❌ Cat listing record has been permanently removed from the system inventory.
            </div>
        <?php endif; ?>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
            <h1 class="page-title">Active Cat Catalog</h1>
            <a href="cats.php" class="btn btn-primary">➕ Manage & Add Listings</a>
        </div>
        <p class="section-subtitle">A simple preview of all felines currently registered in the database infrastructure.</p>

        <div class="catalog-grid">
            <?php foreach ($cats as $c): ?>
                <div class="cat-card">
                    <div class="cat-img-wrapper">
                        <img src="../../uploads/cats/<?= htmlspecialchars($c['image']) ?>" alt="Cat Pic" class="cat-card-img" onerror="this.src='../../uploads/cats/default-cat.png';">
                    </div>
                    <div class="cat-card-body">
                        <div class="cat-card-title">
                            <?= htmlspecialchars($c['name']) ?>
                            <span class="badge badge-<?= strtolower($c['adoption_status']) ?>"><?= $c['adoption_status'] ?></span>
                        </div>
                        <div class="cat-meta"><strong>Breed:</strong> <?= htmlspecialchars($c['breed']) ?></div>
                        <div class="cat-meta"><strong>Gender/Age:</strong> <?= $c['gender'] ?> (<?= htmlspecialchars($c['age']) ?>)</div>
                        <div class="cat-meta"><strong>Health:</strong> <?= htmlspecialchars($c['health_status']) ?></div>
                        
                        <p class="cat-card-desc">"<?= htmlspecialchars($c['description']) ?>"</p>
                        
                        <div class="card-actions">
                            <a href="cats.php?edit_id=<?= $c['cat_id'] ?>" class="btn btn-secondary" style="font-size:12px; padding:6px 12px; flex-grow: 1;">Modify</a>
                            
                            <!-- Clean POST form wrapper to delete row records securely without data leak vulnerabilities -->
                            <form action="view_cats.php" method="POST" style="margin:0; display:inline;">
                                <input type="hidden" name="inline_action" value="delete_card">
                                <input type="hidden" name="cat_id" value="<?= $c['cat_id'] ?>">
                                <button type="submit" class="btn btn-danger confirm-action" style="font-size:12px; padding:6px 12px;">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; if(empty($cats)) echo "<p style='grid-column:1/-1; text-align:center; padding:40px; color:#777;'>No cats have been added to the inventory listings yet.</p>"; ?>
        </div>
    </div>

    <script src="../../common/view/assets/js/app.js"></script>
</body>
</html>
