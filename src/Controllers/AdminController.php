<?php
namespace Tinhu\TaskManager\Controllers;

class AdminController {
    private $db;

    public function __construct() {
        $this->db = \Tinhu\TaskManager\Core\Database::getInstance()->getConnection();
        //Key: Kiểm tra quyền truy cập
        if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            $_SESSION['system_alert'] = "Bạn không có quyền truy cập khu vực Quản trị viên!";
            header("Location: index.php?action=dashboard");
            exit;
        }
    }

    // Đổ dữ liệu ra màn hình
    public function index() {
        $view = $_GET['view'] ?? 'users';

        if ($view === 'users') {
            $stmt = $this->db->query("SELECT id, fullname, email, role, is_locked FROM users ORDER BY id DESC");
            $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } else {
            $stmt = $this->db->query("SELECT config_key, config_value FROM system_configs");
            $configs = $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);
        }

        require_once PROJECT_ROOT . '/views/admin/index.php';
    }

    public function updateRole() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_POST['user_id'];
            $newRole = $_POST['role'];
            if ($userId != $_SESSION['user_id']) { // Không tự đổi quyền chính mình
                $stmt = $this->db->prepare("UPDATE users SET role = ? WHERE id = ?");
                $stmt->execute([$newRole, $userId]);
                $_SESSION['system_alert'] = "Đã cập nhật vai trò người dùng thành công!";
            }
        }
        header("Location: index.php?action=admin&view=users");
        exit;
    }

    public function toggleLock() {
        $userId = $_GET['id'] ?? 0;
        if ($userId && $userId != $_SESSION['user_id']) {
            $stmt = $this->db->prepare("UPDATE users SET is_locked = NOT is_locked WHERE id = ?");
            $stmt->execute([$userId]);
            $_SESSION['system_alert'] = "Đã thay đổi trạng thái hoạt động của tài khoản!";
        }
        header("Location: index.php?action=admin&view=users");
        exit;
    }

    public function saveConfig() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $stmt = $this->db->prepare("UPDATE system_configs SET config_value = ? WHERE config_key = ?");
            foreach ($_POST['configs'] as $key => $value) {
                $stmt->execute([$value, $key]);
            }
            $_SESSION['system_alert'] = "Đã lưu Cấu hình hệ thống thành công!";
        }
        header("Location: index.php?action=admin&view=config");
        exit;
    }
}