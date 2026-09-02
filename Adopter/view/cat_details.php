<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cat Details | MeowGhor</title>
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

    <main class="adopter-dashboard-main cat-details-page-main">
        <section class="cat-details-empty-state" aria-labelledby="cat-details-empty-heading">
            <h1 id="cat-details-empty-heading">No cat selected.</h1>
            <p>Please return to Browse Cats and choose a cat to view its details.</p>
            <a class="cat-back-button" href="cats.php">Back to Browse Cats</a>
        </section>

        <!--
            Future flow: cat_details.php?cat_id=... loads a real cat through
            AdopterController and AdopterModel. No cat ID or cat data is used here.
        -->
        <section class="cat-details-card" hidden aria-labelledby="cat-details-heading">
            <div class="cat-details-image-wrap">
                <img class="cat-details-image" src="" alt="">
            </div>

            <div class="cat-details-content">
                <h1 id="cat-details-heading" class="cat-details-name"></h1>
                <p class="cat-details-breed"></p>

                <dl class="cat-details-facts">
                    <div>
                        <dt>Gender</dt>
                        <dd class="cat-details-gender"></dd>
                    </div>
                    <div>
                        <dt>Age</dt>
                        <dd class="cat-details-age"></dd>
                    </div>
                    <div>
                        <dt>Color</dt>
                        <dd class="cat-details-color"></dd>
                    </div>
                    <div>
                        <dt>Health Status</dt>
                        <dd class="cat-details-health"></dd>
                    </div>
                    <div>
                        <dt>Adoption Status</dt>
                        <dd class="cat-details-status"></dd>
                    </div>
                </dl>

                <div class="cat-details-description">
                    <h2>Description</h2>
                    <p class="cat-details-description-text"></p>
                </div>

                <a class="cat-details-apply-button" href="applications.php?cat_id=">Apply for Adoption</a>
            </div>
        </section>
    </main>
</body>
</html>
