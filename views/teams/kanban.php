<?php require_once PROJECT_ROOT . '/views/layout/header.php'; ?>
<?php $view = $_GET['view'] ?? 'kanban'; ?>

<?php if (isset($_SESSION['system_alert'])): ?>
    <script>
        alert("<?= $_SESSION['system_alert'] ?>");
    </script>
    <?php unset($_SESSION['system_alert']); ?>
<?php endif; ?>

<style>
    .task-card { background: #ffffff; border: 1px solid #e3e2e0; border-radius: 8px; padding: 15px; margin-bottom: 15px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04); transition: all 0.2s ease; }
    .task-card:hover { box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08); border-color: #cfcecc; transform: translateY(-2px); }
    .pri-High { color: #eb3639; font-weight: 600; }
    .pri-Medium { color: #d9730d; font-weight: 600; }
    .pri-Low { color: #0f7b6c; font-weight: 600; }
    .custom-scroll::-webkit-scrollbar { width: 6px; }
    .custom-scroll::-webkit-scrollbar-track { background: transparent; }
    .custom-scroll::-webkit-scrollbar-thumb { background: #e3e2e0; border-radius: 4px; }
    .custom-scroll::-webkit-scrollbar-thumb:hover { background: #cfcecc; }
    @media print {
        .sidebar, button, a, form, .custom-scroll { display: none !important; }
        .main-content { margin-left: 0 !important; width: 100% !important; }
        body { background: white; }
    }
</style>

<div style="margin-bottom: 15px;">
    <a href="index.php?action=team-detail&id=<?= $project['team_id'] ?>" style="color: #787774; text-decoration: none; font-size: 0.95rem;"><i class="fas fa-arrow-left"></i> <?= __('back_to_team_space') ?></a>
</div>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <div>
        <h1 style="font-size: 1.8rem; margin: 0 0 5px 0;"><?= __('project_title_prefix') ?> <?= htmlspecialchars($project['name']) ?></h1>
        <p style="color: #787774; margin: 0; font-size: 0.95rem;"><?= htmlspecialchars($project['description']) ?></p>
    </div>
    
    <?php if (isset($canEdit) && $canEdit): ?>
    <button onclick="document.getElementById('taskModal').style.display='block'" style="background: #2383e2; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: 500; font-size: 0.95rem; box-shadow: 0 2px 5px rgba(35,131,226,0.3);">
        + <?= __('modal_assign_task') ?>
    </button>
    <?php endif; ?>
</div>

<div style="display: flex; gap: 35px; border-bottom: 2px solid #e3e2e0; margin-bottom: 30px;">
    <a href="index.php?action=project-kanban&id=<?= $project['id'] ?>&view=kanban" 
        style="text-decoration: none; font-size: 1.05rem; font-weight: 500; padding-bottom: 12px; margin-bottom: -2px; transition: 0.2s; 
            color: <?= ($view == 'kanban') ? '#37352f' : '#787774' ?>; 
            border-bottom: <?= ($view == 'kanban') ? '3px solid #2383e2' : '3px solid transparent' ?>;">
        <i class="fas fa-layer-group"></i> <?= __('tab_kanban') ?>
    </a>
    <a href="index.php?action=project-kanban&id=<?= $project['id'] ?>&view=dashboard" 
        style="text-decoration: none; font-size: 1.05rem; font-weight: 500; padding-bottom: 12px; margin-bottom: -2px; transition: 0.2s; 
            color: <?= ($view == 'dashboard') ? '#37352f' : '#787774' ?>; 
            border-bottom: <?= ($view == 'dashboard') ? '3px solid #2383e2' : '3px solid transparent' ?>;">
        <i class="fas fa-chart-pie"></i> <?= __('tab_dashboard_stats') ?>
    </a>
    <a href="index.php?action=project-kanban&id=<?= $project['id'] ?>&view=chat" 
        style="text-decoration: none; font-size: 1.05rem; font-weight: 500; padding-bottom: 12px; margin-bottom: -2px; transition: 0.2s; 
            color: <?= ($view == 'chat') ? '#37352f' : '#787774' ?>; 
            border-bottom: <?= ($view == 'chat') ? '3px solid #2383e2' : '3px solid transparent' ?>;">
        <i class="fas fa-comments"></i> <?= __('tab_general_chat') ?>
    </a>
</div>

<?php if ($view == 'dashboard'): ?>
    <div style="text-align: right; margin-bottom: 20px; display: flex; justify-content: flex-end; gap: 10px;">
        <button onclick="window.print()" style="background: #eb3639; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-size: 0.95rem; font-weight: 500; box-shadow: 0 2px 5px rgba(235,54,57,0.3);">
            <i class="fas fa-file-pdf"></i> <?= __('btn_print_pdf') ?>
        </button>
        
        <a href="index.php?action=export-excel&project_id=<?= $project['id'] ?>" style="background: #0f7b6c; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-size: 0.95rem; font-weight: 500; box-shadow: 0 2px 5px rgba(15,123,108,0.3);">
            <i class="fas fa-file-excel"></i> <?= __('btn_export_excel') ?>
        </a>
    </div>
    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 25px; margin-bottom: 25px;">
        
        <div style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #f0f0f0; box-shadow: 0 4px 15px rgba(0,0,0,0.03); text-align: center;">
            <h4 style="margin: 0 0 20px 0; color: #37352f; font-size: 1.1rem;"><?= __('stat_completion_rate') ?></h4>
            <div style="width: 150px; height: 150px; margin: 0 auto; position: relative;">
                <canvas id="progressChart"></canvas>
                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 1.6rem; font-weight: bold; color: #0f7b6c;">
                    <?= $percentDone ?? 0 ?>%
                </div>
            </div>
            <p style="margin: 15px 0 0 0; font-size: 0.95rem; color: #787774;"><?= __('stat_total_tasks') ?> <strong><?= $totalTasks ?? 0 ?></strong> task</p>
        </div>

        <div style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #f0f0f0; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
            <h4 style="margin: 0 0 20px 0; color: #37352f; font-size: 1.1rem;"><?= __('stat_stage_title') ?></h4>
            <div style="display: flex; justify-content: space-around; align-items: center; height: calc(100% - 40px);">
                <div style="text-align: center; background: #fdfdfc; padding: 20px; border-radius: 10px; width: 20%;">
                    <div style="font-size: 2.2rem; font-weight: bold; color: #37352f;"><?= $chartData['Backlog'] ?? 0 ?></div>
                    <div style="font-size: 0.85rem; color: #787774; font-weight: 500; margin-top: 8px;"><?= __('stage_todo') ?></div>
                </div>
                <div style="text-align: center; background: #e8f3fb; padding: 20px; border-radius: 10px; width: 20%;">
                    <div style="font-size: 2.2rem; font-weight: bold; color: #0b6e99;"><?= $chartData['In Progress'] ?? 0 ?></div>
                    <div style="font-size: 0.85rem; color: #0b6e99; font-weight: 500; margin-top: 8px;"><?= __('stage_doing') ?></div>
                </div>
                <div style="text-align: center; background: #fef5e6; padding: 20px; border-radius: 10px; width: 20%;">
                    <div style="font-size: 2.2rem; font-weight: bold; color: #ad7f11;"><?= $chartData['Review'] ?? 0 ?></div>
                    <div style="font-size: 0.85rem; color: #ad7f11; font-weight: 500; margin-top: 8px;"><?= __('stage_review') ?></div>
                </div>
                <div style="text-align: center; background: #ebf5ea; padding: 20px; border-radius: 10px; width: 20%;">
                    <div style="font-size: 2.2rem; font-weight: bold; color: #0f7b6c;"><?= $chartData['Done'] ?? 0 ?></div>
                    <div style="font-size: 0.85rem; color: #0f7b6c; font-weight: 500; margin-top: 8px;"><?= __('stage_done') ?></div>
                </div>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px;">
        <div style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #f0f0f0; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
            <h4 style="margin: 0 0 20px 0; color: #37352f; font-size: 1.1rem;"><?= __('stat_member_productivity') ?></h4>
            <div class="custom-scroll" style="max-height: 250px; overflow-y: auto; padding-right: 15px;">
                <?php if(empty($memberStats)): ?>
                    <p style="color: #787774; font-size: 0.95rem;"><?= __('no_assignee_data') ?></p>
                <?php else: ?>
                    <?php foreach($memberStats as $ms): 
                        $memPercent = ($ms['total_tasks'] > 0) ? round(($ms['done_tasks'] / $ms['total_tasks']) * 100) : 0;
                        $displayName = $ms['fullname'] ?: __('unassigned_label');
                    ?>
                        <div style="margin-bottom: 20px;">
                            <div style="display: flex; justify-content: space-between; font-size: 0.95rem; margin-bottom: 8px;">
                                <span style="font-weight: 500; color: #37352f;"><i class="fas fa-user-circle" style="color: #ccc; margin-right: 5px;"></i> <?= htmlspecialchars($displayName) ?></span>
                                <span style="color: #787774; font-weight: 500;"><?= $ms['done_tasks'] ?>/<?= $ms['total_tasks'] ?> <?= __('task_unit') ?> (<?= $memPercent ?>%)</span>
                            </div>
                            <div style="background: #e3e2e0; border-radius: 10px; height: 8px; width: 100%; overflow: hidden;">
                                <div style="background: <?= $memPercent == 100 ? '#0f7b6c' : '#2383e2' ?>; height: 100%; width: <?= $memPercent ?>%;"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #f0f0f0; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
            <h4 style="margin: 0 0 20px 0; color: #37352f; font-size: 1.1rem;"><i class="fas fa-bell" style="color: #eb3639;"></i> <?= __('stat_deadline_alert') ?></h4>
            <ul class="custom-scroll" style="list-style: none; padding: 0; margin: 0; max-height: 250px; overflow-y: auto; padding-right: 15px;">
                <?php if(empty($deadlineTasks)): ?>
                    <li style="color: #0f7b6c; font-size: 0.95rem; margin-top: 5px;"><i class="fas fa-check-circle"></i> <?= __('safe_kanban_alert') ?></li>
                <?php else: ?>
                    <?php foreach($deadlineTasks as $dt): 
                        $isOverdue = strtotime($dt['due_date']) < strtotime(date('Y-m-d'));
                    ?>
                        <li style="padding: 15px 0; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <a href="index.php?action=team-task-detail&task_id=<?= $dt['id'] ?>&project_id=<?= $project['id'] ?>" style="color: #37352f; text-decoration: none; font-weight: 600; font-size: 0.95rem; display: block; margin-bottom: 6px;"><?= htmlspecialchars($dt['title']) ?></a>
                                <span style="font-size: 0.8rem; color: #787774; background: #fdfdfc; padding: 4px 8px; border-radius: 4px; border: 1px solid #e3e2e0;"><i class="fas fa-user"></i> <?= $dt['assignee_name'] ?: __('unassigned_label') ?></span>
                            </div>
                            <span style="font-size: 0.85rem; font-weight: bold; color: <?= $isOverdue ? '#eb3639' : '#d9730d' ?>; text-align: right; background: <?= $isOverdue ? '#fde8e8' : '#fef5e6' ?>; padding: 6px 12px; border-radius: 6px;">
                                <?= date('d/m/Y', strtotime($dt['due_date'])) ?><br>
                                <?= $isOverdue ? __('overdue_label') : __('upcoming_label') ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
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
                    data: [<?= $chartData['Backlog'] ?? 0 ?>, <?= $chartData['In Progress'] ?? 0 ?>, <?= $chartData['Review'] ?? 0 ?>, <?= $chartData['Done'] ?? 0 ?>],
                    backgroundColor: ['#e3e2e0', '#d3e5ef', '#fdecc8', '#dbeddb'],
                    borderWidth: 0,
                    cutout: '75%'
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });
    });
    </script>

<?php elseif ($view == 'chat'): ?>
    <div style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #e3e2e0; box-shadow: 0 4px 15px rgba(0,0,0,0.03); max-width: 900px; margin: 0 auto;">
        <div style="margin-bottom: 20px; border-bottom: 2px solid #f0f0f0; padding-bottom: 15px;">
            <h2 style="margin: 0; color: #37352f; font-size: 1.4rem;"><i class="fas fa-bullhorn" style="color: #d9730d;"></i> <?= __('project_chat_title') ?></h2>
            <p style="margin: 5px 0 0 0; color: #787774; font-size: 0.95rem;"><?= __('project_chat_desc') ?></p>
        </div>

        <div id="chatBox" class="custom-scroll" style="height: 400px; overflow-y: auto; padding-right: 15px; margin-bottom: 20px; display: flex; flex-direction: column;">
            <?php if(empty($projectComments)): ?>
                <div style="text-align: center; margin: auto; color: #787774;">
                    <i class="far fa-comments" style="font-size: 3rem; color: #e3e2e0; margin-bottom: 15px;"></i>
                    <p><?= __('empty_project_chat') ?></p>
                </div>
            <?php else: ?>
                <?php foreach($projectComments as $c): 
                    $isMe = ($c['user_id'] == $_SESSION['user_id']);
                ?>
                    <div style="display: flex; gap: 12px; margin-bottom: 20px; <?= $isMe ? 'flex-direction: row-reverse;' : '' ?>">
                        <div style="background: <?= $isMe ? '#2383e2' : '#e3e2e0' ?>; color: <?= $isMe ? 'white' : '#37352f' ?>; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; flex-shrink: 0;">
                            <?= strtoupper(substr($c['fullname'], 0, 1)) ?>
                        </div>
                        <div style="max-width: 75%;">
                            <div style="font-size: 0.8rem; color: #787774; margin-bottom: 4px; <?= $isMe ? 'text-align: right;' : '' ?>">
                                <strong><?= $isMe ? __('you_tag') : htmlspecialchars($c['fullname']) ?></strong> • <?= date('H:i d/m', strtotime($c['created_at'])) ?>
                            </div>
                            <div style="background: <?= $isMe ? '#e8f3fb' : '#f7f7f5' ?>; padding: 12px 16px; border-radius: 12px; font-size: 0.95rem; color: #37352f; border: 1px solid <?= $isMe ? '#b9d5e5' : '#e3e2e0' ?>;">
                                <?php if(!empty($c['content'])) echo nl2br(htmlspecialchars($c['content'])); ?>
                                <?php if(!empty($c['file_url'])): ?>
                                    <div style="margin-top: <?= !empty($c['content'])?'10px':'0'?>; padding-top: <?= !empty($c['content'])?'10px':'0'?>; border-top: <?= !empty($c['content'])?'1px dashed #ccc':'none'?>;">
                                        <a href="/public/uploads/teams/<?= htmlspecialchars($c['file_url']) ?>" target="_blank" style="color: #2383e2; text-decoration: none; font-weight: 500;"><i class="fas fa-paperclip"></i> <?= htmlspecialchars($c['file_url']) ?></a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var chatBox = document.getElementById("chatBox");
                if (chatBox) {
                    chatBox.scrollTop = chatBox.scrollHeight;

                    setInterval(function() {
                        fetch(window.location.href)
                        .then(response => response.text())
                        .then(html => {
                            var parser = new DOMParser();
                            var doc = parser.parseFromString(html, 'text/html');
                            var newChatBox = doc.getElementById('chatBox');
                            
                            if (newChatBox && chatBox.innerHTML !== newChatBox.innerHTML) {
                                chatBox.innerHTML = newChatBox.innerHTML;
                                chatBox.scrollTop = chatBox.scrollHeight; 
                            }
                        }).catch(err => console.log('Lỗi load tin nhắn:', err));
                    }, 2000); 
                }
            });
        </script>

        <form action="index.php?action=add-project-comment" method="POST" enctype="multipart/form-data" style="background: #fdfdfc; padding: 15px; border-radius: 8px; border: 1px solid #e3e2e0;">
            <input type="hidden" name="project_id" value="<?= $project['id'] ?>">
            <textarea name="content" rows="2" placeholder="<?= __('chat_input_placeholder') ?>" style="width: 100%; border: 1px solid #ccc; border-radius: 6px; padding: 10px; margin-bottom: 10px; resize: none; font-family: inherit;"></textarea>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <input type="file" name="attachment" style="font-size: 0.85rem;">
                <button type="submit" style="background: #2383e2; color: white; border: none; padding: 8px 25px; border-radius: 6px; cursor: pointer; font-weight: 500;"><i class="fas fa-paper-plane"></i> <?= __('btn_send_to_team') ?></button>
            </div>
        </form>
    </div>

<?php else: ?>
    <div style="background: #f7f7f5; padding: 15px; border-radius: 8px; margin-bottom: 20px; display: flex; gap: 15px; align-items: center; border: 1px solid #e3e2e0;">
        <span style="font-weight: 500; font-size: 0.9rem; color: #787774;"><i class="fas fa-filter"></i> <?= __('filter_sort_label') ?></span>
        <form action="index.php" method="GET" style="display: flex; gap: 15px; margin: 0; width: 100%;">
            <input type="hidden" name="action" value="project-kanban">
            <input type="hidden" name="id" value="<?= $project['id'] ?>">
            <input type="hidden" name="view" value="kanban"> 
            
            <select name="assignee" onchange="this.form.submit()" style="padding: 6px 10px; border-radius: 4px; border: 1px solid #ccc; font-size: 0.85rem;">
                <option value=""><?= __('all_members_opt') ?></option>
                <option value="unassigned" <?= (isset($_GET['assignee']) && $_GET['assignee'] === 'unassigned') ? 'selected' : '' ?>><?= __('unassigned_filter') ?></option>
                <?php foreach($members as $m): ?>
                    <option value="<?= $m['id'] ?>" <?= (isset($_GET['assignee']) && $_GET['assignee'] == $m['id']) ? 'selected' : '' ?>>
                        <?= sprintf(__('task_of_member'), htmlspecialchars($m['fullname'])) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="sort" onchange="this.form.submit()" style="padding: 6px 10px; border-radius: 4px; border: 1px solid #ccc; font-size: 0.85rem;">
                <option value=""><?= __('sort_default_opt') ?></option>
                <option value="deadline_asc" <?= (isset($_GET['sort']) && $_GET['sort'] === 'deadline_asc') ? 'selected' : '' ?>><?= __('sort_deadline_asc') ?></option>
                <option value="deadline_desc" <?= (isset($_GET['sort']) && $_GET['sort'] === 'deadline_desc') ? 'selected' : '' ?>><?= __('sort_deadline_desc') ?></option>
            </select>
            
            <?php if(!empty($_GET['assignee']) || !empty($_GET['sort'])): ?>
                <a href="index.php?action=project-kanban&id=<?= $project['id'] ?>&view=kanban" style="font-size: 0.85rem; color: #eb3639; text-decoration: none; padding-top: 6px;"><?= __('btn_clear_filter_proj') ?></a>
            <?php endif; ?>
        </form>
    </div>

    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; align-items: start;">
        
        <div class="kanban-column">
            <div style="font-weight: 600; margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
                <span style="background: #e3e2e0; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem;"><?= __('col_backlog') ?></span>
                <span style="color: #787774; font-weight: 400;"><?= count($backlogTasks) ?></span>
            </div>
            <?php foreach ($backlogTasks as $t): ?>
                <?php 
                    $isAssignee = ($t['assigned_to'] == $_SESSION['user_id']); 
                    $isOverdue = (!empty($t['due_date']) && strtotime($t['due_date']) < strtotime(date('Y-m-d')));
                ?>
                <div class="task-card">
                    <a href="index.php?action=team-task-detail&task_id=<?= $t['id'] ?>&project_id=<?= $project['id'] ?>#chatBox" 
                        style="font-weight: 600; margin-bottom: 8px; display: block; color: #37352f; text-decoration: none;">
                        <?= htmlspecialchars($t['title']) ?>
                    </a>
                    
                    <div style="font-size: 0.75rem; color: #787774; margin-bottom: 8px; display: flex; justify-content: space-between;">
                        <span><i class="fas fa-user-circle"></i> <?= $t['assignee_name'] ? htmlspecialchars($t['assignee_name']) : __('unassigned_label') ?></span>
                        <span class="pri-<?= $t['priority'] ?>"><i class="fas fa-flag"></i> <?= $t['priority'] ?></span>
                    </div>

                    <?php if(!empty($t['due_date']) && $t['due_date'] != '0000-00-00'): ?>
                    <div style="font-size: 0.75rem; color: <?= $isOverdue ? '#eb3639' : '#787774' ?>; margin-bottom: 10px; font-weight: <?= $isOverdue ? 'bold' : 'normal' ?>;">
                        <i class="far fa-calendar-alt"></i> <?= __('deadline_text') ?> <?= date('d/m/Y', strtotime($t['due_date'])) ?> <?= $isOverdue ? __('overdue_label') : '' ?>
                    </div>
                    <?php endif; ?>

                    <div style="display: flex; gap: 10px; justify-content: space-between;">
                        <?php if ($isAssignee || (isset($canEdit) && $canEdit)): ?>
                            <a href="index.php?action=update-team-task&task_id=<?= $t['id'] ?>&project_id=<?= $project['id'] ?>&status=In Progress" style="font-size: 0.8rem; color: #2383e2; text-decoration: none; font-weight: 500;"><i class="fas fa-play"></i> <?= __('btn_do_now') ?></a>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (isset($canEdit) && $canEdit): ?>
                    <div style="display: flex; gap: 10px; margin-top: 10px; border-top: 1px solid #f0f0f0; padding-top: 8px;">
                        <a href="index.php?action=edit-team-task&id=<?= $t['id'] ?>&project_id=<?= $project['id'] ?>" style="font-size: 0.75rem; color: #2383e2; text-decoration: none;"><i class="fas fa-edit"></i> <?= __('btn_edit') ?></a>
                        <a href="index.php?action=delete-team-task&id=<?= $t['id'] ?>&project_id=<?= $project['id'] ?>" style="font-size: 0.75rem; color: #eb3639; text-decoration: none;" onclick="return confirm('<?= __('confirm_delete') ?>');"><i class="fas fa-trash"></i> <?= __('btn_delete') ?></a>
                    </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="kanban-column">
            <div style="font-weight: 600; margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
                <span style="background: #d3e5ef; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem;"><?= __('col_inprogress') ?></span>
                <span style="color: #787774; font-weight: 400;"><?= count($inProgressTasks) ?></span>
            </div>
            <?php foreach ($inProgressTasks as $t): ?>
                <?php 
                    $isAssignee = ($t['assigned_to'] == $_SESSION['user_id']); 
                    $isOverdue = (!empty($t['due_date']) && strtotime($t['due_date']) < strtotime(date('Y-m-d')));
                ?>
                <div class="task-card">
                    <a href="index.php?action=team-task-detail&task_id=<?= $t['id'] ?>&project_id=<?= $project['id'] ?>#chatBox" 
                        style="font-weight: 600; margin-bottom: 8px; display: block; color: #0b6e99; text-decoration: none;">
                        <?= htmlspecialchars($t['title']) ?>
                    </a>
                    
                    <div style="font-size: 0.75rem; color: #787774; margin-bottom: 8px; display: flex; justify-content: space-between;">
                        <span><i class="fas fa-user-circle"></i> <?= $t['assignee_name'] ? htmlspecialchars($t['assignee_name']) : __('unassigned_label') ?></span>
                        <span class="pri-<?= $t['priority'] ?>"><i class="fas fa-flag"></i> <?= $t['priority'] ?></span>
                    </div>

                    <?php if(!empty($t['due_date']) && $t['due_date'] != '0000-00-00'): ?>
                    <div style="font-size: 0.75rem; color: <?= $isOverdue ? '#eb3639' : '#787774' ?>; margin-bottom: 10px; font-weight: <?= $isOverdue ? 'bold' : 'normal' ?>;">
                        <i class="far fa-calendar-alt"></i> <?= __('deadline_text') ?> <?= date('d/m/Y', strtotime($t['due_date'])) ?> <?= $isOverdue ? __('overdue_label') : '' ?>
                    </div>
                    <?php endif; ?>

                    <div style="display: flex; gap: 10px; justify-content: space-between;">
                        <?php if ($isAssignee || (isset($canEdit) && $canEdit)): ?>
                            <a href="index.php?action=update-team-task&task_id=<?= $t['id'] ?>&project_id=<?= $project['id'] ?>&status=Backlog" style="font-size: 0.8rem; color: #787774; text-decoration: none;"><?= __('btn_back') ?></a>
                            <a href="index.php?action=update-team-task&task_id=<?= $t['id'] ?>&project_id=<?= $project['id'] ?>&status=Review" style="font-size: 0.8rem; color: #d9730d; text-decoration: none; font-weight: 500;"><?= __('btn_request_review') ?></a>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (isset($canEdit) && $canEdit): ?>
                    <div style="display: flex; gap: 10px; margin-top: 10px; border-top: 1px solid #f0f0f0; padding-top: 8px;">
                        <a href="index.php?action=edit-team-task&id=<?= $t['id'] ?>&project_id=<?= $project['id'] ?>" style="font-size: 0.75rem; color: #2383e2; text-decoration: none;"><i class="fas fa-edit"></i> <?= __('btn_edit') ?></a>
                        <a href="index.php?action=delete-team-task&id=<?= $t['id'] ?>&project_id=<?= $project['id'] ?>" style="font-size: 0.75rem; color: #eb3639; text-decoration: none;" onclick="return confirm('<?= __('confirm_delete') ?>');"><i class="fas fa-trash"></i> <?= __('btn_delete') ?></a>
                    </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="kanban-column">
            <div style="font-weight: 600; margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
                <span style="background: #fdecc8; color: #ad7f11; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem;"><?= __('col_review') ?></span>
                <span style="color: #787774; font-weight: 400;"><?= count($reviewTasks) ?></span>
            </div>
            <?php foreach ($reviewTasks as $t): ?>
                <div class="task-card" style="border-left: 3px solid #d9730d;">
                    <a href="index.php?action=team-task-detail&task_id=<?= $t['id'] ?>&project_id=<?= $project['id'] ?>#chatBox" 
                        style="font-weight: 600; margin-bottom: 8px; display: block; color: #ad7f11; text-decoration: none;">
                        <?= htmlspecialchars($t['title']) ?>
                    </a>
                    
                    <div style="font-size: 0.75rem; color: #787774; margin-bottom: 8px; display: flex; justify-content: space-between;">
                        <span><i class="fas fa-user-circle"></i> <?= $t['assignee_name'] ? htmlspecialchars($t['assignee_name']) : __('unassigned_label') ?></span>
                    </div>

                    <div style="display: flex; gap: 10px; justify-content: space-between;">
                        <?php if (isset($canEdit) && $canEdit): ?>
                            <a href="index.php?action=update-team-task&task_id=<?= $t['id'] ?>&project_id=<?= $project['id'] ?>&status=In Progress" style="font-size: 0.8rem; color: #eb3639; text-decoration: none;"><?= __('btn_redo') ?></a>
                            <a href="index.php?action=update-team-task&task_id=<?= $t['id'] ?>&project_id=<?= $project['id'] ?>&status=Done" style="font-size: 0.8rem; color: #0f7b6c; text-decoration: none; font-weight: 500;"><i class="fas fa-check-double"></i> <?= __('btn_approve_done') ?></a>
                        <?php else: ?>
                            <span style="font-size: 0.8rem; color: #ad7f11; font-style: italic;"><i class="fas fa-spinner fa-spin"></i> <?= __('waiting_review_text') ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (isset($canEdit) && $canEdit): ?>
                    <div style="display: flex; gap: 10px; margin-top: 10px; border-top: 1px solid #f0f0f0; padding-top: 8px;">
                        <a href="index.php?action=edit-team-task&id=<?= $t['id'] ?>&project_id=<?= $project['id'] ?>" style="font-size: 0.75rem; color: #2383e2; text-decoration: none;"><i class="fas fa-edit"></i> <?= __('btn_edit') ?></a>
                    </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="kanban-column">
            <div style="font-weight: 600; margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
                <span style="background: #dbeddb; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem;"><?= __('col_done') ?></span>
                <span style="color: #787774; font-weight: 400;"><?= count($doneTasks) ?></span>
            </div>
            <?php foreach ($doneTasks as $t): ?>
                <div class="task-card" style="opacity: 0.6; border-left: 3px solid #0f7b6c;">
                    <a href="index.php?action=team-task-detail&task_id=<?= $t['id'] ?>&project_id=<?= $project['id'] ?>#chatBox" 
                        style="font-weight: 600; margin-bottom: 8px; display: block; color: #37352f; text-decoration: line-through;">
                        <?= htmlspecialchars($t['title']) ?>
                    </a>
                    <div style="font-size: 0.75rem; color: #787774; margin-bottom: 10px;">
                        <i class="fas fa-check-circle" style="color: #0f7b6c;"></i> <?= $t['assignee_name'] ? htmlspecialchars($t['assignee_name']) : __('unassigned_label') ?>
                    </div>
                    
                    <div style="display: flex; gap: 10px; justify-content: space-between;">
                        <?php if (isset($canEdit) && $canEdit): ?>
                            <a href="index.php?action=update-team-task&task_id=<?= $t['id'] ?>&project_id=<?= $project['id'] ?>&status=Review" style="font-size: 0.8rem; color: #787774; text-decoration: none;"><?= __('btn_cancel_approval') ?></a>
                        <?php endif; ?>
                    </div>

                    <?php if (isset($canEdit) && $canEdit): ?>
                    <div style="display: flex; gap: 10px; margin-top: 10px; border-top: 1px solid #f0f0f0; padding-top: 8px;">
                        <a href="index.php?action=delete-team-task&id=<?= $t['id'] ?>&project_id=<?= $project['id'] ?>" style="font-size: 0.75rem; color: #eb3639; text-decoration: none;" onclick="return confirm('<?= __('confirm_delete') ?>');"><i class="fas fa-trash"></i> <?= __('btn_delete') ?></a>
                    </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<div id="taskModal" style="display:none; position:fixed; z-index:100; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.4);">
    <div style="background:white; margin:10% auto; padding:30px; width:400px; border-radius:8px;">
        <h2 style="margin-top:0;"><?= __('modal_assign_task') ?></h2>
        <form action="index.php?action=add-team-task" method="POST">
            <input type="hidden" name="project_id" value="<?= $project['id'] ?>">
            <div class="form-group" style="margin-bottom: 15px;">
                <label><?= __('lbl_assign_question') ?></label>
                <select name="assigned_to" class="form-control" style="width: 100%; padding: 8px; box-sizing: border-box;">
                    <option value=""><?= __('no_assignee_opt') ?></option>
                    <?php foreach($members as $m): ?>
                        <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['fullname']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="margin-bottom: 15px;">
                <label><?= __('lbl_title') ?></label>
                <input type="text" name="title" class="form-control" required style="width: 100%; padding: 8px; box-sizing: border-box;">
            </div>
            <div class="form-group" style="margin-bottom: 15px;">
                <label><?= __('lbl_desc') ?></label>
                <textarea name="description" class="form-control" rows="3" style="width: 100%; padding: 8px; box-sizing: border-box;"></textarea>
            </div>
            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <label><?= __('lbl_deadline') ?></label>
                    <input type="date" name="due_date" class="form-control" style="width: 100%; padding: 8px; box-sizing: border-box;">
                </div>
                <div style="flex: 1;">
                    <label><?= __('lbl_priority') ?></label>
                    <select name="priority" class="form-control" style="width: 100%; padding: 8px; box-sizing: border-box;">
                        <option value="Low"><?= __('priority_low') ?></option>
                        <option value="Medium" selected><?= __('priority_medium') ?></option>
                        <option value="High"><?= __('priority_high') ?></option>
                    </select>
                </div>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
                <button type="button" onclick="document.getElementById('taskModal').style.display='none'" style="background:none; border:1px solid #ccc; padding:8px 15px; cursor:pointer; border-radius:4px;"><?= __('btn_cancel') ?></button>
                <button type="submit" style="background:#2383e2; color:white; border:none; padding:8px 15px; cursor:pointer; border-radius:4px;"><?= __('btn_assign_submit') ?></button>
            </div>
        </form>
    </div>
</div>

<?php require_once PROJECT_ROOT . '/views/layout/footer.php'; ?>