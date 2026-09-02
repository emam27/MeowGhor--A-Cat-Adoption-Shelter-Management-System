<?php
require_once __DIR__ . '/../../config/database.php';

class StaffModel {
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    // Fetch total row counts to display on the staff dashboard metrics cards
    public function getDashboardMetrics() {
        return [
            'intakes' => $this->db->query("SELECT COUNT(*) FROM cat_intake_requests WHERE request_status='Pending'")->fetchColumn(),
            'cats' => $this->db->query("SELECT COUNT(*) FROM cats WHERE adoption_status='Available'")->fetchColumn(),
            'apps' => $this->db->query("SELECT COUNT(*) FROM adoption_applications WHERE application_status='Pending'")->fetchColumn()
        ];
    }

    // --- FEATURE 1: CAT INTAKE MANAGEMENT ---
    public function getIntakes() {
        return $this->db->query("SELECT i.*, u.name as user_name FROM cat_intake_requests i JOIN users u ON i.user_id = u.user_id ORDER BY i.request_id DESC")->fetchAll();
    }

    public function updateIntakeStatus($id, $status, $comment) {
        $stmt = $this->db->prepare("UPDATE cat_intake_requests SET request_status = ?, staff_comment = ? WHERE request_id = ?");
        return $stmt->execute([$status, $comment, $id]);
    }

    // --- FEATURE 2: CAT MANAGEMENT (CRUD) ---
    public function getCats() {
        return $this->db->query("SELECT * FROM cats ORDER BY cat_id DESC")->fetchAll();
    }

    public function getCatById($id) {
        $stmt = $this->db->prepare("SELECT * FROM cats WHERE cat_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function createCatListing($name, $breed, $gender, $age, $color, $health, $desc, $img, $staff_id, $intake_id = null) {
        $stmt = $this->db->prepare("INSERT INTO cats (name, breed, gender, age, color, health_status, description, image, adoption_status, added_by, intake_request_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Available', ?, ?)");
        return $stmt->execute([$name, $breed, $gender, $age, $color, $health, $desc, $img, $staff_id, $intake_id]);
    }

    public function updateCatListing($id, $name, $breed, $gender, $age, $color, $health, $desc, $img, $status) {
        if ($img) {
            $stmt = $this->db->prepare("UPDATE cats SET name=?, breed=?, gender=?, age=?, color=?, health_status=?, description=?, image=?, adoption_status=? WHERE cat_id=?");
            return $stmt->execute([$name, $breed, $gender, $age, $color, $health, $desc, $img, $status, $id]);
        } else {
            $stmt = $this->db->prepare("UPDATE cats SET name=?, breed=?, gender=?, age=?, color=?, health_status=?, description=?, adoption_status=? WHERE cat_id=?");
            return $stmt->execute([$name, $breed, $gender, $age, $color, $health, $desc, $status, $id]);
        }
    }

    public function deleteCatListing($id) {
        $stmt = $this->db->prepare("DELETE FROM cats WHERE cat_id = ?");
        return $stmt->execute([$id]);
    }

    // --- FEATURE 3: ADOPTION APPLICATIONS ---
    public function getApplications() {
        return $this->db->query("SELECT a.*, u.name as user_name, c.name as cat_name FROM adoption_applications a JOIN users u ON a.user_id = u.user_id JOIN cats c ON a.cat_id = c.cat_id ORDER BY a.application_id DESC")->fetchAll();
    }

    public function updateApplicationStatus($app_id, $cat_id, $status, $comment) {
        $stmt = $this->db->prepare("UPDATE adoption_applications SET application_status = ?, staff_comment = ? WHERE application_id = ?");
        $stmt->execute([$status, $comment, $app_id]);

        if ($status === 'Approved') {
            // Update the matching cat to Adopted status automatically
            $this->db->prepare("UPDATE cats SET adoption_status = 'Adopted' WHERE cat_id = ?")->execute([$cat_id]);
            
            // Auto-reject any other competing pending applications submitted for the same cat
            $this->db->prepare("UPDATE adoption_applications SET application_status = 'Rejected', staff_comment = 'Cat adopted by another applicant.' WHERE cat_id = ? AND application_id != ? AND application_status = 'Pending'")->execute([$cat_id, $app_id]);
        }
    }
}
?>
