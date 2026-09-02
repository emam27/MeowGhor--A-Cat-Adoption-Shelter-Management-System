<?php

require_once __DIR__ . "/../model/AuthModel.php";

session_start();

$action = $_POST["action"] ?? $_GET["action"] ?? "";

if ($action === "logout") {
    session_unset();
    session_destroy();
    header("Location: /MeowGhor/common/view/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: /MeowGhor/common/view/login.php");
    exit();
}

$authModel = new AuthModel();

if ($action === "login") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    $_SESSION["auth_old"] = ["email" => $email];

    if ($email === "" || $password === "") {
        $_SESSION["auth_error"] = "Please enter your email and password.";
    } elseif (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $_SESSION["auth_error"] = "Please enter a valid email address.";
    } else {
        $user = $authModel->findUserByEmail($email);

        if ($user === null || !password_verify($password, $user["password"])) {
            $_SESSION["auth_error"] = "Invalid email or password.";
        } elseif (!in_array($user["user_type"], ["adopter", "staff"], true)) {
            $_SESSION["auth_error"] = "This account has an invalid role.";
        } else {
            session_regenerate_id(true);
            $_SESSION["user_id"] = (int) $user["user_id"];
            $_SESSION["name"] = $user["name"];
            $_SESSION["email"] = $user["email"];
            $_SESSION["user_type"] = $user["user_type"];
            unset($_SESSION["auth_old"]);

            $dashboard = $user["user_type"] === "staff"
                ? "/MeowGhor/ShelterStaff/view/dashboard.php"
                : "/MeowGhor/Adopter/view/dashboard.php";
            header("Location: " . $dashboard);
            exit();
        }
    }

    header("Location: /MeowGhor/common/view/login.php");
    exit();
}

if ($action === "register") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $address = trim($_POST["address"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";

    $_SESSION["auth_old"] = compact("name", "email", "phone", "address");

    if ($name === "" || $email === "" || $phone === "" || $address === "" || $password === "" || $confirmPassword === "") {
        $_SESSION["auth_error"] = "Please fill in all fields.";
    } elseif (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $_SESSION["auth_error"] = "Please enter a valid email address.";
    } elseif (strlen($password) < 6) {
        $_SESSION["auth_error"] = "Password must be at least 6 characters.";
    } elseif ($password !== $confirmPassword) {
        $_SESSION["auth_error"] = "Passwords do not match.";
    } elseif ($authModel->emailExists($email)) {
        $_SESSION["auth_error"] = "An account with this email already exists.";
    } elseif (!$authModel->createAdopter($name, $email, password_hash($password, PASSWORD_DEFAULT), $phone, $address)) {
        $_SESSION["auth_error"] = "Registration failed. Please try again.";
    } else {
        $_SESSION["auth_message"] = "Registration successful. Please login.";
        unset($_SESSION["auth_old"]);
        header("Location: /MeowGhor/common/view/login.php");
        exit();
    }

    header("Location: /MeowGhor/common/view/register.php");
    exit();
}

header("Location: /MeowGhor/common/view/login.php");
exit();
