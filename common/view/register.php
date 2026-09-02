<?php

session_start();

$error = $_SESSION["auth_error"] ?? "";
$message = $_SESSION["auth_message"] ?? "";
$old = $_SESSION["auth_old"] ?? [];

unset($_SESSION["auth_error"]);
unset($_SESSION["auth_message"]);
unset($_SESSION["auth_old"]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | MeowGhor</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <main class="auth-container">
        <section class="auth-card" aria-labelledby="register-title">
            <div class="brand">
                <div class="brand-icon" aria-hidden="true">🐾</div>
                <h1>MeowGhor</h1>
                <p>A home for every cat.</p>
            </div>

            <h2 class="auth-title" id="register-title">Create an Account</h2>

            <?php if ($error !== ""): ?>
                <p class="error-message" role="alert"><?php echo htmlspecialchars($error, ENT_QUOTES, "UTF-8"); ?></p>
            <?php endif; ?>

            <?php if ($message !== ""): ?>
                <p class="info-message" role="status"><?php echo htmlspecialchars($message, ENT_QUOTES, "UTF-8"); ?></p>
            <?php endif; ?>

            <form class="auth-form" action="../controller/AuthController.php" method="POST">
                <input type="hidden" name="action" value="register">

                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($old["name"] ?? "", ENT_QUOTES, "UTF-8"); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($old["email"] ?? "", ENT_QUOTES, "UTF-8"); ?>" required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($old["phone"] ?? "", ENT_QUOTES, "UTF-8"); ?>" required>
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" rows="3" required><?php echo htmlspecialchars($old["address"] ?? "", ENT_QUOTES, "UTF-8"); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-container">
                        <input type="password" id="password" name="password" required>
                        <button class="password-toggle" type="button" data-toggle-password="password">Show</button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <div class="password-container">
                        <input type="password" id="confirm_password" name="confirm_password" required>
                        <button class="password-toggle" type="button" data-toggle-password="confirm_password">Show</button>
                    </div>
                </div>

                <button class="auth-btn" type="submit">Register</button>
            </form>

            <p class="auth-link">Already have an account? <a href="login.php">Login</a></p>
        </section>
    </main>

    <script src="assets/js/app.js"></script>
</body>
</html>
