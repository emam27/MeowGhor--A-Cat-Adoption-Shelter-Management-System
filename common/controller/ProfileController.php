<?php

require_once __DIR__ . "/../model/ProfileModel.php";
require_once __DIR__ . "/AuthGuard.php";

requireLogin();

class ProfileController
{
    private $profileModel;

    public function __construct()
    {
        $this->profileModel = new ProfileModel();
    }

    public function getProfile($userId)
    {
        return $this->profileModel->getUserById($userId);
    }

    public function processPost($userId, $post)
    {
        $action = $post["action"] ?? "";
        $userType = $_SESSION["user_type"] ?? "";

        if ($action === "update_profile") {
            $name = trim($post["name"] ?? "");

            if ($name === "") {
                $this->redirectWithError("Name is required.");
            }

            if ($userType === "staff") {
                if (!$this->profileModel->updateName($userId, $name)) {
                    $this->redirectWithError("Profile could not be updated.");
                }

                $_SESSION["name"] = $name;
                $_SESSION["profile_message"] = "Name updated successfully.";
                header("Location: /MeowGhor/common/view/profile.php");
                exit();
            }

            if ($userType !== "adopter") {
                $this->redirectWithError("This account role cannot update profile details.");
            }

            $email = trim($post["email"] ?? "");
            $phone = trim($post["phone"] ?? "");
            $address = trim($post["address"] ?? "");

            if ($email === "") {
                $this->redirectWithError("Email is required.");
            }
            if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                $this->redirectWithError("Please enter a valid email address.");
            }
            if ($this->profileModel->emailTakenByAnotherUser($email, $userId)) {
                $this->redirectWithError("Another account already uses that email address.");
            }
            if (!$this->profileModel->updateAdopterProfile($userId, $name, $email, $phone, $address)) {
                $this->redirectWithError("Profile could not be updated.");
            }

            $_SESSION["name"] = $name;
            $_SESSION["email"] = $email;
            $_SESSION["profile_message"] = "Profile updated successfully.";
            header("Location: /MeowGhor/common/view/profile.php");
            exit();
        }

        if ($userType === "staff" && in_array($action, ["change_email", "update_email", "update_contact"], true)) {
            $this->redirectWithError("Staff accounts cannot change email or contact details.");
        }

        if ($action === "change_password") {
            $current = $post["current_password"] ?? "";
            $new = $post["new_password"] ?? "";
            $confirm = $post["confirm_new_password"] ?? "";
            $user = $this->profileModel->getUserById($userId);

            if ($user === null || !password_verify($current, $user["password"])) {
                $this->redirectWithError("Your current password is incorrect.");
            }
            if (strlen($new) < 6) {
                $this->redirectWithError("New password must be at least 6 characters.");
            }
            if ($new !== $confirm) {
                $this->redirectWithError("New password confirmation does not match.");
            }
            if (!$this->profileModel->updatePassword($userId, password_hash($new, PASSWORD_DEFAULT))) {
                $this->redirectWithError("Password could not be updated.");
            }

            $_SESSION["profile_message"] = "Password changed successfully.";
            header("Location: /MeowGhor/common/view/profile.php");
            exit();
        }

        $this->redirectWithError("Invalid profile action.");
    }

    private function redirectWithError($message)
    {
        $_SESSION["profile_error"] = $message;
        header("Location: /MeowGhor/common/view/profile.php");
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $controller = new ProfileController();
    $controller->processPost((int) $_SESSION["user_id"], $_POST);
}
