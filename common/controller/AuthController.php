<?php

require_once __DIR__ . "/../model/AuthModel.php";

session_start();

$action = $_POST["action"] ?? $_GET["action"] ?? "";

if ($action === "logout") {
    session_unset();
    session_destroy();
    header("Location: ../view/login.php");
    exit();
}

$authModel = new AuthModel();

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

    $user = $authModel->findUserByEmail($email);

    if ($user === null || !password_verify($password, $user["password"])) {
        $_SESSION["auth_error"] = "Invalid email or password.";
        $_SESSION["auth_old"] = ["email" => $email];
        header("Location: ../view/login.php");
        exit();
    }

    if ($user["user_type"] === "adopter") {
        $_SESSION["user_id"] = $user["user_id"];
        $_SESSION["name"] = $user["name"];
        $_SESSION["email"] = $user["email"];
        $_SESSION["user_type"] = $user["user_type"];

        unset($_SESSION["auth_old"]);
        header("Location: /MeowGhor/Adopter/view/dashboard.php");
        exit();
    }

    // NEXT INTEGRATION STEP: Add the verified Shelter Staff dashboard route.
    // Do not add a Staff account or invent a Staff dashboard in this flow.
    $_SESSION["auth_error"] = "Invalid email or password.";
    $_SESSION["auth_old"] = ["email" => $email];
    header("Location: ../view/login.php");
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

    if ($authModel->emailExists($email)) {
        $_SESSION["auth_error"] = "An account with this email already exists.";
        header("Location: ../view/register.php");
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $created = $authModel->createAdopter($name, $email, $hashedPassword, $phone, $address);

    if (!$created) {
        $_SESSION["auth_error"] = "Registration failed. Please try again.";
        header("Location: ../view/register.php");
        exit();
    }

    $_SESSION["auth_message"] = "Registration successful. Please login.";
    unset($_SESSION["auth_old"]);
    header("Location: ../view/login.php");
    exit();
}

header("Location: ../view/login.php");
exit();
