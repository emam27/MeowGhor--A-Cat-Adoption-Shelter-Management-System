<?php

function requireLogin()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION["user_id"], $_SESSION["user_type"]) || (int) $_SESSION["user_id"] <= 0) {
        header("Location: /MeowGhor/common/view/login.php");
        exit();
    }
}

function requireRole($role)
{
    requireLogin();

    if ($_SESSION["user_type"] !== $role) {
        header("Location: /MeowGhor/common/view/login.php");
        exit();
    }
}
