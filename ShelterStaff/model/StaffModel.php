<?php

require_once __DIR__ . "/../../config/database.php";

class StaffModel
{
    private $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    public function getDashboardMetrics()
    {
        return [
            "total_cats" => $this->count("SELECT COUNT(*) AS total FROM cats"),
            "available_cats" => $this->count("SELECT COUNT(*) AS total FROM cats WHERE adoption_status = 'Available'"),
            "pending_intakes" => $this->count("SELECT COUNT(*) AS total FROM cat_intake_requests WHERE request_status = 'Pending'"),
            "pending_applications" => $this->count("SELECT COUNT(*) AS total FROM adoption_applications WHERE application_status = 'Pending'")
        ];
    }

    public function getIntakes()
    {
        return $this->selectRows("SELECT i.request_id, i.cat_name, i.breed, i.gender, i.age, i.health_status,
                i.description, i.reason, i.image, i.request_status, i.staff_comment, i.submitted_at, i.reviewed_at,
                u.name AS user_name, u.email AS user_email, u.phone AS user_phone
            FROM cat_intake_requests i
            INNER JOIN users u ON i.user_id = u.user_id
            ORDER BY i.submitted_at DESC");
    }

    public function reviewIntake($requestId, $status, $comment)
    {
        $stmt = $this->conn->prepare("UPDATE cat_intake_requests
            SET request_status = ?, staff_comment = ?, reviewed_at = NOW()
            WHERE request_id = ? AND request_status = 'Pending'");
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param("ssi", $status, $comment, $requestId);
        $updated = $stmt->execute() && $stmt->affected_rows > 0;
        $stmt->close();
        return $updated;
    }

    public function getCats()
    {
        return $this->selectRows("SELECT c.*, u.name AS staff_name
            FROM cats c INNER JOIN users u ON c.added_by = u.user_id
            ORDER BY c.created_at DESC");
    }

    public function getCatById($catId)
    {
        $rows = $this->selectRows("SELECT * FROM cats WHERE cat_id = ? LIMIT 1", "i", [$catId]);
        return $rows[0] ?? null;
    }

    public function createCat($data)
    {
        if ($data["age"] === null) {
            $stmt = $this->conn->prepare("INSERT INTO cats
                (name, breed, gender, age, color, health_status, description, image, adoption_status, added_by, intake_request_id)
                VALUES (?, ?, ?, NULL, ?, ?, ?, ?, ?, ?, NULL)");
            if (!$stmt) {
                return false;
            }
            $stmt->bind_param("ssssssssi", $data["name"], $data["breed"], $data["gender"], $data["color"], $data["health_status"], $data["description"], $data["image"], $data["adoption_status"], $data["added_by"]);
        } else {
            $stmt = $this->conn->prepare("INSERT INTO cats
                (name, breed, gender, age, color, health_status, description, image, adoption_status, added_by, intake_request_id)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NULL)");
            if (!$stmt) {
                return false;
            }
            $stmt->bind_param("sssdsssssi", $data["name"], $data["breed"], $data["gender"], $data["age"], $data["color"], $data["health_status"], $data["description"], $data["image"], $data["adoption_status"], $data["added_by"]);
        }
        $created = $stmt->execute();
        $stmt->close();
        return $created;
    }

    public function updateCat($catId, $data)
    {
        if ($data["image"] !== null && $data["age"] !== null) {
            $stmt = $this->conn->prepare("UPDATE cats SET name = ?, breed = ?, gender = ?, age = ?, color = ?,
                health_status = ?, description = ?, image = ?, adoption_status = ? WHERE cat_id = ?");
            if (!$stmt) {
                return false;
            }
            $stmt->bind_param("sssdsssssi", $data["name"], $data["breed"], $data["gender"], $data["age"], $data["color"], $data["health_status"], $data["description"], $data["image"], $data["adoption_status"], $catId);
        } elseif ($data["image"] === null && $data["age"] !== null) {
            $stmt = $this->conn->prepare("UPDATE cats SET name = ?, breed = ?, gender = ?, age = ?, color = ?,
                health_status = ?, description = ?, adoption_status = ? WHERE cat_id = ?");
            if (!$stmt) {
                return false;
            }
            $stmt->bind_param("sssdssssi", $data["name"], $data["breed"], $data["gender"], $data["age"], $data["color"], $data["health_status"], $data["description"], $data["adoption_status"], $catId);
        } elseif ($data["image"] !== null) {
            $stmt = $this->conn->prepare("UPDATE cats SET name = ?, breed = ?, gender = ?, age = NULL, color = ?,
                health_status = ?, description = ?, image = ?, adoption_status = ? WHERE cat_id = ?");
            if (!$stmt) {
                return false;
            }
            $stmt->bind_param("ssssssssi", $data["name"], $data["breed"], $data["gender"], $data["color"], $data["health_status"], $data["description"], $data["image"], $data["adoption_status"], $catId);
        } else {
            $stmt = $this->conn->prepare("UPDATE cats SET name = ?, breed = ?, gender = ?, age = NULL, color = ?,
                health_status = ?, description = ?, adoption_status = ? WHERE cat_id = ?");
            if (!$stmt) {
                return false;
            }
            $stmt->bind_param("sssssssi", $data["name"], $data["breed"], $data["gender"], $data["color"], $data["health_status"], $data["description"], $data["adoption_status"], $catId);
        }
        $updated = $stmt->execute();
        $stmt->close();
        return $updated;
    }

    public function archiveCat($catId)
    {
        $stmt = $this->conn->prepare("UPDATE cats SET adoption_status = 'Unavailable' WHERE cat_id = ? AND adoption_status = 'Available'");
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param("i", $catId);
        $archived = $stmt->execute() && $stmt->affected_rows > 0;
        $stmt->close();
        return $archived;
    }

    public function getApplications()
    {
        return $this->selectRows("SELECT a.application_id, a.user_id, a.cat_id, a.reason, a.living_situation,
                a.application_status, a.staff_comment, a.applied_at, a.reviewed_at,
                u.name AS adopter_name, u.email AS adopter_email, c.name AS cat_name, c.adoption_status
            FROM adoption_applications a
            INNER JOIN users u ON a.user_id = u.user_id
            INNER JOIN cats c ON a.cat_id = c.cat_id
            ORDER BY a.applied_at DESC");
    }

    public function reviewApplication($applicationId, $status, $comment)
    {
        if ($status === "Approved") {
            return $this->approveApplication($applicationId, $comment);
        }

        $stmt = $this->conn->prepare("UPDATE adoption_applications
            SET application_status = 'Rejected', staff_comment = ?, reviewed_at = NOW()
            WHERE application_id = ? AND application_status = 'Pending'");
        if (!$stmt) {
            return ["success" => false, "message" => "The application could not be reviewed."];
        }
        $stmt->bind_param("si", $comment, $applicationId);
        $success = $stmt->execute() && $stmt->affected_rows > 0;
        $stmt->close();
        return $success
            ? ["success" => true, "message" => "Application rejected."]
            : ["success" => false, "message" => "Only pending applications can be reviewed."];
    }

    private function approveApplication($applicationId, $comment)
    {
        $this->conn->begin_transaction();
        try {
            $applicationStmt = $this->conn->prepare("SELECT application_id, cat_id FROM adoption_applications
                WHERE application_id = ? AND application_status = 'Pending' FOR UPDATE");
            if (!$applicationStmt) {
                throw new Exception("Application could not be prepared.");
            }
            $applicationStmt->bind_param("i", $applicationId);
            $applicationStmt->execute();
            $application = $applicationStmt->get_result()->fetch_assoc();
            $applicationStmt->close();
            if ($application === null) {
                $this->conn->rollback();
                return ["success" => false, "message" => "Only pending applications can be approved."];
            }

            $catId = (int) $application["cat_id"];
            $catStmt = $this->conn->prepare("SELECT adoption_status FROM cats WHERE cat_id = ? FOR UPDATE");
            if (!$catStmt) {
                throw new Exception("Cat could not be prepared.");
            }
            $catStmt->bind_param("i", $catId);
            $catStmt->execute();
            $cat = $catStmt->get_result()->fetch_assoc();
            $catStmt->close();
            if ($cat === null || $cat["adoption_status"] !== "Available") {
                $this->conn->rollback();
                return ["success" => false, "message" => "This cat is no longer available for approval."];
            }

            $approveStmt = $this->conn->prepare("UPDATE adoption_applications
                SET application_status = 'Approved', staff_comment = ?, reviewed_at = NOW()
                WHERE application_id = ? AND application_status = 'Pending'");
            if (!$approveStmt) {
                throw new Exception("Approval could not be prepared.");
            }
            $approveStmt->bind_param("si", $comment, $applicationId);
            if (!$approveStmt->execute() || $approveStmt->affected_rows !== 1) {
                $approveStmt->close();
                throw new Exception("Approval could not be saved.");
            }
            $approveStmt->close();

            $catUpdateStmt = $this->conn->prepare("UPDATE cats SET adoption_status = 'Adopted'
                WHERE cat_id = ? AND adoption_status = 'Available'");
            if (!$catUpdateStmt) {
                throw new Exception("Cat update could not be prepared.");
            }
            $catUpdateStmt->bind_param("i", $catId);
            if (!$catUpdateStmt->execute() || $catUpdateStmt->affected_rows !== 1) {
                $catUpdateStmt->close();
                throw new Exception("Cat adoption status could not be saved.");
            }
            $catUpdateStmt->close();

            $otherComment = "Cat adopted by another applicant.";
            $otherStmt = $this->conn->prepare("UPDATE adoption_applications
                SET application_status = 'Rejected', staff_comment = ?, reviewed_at = NOW()
                WHERE cat_id = ? AND application_id <> ? AND application_status = 'Pending'");
            if (!$otherStmt) {
                throw new Exception("Other applications could not be prepared.");
            }
            $otherStmt->bind_param("sii", $otherComment, $catId, $applicationId);
            $otherStmt->execute();
            $otherStmt->close();

            $this->conn->commit();
            return ["success" => true, "message" => "Application approved and cat marked as adopted."];
        } catch (Throwable $exception) {
            $this->conn->rollback();
            return ["success" => false, "message" => "The approval could not be completed safely."];
        }
    }

    private function count($sql)
    {
        $rows = $this->selectRows($sql);
        return (int) ($rows[0]["total"] ?? 0);
    }

    private function selectRows($sql, $types = "", $values = [])
    {
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return [];
        }
        if ($types !== "") {
            $params = [$types];
            foreach ($values as $key => $value) {
                $params[] = &$values[$key];
            }
            call_user_func_array([$stmt, "bind_param"], $params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();
        return $rows;
    }
}
