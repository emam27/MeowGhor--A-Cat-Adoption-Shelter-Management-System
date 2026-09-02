<?php

require_once __DIR__ . "/../model/AdopterModel.php";
require_once __DIR__ . "/../../common/controller/AuthGuard.php";

requireRole("adopter");

class AdopterController
{
    private $adopterModel;

    public function __construct()
    {
        $this->adopterModel = new AdopterModel();
    }

    public function getCats($filters)
    {
        return $this->adopterModel->getAvailableCats($filters);
    }

    public function getCat($catId)
    {
        return $this->adopterModel->getCatById($catId);
    }

    public function getAvailableCat($catId)
    {
        return $this->adopterModel->getAvailableCatById($catId);
    }

    public function getIntakeRequests($userId)
    {
        return $this->adopterModel->getIntakeRequestsByUser($userId);
    }

    public function getApplications($userId)
    {
        return $this->adopterModel->getApplicationsByUser($userId);
    }

    public function getDashboardCounts($userId)
    {
        return $this->adopterModel->getDashboardCounts($userId);
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

        if ($catName === "" || $breed === "" || $healthStatus === "" || $description === "" || $reason === "") {
            $this->redirectIntakeError("Please complete all required intake fields.");
        }
        if (!in_array($gender, ["Male", "Female"], true)) {
            $this->redirectIntakeError("Please select a valid gender.");
        }
        if ($ageText !== "" && (!is_numeric($ageText) || (float) $ageText < 0)) {
            $this->redirectIntakeError("Age must be a non-negative number.");
        }

        $imageUpload = $this->handleImageUpload($files["cat_image"] ?? null);
        if ($imageUpload["error"] !== null) {
            $this->redirectIntakeError($imageUpload["error"]);
        }

        $age = $ageText === "" ? null : (float) $ageText;
        if (!$this->adopterModel->createIntakeRequest($userId, $catName, $breed, $gender, $age, $healthStatus, $description, $reason, $imageUpload["path"])) {
            $this->removeUploadedFile($imageUpload["full_path"]);
            $this->redirectIntakeError("The intake request could not be submitted.");
        }

        $_SESSION["auth_message"] = "Intake request submitted successfully.";
        header("Location: /MeowGhor/Adopter/view/intakes.php");
        exit();
    }

    public function cancelIntake($userId, $post)
    {
        $requestId = filter_var($post["request_id"] ?? null, FILTER_VALIDATE_INT);
        if (!$requestId || !$this->adopterModel->cancelPendingIntakeRequest($requestId, $userId)) {
            $this->redirectIntakeError("Only your pending intake requests can be cancelled.");
        }
        $_SESSION["auth_message"] = "Intake request cancelled.";
        header("Location: /MeowGhor/Adopter/view/intakes.php");
        exit();
    }

    public function submitApplication($userId, $post)
    {
        $catId = filter_var($post["cat_id"] ?? null, FILTER_VALIDATE_INT);
        $reason = trim($post["reason"] ?? "");
        $livingSituation = trim($post["living_situation"] ?? "");
        $returnUrl = "/MeowGhor/Adopter/view/applications.php" . ($catId ? "?cat_id=" . $catId : "");

        if (!$catId || $reason === "" || $livingSituation === "") {
            $this->redirectApplicationError("Please complete all application fields.", $returnUrl);
        }
        if ($this->adopterModel->getAvailableCatById($catId) === null) {
            $this->redirectApplicationError("That cat is no longer available for adoption.", "/MeowGhor/Adopter/view/cats.php");
        }
        if ($this->adopterModel->hasPendingApplication($userId, $catId)) {
            $this->redirectApplicationError("You already have a pending application for this cat.", $returnUrl);
        }
        if (!$this->adopterModel->createApplication($userId, $catId, $reason, $livingSituation)) {
            $this->redirectApplicationError("The adoption application could not be submitted.", $returnUrl);
        }

        $_SESSION["auth_message"] = "Adoption application submitted successfully.";
        header("Location: /MeowGhor/Adopter/view/applications.php");
        exit();
    }

    public function withdrawApplication($userId, $post)
    {
        $applicationId = filter_var($post["application_id"] ?? null, FILTER_VALIDATE_INT);
        if (!$applicationId || !$this->adopterModel->withdrawPendingApplication($applicationId, $userId)) {
            $this->redirectApplicationError("Only your pending applications can be withdrawn.", "/MeowGhor/Adopter/view/applications.php");
        }
        $_SESSION["auth_message"] = "Adoption application withdrawn.";
        header("Location: /MeowGhor/Adopter/view/applications.php");
        exit();
    }

    private function redirectIntakeError($message)
    {
        $_SESSION["auth_error"] = $message;
        header("Location: /MeowGhor/Adopter/view/intakes.php");
        exit();
    }

    private function redirectApplicationError($message, $url)
    {
        $_SESSION["auth_error"] = $message;
        header("Location: " . $url);
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
        $imageInfo = @getimagesize($file["tmp_name"]);
        $allowedMimeTypes = ["image/jpeg", "image/png", "image/webp"];
        if (!in_array($extension, $allowedExtensions, true) || $imageInfo === false || !in_array($imageInfo["mime"], $allowedMimeTypes, true)) {
            return ["path" => null, "full_path" => null, "error" => "Only valid JPG, JPEG, PNG, and WEBP images are allowed."];
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
        return ["path" => "uploads/cats/" . $filename, "full_path" => $targetPath, "error" => null];
    }

    private function removeUploadedFile($path)
    {
        if ($path !== null && is_file($path)) {
            unlink($path);
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $adopterController = new AdopterController();
    $action = $_POST["action"] ?? "";
    if ($action === "submit_intake") {
        $adopterController->submitIntake((int) $_SESSION["user_id"], $_POST, $_FILES);
    } elseif ($action === "cancel_intake") {
        $adopterController->cancelIntake((int) $_SESSION["user_id"], $_POST);
    } elseif ($action === "submit_application") {
        $adopterController->submitApplication((int) $_SESSION["user_id"], $_POST);
    } elseif ($action === "withdraw_application") {
        $adopterController->withdrawApplication((int) $_SESSION["user_id"], $_POST);
    }
    header("Location: /MeowGhor/Adopter/view/dashboard.php");
    exit();
}
