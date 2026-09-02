<?php

require_once __DIR__ . "/../../config/database.php";

class ProfileModel
{
    private $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    public function getUserById($userId)
    {
        $stmt = $this->conn->prepare("SELECT user_id, name, email, phone, address, password, user_type, created_at FROM users WHERE user_id = ? LIMIT 1");
        if (!$stmt) {
            return null;
        }
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result ? $result->fetch_assoc() : null;
        $stmt->close();
        return $user;
    }

    public function emailTakenByAnotherUser($email, $userId)
    {
        $stmt = $this->conn->prepare("SELECT user_id FROM users WHERE email = ? AND user_id <> ? LIMIT 1");
        if (!$stmt) {
            return true;
        }
        $stmt->bind_param("si", $email, $userId);
        $stmt->execute();
        $stmt->store_result();
        $taken = $stmt->num_rows > 0;
        $stmt->close();
        return $taken;
    }

    public function updateAdopterProfile($userId, $name, $email, $phone, $address)
    {
        $stmt = $this->conn->prepare("UPDATE users SET name = ?, email = ?, phone = ?, address = ? WHERE user_id = ?");
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param("ssssi", $name, $email, $phone, $address, $userId);
        $updated = $stmt->execute();
        $stmt->close();
        return $updated;
    }

    public function updateName($userId, $name)
    {
        $stmt = $this->conn->prepare("UPDATE users SET name = ? WHERE user_id = ?");
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param("si", $name, $userId);
        $updated = $stmt->execute();
        $stmt->close();
        return $updated;
    }

    public function updatePassword($userId, $passwordHash)
    {
        $stmt = $this->conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param("si", $passwordHash, $userId);
        $updated = $stmt->execute();
        $stmt->close();
        return $updated;
    }
}
