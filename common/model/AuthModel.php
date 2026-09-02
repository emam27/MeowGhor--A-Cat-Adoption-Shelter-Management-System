<?php

require_once __DIR__ . "/../../config/database.php";

class AuthModel
{
    private $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    public function emailExists($email)
    {
        $sql = "SELECT user_id FROM users WHERE email = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();

        return $exists;
    }

    public function createAdopter($name, $email, $hashedPassword, $phone, $address)
    {
        $sql = "INSERT INTO users (name, email, password, phone, address, user_type)
                VALUES (?, ?, ?, ?, ?, 'adopter')";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("sssss", $name, $email, $hashedPassword, $phone, $address);
        $created = $stmt->execute();
        $stmt->close();

        return $created;
    }

    public function findUserByEmail($email)
    {
        $sql = "SELECT user_id, name, email, password, user_type
                FROM users
                WHERE email = ?
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return null;
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($userId, $name, $userEmail, $passwordHash, $userType);

        if (!$stmt->fetch()) {
            $stmt->close();
            return null;
        }

        $stmt->close();

        return [
            "user_id" => $userId,
            "name" => $name,
            "email" => $userEmail,
            "password" => $passwordHash,
            "user_type" => $userType
        ];
    }
}
