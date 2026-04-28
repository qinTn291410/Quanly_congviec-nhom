<?php 
require_once PROJECT_ROOT . '/views/layout/header.php'; 
require_once PROJECT_ROOT . '/src/Core/Database.php';
require_once PROJECT_ROOT . '/src/Models/GoalModel.php';

$db = \Tinhu\TaskManager\Core\Database::getInstance()->getConnection();
$userId = $_SESSION['user_id'];

// TÍNH TOÁN SỐ LIỆU THỐNG KÊ 
// Tổng số việc
$stmt = $db->prepare("SELECT COUNT(*) FROM tasks WHERE user_id = ?");
$stmt->execute([$userId]);
$totalTasks = $stmt->fetchColumn();

// Việc đã xong
$stmt = $db->prepare("SELECT COUNT(*) FROM tasks WHERE user_id = ? AND status = 'Done'");
$stmt->execute([$userId]);
$doneTasks = $stmt->fetchColumn();

// Việc đang làm (To-do, Doing, Pending)
$stmt = $db->prepare("SELECT COUNT(*) FROM tasks WHERE user_id = ? AND status != 'Done'");
$stmt->execute([$userId]);
$activeTasks = $stmt->fetchColumn();

// Việc trễ hạn
$stmt = $db->prepare("SELECT COUNT(*) FROM tasks WHERE user_id = ? AND status != 'Done' AND due_date < CURDATE() AND due_date != '0000-00-00'");
$stmt->execute([$userId]);
$overdueTasks = $stmt->fetchColumn();

//LẤY MỤC TIÊU & VIỆC SẮP TỚI HẠN 
$goalModel = new \Tinhu\TaskManager\Models\GoalModel();
$goals = $goalModel->getGoalsWithProgress($userId);

$stmt = $db->prepare("SELECT * FROM tasks WHERE user_id = ? AND status != 'Done' AND due_date != '0000-00-00' ORDER BY due_date ASC LIMIT 5");
$stmt->execute([$userId]);
$upcomingTasks = $stmt->fetchAll(\PDO::FETCH_ASSOC);

$hour = date('H');
if ($hour < 12) $greeting = 'Chào buổi sáng';
elseif ($hour < 18) $greeting = 'Chào buổi chiều';
else $greeting = 'Chào buổi tối';
?>

<div style="margin-bottom: 30px;">
    <h1 style="font-size: 2rem; color: #37352f; margin-bottom: 5px;"><?= $greeting ?>, <?= htmlspecialchars($_SESSION['fullname'] ?? 'Sếp') ?>! </h1>
    <p style="color: #787774; font-size: 1rem;">Đây là tổng quan tình hình công việc hôm nay.</p>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
    
    <div style="background: white; padding: 20px; border-radius: 10px; border: 1px solid #e3e2e0; box-shadow: 0 2px 4px rgba(0,0,0,0.02); display: flex; align-items: center; gap: 15px;">
        <div style="background: #e8f3fb; color: #0b6e99; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
            <i class="fas fa-tasks"></i>
        </div>
        <div>
            <div style="font-size: 2rem; font-weight: bold; color: #37352f;"><?= $totalTasks ?></div>
            <div style="color: #787774; font-size: 0.9rem; font-weight: 500;">Tổng số công việc</div>
        </div>
    </div>

    <div style="background: white; padding: 20px; border-radius: 10px; border: 1px solid #e3e2e0; box-shadow: 0 2px 4px rgba(0,0,0,0.02); display: flex; align-items: center; gap: 15px;">
        <div style="background: #edf3ec; color: #0f7b6c; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
            <i class="fas fa-check-circle"></i>
        </div>
        <div>
            <div style="font-size: 2rem; font-weight: bold; color: #37352f;"><?= $doneTasks ?></div>
            <div style="color: #787774; font-size: 0.9rem; font-weight: 500;">Đã hoàn thành</div>
        </div>
    </div>

    <div style="background: white; padding: 20px; border-radius: 10px; border: 1px solid #e3e2e0; box-shadow: 0 2px 4px rgba(0,0,0,0.02); display: flex; align-items: center; gap: 15px;">
        <div style="background: #fdf3c0; color: #d9730d; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
            <i class="fas fa-spinner"></i>
        </div>
        <div>
            <div style="font-size: 2rem; font-weight: bold; color: #37352f;"><?= $activeTasks ?></div>
            <div style="color: #787774; font-size: 0.9rem; font-weight: 500;">Đang tiến hành</div>
        </div>
    </div>

    <div style="background: white; padding: 20px; border-radius: 10px; border: 1px solid #f9c2c2; box-shadow: 0 2px 4px rgba(0,0,0,0.02); display: flex; align-items: center; gap: 15px;">
        <div style="background: #fde8e8; color: #eb3639; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div>
            <div style="font-size: 2rem; font-weight: bold; color: #eb3639;"><?= $overdueTasks ?></div>
            <div style="color: #eb3639; font-size: 0.9rem; font-weight: 500;">Quá hạn</div>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
    
    <div style="background: white; padding: 25px; border-radius: 10px; border: 1px solid #e3e2e0; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <h2 style="font-size: 1.2rem; margin: 0; color: #37352f;"><i class="fas fa-bullseye" style="color: #2383e2; margin-right: 8px;"></i>Tiến độ mục tiêu</h2>
            <a href="index.php?action=goals" style="color: #2383e2; text-decoration: none; font-size: 0.9rem; font-weight: 500;">Quản lý &rarr;</a>
        </div>
        
        <?php if(empty($goals)): ?>
            <p style="color: #787774; font-size: 0.95rem;">Sếp chưa có mục tiêu nào. <a href="index.php?action=goals" style="color:#2383e2;">Tạo ngay</a></p>
        <?php else: ?>
            <?php foreach(array_slice($goals, 0, 5) as $g): // Chỉ show 5 mục tiêu đầu tiên trên Dashboard ?>
                <?php 
                    $total = $g['total_tasks'] ?? 0;
                    $completed = $g['completed_tasks'] ?? 0;
                    $percent = $total > 0 ? round(($completed / $total) * 100) : 0;
                    $progressBarColor = $percent == 100 ? '#0f7b6c' : '#2383e2';
                ?>
                <div style="margin-bottom: 20px;">
                    <div style="display: flex; justify-content: space-between; font-size: 0.95rem; margin-bottom: 8px;">
                        <span style="font-weight: 500; color: #37352f;"><?= htmlspecialchars($g['title']) ?></span>
                        <span style="color: <?= $progressBarColor ?>; font-weight: bold;"><?= $percent ?>%</span>
                    </div>
                    <div style="background: #e3e2e0; height: 8px; border-radius: 10px; overflow: hidden;">
                        <div style="background: <?= $progressBarColor ?>; height: 100%; width: <?= $percent ?>%; transition: width 0.5s ease;"></div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div style="background: white; padding: 25px; border-radius: 10px; border: 1px solid #e3e2e0; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
        <h2 style="font-size: 1.2rem; margin: 0 0 25px 0; color: #37352f;"><i class="fas fa-clock" style="color: #d9730d; margin-right: 8px;"></i>Sắp đến hạn</h2>
        
        <?php if(empty($upcomingTasks)): ?>
            <div style="text-align: center; padding: 20px 0; color: #787774;">
                <i class="fas fa-mug-hot" style="font-size: 2rem; color: #ccc; margin-bottom: 10px;"></i>
                <p style="font-size: 0.95rem; margin: 0;">Không có việc nào sắp đến hạn.</p>
            </div>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 15px;">
                <?php foreach($upcomingTasks as $t): ?>
                    <?php
                        $dueDate = strtotime($t['due_date']);
                        $isOverdue = $dueDate < strtotime(date('Y-m-d'));
                        $dateColor = $isOverdue ? '#eb3639' : '#d9730d';
                    ?>
                    <div style="padding-bottom: 12px; border-bottom: 1px solid #f0f0f0;">
                        <a href="index.php?action=edit-task&id=<?= $t['id'] ?>" style="text-decoration: none; color: #37352f; font-weight: 600; font-size: 0.95rem; display: block; margin-bottom: 5px;">
                            <?= htmlspecialchars($t['title']) ?>
                        </a>
                        <div style="font-size: 0.8rem; color: <?= $dateColor ?>; font-weight: 500;">
                            <i class="far fa-calendar-alt"></i> <?= date('d/m/Y', $dueDate) ?> 
                            <?= $isOverdue ? '<span style="background:#fde8e8; padding:2px 6px; border-radius:4px; margin-left:5px;">Quá hạn</span>' : '' ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once PROJECT_ROOT . '/views/layout/footer.php'; ?>