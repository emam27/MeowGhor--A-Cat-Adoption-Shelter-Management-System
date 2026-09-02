<?php

require_once __DIR__ . "/../model/AdopterModel.php";
require_once __DIR__ . "/../../common/controller/AuthGuard.php";

requireRole("adopter");

/**
 * Shared controller for future Adopter actions, including Browse Cats.
 *
 * Browse Cats validation and filtering will be added here when MySQL is
 * connected. No database results are returned during the development phase.
 */
class AdopterController
{
    private $adopterModel;

    public function __construct()
    {
        $this->adopterModel = new AdopterModel();
    }

    public function getIntakeRequests($userId)
    {
        return $this->adopterModel->getIntakeRequestsByUser($userId);
    }

    public function submitIntake($userId, $post, $files)
    {
        $catName = trim($post["cat_name"] ?? "");
        $breed = trim($post["breed"] ?? "");
        $gender = $post["gender"] ?? "";
        $ageText = trim($post["age"] ?? "");
        $healthStatus = trim($post["health_status"] ?? "");
        $description = trim($post["description"] ?? "");
        $reason = trim($post["reason_for_intake"] ?? "");

        if ($catName === "") {
            $this->redirectWithError("Cat name is required.");
        }

        if (!in_array($gender, ["Male", "Female"], true)) {
            $this->redirectWithError("Please select a valid gender.");
        }

        if ($ageText !== "" && (!is_numeric($ageText) || (float) $ageText < 0)) {
            $this->redirectWithError("Age must be a number greater than or equal to 0.");
        }

        if ($breed === "" || $healthStatus === "" || $description === "") {
            $this->redirectWithError("Please complete the cat details.");
        }

        if ($reason === "") {
            $this->redirectWithError("Reason for intake is required.");
        }

        $age = $ageText === "" ? null : (float) $ageText;
        $imageUpload = $this->handleImageUpload($files["cat_image"] ?? null);

        if ($imageUpload["error"] !== null) {
            $this->redirectWithError($imageUpload["error"]);
        }

        $created = $this->adopterModel->createIntakeRequest(
            $userId,
            $catName,
            $breed,
            $gender,
            $age,
            $healthStatus,
            $description,
            $reason,
            $imageUpload["path"]
        );

        if (!$created) {
            if ($imageUpload["full_path"] !== null && is_file($imageUpload["full_path"])) {
                unlink($imageUpload["full_path"]);
            }

            $this->redirectWithError("The intake request could not be submitted.");
        }

        $_SESSION["auth_message"] = "Intake request submitted successfully.";
        header("Location: /MeowGhor/Adopter/view/intakes.php");
        exit();
    }

    public function cancelIntake($userId, $post)
    {
        $requestId = (int) ($post["request_id"] ?? 0);

        if ($requestId <= 0 || !$this->adopterModel->cancelPendingIntakeRequest($requestId, $userId)) {
            $this->redirectWithError("Only your pending intake requests can be cancelled.");
        }

        $_SESSION["auth_message"] = "Intake request cancelled.";
        header("Location: /MeowGhor/Adopter/view/intakes.php");
        exit();
    }

    private function redirectWithError($message)
    {
        $_SESSION["auth_error"] = $message;
        header("Location: /MeowGhor/Adopter/view/intakes.php");
        exit();
    }

    private function handleImageUpload($file)
    {
        if ($file === null || ($file["error"] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return ["path" => null, "full_path" => null, "error" => null];
        }

        if (($file["error"] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return ["path" => null, "full_path" => null, "error" => "The cat image could not be uploaded."];
        }

        $extension = strtolower(pathinfo($file["name"] ?? "", PATHINFO_EXTENSION));
        $allowedExtensions = ["jpg", "jpeg", "png", "webp"];

        if (!in_array($extension, $allowedExtensions, true)) {
            return ["path" => null, "full_path" => null, "error" => "Only JPG, JPEG, PNG, and WEBP images are allowed."];
        }

        $imageInfo = @getimagesize($file["tmp_name"]);
        $allowedMimeTypes = ["image/jpeg", "image/png", "image/webp"];

        if ($imageInfo === false || !in_array($imageInfo["mime"], $allowedMimeTypes, true)) {
            return ["path" => null, "full_path" => null, "error" => "Please upload a valid cat image."];
        }

        $uploadDirectory = __DIR__ . "/../../uploads/cats/";

        if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0755, true)) {
            return ["path" => null, "full_path" => null, "error" => "The image upload folder could not be created."];
        }

        $filename = bin2hex(random_bytes(16)) . "." . $extension;
        $targetPath = $uploadDirectory . $filename;

        if (!move_uploaded_file($file["tmp_name"], $targetPath)) {
            return ["path" => null, "full_path" => null, "error" => "The cat image could not be saved."];
        }

        return [
            "path" => "uploads/cats/" . $filename,
            "full_path" => $targetPath,
            "error" => null
        ];
    }

    // Future action: submit_application for a selected cat.
    // Future action: withdraw_application for pending applications only.

    // Future action: submit_intake for a new cat intake request.
    // Future action: cancel_intake for pending intake requests only.

    // Future action: view_cat_details after receiving and validating cat_id.
}

$adopterController = new AdopterController();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";

    if ($action === "submit_intake") {
        $adopterController->submitIntake($_SESSION["user_id"], $_POST, $_FILES);
    }

    if ($action === "cancel_intake") {
        $adopterController->cancelIntake($_SESSION["user_id"], $_POST);
    }

    header("Location: /MeowGhor/Adopter/view/intakes.php");
    exit();
}
