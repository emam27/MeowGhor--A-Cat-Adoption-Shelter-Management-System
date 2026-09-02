<?php

/**
 * Shared model for future Adopter data operations.
 *
 * Cat-listing queries will be added here when MySQL is connected.
 */
require_once __DIR__ . "/../../config/database.php";

class AdopterModel
{
    private $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    public function createIntakeRequest($userId, $catName, $breed, $gender, $age, $healthStatus, $description, $reason, $image)
    {
        if ($age === null) {
            $sql = "INSERT INTO cat_intake_requests
                    (user_id, cat_name, breed, gender, age, health_status, description, reason, image, request_status)
                    VALUES (?, ?, ?, ?, NULL, ?, ?, ?, ?, 'Pending')";
            $stmt = $this->conn->prepare($sql);

            if (!$stmt) {
                return false;
            }

            $stmt->bind_param("isssssss", $userId, $catName, $breed, $gender, $healthStatus, $description, $reason, $image);
        } else {
            $sql = "INSERT INTO cat_intake_requests
                    (user_id, cat_name, breed, gender, age, health_status, description, reason, image, request_status)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')";
            $stmt = $this->conn->prepare($sql);

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
        $sql = "SELECT request_id, cat_name, submitted_at, request_status, staff_comment
                FROM cat_intake_requests
                WHERE user_id = ?
                ORDER BY submitted_at DESC";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->bind_result($requestId, $catName, $submittedAt, $requestStatus, $staffComment);

        $requests = [];
        while ($stmt->fetch()) {
            $requests[] = [
                "request_id" => $requestId,
                "cat_name" => $catName,
                "submitted_at" => $submittedAt,
                "request_status" => $requestStatus,
                "staff_comment" => $staffComment
            ];
        }

        $stmt->close();

        return $requests;
    }

    public function cancelPendingIntakeRequest($requestId, $userId)
    {
        $sql = "UPDATE cat_intake_requests
                SET request_status = 'Cancelled'
                WHERE request_id = ?
                  AND user_id = ?
                  AND request_status = 'Pending'";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("ii", $requestId, $userId);
        $updated = $stmt->execute() && $stmt->affected_rows > 0;
        $stmt->close();

        return $updated;
    }

    // Future operation: create an adoption application.
    // Future operation: get the adopter's applications.
    // Future operation: withdraw a pending adoption application.

    // Future operation: create an intake request.
    // Future operation: get the adopter's intake requests.
    // Future operation: cancel a pending intake request.

    // Future operation: get a cat by ID.
}
