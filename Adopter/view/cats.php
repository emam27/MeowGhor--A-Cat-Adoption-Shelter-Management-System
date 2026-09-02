<?php
require_once __DIR__ . '/../../common/controller/AuthGuard.php';
requireRole("adopter");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Cats | MeowGhor</title>
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

    <main class="adopter-dashboard-main cats-page-main">
        <section class="cats-page-intro" aria-labelledby="cats-heading">
            <h1 id="cats-heading">Available Cats</h1>
            <p>Find a cat that could be your new companion.</p>
        </section>

        <!-- Filtering will be connected through AdopterController and AdopterModel when MySQL is added. -->
        <form class="cat-filters" action="cats.php" method="get">
            <div class="cat-filter-field">
                <label for="cat-search">Search</label>
                <input type="search" id="cat-search" name="search" placeholder="Cat name or breed">
            </div>

            <div class="cat-filter-field">
                <label for="cat-gender">Gender:</label>
                <select id="cat-gender" name="gender">
                    <option value="">All</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select>
            </div>

            <div class="cat-filter-field">
                <label for="cat-age">Age:</label>
                <select id="cat-age" name="age">
                    <option value="">All Ages</option>
                    <option value="kitten">Kitten</option>
                    <option value="young">Young</option>
                    <option value="adult">Adult</option>
                    <option value="senior">Senior</option>
                </select>
            </div>

            <button class="cat-filter-button" type="submit">Apply Filters</button>
            <a class="cat-reset-button" href="cats.php">Reset</a>
        </form>

        <section class="cat-listings" aria-label="Cat listings">
            <div class="cat-empty-state">
                <h2>No cats are available yet.</h2>
                <p>Cat listings will appear here once they are added by Shelter Staff.</p>
            </div>
        </section>
    </main>
</body>
</html>
