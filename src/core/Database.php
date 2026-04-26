<?php
namespace Tinhu\TaskManager\Core;

use PDO;
use PDOException;

class Database {
    private static $instance = null;
    private $conn;

    // Cấu hình Database
    private $host = 'localhost';
    private $db_name = 'quanly_congviec';
    private $username = 'root';
    private $password = '';

    private function __construct() {
        try {
            // Kết nối PDO
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            die("Lỗi kết nối Cơ sở dữ liệu: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }
}