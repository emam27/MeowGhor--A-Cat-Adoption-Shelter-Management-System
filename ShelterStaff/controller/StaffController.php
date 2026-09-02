<?php

require_once __DIR__ . "/../model/StaffModel.php";
require_once __DIR__ . "/../../common/controller/AuthGuard.php";

requireRole("staff");

class StaffController
{
    private $model;

    public function __construct()
    {
        $this->model = new StaffModel();
    }

    public function getDashboardMetrics()
    {
        return $this->model->getDashboardMetrics();
    }

    public function getIntakes()
    {
        return $this->model->getIntakes();
    }

    public function getCats()
    {
        return $this->model->getCats();
    }

    public function getCatById($catId)
    {
        return $this->model->getCatById($catId);
    }

    public function getApplications()
    {
        return $this->model->getApplications();
    }

    public function processPost($post, $files)
    {
        $action = $post["action"] ?? "";
        if ($action === "add_cat") {
            $this->addCat($post, $files);
        } elseif ($action === "edit_cat") {
            $this->editCat($post, $files);
        } elseif ($action === "archive_cat") {
            $this->archiveCat($post);
        } elseif ($action === "review_intake") {
            $this->reviewIntake($post);
        } elseif ($action === "review_adoption") {
            $this->reviewAdoption($post);
        }
        $this->setErrorAndRedirect("Invalid staff action.", "/MeowGhor/ShelterStaff/view/dashboard.php");
    }

    private function addCat($post, $files)
    {
        $data = $this->validateCat($post, true);
        $upload = $this->handleImageUpload($files["image"] ?? null);
        if ($upload["error"] !== null) {
            $this->setErrorAndRedirect($upload["error"], "/MeowGhor/ShelterStaff/view/cats.php");
        }
        $data["image"] = $upload["path"];
        $data["added_by"] = (int) $_SESSION["user_id"];
        if (!$this->model->createCat($data)) {
            $this->removeUploadedFile($upload["full_path"]);
            $this->setErrorAndRedirect("Cat listing could not be created.", "/MeowGhor/ShelterStaff/view/cats.php");
        }
        $this->setMessageAndRedirect("Cat listing added successfully.", "/MeowGhor/ShelterStaff/view/cats.php");
    }

    private function editCat($post, $files)
    {
        $catId = filter_var($post["cat_id"] ?? null, FILTER_VALIDATE_INT);
        if (!$catId || $this->model->getCatById($catId) === null) {
            $this->setErrorAndRedirect("The selected cat was not found.", "/MeowGhor/ShelterStaff/view/cats.php");
        }
        $data = $this->validateCat($post, false);
        $upload = $this->handleImageUpload($files["image"] ?? null);
        if ($upload["error"] !== null) {
            $this->setErrorAndRedirect($upload["error"], "/MeowGhor/ShelterStaff/view/cats.php?edit_id=" . $catId);
        }
        $data["image"] = $upload["path"];
        if (!$this->model->updateCat($catId, $data)) {
            $this->removeUploadedFile($upload["full_path"]);
            $this->setErrorAndRedirect("Cat listing could not be updated.", "/MeowGhor/ShelterStaff/view/cats.php?edit_id=" . $catId);
        }
        $this->setMessageAndRedirect("Cat listing updated successfully.", "/MeowGhor/ShelterStaff/view/cats.php");
    }

    private function archiveCat($post)
    {
        $catId = filter_var($post["cat_id"] ?? null, FILTER_VALIDATE_INT);
        if (!$catId || !$this->model->archiveCat($catId)) {
            $this->setErrorAndRedirect("The cat could not be archived.", "/MeowGhor/ShelterStaff/view/view_cats.php");
        }
        $this->setMessageAndRedirect("Cat listing archived as Unavailable.", "/MeowGhor/ShelterStaff/view/view_cats.php");
    }

    private function reviewIntake($post)
    {
        $requestId = filter_var($post["request_id"] ?? null, FILTER_VALIDATE_INT);
        $status = $post["status"] ?? "";
        $comment = trim($post["staff_comment"] ?? "");
        if (!$requestId || !in_array($status, ["Accepted", "Rejected"], true)) {
            $this->setErrorAndRedirect("Invalid intake review.", "/MeowGhor/ShelterStaff/view/intakes.php");
        }
        if (!$this->model->reviewIntake($requestId, $status, $comment)) {
            $this->setErrorAndRedirect("Only pending intake requests can be reviewed.", "/MeowGhor/ShelterStaff/view/intakes.php");
        }
        $this->setMessageAndRedirect("Intake request " . strtolower($status) . ".", "/MeowGhor/ShelterStaff/view/intakes.php");
    }

    private function reviewAdoption($post)
    {
        $applicationId = filter_var($post["application_id"] ?? null, FILTER_VALIDATE_INT);
        $status = $post["status"] ?? "";
        $comment = trim($post["staff_comment"] ?? "");
        if (!$applicationId || !in_array($status, ["Approved", "Rejected"], true)) {
            $this->setErrorAndRedirect("Invalid application review.", "/MeowGhor/ShelterStaff/view/applications.php");
        }
        $result = $this->model->reviewApplication($applicationId, $status, $comment);
        if (!$result["success"]) {
            $this->setErrorAndRedirect($result["message"], "/MeowGhor/ShelterStaff/view/applications.php");
        }
        $this->setMessageAndRedirect($result["message"], "/MeowGhor/ShelterStaff/view/applications.php");
    }

    private function validateCat($post, $isNew)
    {
        $name = trim($post["name"] ?? "");
        $breed = trim($post["breed"] ?? "");
        $gender = $post["gender"] ?? "";
        $ageText = trim($post["age"] ?? "");
        $color = trim($post["color"] ?? "");
        $health = trim($post["health_status"] ?? "");
        $description = trim($post["description"] ?? "");
        $status = $post["adoption_status"] ?? "Available";

        if ($name === "") {
            $this->setErrorAndRedirect("Cat name is required.", "/MeowGhor/ShelterStaff/view/cats.php");
        }
        if (!in_array($gender, ["Male", "Female"], true)) {
            $this->setErrorAndRedirect("Please select a valid cat gender.", "/MeowGhor/ShelterStaff/view/cats.php");
        }
        if ($ageText !== "" && (!is_numeric($ageText) || (float) $ageText < 0)) {
            $this->setErrorAndRedirect("Age must be a non-negative number.", "/MeowGhor/ShelterStaff/view/cats.php");
        }
        if (!in_array($status, ["Available", "Adopted", "Unavailable"], true)) {
            $this->setErrorAndRedirect("Please select a valid adoption status.", "/MeowGhor/ShelterStaff/view/cats.php");
        }
        return [
            "name" => $name,
            "breed" => $breed,
            "gender" => $gender,
            "age" => $ageText === "" ? null : (float) $ageText,
            "color" => $color,
            "health_status" => $health,
            "description" => $description,
            "adoption_status" => $status
        ];
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
        $imageInfo = @getimagesize($file["tmp_name"]);
        if (!in_array($extension, ["jpg", "jpeg", "png", "webp"], true) || $imageInfo === false || !in_array($imageInfo["mime"], ["image/jpeg", "image/png", "image/webp"], true)) {
            return ["path" => null, "full_path" => null, "error" => "Only valid JPG, JPEG, PNG, and WEBP images are allowed."];
        }
        $directory = __DIR__ . "/../../uploads/cats/";
        if (!is_dir($directory) && !mkdir($directory, 0755, true)) {
            return ["path" => null, "full_path" => null, "error" => "The image upload folder could not be created."];
        }
        $filename = bin2hex(random_bytes(16)) . "." . $extension;
        $fullPath = $directory . $filename;
        if (!move_uploaded_file($file["tmp_name"], $fullPath)) {
            return ["path" => null, "full_path" => null, "error" => "The cat image could not be saved."];
        }
        return ["path" => "uploads/cats/" . $filename, "full_path" => $fullPath, "error" => null];
    }

    private function removeUploadedFile($path)
    {
        if ($path !== null && is_file($path)) {
            unlink($path);
        }
    }

    private function setErrorAndRedirect($message, $location)
    {
        $_SESSION["staff_error"] = $message;
        header("Location: " . $location);
        exit();
    }

    private function setMessageAndRedirect($message, $location)
    {
        $_SESSION["staff_message"] = $message;
        header("Location: " . $location);
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $controller = new StaffController();
    $controller->processPost($_POST, $_FILES);
}
