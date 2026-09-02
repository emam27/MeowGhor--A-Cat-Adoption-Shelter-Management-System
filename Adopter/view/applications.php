<?php
require_once __DIR__ . '/../../common/controller/AuthGuard.php';
requireRole("adopter");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Adoption Applications | MeowGhor</title>
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

    <main class="adopter-dashboard-main applications-page-main">
        <section class="applications-page-intro" aria-labelledby="applications-heading">
            <h1 id="applications-heading">My Adoption Applications</h1>
            <p>Track your adoption applications and their current status.</p>
        </section>

        <section class="applications-empty-state" aria-labelledby="applications-empty-heading">
            <h2 id="applications-empty-heading">No adoption applications yet.</h2>
            <p>Browse available cats and choose a cat to start an adoption application.</p>
            <a class="applications-browse-button" href="cats.php">Browse Cats</a>
        </section>

        <!--
            Future flow: Browse Cats -> View Cat Details -> Apply for Adoption.
            The selected cat may later be passed as applications.php?cat_id=...
            and used to populate the form below. No cat ID is created here.
        -->
        <section class="adoption-application-form" hidden aria-labelledby="application-form-heading">
            <h2 id="application-form-heading">Apply for Adoption</h2>
            <form action="applications.php" method="post">
                <div class="application-form-field">
                    <label for="selected-cat">Selected Cat</label>
                    <input type="text" id="selected-cat" name="selected_cat" readonly>
                </div>

                <div class="application-form-field">
                    <label for="adoption-reason">Reason for Adoption</label>
                    <textarea id="adoption-reason" name="reason" rows="4"></textarea>
                </div>

                <div class="application-form-field">
                    <label for="living-situation">Living Situation</label>
                    <textarea id="living-situation" name="living_situation" rows="4"></textarea>
                </div>

                <button class="application-submit-button" type="submit">Submit Application</button>
            </form>
        </section>

        <!-- Future application list columns: Cat, Applied Date, Status, and Action. -->
        <section class="application-list" hidden aria-labelledby="application-list-heading">
            <h2 id="application-list-heading">Applications</h2>
            <div class="application-list-header">
                <span>Cat</span>
                <span>Applied Date</span>
                <span>Status</span>
                <span>Action</span>
            </div>
        </section>
    </main>
</body>
</html>
