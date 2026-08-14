<?php require_once PROJECT_ROOT . '/views/layout/header.php'; ?>

<?php $view = $_GET['view'] ?? 'list'; ?>

<?php if (isset($_SESSION['system_alert'])): ?>
    <script>
        alert("<?= __('sys_alert_title') ?>\n\n<?= $_SESSION['system_alert'] ?>");
    </script>
    <?php unset($_SESSION['system_alert']); ?>
<?php endif; ?>

<?php
// --- KÉO DỮ LIỆU TỪ ADMIN ---
$dbConfig = \Tinhu\TaskManager\Core\Database::getInstance()->getConnection();
$stmtLbl = $dbConfig->query("SELECT config_value FROM system_configs WHERE config_key = 'task_labels'");
$taskLabelsStr = $stmtLbl->fetchColumn() ?: 'Công việc, Học tập, Sức khỏe, Tài chính, Khác';
$taskLabels = array_map('trim', explode(',', $taskLabelsStr));

$stmtPri = $dbConfig->query("SELECT config_value FROM system_configs WHERE config_key = 'task_priorities'");
$taskPrioritiesStr = $stmtPri->fetchColumn() ?: 'Low, Medium, High';
$taskPriorities = array_map('trim', explode(',', $taskPrioritiesStr));

$catColors = [
    'Công việc' => ['bg' => '#fdecc8', 'text' => '#ad7f11'], 
    'Học tập' => ['bg' => '#e8f3fb', 'text' => '#0b6e99'], 
    'Sức khỏe' => ['bg' => '#f4e0f9', 'text' => '#8f24b2'], 
    'Tài chính' => ['bg' => '#e3f2fd', 'text' => '#1565c0'], 
    'Khác' => ['bg' => '#f1f1f0', 'text' => '#787774']
];
?>
<style>
    .task-card { background: #ffffff; border: 1px solid #e3e2e0; border-radius: 8px; padding: 15px; margin-bottom: 15px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04); transition: all 0.2s ease; }
    .task-card:hover { box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08); border-color: #cfcecc; transform: translateY(-2px); }
</style>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h1 style="font-size: 1.8rem; margin: 0; color: #37352f;"><?= __('personal_space') ?></h1>
    <button onclick="openModal()" style="background: #2383e2; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: 500; font-size: 0.95rem; box-shadow: 0 2px 5px rgba(35,131,226,0.3);">
        <?= __('add_task') ?>
    </button>
</div>

<div style="display: flex; gap: 35px; border-bottom: 2px solid #e3e2e0; margin-bottom: 30px;">
    <a href="index.php?action=tasks&view=list" style="text-decoration: none; font-size: 1.05rem; font-weight: 500; padding-bottom: 12px; margin-bottom: -2px; transition: 0.2s; color: <?= ($view == 'list') ? '#37352f' : '#787774' ?>; border-bottom: <?= ($view == 'list') ? '3px solid #2383e2' : '3px solid transparent' ?>;">
        <i class="fas fa-layer-group"></i> <?= __('task_board') ?>
    </a>
    <a href="index.php?action=tasks&view=dashboard" style="text-decoration: none; font-size: 1.05rem; font-weight: 500; padding-bottom: 12px; margin-bottom: -2px; transition: 0.2s; color: <?= ($view == 'dashboard') ? '#37352f' : '#787774' ?>; border-bottom: <?= ($view == 'dashboard') ? '3px solid #2383e2' : '3px solid transparent' ?>;">
        <i class="fas fa-chart-pie"></i> <?= __('dashboard_stats') ?>
    </a>
</div>

<?php if ($view == 'dashboard'): ?>
    <?php
        $allTasks = array_merge($todoTasks, $doingTasks, $pendingTasks, $doneTasks);
        
        $catStats = [];
        foreach($taskLabels as $lbl) { $catStats[$lbl] = 0; }
        $catStats['Khác'] = 0;
        foreach ($allTasks as $t) {
            $c = $t['category'] ?? 'Khác';
            if (isset($catStats[$c])) $catStats[$c]++; else $catStats[$c] = 1;
        }

        $upcomingPersonal = [];
        foreach (array_merge($todoTasks, $doingTasks, $pendingTasks) as $t) {
            if (!empty($t['due_date']) && $t['due_date'] != '0000-00-00') {
                $upcomingPersonal[] = $t;
            }
        }
        usort($upcomingPersonal, function($a, $b) { return strtotime($a['due_date']) - strtotime($b['due_date']); });
        $upcomingPersonal = array_slice($upcomingPersonal, 0, 5);

        $timelineLabels = [];
        $timelineData = [];
        for($i = 0; $i < 7; $i++) {
            $d = date('Y-m-d', strtotime("+$i days"));
            $timelineLabels[] = date('d/m', strtotime($d));
            $count = 0;
            foreach(array_merge($todoTasks, $doingTasks, $pendingTasks) as $t) {
                if ($t['due_date'] == $d) $count++;
            }
            $timelineData[] = $count;
        }
    ?>
    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 25px; margin-bottom: 25px;">
        <div style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #f0f0f0; box-shadow: 0 4px 15px rgba(0,0,0,0.03); text-align: center;">
            <h4 style="margin: 0 0 20px 0; color: #37352f; font-size: 1.1rem;"><?= __('stat_completion') ?></h4>
            <div style="width: 150px; height: 150px; margin: 0 auto; position: relative;">
                <canvas id="personalProgressChart"></canvas>
                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 1.6rem; font-weight: bold; color: #0f7b6c;">
                    <?= $percentPersonalDone ?? 0 ?>%
                </div>
            </div>
            <p style="margin: 15px 0 0 0; font-size: 0.95rem; color: #787774;"><?= __('stat_total_tasks') ?> <strong><?= $totalPersonalTasks ?? 0 ?></strong> <?= __('task_unit') ?></p>
        </div>
        <div style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #f0f0f0; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
            <h4 style="margin: 0 0 20px 0; color: #37352f; font-size: 1.1rem;"><?= __('stat_stages') ?></h4>
            <div style="display: flex; justify-content: space-around; align-items: center; height: calc(100% - 40px);">
                <div style="text-align: center; background: #fdfdfc; padding: 20px; border-radius: 10px; width: 20%;">
                    <div style="font-size: 2.2rem; font-weight: bold; color: #37352f;"><?= count($todoTasks) ?></div>
                    <div style="font-size: 0.85rem; color: #787774; font-weight: 500; margin-top: 8px;"><?= __('status_todo') ?></div>
                </div>
                <div style="text-align: center; background: #e8f3fb; padding: 20px; border-radius: 10px; width: 20%;">
                    <div style="font-size: 2.2rem; font-weight: bold; color: #0b6e99;"><?= count($doingTasks) ?></div>
                    <div style="font-size: 0.85rem; color: #0b6e99; font-weight: 500; margin-top: 8px;"><?= __('status_doing') ?></div>
                </div>
                <div style="text-align: center; background: #fef5e6; padding: 20px; border-radius: 10px; width: 20%;">
                    <div style="font-size: 2.2rem; font-weight: bold; color: #ad7f11;"><?= count($pendingTasks) ?></div>
                    <div style="font-size: 0.85rem; color: #ad7f11; font-weight: 500; margin-top: 8px;"><?= __('status_pending') ?></div>
                </div>
                <div style="text-align: center; background: #ebf5ea; padding: 20px; border-radius: 10px; width: 20%;">
                    <div style="font-size: 2.2rem; font-weight: bold; color: #0f7b6c;"><?= count($doneTasks) ?></div>
                    <div style="font-size: 0.85rem; color: #0f7b6c; font-weight: 500; margin-top: 8px;"><?= __('status_done') ?></div>
                </div>
            </div>
        </div>
    </div>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-bottom: 25px;">
        <div style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #f0f0f0; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
            <h4 style="margin: 0 0 20px 0; color: #37352f; font-size: 1.1rem;"><?= __('stat_category') ?></h4>
            <div style="max-height: 200px; overflow-y: auto; padding-right: 10px;">
                <?php if($totalPersonalTasks == 0): ?>
                    <p style="color: #787774; font-size: 0.95rem;"><?= __('no_data') ?></p>
                <?php else: ?>
                    <?php foreach($catStats as $catName => $count): 
                        if ($count == 0) continue;
                        $catPercent = round(($count / $totalPersonalTasks) * 100);
                        $cColor = $catColors[$catName]['text'] ?? '#787774';
                    ?>
                        <div style="margin-bottom: 15px;">
                            <div style="display: flex; justify-content: space-between; font-size: 0.9rem; margin-bottom: 8px;">
                                <span style="font-weight: 500; color: <?= $cColor ?>;"><?= $catName ?></span>
                                <span style="color: #787774; font-weight: 500;"><?= $count ?> <?= __('task_unit') ?> (<?= $catPercent ?>%)</span>
                            </div>
                            <div style="background: #e3e2e0; border-radius: 10px; height: 6px; width: 100%; overflow: hidden;">
                                <div style="background: <?= $cColor ?>; height: 100%; width: <?= $catPercent ?>%;"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <div style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #f0f0f0; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
            <h4 style="margin: 0 0 20px 0; color: #37352f; font-size: 1.1rem;"><i class="fas fa-bell" style="color: #eb3639;"></i> <?= __('stat_overdue') ?></h4>
            <ul style="list-style: none; padding: 0; margin: 0; max-height: 200px; overflow-y: auto; padding-right: 10px;">
                <?php if(empty($upcomingPersonal)): ?>
                    <li style="color: #0f7b6c; font-size: 0.95rem; margin-top: 5px;"><i class="fas fa-check-circle"></i> <?= __('safe_no_overdue') ?></li>
                <?php else: ?>
                    <?php foreach($upcomingPersonal as $dt): 
                        $isOverdue = strtotime($dt['due_date']) < strtotime(date('Y-m-d'));
                    ?>
                        <li style="padding: 12px 0; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <a href="index.php?action=edit-task&id=<?= $dt['id'] ?>" style="color: #37352f; text-decoration: none; font-weight: 600; font-size: 0.95rem; display: block; margin-bottom: 4px;"><?= htmlspecialchars($dt['title']) ?></a>
                                <span style="font-size: 0.75rem; color: <?= $catColors[$dt['category']]['text'] ?? '#787774' ?>; background: <?= $catColors[$dt['category']]['bg'] ?? '#f0f0f0' ?>; padding: 2px 6px; border-radius: 4px; font-weight: 500;"><?= htmlspecialchars($dt['category']) ?></span>
                            </div>
                            <span style="font-size: 0.8rem; font-weight: bold; color: <?= $isOverdue ? '#eb3639' : '#d9730d' ?>; text-align: right;">
                                <?= date('d/m/Y', strtotime($dt['due_date'])) ?><br>
                                <?= $isOverdue ? __('overdue_label') : __('upcoming_label') ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    <div style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #f0f0f0; box-shadow: 0 4px 15px rgba(0,0,0,0.03); margin-bottom: 25px;">
        <h4 style="margin: 0 0 20px 0; color: #37352f; font-size: 1.1rem;"><i class="fas fa-chart-line" style="color: #2383e2;"></i> <?= __('stat_workload') ?></h4>
        <div style="height: 250px; width: 100%;">
            <canvas id="timelineChart"></canvas>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctxPie = document.getElementById('personalProgressChart').getContext('2d');
        new Chart(ctxPie, {
            type: 'doughnut',
            data: { labels: ['Cần làm', 'Đang làm', 'Tạm hoãn', 'Xong'], datasets: [{ data: [<?= count($todoTasks) ?>, <?= count($doingTasks) ?>, <?= count($pendingTasks) ?>, <?= count($doneTasks) ?>], backgroundColor: ['#e3e2e0', '#d3e5ef', '#fdecc8', '#dbeddb'], borderWidth: 0, cutout: '75%' }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });
        const ctxTimeline = document.getElementById('timelineChart').getContext('2d');
        new Chart(ctxTimeline, {
            type: 'line',
            data: { labels: <?= json_encode($timelineLabels) ?>, datasets: [{ data: <?= json_encode($timelineData) ?>, borderColor: '#2383e2', backgroundColor: 'rgba(35, 131, 226, 0.1)', borderWidth: 2, fill: true, tension: 0.4, pointBackgroundColor: '#2383e2', pointRadius: 4 }] },
            options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } }, x: { grid: { display: false } } }, plugins: { legend: { display: false } } }
        });
    });
    </script>
<?php else: ?>
    <div style="background: #f7f7f5; padding: 15px; border-radius: 8px; margin-bottom: 20px; display: flex; gap: 15px; align-items: center; border: 1px solid #e3e2e0;">
        <span style="font-weight: 500; font-size: 0.9rem; color: #787774; min-width: max-content;">
            <i class="fas fa-filter"></i> <?= __('filter_search') ?>
        </span>
        <form action="index.php" method="GET" style="display: flex; gap: 15px; margin: 0; width: 100%; align-items: center;">
            <input type="hidden" name="action" value="tasks">
            <input type="hidden" name="view" value="list">
            <input type="text" name="search" placeholder="<?= __('search_placeholder') ?>" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" style="flex: 1; padding: 6px 10px; border-radius: 4px; border: 1px solid #ccc; font-size: 0.85rem;">
            <select name="category" onchange="this.form.submit()" style="padding: 6px 10px; border-radius: 4px; border: 1px solid #ccc; font-size: 0.85rem;">
                <option value=""><?= __('all_categories') ?></option>
                <?php foreach($taskLabels as $lbl): ?>
                    <option value="<?= htmlspecialchars($lbl) ?>" <?= (isset($_GET['category']) && $_GET['category'] == $lbl) ? 'selected' : '' ?>><?= htmlspecialchars($lbl) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="priority" onchange="this.form.submit()" style="padding: 6px 10px; border-radius: 4px; border: 1px solid #ccc; font-size: 0.85rem;">
                <option value=""><?= __('all_priorities') ?></option>
                <?php foreach($taskPriorities as $pri): ?>
                    <option value="<?= htmlspecialchars($pri) ?>" <?= (isset($_GET['priority']) && $_GET['priority'] == $pri) ? 'selected' : '' ?>><?= htmlspecialchars($pri) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" style="background: #2383e2; color: white; border: none; padding: 6px 15px; border-radius: 4px; cursor: pointer; font-size: 0.85rem;"><?= __('btn_filter') ?></button>
            <?php if(!empty($_GET['search']) || !empty($_GET['category']) || !empty($_GET['priority'])): ?>
                <a href="index.php?action=tasks&view=list" style="font-size: 0.85rem; color: #eb3639; text-decoration: none;"><?= __('btn_clear_filter') ?></a>
            <?php endif; ?>
        </form>
    </div>

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
                        <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 6px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4;"><?= nl2br(htmlspecialchars($task['description'])) ?></div>
                    <?php endif; ?>
                    <div style="margin-top: 6px; display: flex; gap: 5px; flex-wrap: wrap;">
                        <?php $cat = $task['category'] ?? 'Khác'; $catColor = $catColors[$cat] ?? $catColors['Khác']; ?>
                        <span style="background: <?= $catColor['bg'] ?>; color: <?= $catColor['text'] ?>; padding: 2px 6px; border-radius: 4px; font-size: 0.65rem; font-weight: 600;"><?= htmlspecialchars($cat) ?></span>
                    </div>
                    <?php 
                        $isOverdue = ($task['due_date'] != '0000-00-00' && !empty($task['due_date']) && $task['due_date'] <= date('Y-m-d'));
                        $dateColor = $isOverdue ? '#eb3639' : 'var(--text-muted)';
                    ?>
                    <div style="font-size: 0.75rem; color: <?= $dateColor ?>; margin-top: 8px; display: flex; align-items: center; gap: 5px; <?= $isOverdue ? 'font-weight: 600;' : '' ?>">
                        <i class="far fa-calendar-alt"></i> 
                        <span>
                            <?php 
                                if (empty($task['due_date']) || $task['due_date'] == '0000-00-00') echo __('no_deadline'); 
                                else { echo date('d/m/Y', strtotime($task['due_date'])); if ($isOverdue) echo ' ' . __('overdue_label'); }
                            ?>
                        </span>
                    </div>
                    <div style="display: flex; gap: 10px; margin-top: 10px;">
                        <a href="index.php?action=update-task&id=<?= $task['id'] ?>&status=Doing" class="btn-status"><i class="fas fa-play" style="font-size: 0.6rem;"></i> <?= __('btn_start') ?></a>
                        <a href="index.php?action=edit-task&id=<?= $task['id'] ?>" class="btn-status" style="color: #2383e2;"><i class="fas fa-edit"></i> <?= __('btn_edit') ?></a>
                        <a href="index.php?action=delete-task&id=<?= $task['id'] ?>" class="btn-status" style="color: #dc3545;" onclick="return confirm('<?= __('confirm_delete') ?>');"><i class="fas fa-trash"></i></a>
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
                    <div style="display: flex; gap: 10px; margin-top: 10px;">
                        <a href="index.php?action=update-task&id=<?= $task['id'] ?>&status=Done" class="btn-status" style="color: #0f7b6c;"><i class="fas fa-check"></i> Done</a>
                        <a href="index.php?action=update-task&id=<?= $task['id'] ?>&status=Pending" class="btn-status" style="color: #ad7f11;"><i class="fas fa-pause"></i> Delay</a>
                        <a href="index.php?action=delete-task&id=<?= $task['id'] ?>" class="btn-status" style="color: #dc3545;" onclick="return confirm('<?= __('confirm_delete') ?>');"><i class="fas fa-trash"></i></a>
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
                    <div style="display: flex; gap: 10px; margin-top: 10px;">
                        <a href="index.php?action=update-task&id=<?= $task['id'] ?>&status=Doing" class="btn-status"><i class="fas fa-play"></i> <?= __('btn_resume') ?></a>
                        <a href="index.php?action=delete-task&id=<?= $task['id'] ?>" class="btn-status" style="color: #dc3545;" onclick="return confirm('<?= __('confirm_delete') ?>');"><i class="fas fa-trash"></i></a>
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
                    <div style="font-weight: 500; text-decoration: line-through;"><?= htmlspecialchars($task['title']); ?></div>
                    <div style="display: flex; gap: 10px; margin-top: 10px;">
                        <a href="index.php?action=edit-task&id=<?= $task['id'] ?>" class="btn-status" style="color: #2383e2;"><i class="fas fa-edit"></i> <?= __('btn_edit') ?></a>
                        <a href="index.php?action=delete-task&id=<?= $task['id'] ?>" class="btn-status" style="color: #dc3545;" onclick="return confirm('<?= __('confirm_delete_done') ?>');"><i class="fas fa-trash"></i> <?= __('btn_delete') ?></a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div> 
<?php endif; ?>

<div id="taskModal" style="display:none; position:fixed; z-index:100; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.4);">
    <div style="background:white; margin:10% auto; padding:30px; width:400px; border-radius:8px; position:relative;">
        <h2 style="margin-top:0;"><?= __('modal_add_task') ?></h2>
        <form action="index.php?action=add-task" method="POST">
            <div class="form-group">
                <label><?= __('lbl_title') ?></label>
                <input type="text" name="title" class="form-control" placeholder="..." required>
            </div>
            <div class="form-group">
                <label><?= __('lbl_desc') ?></label>
                <textarea name="description" class="form-control" rows="3"></textarea>
            </div>
            <div style="display:flex; gap:10px;">
                <div class="form-group" style="flex:1;"><label><?= __('lbl_start') ?></label><input type="date" name="start_date" class="form-control"></div>
                <div class="form-group" style="flex:1;"><label><?= __('lbl_deadline') ?></label><input type="date" name="due_date" class="form-control"></div>
            </div>
            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                <div class="form-group" style="flex: 1; margin-bottom: 0;">
                    <label><?= __('lbl_category') ?></label>
                    <select name="category" class="form-control">
                        <?php foreach($taskLabels as $lbl): ?>
                            <option value="<?= htmlspecialchars($lbl) ?>"><?= htmlspecialchars($lbl) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group" style="flex: 1; margin-bottom: 0;">
                    <label><?= __('lbl_goal') ?></label>
                    <select name="goal_id" class="form-control">
                        <option value=""><?= __('no_goal') ?></option>
                        <?php foreach($userGoals as $g): ?>
                            <option value="<?= $g['id'] ?>"><?= htmlspecialchars($g['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label><?= __('lbl_priority') ?></label>
                <select name="priority" class="form-control">
                    <?php foreach($taskPriorities as $pri): ?>
                        <option value="<?= htmlspecialchars($pri) ?>" <?= $pri == 'Medium' ? 'selected' : '' ?>><?= htmlspecialchars($pri) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
                <button type="button" onclick="closeModal()" style="background:none; border:1px solid #ccc; padding:8px 15px; cursor:pointer; border-radius:4px;"><?= __('btn_cancel') ?></button>
                <button type="submit" class="btn-submit" style="width:auto; padding:8px 20px; background: #2383e2; color: white; border: none; border-radius: 4px;"><?= __('btn_save_task') ?></button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal() { document.getElementById('taskModal').style.display = 'block'; }
function closeModal() { document.getElementById('taskModal').style.display = 'none'; }
</script>

<?php require_once PROJECT_ROOT . '/views/layout/footer.php'; ?>