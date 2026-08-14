<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?action=login');
    exit();
}

$fullname = $_SESSION['fullname'] ?? 'Admin';
$lang = $_SESSION['language'] ?? 'vi';
global $dictionary;
$dictionary = require PROJECT_ROOT . "/lang/{$lang}.php";

if (!function_exists('__')) {
    function __($keyword) {
        global $dictionary;
        return $dictionary[$keyword] ?? $keyword;
    }
}

$dueCount = 0;
// 1. Tạo URL avatar mặc định nếu chưa có ảnh
$userAvatarPath = 'https://ui-avatars.com/api/?name=' . urlencode($fullname) . '&background=random';

try {
    $db = \Tinhu\TaskManager\Core\Database::getInstance()->getConnection();
    $uid = $_SESSION['user_id'];
    
    $stmt1 = $db->prepare("SELECT COUNT(*) FROM tasks WHERE user_id = ? AND status != 'Done' AND due_date <= CURDATE() AND due_date != '0000-00-00' AND due_date IS NOT NULL");
    $stmt1->execute([$uid]);
    $countPersonal = (int)$stmt1->fetchColumn();

    $stmt2 = $db->prepare("SELECT COUNT(*) FROM team_tasks WHERE assigned_to = ? AND status != 'Done' AND due_date <= CURDATE() AND due_date != '0000-00-00' AND due_date IS NOT NULL");
    $stmt2->execute([$uid]);
    $countTeam = (int)$stmt2->fetchColumn();

    $dueCount = $countPersonal + $countTeam;

    // 2. Lấy Avatar từ Database
    $stmtAvatar = $db->prepare("SELECT avatar FROM users WHERE id = ?");
    $stmtAvatar->execute([$uid]);
    $dbAvatar = $stmtAvatar->fetchColumn();
    
    if (!empty($dbAvatar) && $dbAvatar !== 'default.png') {
        $userAvatarPath = '/task_manager/public/uploads/' . $dbAvatar;
    }
} catch (\Exception $e) {}
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title>Workspace - Task Manager</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @media print {
            .sidebar, button, a.btn-status, form, .custom-scroll { display: none !important; }
            .main-content { margin-left: 0 !important; width: 100% !important; padding: 0 !important; }
            body { background: white; }
        }
    </style>
</head>
<body>
<div class="workspace-wrapper">
    
    <div class="sidebar">
        <div class="workspace-header">
            <a href="index.php?action=profile" style="display: flex; align-items: center; gap: 10px; padding: 10px; text-decoration: none; color: inherit; border-radius: 6px; margin-bottom: 20px;">
                
                <!-- 3. HIỂN THỊ AVATAR TRÊN MENU -->
                <img src="<?= $userAvatarPath ?>" style="width: 32px; height: 32px; min-width: 32px; border-radius: 6px; object-fit: cover; border: 1px solid #e3e2e0;">
                
                <div style="font-weight: 600; font-size: 0.95rem; line-height: 1.2;">
                    <?= htmlspecialchars($fullname) ?>'s Workspace
                </div>
            </a>
        </div>

        <a href="index.php?action=dashboard" class="menu-item"><i class="fas fa-home"></i> <?= __('menu_workspace') ?></a>
        <a href="index.php?action=goals" class="menu-item"><i class="fas fa-bullseye"></i> <?= __('menu_goals') ?></a>
        <a href="index.php?action=tasks" class="menu-item"><i class="fas fa-check-square"></i> <?= __('menu_tasks') ?></a>
        <a href="index.php?action=teams" class="menu-item"><i class="fas fa-users"></i> <?= __('menu_teams') ?></a>
        <a href="index.php?action=calendar" class="menu-item"><i class="far fa-calendar-alt"></i> <?= __('menu_calendar') ?></a>

        <div style="margin-top: auto;">
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="index.php?action=admin" class="menu-item" style="color: #eb3639; font-weight: bold; background: #fde8e8; margin-bottom: 15px;">
                    <i class="fas fa-shield-alt"></i> <?= __('menu_admin') ?>
                </a>
            <?php endif; ?>

            <a href="index.php?action=logout" class="menu-item"><i class="fas fa-sign-out-alt"></i> <?= __('menu_logout') ?></a>
            <a href="index.php?action=settings" style="display: block; padding: 10px 15px; color: var(--text-muted); text-decoration: none; border-radius: 4px; margin-top: 5px;">
                <i class="fas fa-cog" style="width: 20px;"></i> <?= __('menu_settings') ?>
            </a>
        </div>
    </div>

    <div class="main-content">
        
        <?php if ($dueCount > 0): ?>
            <div style="background: #fde8e8; border: 1px solid #f9c2c2; color: #eb3639; padding: 12px 20px; border-radius: 6px; margin-bottom: 25px; display: flex; align-items: center; justify-content: space-between; font-size: 0.95rem;">
                <div>
                    <i class="fas fa-exclamation-circle" style="margin-right: 8px;"></i>
                    <strong><?= __('deadline_warning') ?></strong> 
                    <?= sprintf(__('deadline_msg'), $dueCount) ?>
                </div>
                <a href="index.php?action=calendar" style="background: #eb3639; color: white; padding: 5px 12px; border-radius: 4px; text-decoration: none; font-size: 0.85rem; font-weight: 500;">
                    <?= __('btn_resolve') ?>
                </a>
            </div>
        <?php endif; ?>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                setInterval(function() {
                    fetch('index.php?action=api-check-notifications')
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            data.data.forEach(notif => {
                                alert(notif.message);
                            });
                        }
                    })
                    .catch(err => console.error('Lỗi quét Radar:', err));
                }, 2000);
            });
        </script>