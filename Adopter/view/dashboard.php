<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adopter Dashboard | MeowGhor</title>
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

    <main class="adopter-dashboard-main">
        <section class="adopter-dashboard-intro" aria-labelledby="dashboard-heading">
            <h1 id="dashboard-heading">Welcome to MeowGhor</h1>
            <p>Manage your adoption and cat intake activities.</p>
        </section>

        <section class="adopter-summary-cards" aria-label="Dashboard summary">
            <article class="adopter-summary-card">
                <h2>Available Cats</h2>
                <p>--</p>
            </article>

            <article class="adopter-summary-card">
                <h2>My Applications</h2>
                <p>--</p>
            </article>

            <article class="adopter-summary-card">
                <h2>My Intake Requests</h2>
                <p>--</p>
            </article>
        </section>

        <section class="adopter-recent-activity" aria-labelledby="recent-activity-heading">
            <h2 id="recent-activity-heading">Recent Activity</h2>
            <p>No recent activity yet.</p>
            <p>Your adoption and intake updates will appear here.</p>
        </section>
    </main>
</body>
</html>
