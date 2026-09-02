<?php
require_once __DIR__ . '/../model/StaffModel.php';

class StaffController {
    private $model;

    public function __construct() {
        $this->model = new StaffModel();
    }

    public function processActions() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['action'])) return;
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        
        $action = $_POST['action'];

        if ($action === 'review_intake') {
            $this->model->updateIntakeStatus($_POST['request_id'], $_POST['status'], $_POST['staff_comment']);
            
            // If accepted, automatically push the cat straight into your active available inventory listings
            if ($_POST['status'] === 'Accepted') {
                $this->model->createCatListing($_POST['cat_name'], $_POST['breed'], $_POST['gender'], $_POST['age'], 'Mixed', 'Healthy', $_POST['desc'], $_POST['image'], $_SESSION['user_id'], $_POST['request_id']);
            }
            header("Location: intakes.php?msg=processed");
            exit();
        }

        if ($action === 'add_cat') {
            $imgName = 'default-cat.png';
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $imgName = time() . '_' . basename($_FILES['image']['name']);
                move_uploaded_file($_FILES['image']['tmp_name'], '../../uploads/cats/' . $imgName);
            }
            $this->model->createCatListing($_POST['name'], $_POST['breed'], $_POST['gender'], $_POST['age'], $_POST['color'], $_POST['health_status'], $_POST['description'], $imgName, $_SESSION['user_id']);
            header("Location: cats.php?msg=added");
            exit();
        }

        if ($action === 'edit_cat') {
            $imgName = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $imgName = time() . '_' . basename($_FILES['image']['name']);
                move_uploaded_file($_FILES['image']['tmp_name'], '../../uploads/cats/' . $imgName);
            }
            $this->model->updateCatListing($_POST['cat_id'], $_POST['name'], $_POST['breed'], $_POST['gender'], $_POST['age'], $_POST['color'], $_POST['health_status'], $_POST['description'], $imgName, $_POST['adoption_status']);
            header("Location: cats.php?msg=updated");
            exit();
        }

        if ($action === 'review_adoption') {
            $this->model->updateApplicationStatus($_POST['application_id'], $_POST['cat_id'], $_POST['status'], $_POST['staff_comment']);
            header("Location: applications.php?msg=evaluated");
            exit();
        }
    }

    public function fetchViewData($type, $id = null) {
        if ($type === 'metrics') return $this->model->getDashboardMetrics();
        if ($type === 'intakes') return $this->model->getIntakes();
        if ($type === 'cats') return $this->model->getCats();
        if ($type === 'cat_single') return $this->model->getCatById($id);
        if ($type === 'applications') return $this->model->getApplications();
    }
}
?>
