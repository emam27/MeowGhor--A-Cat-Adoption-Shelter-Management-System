<?php

function requireRole($role)
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $isLoggedIn = isset($_SESSION["user_id"], $_SESSION["user_type"])
        && $_SESSION["user_id"] !== ""
        && $_SESSION["user_type"] !== "";

    if (!$isLoggedIn || $_SESSION["user_type"] !== $role) {
        header("Location: /MeowGhor/common/view/login.php");
        exit();
    }
}
