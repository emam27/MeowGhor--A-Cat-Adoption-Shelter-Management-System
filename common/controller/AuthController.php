<?php

session_start();

$action = $_POST["action"] ?? $_GET["action"] ?? "";

if ($action === "logout") {
    session_unset();
    session_destroy();
    header("Location: ../view/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../view/login.php");
    exit();
}

if ($action === "login") {
    $email = trim($_POST["email"] ?? "");
    $password = trim($_POST["password"] ?? "");

    if ($email === "" || $password === "") {
        $_SESSION["auth_error"] = "Please enter your email and password.";
        $_SESSION["auth_old"] = ["email" => $email];
        header("Location: ../view/login.php");
        exit();
    }

    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $_SESSION["auth_error"] = "Please enter a valid email address.";
        $_SESSION["auth_old"] = ["email" => $email];
        header("Location: ../view/login.php");
        exit();
    }

    // DEVELOPMENT ONLY:
    // Database authentication is not connected yet.
    // Valid login form input temporarily redirects to the Adopter dashboard.
    // Replace this with database lookup, password_verify(), user_type checking,
    // and role-based dashboard redirect when MySQL is integrated.
    unset($_SESSION["auth_old"]);
    header("Location: ../../Adopter/view/dashboard.php");
    exit();
}

if ($action === "register") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $address = trim($_POST["address"] ?? "");
    $password = trim($_POST["password"] ?? "");
    $confirmPassword = trim($_POST["confirm_password"] ?? "");

    $_SESSION["auth_old"] = [
        "name" => $name,
        "email" => $email,
        "phone" => $phone,
        "address" => $address
    ];

    if ($name === "" || $email === "" || $phone === "" || $address === "" || $password === "" || $confirmPassword === "") {
        $_SESSION["auth_error"] = "Please fill in all fields.";
        header("Location: ../view/register.php");
        exit();
    }

    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $_SESSION["auth_error"] = "Please enter a valid email address.";
        header("Location: ../view/register.php");
        exit();
    }

    if (strlen($password) < 6) {
        $_SESSION["auth_error"] = "Password must be at least 6 characters.";
        header("Location: ../view/register.php");
        exit();
    }

    if ($password !== $confirmPassword) {
        $_SESSION["auth_error"] = "Passwords do not match.";
        header("Location: ../view/register.php");
        exit();
    }

    // During database integration, public registration will create
    // user_type = 'community_user'.
    // TEMPORARY: This only confirms form validation. No account is created yet.
    $_SESSION["auth_message"] = "Registration form is valid. Database registration will be added next.";
    unset($_SESSION["auth_old"]);
    header("Location: ../view/login.php");
    exit();
}

header("Location: ../view/login.php");
exit();
