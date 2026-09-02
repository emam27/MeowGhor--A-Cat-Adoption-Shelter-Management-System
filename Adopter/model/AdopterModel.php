<?php

require_once __DIR__ . "/../../config/database.php";

class AdopterModel
{
    private $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    public function getAvailableCats($filters = [])
    {
        $sql = "SELECT cat_id, name, breed, gender, age, color, health_status, description, image, adoption_status
                FROM cats WHERE adoption_status = 'Available'";
        $types = "";
        $values = [];

        $search = trim($filters["search"] ?? "");
        if ($search !== "") {
            $sql .= " AND (name LIKE ? OR breed LIKE ?)";
            $types .= "ss";
            $like = "%" . $search . "%";
            $values[] = $like;
            $values[] = $like;
        }

        if (in_array($filters["gender"] ?? "", ["Male", "Female"], true)) {
            $sql .= " AND gender = ?";
            $types .= "s";
            $values[] = $filters["gender"];
        }

        $ageGroup = $filters["age"] ?? "";
        $ageConditions = [
            "kitten" => "age < 1",
            "young" => "age >= 1 AND age < 4",
            "adult" => "age >= 4 AND age < 8",
            "senior" => "age >= 8"
        ];
        if (isset($ageConditions[$ageGroup])) {
            $sql .= " AND " . $ageConditions[$ageGroup];
        }

        $sql .= " ORDER BY created_at DESC";
        return $this->selectRows($sql, $types, $values);
    }

    public function getAvailableCatById($catId)
    {
        $rows = $this->selectRows(
            "SELECT cat_id, name, breed, gender, age, color, health_status, description, image, adoption_status
             FROM cats WHERE cat_id = ? AND adoption_status = 'Available' LIMIT 1",
            "i",
            [$catId]
        );
        return $rows[0] ?? null;
    }

    public function getCatById($catId)
    {
        $rows = $this->selectRows(
            "SELECT cat_id, name, breed, gender, age, color, health_status, description, image, adoption_status
             FROM cats WHERE cat_id = ? LIMIT 1",
            "i",
            [$catId]
        );
        return $rows[0] ?? null;
    }

    public function createIntakeRequest($userId, $catName, $breed, $gender, $age, $healthStatus, $description, $reason, $image)
    {
        if ($age === null) {
            $stmt = $this->conn->prepare("INSERT INTO cat_intake_requests
                (user_id, cat_name, breed, gender, age, health_status, description, reason, image, request_status)
                VALUES (?, ?, ?, ?, NULL, ?, ?, ?, ?, 'Pending')");
            if (!$stmt) {
                return false;
            }
            $stmt->bind_param("isssssss", $userId, $catName, $breed, $gender, $healthStatus, $description, $reason, $image);
        } else {
            $stmt = $this->conn->prepare("INSERT INTO cat_intake_requests
                (user_id, cat_name, breed, gender, age, health_status, description, reason, image, request_status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')");
            if (!$stmt) {
                return false;
            }
            $stmt->bind_param("isssdssss", $userId, $catName, $breed, $gender, $age, $healthStatus, $description, $reason, $image);
        }
        $created = $stmt->execute();
        $stmt->close();
        return $created;
    }

    public function getIntakeRequestsByUser($userId)
    {
        return $this->selectRows(
            "SELECT request_id, cat_name, submitted_at, request_status, staff_comment
             FROM cat_intake_requests WHERE user_id = ? ORDER BY submitted_at DESC",
            "i",
            [$userId]
        );
    }

    public function cancelPendingIntakeRequest($requestId, $userId)
    {
        $stmt = $this->conn->prepare("UPDATE cat_intake_requests SET request_status = 'Cancelled'
            WHERE request_id = ? AND user_id = ? AND request_status = 'Pending'");
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param("ii", $requestId, $userId);
        $updated = $stmt->execute() && $stmt->affected_rows > 0;
        $stmt->close();
        return $updated;
    }

    public function hasPendingApplication($userId, $catId)
    {
        $stmt = $this->conn->prepare("SELECT application_id FROM adoption_applications
            WHERE user_id = ? AND cat_id = ? AND application_status = 'Pending' LIMIT 1");
        if (!$stmt) {
            return true;
        }
        $stmt->bind_param("ii", $userId, $catId);
        $stmt->execute();
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();
        return $exists;
    }

    public function createApplication($userId, $catId, $reason, $livingSituation)
    {
        $stmt = $this->conn->prepare("INSERT INTO adoption_applications
            (user_id, cat_id, reason, living_situation, application_status)
            VALUES (?, ?, ?, ?, 'Pending')");
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param("iiss", $userId, $catId, $reason, $livingSituation);
        $created = $stmt->execute();
        $stmt->close();
        return $created;
    }

    public function getApplicationsByUser($userId)
    {
        return $this->selectRows(
            "SELECT a.application_id, a.cat_id, a.application_status, a.staff_comment, a.applied_at,
                    c.name AS cat_name, c.breed AS cat_breed
             FROM adoption_applications a
             INNER JOIN cats c ON a.cat_id = c.cat_id
             WHERE a.user_id = ? ORDER BY a.applied_at DESC",
            "i",
            [$userId]
        );
    }

    public function withdrawPendingApplication($applicationId, $userId)
    {
        $stmt = $this->conn->prepare("UPDATE adoption_applications SET application_status = 'Withdrawn'
            WHERE application_id = ? AND user_id = ? AND application_status = 'Pending'");
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param("ii", $applicationId, $userId);
        $updated = $stmt->execute() && $stmt->affected_rows > 0;
        $stmt->close();
        return $updated;
    }

    public function getDashboardCounts($userId)
    {
        return [
            "available_cats" => $this->count("SELECT COUNT(*) AS total FROM cats WHERE adoption_status = 'Available'"),
            "applications" => $this->count("SELECT COUNT(*) AS total FROM adoption_applications WHERE user_id = ?", "i", [$userId]),
            "intakes" => $this->count("SELECT COUNT(*) AS total FROM cat_intake_requests WHERE user_id = ?", "i", [$userId])
        ];
    }

    private function count($sql, $types = "", $values = [])
    {
        $rows = $this->selectRows($sql, $types, $values);
        return (int) ($rows[0]["total"] ?? 0);
    }

    private function selectRows($sql, $types = "", $values = [])
    {
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return [];
        }
        if ($types !== "") {
            $this->bindValues($stmt, $types, $values);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();
        return $rows;
    }

    private function bindValues($stmt, $types, $values)
    {
        $params = [$types];
        foreach ($values as $key => $value) {
            $params[] = &$values[$key];
        }
        call_user_func_array([$stmt, "bind_param"], $params);
    }
}
