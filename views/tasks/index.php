<?php require_once PROJECT_ROOT . '/views/layout/header.php'; ?>

<?php if (isset($_SESSION['system_alert'])): ?>
    <script>
        alert("THÔNG BÁO HỆ THỐNG:\n\n<?= $_SESSION['system_alert'] ?>");
    </script>
    <?php unset($_SESSION['system_alert']); ?>
<?php endif; ?>

<?php
$catColors = [
    'Công việc' => ['bg' => '#fdecc8', 'text' => '#ad7f11'], 
    'Học tập' => ['bg' => '#e8f3fb', 'text' => '#0b6e99'], 
    'Sức khỏe' => ['bg' => '#f4e0f9', 'text' => '#8f24b2'], 
    'Tài chính' => ['bg' => '#e3f2fd', 'text' => '#1565c0'], 
    'Khác' => ['bg' => '#f1f1f0', 'text' => '#787774']
];
$goalColors = [
    'Ngắn hạn' => ['bg' => '#fff3e0', 'text' => '#e65100'], 
    'Dài hạn' => ['bg' => '#e0f7fa', 'text' => '#006064'], 
    'Thói quen' => ['bg' => '#fbe9e7', 'text' => '#d84315']
];
?>
<style>
    .task-card {
        background: #ffffff;
        border: 1px solid #e3e2e0; 
        border-radius: 8px;      
        padding: 15px;            
        margin-bottom: 15px;      
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        transition: all 0.2s ease;
    }
    
    .task-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        border-color: #cfcecc;
        transform: translateY(-2px);
    }
</style>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h1 style="font-size: 1.8rem; margin: 0;">Việc cá nhân</h1>
    <button onclick="openModal()" style="background: var(--text-main); color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-weight: 500;">
        + New Task
    </button>
</div>

<form action="index.php" method="GET" style="display: flex; gap: 10px; margin-bottom: 25px; background: #f7f7f5; padding: 15px; border-radius: 8px;">
    <input type="hidden" name="action" value="tasks">
    
    <input type="text" name="search" placeholder="Tìm tên việc..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" 
           style="flex: 2; padding: 8px 12px; border: 1px solid #e3e2e0; border-radius: 6px;">
    
    <select name="category" style="flex: 1; padding: 8px; border: 1px solid #e3e2e0; border-radius: 6px;">
        <option value="">Tất cả danh mục</option>
        <option value="Công việc" <?= ($_GET['category']??'') == 'Công việc' ? 'selected' : '' ?>>Công việc</option>
        <option value="Học tập" <?= ($_GET['category']??'') == 'Học tập' ? 'selected' : '' ?>>Học tập</option>
        <option value="Sức khỏe" <?= ($_GET['category']??'') == 'Sức khỏe' ? 'selected' : '' ?>>Sức khỏe</option>
        <option value="Tài chính" <?= ($_GET['category']??'') == 'Tài chính' ? 'selected' : '' ?>>Tài chính</option>
    </select>

    <select name="priority" style="flex: 1; padding: 8px; border: 1px solid #e3e2e0; border-radius: 6px;">
        <option value="">Mọi ưu tiên</option>
        <option value="High" <?= ($_GET['priority']??'') == 'High' ? 'selected' : '' ?>>Cao</option>
        <option value="Medium" <?= ($_GET['priority']??'') == 'Medium' ? 'selected' : '' ?>>Trung bình</option>
        <option value="Low" <?= ($_GET['priority']??'') == 'Low' ? 'selected' : '' ?>>Thấp</option>
    </select>

    <button type="submit" style="background: #2383e2; color: white; border: none; padding: 8px 20px; border-radius: 6px; cursor: pointer;">Lọc</button>
    <a href="index.php?action=tasks" style="text-decoration: none; color: #787774; padding-top: 8px; font-size: 0.9rem;">Xóa lọc</a>
</form>

<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; align-items: start;">
    
    <div class="kanban-column">
        <div style="font-weight: 600; margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
            <span style="background: #e3e2e0; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem;">To-do</span>
            <span style="color: var(--text-muted); font-weight: 400;"><?php echo count($todoTasks); ?></span>
        </div>
        <?php foreach ($todoTasks as $task): ?>
            <div class="task-card">
                <div style="font-weight: 500;"><?= htmlspecialchars($task['title']) ?></div>
                <?php if (!empty($task['description'])): ?>
                    <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 6px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4;">
                        <?= nl2br(htmlspecialchars($task['description'])) ?>
                    </div>
                <?php endif; ?>

                <div style="margin-top: 6px; display: flex; gap: 5px; flex-wrap: wrap;">
                    <?php
                        $cat = $task['category'] ?? 'Khác'; 
                        $catColor = $catColors[$cat] ?? $catColors['Khác'];
                        $gl = $task['goal'] ?? 'Không';
                    ?>
                    <span style="background: <?= $catColor['bg'] ?>; color: <?= $catColor['text'] ?>; padding: 2px 6px; border-radius: 4px; font-size: 0.65rem; font-weight: 600;">
                        <?= htmlspecialchars($cat) ?>
                    </span>
                    <?php if ($gl !== 'Không'): $glColor = $goalColors[$gl] ?? ['bg' => '#fff', 'text' => '#333']; ?>
                        <span style="border: 1px solid <?= $glColor['text'] ?>; color: <?= $glColor['text'] ?>; padding: 1px 5px; border-radius: 4px; font-size: 0.65rem; font-weight: 600;">
                            <?= htmlspecialchars($gl) ?>
                        </span>
                    <?php endif; ?>
                </div>
                
                <?php 
                    $isOverdue = ($task['due_date'] != '0000-00-00' && !empty($task['due_date']) && $task['due_date'] <= date('Y-m-d') && $task['status'] != 'Done');
                    $dateColor = $isOverdue ? '#eb3639' : 'var(--text-muted)';
                    $iconShake = $isOverdue ? 'fa-shake' : '';
                ?>
                <div style="font-size: 0.75rem; color: <?= $dateColor ?>; margin-top: 8px; display: flex; align-items: center; gap: 5px; <?= $isOverdue ? 'font-weight: 600;' : '' ?>">
                    <i class="far fa-calendar-alt <?= $iconShake ?>"></i> 
                    <span>
                        <?php 
                            if ($task['due_date'] == '0000-00-00' || empty($task['due_date'])) {
                                echo 'No deadline';
                            } else {
                                echo date('d/m/Y', strtotime($task['due_date']));
                                if ($isOverdue) echo ' (Quá hạn)';
                            }
                        ?>
                    </span>
                </div>

                <div style="display: flex; gap: 10px; margin-top: 10px;">
                    <a href="index.php?action=update-task&id=<?= $task['id'] ?>&status=Doing" class="btn-status">
                        <i class="fas fa-play" style="font-size: 0.6rem;"></i> Start
                    </a>
                    <a href="index.php?action=edit-task&id=<?= $task['id'] ?>" class="btn-status" style="color: #2383e2;">
                        <i class="fas fa-edit"></i> Sửa
                    </a>
                    <a href="index.php?action=delete-task&id=<?= $task['id'] ?>" class="btn-status" style="color: #dc3545;" onclick="return confirm('Chắc chắn muốn xóa việc này?');">
                        <i class="fas fa-trash"></i>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="kanban-column">
        <div style="font-weight: 600; margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
            <span style="background: #d3e5ef; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem;">Doing</span>
            <span style="color: var(--text-muted); font-weight: 400;"><?= count($doingTasks) ?></span>
        </div>
        <?php foreach ($doingTasks as $task): ?>
            <div class="task-card">
                <div style="font-weight: 500;"><?= htmlspecialchars($task['title']) ?></div>
                <?php if (!empty($task['description'])): ?>
                    <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 6px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4;">
                        <?= nl2br(htmlspecialchars($task['description'])) ?>
                    </div>
                <?php endif; ?>

                <div style="margin-top: 6px; display: flex; gap: 5px; flex-wrap: wrap;">
                    <?php
                        $cat = $task['category'] ?? 'Khác'; 
                        $catColor = $catColors[$cat] ?? $catColors['Khác'];
                        $gl = $task['goal'] ?? 'Không';
                    ?>
                    <span style="background: <?= $catColor['bg'] ?>; color: <?= $catColor['text'] ?>; padding: 2px 6px; border-radius: 4px; font-size: 0.65rem; font-weight: 600;">
                        <?= htmlspecialchars($cat) ?>
                    </span>
                    <?php if ($gl !== 'Không'): $glColor = $goalColors[$gl] ?? ['bg' => '#fff', 'text' => '#333']; ?>
                        <span style="border: 1px solid <?= $glColor['text'] ?>; color: <?= $glColor['text'] ?>; padding: 1px 5px; border-radius: 4px; font-size: 0.65rem; font-weight: 600;">
                            <?= htmlspecialchars($gl) ?>
                        </span>
                    <?php endif; ?>
                </div>

                <?php 
                    $isOverdue = ($task['due_date'] != '0000-00-00' && !empty($task['due_date']) && $task['due_date'] <= date('Y-m-d') && $task['status'] != 'Done');
                    $dateColor = $isOverdue ? '#eb3639' : 'var(--text-muted)';
                    $iconShake = $isOverdue ? 'fa-shake' : '';
                ?>
                <div style="font-size: 0.75rem; color: <?= $dateColor ?>; margin-top: 8px; display: flex; align-items: center; gap: 5px; <?= $isOverdue ? 'font-weight: 600;' : '' ?>">
                    <i class="far fa-calendar-alt <?= $iconShake ?>"></i> 
                    <span>
                        <?php 
                            if ($task['due_date'] == '0000-00-00' || empty($task['due_date'])) {
                                echo 'No deadline';
                            } else {
                                echo date('d/m/Y', strtotime($task['due_date']));
                                if ($isOverdue) echo ' (Quá hạn)';
                            }
                        ?>
                    </span>
                </div>

                <div style="display: flex; gap: 10px; margin-top: 10px;">
                    <a href="index.php?action=update-task&id=<?= $task['id'] ?>&status=Done" class="btn-status" style="color: #0f7b6c;">
                        <i class="fas fa-check"></i> Done
                    </a>
                    <a href="index.php?action=update-task&id=<?= $task['id'] ?>&status=Pending" class="btn-status" style="color: #ad7f11;">
                        <i class="fas fa-pause"></i> Delay
                    </a>
                    <a href="index.php?action=edit-task&id=<?= $task['id'] ?>" class="btn-status" style="color: #2383e2;">
                        <i class="fas fa-edit"></i> Sửa
                    </a>
                    <a href="index.php?action=delete-task&id=<?= $task['id'] ?>" class="btn-status" style="color: #dc3545;" onclick="return confirm('Chắc chắn muốn xóa việc này?');">
                        <i class="fas fa-trash"></i>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="kanban-column">
        <div style="font-weight: 600; margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
            <span style="background: #fdecc8; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem; color: #ad7f11;">Pending</span>
            <span style="color: var(--text-muted); font-weight: 400;"><?= count($pendingTasks) ?></span>
        </div>
        <?php foreach ($pendingTasks as $task): ?>
            <div class="task-card">
                <div style="font-weight: 500; color: #ad7f11;"><?= htmlspecialchars($task['title']) ?></div>
                <?php if (!empty($task['description'])): ?>
                    <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 6px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4;">
                        <?= nl2br(htmlspecialchars($task['description'])) ?>
                    </div>
                <?php endif; ?>
                
                <div style="margin-top: 6px; display: flex; gap: 5px; flex-wrap: wrap;">
                    <?php
                        $cat = $task['category'] ?? 'Khác'; 
                        $catColor = $catColors[$cat] ?? $catColors['Khác'];
                        $gl = $task['goal'] ?? 'Không';
                    ?>
                    <span style="background: <?= $catColor['bg'] ?>; color: <?= $catColor['text'] ?>; padding: 2px 6px; border-radius: 4px; font-size: 0.65rem; font-weight: 600;">
                        <?= htmlspecialchars($cat) ?>
                    </span>
                    <?php if ($gl !== 'Không'): $glColor = $goalColors[$gl] ?? ['bg' => '#fff', 'text' => '#333']; ?>
                        <span style="border: 1px solid <?= $glColor['text'] ?>; color: <?= $glColor['text'] ?>; padding: 1px 5px; border-radius: 4px; font-size: 0.65rem; font-weight: 600;">
                            <?= htmlspecialchars($gl) ?>
                        </span>
                    <?php endif; ?>
                </div>

                <?php 
                    $isOverdue = ($task['due_date'] != '0000-00-00' && !empty($task['due_date']) && $task['due_date'] <= date('Y-m-d') && $task['status'] != 'Done');
                    $dateColor = $isOverdue ? '#eb3639' : 'var(--text-muted)';
                    $iconShake = $isOverdue ? 'fa-shake' : '';
                ?>
                <div style="font-size: 0.75rem; color: <?= $dateColor ?>; margin-top: 8px; display: flex; align-items: center; gap: 5px; <?= $isOverdue ? 'font-weight: 600;' : '' ?>">
                    <i class="far fa-calendar-alt <?= $iconShake ?>"></i> 
                    <span>
                        <?php 
                            if ($task['due_date'] == '0000-00-00' || empty($task['due_date'])) {
                                echo 'No deadline';
                            } else {
                                echo date('d/m/Y', strtotime($task['due_date']));
                                if ($isOverdue) echo ' (Quá hạn)';
                            }
                        ?>
                    </span>
                </div>

                <div style="display: flex; gap: 10px; margin-top: 10px;">
                    <a href="index.php?action=update-task&id=<?= $task['id'] ?>&status=Doing" class="btn-status">
                        <i class="fas fa-play"></i> Tiếp tục làm
                    </a>
                    <a href="index.php?action=edit-task&id=<?= $task['id'] ?>" class="btn-status" style="color: #2383e2;">
                        <i class="fas fa-edit"></i> Sửa
                    </a>
                    <a href="index.php?action=delete-task&id=<?= $task['id'] ?>" class="btn-status" style="color: #dc3545;" onclick="return confirm('Sếp chắc chắn muốn xóa việc này?');">
                        <i class="fas fa-trash"></i>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="kanban-column">
        <div style="font-weight: 600; margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
            <span style="background: #dbeddb; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem;">Done</span>
            <span style="color: var(--text-muted); font-weight: 400;"><?php echo count($doneTasks); ?></span>
        </div>
        <?php foreach ($doneTasks as $task): ?>
            <div class="task-card" style="opacity: 0.7;">
                <div style="font-weight: 500; text-decoration: line-through;"><?php echo htmlspecialchars($task['title']); ?></div>
                <?php if (!empty($task['description'])): ?>
                    <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 6px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4;">
                        <?= nl2br(htmlspecialchars($task['description'])) ?>
                    </div>
                <?php endif; ?>

                <div style="margin-top: 6px; display: flex; gap: 5px; flex-wrap: wrap;">
                    <?php
                        $cat = $task['category'] ?? 'Khác'; 
                        $catColor = $catColors[$cat] ?? $catColors['Khác'];
                        $gl = $task['goal'] ?? 'Không';
                    ?>
                    <span style="background: <?= $catColor['bg'] ?>; color: <?= $catColor['text'] ?>; padding: 2px 6px; border-radius: 4px; font-size: 0.65rem; font-weight: 600; opacity: 0.8;">
                        <?= htmlspecialchars($cat) ?>
                    </span>
                    <?php if ($gl !== 'Không'): $glColor = $goalColors[$gl] ?? ['bg' => '#fff', 'text' => '#333']; ?>
                        <span style="border: 1px solid <?= $glColor['text'] ?>; color: <?= $glColor['text'] ?>; padding: 1px 5px; border-radius: 4px; font-size: 0.65rem; font-weight: 600; opacity: 0.8;">
                            <?= htmlspecialchars($gl) ?>
                        </span>
                    <?php endif; ?>
                </div>

                <div style="display: flex; gap: 10px; margin-top: 10px;">
                    <a href="index.php?action=edit-task&id=<?= $task['id'] ?>" class="btn-status" style="color: #2383e2;">
                        <i class="fas fa-edit"></i> Sửa
                    </a>
                    <a href="index.php?action=delete-task&id=<?= $task['id'] ?>" class="btn-status" style="color: #dc3545;" onclick="return confirm('Việc đã xong, sếp muốn xóa hẳn cho sạch bảng?');">
                        <i class="fas fa-trash"></i> Xóa
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</div> 

<div id="taskModal" style="display:none; position:fixed; z-index:100; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.4);">
    <div style="background:white; margin:10% auto; padding:30px; width:400px; border-radius:8px; position:relative;">
        <h2 style="margin-top:0;">Thêm việc mới</h2>
        <form action="index.php?action=add-task" method="POST">
            <div class="form-group">
                <label>Tiêu đề</label>
                <input type="text" name="title" class="form-control" placeholder="Tên công việc..." required>
            </div>
            <div class="form-group">
                <label>Mô tả</label>
                <textarea name="description" class="form-control" rows="3"></textarea>
            </div>
            <div style="display:flex; gap:10px;">
                <div class="form-group" style="flex:1;">
                    <label>Bắt đầu</label>
                    <input type="date" name="start_date" class="form-control">
                </div>
                <div class="form-group" style="flex:1;">
                    <label>Deadline</label>
                    <input type="date" name="due_date" class="form-control">
                </div>
            </div>
            
            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                <div class="form-group" style="flex: 1; margin-bottom: 0;">
                    <label>Danh mục</label>
                    <select name="category" class="form-control">
                        <option value="Công việc">Công việc</option>
                        <option value="Học tập">Học tập</option>
                        <option value="Sức khỏe">Sức khỏe</option>
                        <option value="Tài chính">Tài chính</option>
                        <option value="Khác" selected>Khác</option>
                    </select>
                </div>
                <div class="form-group" style="flex: 1; margin-bottom: 0;">
                    <label>Mục tiêu liên kết</label>
                    <select name="goal_id" class="form-control">
                        <option value="">-- Không gắn mục tiêu --</option>
                        <?php foreach($userGoals as $g): ?>
                            <option value="<?= $g['id'] ?>"><?= htmlspecialchars($g['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Ưu tiên</label>
                <select name="priority" class="form-control">
                    <option value="Low">Thấp</option>
                    <option value="Medium" selected>Trung bình</option>
                    <option value="High">Cao</option>
                </select>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
                <button type="button" onclick="closeModal()" style="background:none; border:none; cursor:pointer;">Hủy</button>
                <button type="submit" class="btn-submit" style="width:auto; padding:8px 20px;">Lưu công việc</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal() { document.getElementById('taskModal').style.display = 'block'; }
function closeModal() { document.getElementById('taskModal').style.display = 'none'; }
</script>

<?php require_once PROJECT_ROOT . '/views/layout/footer.php'; ?>