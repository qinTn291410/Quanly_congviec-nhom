<?php
// Kiểm tra nếu chưa đăng nhập thì đá ra trang login
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?action=login');
    exit();
}

// Lấy tên từ Session và trích ra chữ cái đầu tiên (Dùng mb_substr để không lỗi dấu tiếng Việt)
$fullname = $_SESSION['fullname'] ?? 'Admin';
$firstChar = mb_substr($fullname, 0, 1, "UTF-8");

// 1. ÁP DỤNG MÚI GIỜ
$userTimezone = $_SESSION['timezone'] ?? 'Asia/Ho_Chi_Minh';
date_default_timezone_set($userTimezone);

// 2. CHUẨN BỊ BỘ TỪ ĐIỂN NGÔN NGỮ
$lang = $_SESSION['language'] ?? 'vi';

$menu_home = ($lang === 'en') ? 'Dashboard' : 'Trang chủ';
$menu_tasks = ($lang === 'en') ? 'Personal Tasks' : 'Việc cá nhân';
$menu_team = ($lang === 'en') ? 'Team Workspace' : 'Việc nhóm';
$menu_logout = ($lang === 'en') ? 'Logout' : 'Đăng xuất';
$menu_settings = ($lang === 'en') ? 'System Settings' : 'Cài đặt hệ thống';

// 3. HỆ THỐNG THÔNG BÁO (RADAR QUÉT DEADLINE)
$dueCount = 0;
if (isset($_SESSION['user_id']) && ($_SESSION['notifications'] ?? 1) == 1) {
    try {
        $db = new \PDO("mysql:host=localhost;dbname=quanly_congviec;charset=utf8", "root", "");
        $stmt = $db->prepare("SELECT COUNT(*) FROM tasks WHERE user_id = ? AND status != 'Done' AND due_date <= CURDATE() AND due_date != '0000-00-00'");
        $stmt->execute([$_SESSION['user_id']]);
        $dueCount = $stmt->fetchColumn();
    } catch (\PDOException $e) {
    }
}
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title>Workspace - Task Manager</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<div class="workspace-wrapper">
    
    <div class="sidebar">
        <div class="workspace-header">
            <a href="index.php?action=profile" style="display: flex; align-items: center; gap: 10px; padding: 10px; text-decoration: none; color: inherit; border-radius: 6px; margin-bottom: 20px;">
                <div style="background: #e03e3e; color: white; width: 28px; height: 28px; min-width: 28px; display: flex; justify-content: center; align-items: center; border-radius: 4px; font-weight: bold; font-size: 0.9rem;">
                    <?= $firstChar ?>
                </div>
                
                <div style="font-weight: 600; font-size: 0.95rem; line-height: 1.2;">
                    <?= htmlspecialchars($fullname) ?>'s Workspace
                </div>
            </a>
        </div>

        <a href="index.php?action=dashboard" class="menu-item"><i class="fas fa-home"></i> <?= $menu_home ?></a>
        <a href="index.php?action=tasks" class="menu-item"><i class="fas fa-check-square"></i> <?= $menu_tasks ?></a>
        <a href="index.php?action=teams" class="menu-item"><i class="fas fa-users"></i> <?= $menu_team ?></a>

        <div style="margin-top: auto;">
            <a href="index.php?action=logout" class="menu-item"><i class="fas fa-sign-out-alt"></i> <?= $menu_logout ?></a>
            <a href="index.php?action=settings" style="display: block; padding: 10px 15px; color: var(--text-muted); text-decoration: none; border-radius: 4px; margin-top: 5px;">
                <i class="fas fa-cog" style="width: 20px;"></i> <?= $menu_settings ?>
            </a>
        </div>
    </div>

    <div class="main-content">
        
        <?php if ($dueCount > 0): ?>
            <div style="background: #fde8e8; border: 1px solid #f9c2c2; color: #eb3639; padding: 12px 20px; border-radius: 6px; margin-bottom: 25px; display: flex; align-items: center; justify-content: space-between; font-size: 0.95rem;">
                <div>
                    <i class="fas fa-exclamation-circle" style="margin-right: 8px;"></i>
                    <strong><?= ($lang === 'en') ? 'Deadline Warning:' : 'Cảnh báo Deadline:' ?></strong> 
                    <?= ($lang === 'en') ? "You have <b>$dueCount</b> tasks due today or overdue!" : "Bạn đang có <b>$dueCount</b> công việc sắp hết hạn hoặc đã quá hạn hôm nay!" ?>
                </div>
                <a href="index.php?action=tasks" style="background: #eb3639; color: white; padding: 5px 12px; border-radius: 4px; text-decoration: none; font-size: 0.85rem; font-weight: 500;">
                    <?= ($lang === 'en') ? 'View Tasks' : 'Xử lý ngay' ?>
                </a>
            </div>
        <?php endif; ?>