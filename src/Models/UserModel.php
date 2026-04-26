<?php
namespace Tinhu\TaskManager\Models;

use Tinhu\TaskManager\Core\Database;
use PDO;

class UserModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    // Tìm user để Đăng nhập
    public function findUserByEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Thêm user mới để Đăng ký
    public function createUser($fullname, $email, $password) {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role = 'user'; 

            $stmt = $this->conn->prepare("INSERT INTO users (fullname, email, password, role) VALUES (:fullname, :email, :password, :role)");
            $stmt->bindParam(':fullname', $fullname);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':role', $role);

            return $stmt->execute();
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function checkEmailExists($email) {
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function resetPassword($email, $newPassword) {
        $hashed_password = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("UPDATE users SET password = :password WHERE email = :email");
        return $stmt->execute(['password' => $hashed_password, 'email' => $email]);
    }

    // Lấy toàn bộ thông tin user
    public function getUserById($id) {
        $stmt = $this->conn->prepare("SELECT id, fullname, email, phone, avatar FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // Cập nhật thông tin
    public function updateProfile($id, $fullname, $email, $phone, $avatar) {
        $sql = "UPDATE users SET fullname = :fullname, email = :email, phone = :phone";
        $params = ['fullname' => $fullname, 'email' => $email, 'phone' => $phone, 'id' => $id];
        
        if ($avatar !== null) {
            $sql .= ", avatar = :avatar";
            $params['avatar'] = $avatar;
        }
        $sql .= " WHERE id = :id";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($params);
    }

    public function getSettings($id) {
        $stmt = $this->conn->prepare("SELECT notifications, language, timezone FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function updateSettings($id, $notifications, $language, $timezone) {
        $stmt = $this->conn->prepare("UPDATE users SET notifications = :notifications, language = :language, timezone = :timezone WHERE id = :id");
        return $stmt->execute([
            'notifications' => $notifications,
            'language' => $language,
            'timezone' => $timezone,
            'id' => $id
        ]);
    }
}