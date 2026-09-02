<?php
require_once __DIR__ . "/../../common/controller/AuthGuard.php";
requireRole("staff");
require_once __DIR__ . "/../controller/StaffController.php";
$controller = new StaffController();
$editId = filter_var($_GET["edit_id"] ?? null, FILTER_VALIDATE_INT);
$edit = $editId ? $controller->getCatById($editId) : null;
$error = $_SESSION["staff_error"] ?? "";
$message = $_SESSION["staff_message"] ?? "";
unset($_SESSION["staff_error"], $_SESSION["staff_message"]);
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Manage Cats | MeowGhor</title><link rel="stylesheet" href="../../common/view/assets/css/style.css"></head>
<body>
    <?php include __DIR__ . "/shared_nav.php"; ?>
    <main class="page-container">
        <div class="page-heading-row"><div><h1 class="page-title"><?= $edit ? "Edit Cat Listing" : "Add New Cat" ?></h1><p class="section-subtitle">Create and maintain shelter cat listings.</p></div><a class="btn btn-secondary" href="view_cats.php">View Cat Catalog</a></div>
        <?php if ($error !== ""): ?><p class="intake-feedback intake-feedback-error"><?= htmlspecialchars($error, ENT_QUOTES, "UTF-8") ?></p><?php endif; ?>
        <?php if ($message !== ""): ?><p class="intake-feedback intake-feedback-success"><?= htmlspecialchars($message, ENT_QUOTES, "UTF-8") ?></p><?php endif; ?>
        <section class="form-box staff-form-box">
            <form action="../controller/StaffController.php" method="post" enctype="multipart/form-data" class="staff-form">
                <input type="hidden" name="action" value="<?= $edit ? "edit_cat" : "add_cat" ?>"><?php if ($edit): ?><input type="hidden" name="cat_id" value="<?= (int) $edit["cat_id"] ?>"><?php endif; ?>
                <div class="staff-form-grid">
                    <div class="form-group"><label for="name">Name</label><input id="name" name="name" value="<?= htmlspecialchars($edit["name"] ?? "", ENT_QUOTES, "UTF-8") ?>" required></div>
                    <div class="form-group"><label for="breed">Breed</label><input id="breed" name="breed" value="<?= htmlspecialchars($edit["breed"] ?? "", ENT_QUOTES, "UTF-8") ?>"></div>
                    <div class="form-group"><label for="gender">Gender</label><select id="gender" name="gender" required><option value="Male" <?= ($edit["gender"] ?? "") === "Male" ? "selected" : "" ?>>Male</option><option value="Female" <?= ($edit["gender"] ?? "") === "Female" ? "selected" : "" ?>>Female</option></select></div>
                    <div class="form-group"><label for="age">Age</label><input type="number" min="0" step="0.1" id="age" name="age" value="<?= htmlspecialchars($edit["age"] ?? "", ENT_QUOTES, "UTF-8") ?>"></div>
                    <div class="form-group"><label for="color">Color</label><input id="color" name="color" value="<?= htmlspecialchars($edit["color"] ?? "", ENT_QUOTES, "UTF-8") ?>"></div>
                    <div class="form-group"><label for="health-status">Health Status</label><input id="health-status" name="health_status" value="<?= htmlspecialchars($edit["health_status"] ?? "", ENT_QUOTES, "UTF-8") ?>"></div>
                    <div class="form-group"><label for="adoption-status">Adoption Status</label><select id="adoption-status" name="adoption_status"><option value="Available" <?= ($edit["adoption_status"] ?? "Available") === "Available" ? "selected" : "" ?>>Available</option><option value="Adopted" <?= ($edit["adoption_status"] ?? "") === "Adopted" ? "selected" : "" ?>>Adopted</option><option value="Unavailable" <?= ($edit["adoption_status"] ?? "") === "Unavailable" ? "selected" : "" ?>>Unavailable</option></select></div>
                    <div class="form-group"><label for="image">Cat Image</label><input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"></div>
                    <div class="form-group staff-form-wide"><label for="description">Description</label><textarea id="description" name="description" rows="5"><?= htmlspecialchars($edit["description"] ?? "", ENT_QUOTES, "UTF-8") ?></textarea></div>
                </div>
                <button class="btn btn-primary" type="submit"><?= $edit ? "Save Changes" : "Publish Listing" ?></button><?php if ($edit): ?> <a class="btn btn-secondary" href="cats.php">Cancel Edit</a><?php endif; ?>
            </form>
        </section>
    </main>
</body>
</html>
