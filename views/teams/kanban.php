<?php require_once PROJECT_ROOT . '/views/layout/header.php'; ?>

<style>
    .task-card { background: #ffffff; border: 1px solid #e3e2e0; border-radius: 8px; padding: 15px; margin-bottom: 15px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04); transition: all 0.2s ease; }
    .task-card:hover { box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08); border-color: #cfcecc; transform: translateY(-2px); }
    .pri-High { color: #eb3639; font-weight: 600; }
    .pri-Medium { color: #d9730d; font-weight: 600; }
    .pri-Low { color: #0f7b6c; font-weight: 600; }
</style>

<div style="margin-bottom: 15px;">
    <a href="index.php?action=team-detail&id=<?= $project['team_id'] ?>" style="color: #787774; text-decoration: none; font-size: 0.95rem;"><i class="fas fa-arrow-left"></i> Về không gian nhóm</a>
</div>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <div>
        <h1 style="font-size: 1.8rem; margin: 0 0 5px 0;">Dự án: <?= htmlspecialchars($project['name']) ?></h1>
        <p style="color: #787774; margin: 0; font-size: 0.95rem;"><?= htmlspecialchars($project['description']) ?></p>
    </div>
    
    <?php if (isset($canEdit) && $canEdit): ?>
    <button onclick="document.getElementById('taskModal').style.display='block'" style="background: #2383e2; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: 500;">
        + Phân công việc
    </button>
    <?php endif; ?>
</div>

<div style="background: #f7f7f5; padding: 15px; border-radius: 8px; margin-bottom: 20px; display: flex; gap: 15px; align-items: center; border: 1px solid #e3e2e0;">
    <span style="font-weight: 500; font-size: 0.9rem; color: #787774;"><i class="fas fa-filter"></i> Lọc & Sắp xếp:</span>
    
    <form action="index.php" method="GET" style="display: flex; gap: 15px; margin: 0; width: 100%;">
        <input type="hidden" name="action" value="project-kanban">
        <input type="hidden" name="id" value="<?= $project['id'] ?>">
        
        <select name="assignee" onchange="this.form.submit()" style="padding: 6px 10px; border-radius: 4px; border: 1px solid #ccc; font-size: 0.85rem;">
            <option value="">Tất cả thành viên</option>
            <option value="unassigned" <?= (isset($_GET['assignee']) && $_GET['assignee'] === 'unassigned') ? 'selected' : '' ?>>Chưa giao ai</option>
            <?php foreach($members as $m): ?>
                <option value="<?= $m['id'] ?>" <?= (isset($_GET['assignee']) && $_GET['assignee'] == $m['id']) ? 'selected' : '' ?>>
                    Việc của <?= htmlspecialchars($m['fullname']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="sort" onchange="this.form.submit()" style="padding: 6px 10px; border-radius: 4px; border: 1px solid #ccc; font-size: 0.85rem;">
            <option value="">Sắp xếp mặc định</option>
            <option value="deadline_asc" <?= (isset($_GET['sort']) && $_GET['sort'] === 'deadline_asc') ? 'selected' : '' ?>>Deadline: Gần nhất trước</option>
            <option value="deadline_desc" <?= (isset($_GET['sort']) && $_GET['sort'] === 'deadline_desc') ? 'selected' : '' ?>>Deadline: Xa nhất trước</option>
        </select>
        
        <?php if(!empty($_GET['assignee']) || !empty($_GET['sort'])): ?>
            <a href="index.php?action=project-kanban&id=<?= $project['id'] ?>" style="font-size: 0.85rem; color: #eb3639; text-decoration: none; padding-top: 6px;">Xóa bộ lọc</a>
        <?php endif; ?>
    </form>
</div>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; margin-bottom: 30px;">
    <div style="background: white; padding: 20px; border-radius: 8px; border: 1px solid #e3e2e0; display: flex; align-items: center; gap: 25px;">
        <div style="width: 120px; height: 120px; position: relative;">
            <canvas id="progressChart"></canvas>
            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 1.2rem; font-weight: bold; color: #0f7b6c;">
                <?= $percentDone ?? 0 ?>%
            </div>
        </div>
        <div>
            <h4 style="margin: 0 0 5px 0; color: #37352f; font-size: 1rem;">Tiến độ tổng thể</h4>
            <p style="margin: 0; font-size: 0.85rem; color: #787774;">Dựa trên <strong><?= $totalTasks ?? 0 ?></strong> công việc</p>
        </div>
    </div>

    <div style="background: white; padding: 20px; border-radius: 8px; border: 1px solid #e3e2e0; display: flex; justify-content: space-around; align-items: center;">
        <div style="text-align: center;">
            <div style="font-size: 1.8rem; font-weight: bold; color: #37352f;"><?= $chartData['Backlog'] ?? 0 ?></div>
            <div style="font-size: 0.85rem; color: #787774; margin-top: 5px; font-weight: 500;">Chờ làm (Backlog)</div>
        </div>
        <div style="text-align: center;">
            <div style="font-size: 1.8rem; font-weight: bold; color: #0b6e99;"><?= $chartData['In Progress'] ?? 0 ?></div>
            <div style="font-size: 0.85rem; color: #787774; margin-top: 5px; font-weight: 500;">Đang làm (In Progress)</div>
        </div>
        <div style="text-align: center;">
            <div style="font-size: 1.8rem; font-weight: bold; color: #ad7f11;"><?= $chartData['Review'] ?? 0 ?></div>
            <div style="font-size: 0.85rem; color: #787774; margin-top: 5px; font-weight: 500;">Chờ duyệt (Review)</div>
        </div>
        <div style="text-align: center;">
            <div style="font-size: 1.8rem; font-weight: bold; color: #0f7b6c;"><?= $chartData['Done'] ?? 0 ?></div>
            <div style="font-size: 0.85rem; color: #787774; margin-top: 5px; font-weight: 500;">Hoàn thành (Done)</div>
        </div>
    </div>
</div>
<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; align-items: start;">
    
    <div class="kanban-column">
        <div style="font-weight: 600; margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
            <span style="background: #e3e2e0; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem;">Backlog</span>
            <span style="color: var(--text-muted); font-weight: 400;"><?= count($backlogTasks) ?></span>
        </div>
        <?php foreach ($backlogTasks as $t): ?>
            <?php 
                $isAssignee = ($t['assigned_to'] == $_SESSION['user_id']); 
                $isOverdue = (!empty($t['due_date']) && strtotime($t['due_date']) < strtotime(date('Y-m-d')));
            ?>
            <div class="task-card">
                <a href="index.php?action=team-task-detail&task_id=<?= $t['id'] ?>&project_id=<?= $project['id'] ?>" 
                   style="font-weight: 500; margin-bottom: 8px; display: block; color: #37352f; text-decoration: none;">
                    <?= htmlspecialchars($t['title']) ?>
                </a>
                
                <div style="font-size: 0.75rem; color: #787774; margin-bottom: 8px; display: flex; justify-content: space-between;">
                    <span><i class="fas fa-user-circle"></i> <?= $t['assignee_name'] ? htmlspecialchars($t['assignee_name']) : 'Chưa giao' ?></span>
                    <span class="pri-<?= $t['priority'] ?>"><i class="fas fa-flag"></i> <?= $t['priority'] ?></span>
                </div>

                <?php if(!empty($t['due_date']) && $t['due_date'] != '0000-00-00'): ?>
                <div style="font-size: 0.75rem; color: <?= $isOverdue ? '#eb3639' : '#787774' ?>; margin-bottom: 10px; font-weight: <?= $isOverdue ? 'bold' : 'normal' ?>;">
                    <i class="far fa-calendar-alt"></i> Deadline: <?= date('d/m/Y', strtotime($t['due_date'])) ?> <?= $isOverdue ? '(Quá hạn)' : '' ?>
                </div>
                <?php endif; ?>

                <div style="display: flex; gap: 10px; justify-content: space-between;">
                    <?php if ($isAssignee || (isset($canEdit) && $canEdit)): ?>
                        <a href="index.php?action=update-team-task&task_id=<?= $t['id'] ?>&project_id=<?= $project['id'] ?>&status=In Progress" style="font-size: 0.8rem; color: #2383e2; text-decoration: none; font-weight: 500;"><i class="fas fa-play"></i> Làm ngay &rarr;</a>
                    <?php endif; ?>
                </div>
                
                <?php if (isset($canEdit) && $canEdit): ?>
                <div style="display: flex; gap: 10px; margin-top: 10px; border-top: 1px solid #f0f0f0; padding-top: 8px;">
                    <a href="index.php?action=edit-team-task&id=<?= $t['id'] ?>&project_id=<?= $project['id'] ?>" style="font-size: 0.75rem; color: #2383e2; text-decoration: none;"><i class="fas fa-edit"></i> Sửa</a>
                    <a href="index.php?action=delete-team-task&id=<?= $t['id'] ?>&project_id=<?= $project['id'] ?>" style="font-size: 0.75rem; color: #eb3639; text-decoration: none;" onclick="return confirm('Chắc chắn muốn xóa việc nhóm này?');"><i class="fas fa-trash"></i> Xóa</a>
                </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="kanban-column">
        <div style="font-weight: 600; margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
            <span style="background: #d3e5ef; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem;">In Progress</span>
            <span style="color: var(--text-muted); font-weight: 400;"><?= count($inProgressTasks) ?></span>
        </div>
        <?php foreach ($inProgressTasks as $t): ?>
            <?php 
                $isAssignee = ($t['assigned_to'] == $_SESSION['user_id']); 
                $isOverdue = (!empty($t['due_date']) && strtotime($t['due_date']) < strtotime(date('Y-m-d')));
            ?>
            <div class="task-card">
                <a href="index.php?action=team-task-detail&task_id=<?= $t['id'] ?>&project_id=<?= $project['id'] ?>" 
                   style="font-weight: 500; margin-bottom: 8px; display: block; color: #0b6e99; text-decoration: none;">
                    <?= htmlspecialchars($t['title']) ?>
                </a>
                
                <div style="font-size: 0.75rem; color: #787774; margin-bottom: 8px; display: flex; justify-content: space-between;">
                    <span><i class="fas fa-user-circle"></i> <?= $t['assignee_name'] ? htmlspecialchars($t['assignee_name']) : 'Chưa giao' ?></span>
                    <span class="pri-<?= $t['priority'] ?>"><i class="fas fa-flag"></i> <?= $t['priority'] ?></span>
                </div>

                <?php if(!empty($t['due_date']) && $t['due_date'] != '0000-00-00'): ?>
                <div style="font-size: 0.75rem; color: <?= $isOverdue ? '#eb3639' : '#787774' ?>; margin-bottom: 10px; font-weight: <?= $isOverdue ? 'bold' : 'normal' ?>;">
                    <i class="far fa-calendar-alt"></i> Deadline: <?= date('d/m/Y', strtotime($t['due_date'])) ?> <?= $isOverdue ? '(Quá hạn)' : '' ?>
                </div>
                <?php endif; ?>

                <div style="display: flex; gap: 10px; justify-content: space-between;">
                    <?php if ($isAssignee || (isset($canEdit) && $canEdit)): ?>
                        <a href="index.php?action=update-team-task&task_id=<?= $t['id'] ?>&project_id=<?= $project['id'] ?>&status=Backlog" style="font-size: 0.8rem; color: #787774; text-decoration: none;">&larr; Lùi</a>
                        <a href="index.php?action=update-team-task&task_id=<?= $t['id'] ?>&project_id=<?= $project['id'] ?>&status=Review" style="font-size: 0.8rem; color: #d9730d; text-decoration: none; font-weight: 500;">Trình duyệt &rarr;</a>
                    <?php endif; ?>
                </div>
                
                <?php if (isset($canEdit) && $canEdit): ?>
                <div style="display: flex; gap: 10px; margin-top: 10px; border-top: 1px solid #f0f0f0; padding-top: 8px;">
                    <a href="index.php?action=edit-team-task&id=<?= $t['id'] ?>&project_id=<?= $project['id'] ?>" style="font-size: 0.75rem; color: #2383e2; text-decoration: none;"><i class="fas fa-edit"></i> Sửa</a>
                    <a href="index.php?action=delete-team-task&id=<?= $t['id'] ?>&project_id=<?= $project['id'] ?>" style="font-size: 0.75rem; color: #eb3639; text-decoration: none;" onclick="return confirm('Chắc chắn muốn xóa việc nhóm này?');"><i class="fas fa-trash"></i> Xóa</a>
                </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="kanban-column">
        <div style="font-weight: 600; margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
            <span style="background: #fdecc8; color: #ad7f11; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem;">Review</span>
            <span style="color: var(--text-muted); font-weight: 400;"><?= count($reviewTasks) ?></span>
        </div>
        <?php foreach ($reviewTasks as $t): ?>
            <div class="task-card" style="border-left: 3px solid #d9730d;">
                <a href="index.php?action=team-task-detail&task_id=<?= $t['id'] ?>&project_id=<?= $project['id'] ?>" 
                   style="font-weight: 500; margin-bottom: 8px; display: block; color: #ad7f11; text-decoration: none;">
                    <?= htmlspecialchars($t['title']) ?>
                </a>
                
                <div style="font-size: 0.75rem; color: #787774; margin-bottom: 8px; display: flex; justify-content: space-between;">
                    <span><i class="fas fa-user-circle"></i> <?= $t['assignee_name'] ? htmlspecialchars($t['assignee_name']) : 'Chưa giao' ?></span>
                </div>

                <div style="display: flex; gap: 10px; justify-content: space-between;">
                    <?php if (isset($canEdit) && $canEdit): ?>
                        <a href="index.php?action=update-team-task&task_id=<?= $t['id'] ?>&project_id=<?= $project['id'] ?>&status=In Progress" style="font-size: 0.8rem; color: #eb3639; text-decoration: none;">&larr; Bắt làm lại</a>
                        <a href="index.php?action=update-team-task&task_id=<?= $t['id'] ?>&project_id=<?= $project['id'] ?>&status=Done" style="font-size: 0.8rem; color: #0f7b6c; text-decoration: none; font-weight: 500;"><i class="fas fa-check-double"></i> Duyệt (Done)</a>
                    <?php else: ?>
                        <span style="font-size: 0.8rem; color: #ad7f11; font-style: italic;"><i class="fas fa-spinner fa-spin"></i> Đang chờ sếp duyệt...</span>
                    <?php endif; ?>
                </div>
                
                <?php if (isset($canEdit) && $canEdit): ?>
                <div style="display: flex; gap: 10px; margin-top: 10px; border-top: 1px solid #f0f0f0; padding-top: 8px;">
                    <a href="index.php?action=edit-team-task&id=<?= $t['id'] ?>&project_id=<?= $project['id'] ?>" style="font-size: 0.75rem; color: #2383e2; text-decoration: none;"><i class="fas fa-edit"></i> Sửa</a>
                </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="kanban-column">
        <div style="font-weight: 600; margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
            <span style="background: #dbeddb; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem;">Done</span>
            <span style="color: var(--text-muted); font-weight: 400;"><?= count($doneTasks) ?></span>
        </div>
        <?php foreach ($doneTasks as $t): ?>
            <div class="task-card" style="opacity: 0.6; border-left: 3px solid #0f7b6c;">
                <a href="index.php?action=team-task-detail&task_id=<?= $t['id'] ?>&project_id=<?= $project['id'] ?>" 
                   style="font-weight: 500; margin-bottom: 8px; display: block; color: #37352f; text-decoration: line-through;">
                    <?= htmlspecialchars($t['title']) ?>
                </a>
                <div style="font-size: 0.75rem; color: #787774; margin-bottom: 10px;">
                    <i class="fas fa-check-circle" style="color: #0f7b6c;"></i> <?= $t['assignee_name'] ? htmlspecialchars($t['assignee_name']) : 'Chưa giao' ?>
                </div>
                
                <div style="display: flex; gap: 10px; justify-content: space-between;">
                    <?php if (isset($canEdit) && $canEdit): ?>
                        <a href="index.php?action=update-team-task&task_id=<?= $t['id'] ?>&project_id=<?= $project['id'] ?>&status=Review" style="font-size: 0.8rem; color: #787774; text-decoration: none;">&larr; Hủy duyệt (Undo)</a>
                    <?php endif; ?>
                </div>

                <?php if (isset($canEdit) && $canEdit): ?>
                <div style="display: flex; gap: 10px; margin-top: 10px; border-top: 1px solid #f0f0f0; padding-top: 8px;">
                    <a href="index.php?action=delete-team-task&id=<?= $t['id'] ?>&project_id=<?= $project['id'] ?>" style="font-size: 0.75rem; color: #eb3639; text-decoration: none;" onclick="return confirm('Chắc chắn muốn xóa việc nhóm này?');"><i class="fas fa-trash"></i> Xóa</a>
                </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div id="taskModal" style="display:none; position:fixed; z-index:100; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.4);">
    <div style="background:white; margin:10% auto; padding:30px; width:400px; border-radius:8px;">
        <h2 style="margin-top:0;">Phân công việc</h2>
        <form action="index.php?action=add-team-task" method="POST">
            <input type="hidden" name="project_id" value="<?= $project['id'] ?>">
            <div class="form-group" style="margin-bottom: 15px;">
                <label>Giao cho ai?</label>
                <select name="assigned_to" class="form-control" style="width: 100%; padding: 8px; box-sizing: border-box;">
                    <option value="">-- Chưa giao ai --</option>
                    <?php foreach($members as $m): ?>
                        <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['fullname']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="margin-bottom: 15px;">
                <label>Tên công việc</label>
                <input type="text" name="title" class="form-control" required style="width: 100%; padding: 8px; box-sizing: border-box;">
            </div>
            <div class="form-group" style="margin-bottom: 15px;">
                <label>Mô tả chi tiết</label>
                <textarea name="description" class="form-control" rows="3" style="width: 100%; padding: 8px; box-sizing: border-box;"></textarea>
            </div>
            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <label>Deadline</label>
                    <input type="date" name="due_date" class="form-control" style="width: 100%; padding: 8px; box-sizing: border-box;">
                </div>
                <div style="flex: 1;">
                    <label>Ưu tiên</label>
                    <select name="priority" class="form-control" style="width: 100%; padding: 8px; box-sizing: border-box;">
                        <option value="Low">Thấp</option>
                        <option value="Medium" selected>Trung bình</option>
                        <option value="High">Cao</option>
                    </select>
                </div>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
                <button type="button" onclick="document.getElementById('taskModal').style.display='none'" style="background:none; border:1px solid #ccc; padding:8px 15px; cursor:pointer; border-radius:4px;">Hủy</button>
                <button type="submit" style="background:#2383e2; color:white; border:none; padding:8px 15px; cursor:pointer; border-radius:4px;">Giao việc</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('progressChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Backlog', 'In Progress', 'Review', 'Done'],
            datasets: [{
                data: [
                    <?= $chartData['Backlog'] ?? 0 ?>, 
                    <?= $chartData['In Progress'] ?? 0 ?>, 
                    <?= $chartData['Review'] ?? 0 ?>, 
                    <?= $chartData['Done'] ?? 0 ?>
                ],
                backgroundColor: ['#e3e2e0', '#d3e5ef', '#fdecc8', '#dbeddb'],
                hoverBackgroundColor: ['#cfcecc', '#b9d5e5', '#fbd58e', '#c1e1c1'],
                borderWidth: 0,
                cutout: '75%'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return ' ' + context.label + ': ' + context.raw + ' task';
                        }
                    }
                }
            }
        }
    });
});
</script>

<?php require_once PROJECT_ROOT . '/views/layout/footer.php'; ?>