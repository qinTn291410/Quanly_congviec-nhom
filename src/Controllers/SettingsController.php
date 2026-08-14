<?php
namespace Tinhu\TaskManager\Controllers;

class SettingsController {
    public function index() {
        $db = \Tinhu\TaskManager\Core\Database::getInstance()->getConnection();
        $userId = $_SESSION['user_id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $notify_deadline = isset($_POST['notify_deadline']) ? 1 : 0;
            $notify_alerts = isset($_POST['notify_alerts']) ? 1 : 0;
            
            $language = $_POST['language'] ?? 'vi';
            $timezone = $_POST['timezone'] ?? 'Asia/Ho_Chi_Minh';

            $stmt = $db->prepare("UPDATE users SET notify_deadline = ?, notify_alerts = ?, language = ?, timezone = ? WHERE id = ?");
            $stmt->execute([$notify_deadline, $notify_alerts, $language, $timezone, $userId]);

            $_SESSION['language'] = $language;
            $_SESSION['timezone'] = $timezone;

            $_SESSION['system_alert'] = "Đã lưu toàn bộ cài đặt thành công!";
            header("Location: index.php?action=settings");
            exit;
        }

        $stmt = $db->prepare("SELECT notify_deadline, notify_alerts, language, timezone FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        $nDeadline = $user['notify_deadline'] ?? 1;
        $nAlerts = $user['notify_alerts'] ?? 1;
        $_SESSION['language'] = $user['language'] ?? 'vi';
        $_SESSION['timezone'] = $user['timezone'] ?? 'Asia/Ho_Chi_Minh';

        require_once PROJECT_ROOT . '/views/settings.php';
    }
}