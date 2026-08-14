<?php require_once PROJECT_ROOT . '/views/layout/header.php'; ?>

<?php
$isLeader = false;
foreach($members as $m) {
    if($m['id'] == $_SESSION['user_id'] && $m['role'] == 'Leader') {
        $isLeader = true;
        break;
    }
}
?>

<?php if (isset($_SESSION['system_alert'])): ?>
    <script>alert("<?= __('sys_alert_title') ?>\n\n<?= $_SESSION['system_alert'] ?>");</script>
    <?php unset($_SESSION['system_alert']); ?>
<?php endif; ?>

<div style="margin-bottom: 20px;">
    <a href="index.php?action=teams" style="color: #787774; text-decoration: none; font-size: 0.95rem;">
        <i class="fas fa-arrow-left"></i> <?= __('back_to_team_list') ?>
    </a>
</div>

<div style="background: white; padding: 25px; border-radius: 10px; border: 1px solid #e3e2e0; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 style="margin: 0 0 10px 0; font-size: 1.8rem; color: #37352f;"><?= htmlspecialchars($team['name']) ?></h1>
        <p style="color: #787774; margin: 0;"><?= htmlspecialchars($team['description']) ?></p>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; margin-bottom: 30px;">
    <div style="background: white; padding: 25px; border-radius: 10px; border: 1px solid #e3e2e0;">
        <h2 style="font-size: 1.2rem; margin: 0 0 20px 0; color: #37352f;"><i class="fas fa-users" style="color: #2383e2;"></i> <?= __('team_members_count') ?> (<?= count($members) ?>)</h2>
        <table style="width: 100%; border-collapse: collapse;">
            <tr style="border-bottom: 2px solid #f0f0f0; text-align: left;">
                <th style="padding: 10px; color: #787774; font-weight: 500;"><?= __('th_fullname') ?></th>
                <th style="padding: 10px; color: #787774; font-weight: 500;"><?= __('th_email') ?></th>
                <th style="padding: 10px; color: #787774; font-weight: 500;"><?= __('th_role') ?></th>
            </tr>
            <?php foreach($members as $m): ?>
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td style="padding: 15px 10px; font-weight: 500; color: #37352f;">
                        <?= htmlspecialchars($m['fullname']) ?> <?= ($m['id'] == $_SESSION['user_id']) ? '<span style="color:#787774; font-size:0.8rem; font-weight:normal;">' . __('you_tag') . '</span>' : '' ?>
                    </td>
                    <td style="padding: 15px 10px; color: #787774;"><?= htmlspecialchars($m['email']) ?></td>
                    <td style="padding: 15px 10px; display: flex; align-items: center;">
                        <?php if($m['role'] == 'Leader'): ?>
                            <span style="background: #fde8e8; color: #eb3639; padding: 4px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: bold;">Leader</span>
                        <?php else: ?>
                            <span style="background: #e8f3fb; color: #0b6e99; padding: 4px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: bold;">Member</span>
                        <?php endif; ?>

                        <?php if ($isLeader && $m['id'] != $_SESSION['user_id']): ?>
                            <a href="index.php?action=kick-member&team_id=<?= $team['id'] ?>&user_id=<?= $m['id'] ?>" 
                                onclick="return confirm('<?= __('confirm_kick_msg') ?>');" 
                                style="color: #eb3639; font-size: 0.8rem; text-decoration: none; margin-left: 15px;" title="<?= __('btn_kick') ?>">
                                <i class="fas fa-user-times"></i> <?= __('btn_kick') ?>
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <?php if ($isLeader): ?>
    <div style="background: white; padding: 25px; border-radius: 10px; border: 1px solid #e3e2e0; height: fit-content;">
        <h2 style="font-size: 1.1rem; margin: 0 0 15px 0; color: #37352f;"><i class="fas fa-user-plus"></i> <?= __('add_teammate_title') ?></h2>
        <form action="index.php?action=invite-member" method="POST">
            <input type="hidden" name="team_id" value="<?= $team['id'] ?>">
            <input type="email" name="email" placeholder="<?= __('email_placeholder') ?>" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; margin-bottom: 15px; box-sizing: border-box;">
            <button type="submit" style="width: 100%; background: #2383e2; color: white; border: none; padding: 10px; border-radius: 4px; font-weight: 500; cursor: pointer;"><?= __('btn_send_invite') ?></button>
        </form>
    </div>
    <?php endif; ?>
</div>

<div style="background: white; padding: 25px; border-radius: 10px; border: 1px solid #e3e2e0;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="font-size: 1.2rem; margin: 0; color: #37352f;"><i class="fas fa-project-diagram" style="color: #0f7b6c;"></i> <?= __('running_projects_title') ?></h2>
        <?php if ($isLeader): ?>
        <button onclick="document.getElementById('projectModal').style.display='block'" style="background: #0f7b6c; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-weight: 500;">
            <?= __('btn_create_project') ?>
        </button>
        <?php endif; ?>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
        <?php if(empty($projects)): ?>
            <p style="color: #787774;"><?= __('no_projects_msg') ?></p>
        <?php else: ?>
            <?php foreach($projects as $p): ?>
                <div style="position: relative; border: 1px solid #e3e2e0; border-radius: 8px; padding: 20px; transition: 0.2s; background: white;">
                    
                    <?php if ($isLeader): ?>
                        <a href="index.php?action=delete-project&project_id=<?= $p['id'] ?>&team_id=<?= $team['id'] ?>" 
                            onclick="return confirm('<?= __('confirm_delete_project_msg') ?>');" 
                            style="position: absolute; top: 15px; right: 15px; color: #eb3639; text-decoration: none;" title="Xóa Dự án">
                            <i class="fas fa-trash"></i>
                        </a>
                    <?php endif; ?>

                    <h3 style="margin: 0 0 10px 0; color: #37352f; font-size: 1.1rem; padding-right: 25px;"><?= htmlspecialchars($p['name']) ?></h3>
                    <p style="color: #787774; font-size: 0.85rem; margin-bottom: 15px; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?= htmlspecialchars($p['description']) ?></p>
                    
                    <div style="font-size: 0.8rem; color: #37352f; margin-bottom: 5px;">
                        <i class="fas fa-user-tie" style="color: #d9730d;"></i> <?= __('manager_label') ?> <strong><?= htmlspecialchars($p['manager_name']) ?></strong>
                    </div>
                    <div style="font-size: 0.8rem; color: #787774; margin-bottom: 15px;">
                        <i class="far fa-calendar-alt"></i> <?= date('d/m/Y', strtotime($p['start_date'])) ?> - <?= date('d/m/Y', strtotime($p['end_date'])) ?>
                    </div>
                    
                    <div style="margin-bottom: 15px; background: #fdfdfc; padding: 10px; border-radius: 6px; border: 1px solid #f0f0f0;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 0.8rem;">
                            <span style="color: #787774; font-weight: 500;"><?= __('project_progress_label') ?></span>
                            <span style="font-weight: bold; color: <?= ($p['percent'] == 100) ? '#0f7b6c' : '#2383e2' ?>;"><?= $p['percent'] ?>%</span>
                        </div>
                        <div style="background: #e3e2e0; border-radius: 10px; height: 6px; width: 100%; overflow: hidden;">
                            <div style="background: <?= ($p['percent'] == 100) ? '#0f7b6c' : '#2383e2' ?>; height: 100%; width: <?= $p['percent'] ?>%; transition: width 0.4s ease;"></div>
                        </div>
                    </div>

                    <a href="index.php?action=project-kanban&id=<?= $p['id'] ?>" style="display: block; text-align: center; background: #f7f7f5; color: #37352f; text-decoration: none; padding: 8px; border-radius: 4px; font-size: 0.85rem; font-weight: 500; border: 1px solid #e3e2e0;">
                        <?= __('enter_kanban_btn') ?>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<div id="projectModal" style="display:none; position:fixed; z-index:100; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.4);">
    <div style="background:white; margin:5% auto; padding:30px; width:500px; border-radius:8px;">
        <h2 style="margin-top:0;"><?= __('modal_create_project_title') ?></h2>
        <form action="index.php?action=create-project" method="POST">
            <input type="hidden" name="team_id" value="<?= $team['id'] ?>">
            
            <div class="form-group" style="margin-bottom: 15px;">
                <label><?= __('lbl_project_name') ?></label>
                <input type="text" name="name" class="form-control" required style="width: 100%; padding: 8px; box-sizing: border-box;">
            </div>
            
            <div class="form-group" style="margin-bottom: 15px;">
                <label><?= __('lbl_project_desc') ?></label>
                <textarea name="description" class="form-control" rows="3" style="width: 100%; padding: 8px; box-sizing: border-box;"></textarea>
            </div>
            
            <div style="display:flex; gap:10px; margin-bottom: 15px;">
                <div style="flex:1;">
                    <label><?= __('lbl_start_date') ?></label>
                    <input type="date" name="start_date" required class="form-control" style="width: 100%; padding: 8px; box-sizing: border-box;">
                </div>
                <div style="flex:1;">
                    <label><?= __('lbl_end_date') ?></label>
                    <input type="date" name="end_date" required class="form-control" style="width: 100%; padding: 8px; box-sizing: border-box;">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label><?= __('lbl_project_manager') ?></label>
                <select name="manager_id" required class="form-control" style="width: 100%; padding: 8px; box-sizing: border-box;">
                    <?php foreach($members as $m): ?>
                        <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['fullname']) ?> <?= ($m['role'] == 'Leader') ? '(Leader)' : '' ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
                <button type="button" onclick="document.getElementById('projectModal').style.display='none'" style="background:none; border:1px solid #ccc; padding:8px 15px; cursor:pointer; border-radius:4px;"><?= __('btn_cancel') ?></button>
                <button type="submit" style="background:#0f7b6c; color:white; border:none; padding:8px 15px; cursor:pointer; border-radius:4px;"><?= __('btn_save_project') ?></button>
            </div>
        </form>
    </div>
</div>

<div style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #e3e2e0; box-shadow: 0 4px 15px rgba(0,0,0,0.03); margin-top: 30px; margin-bottom: 30px;">
    <div style="margin-bottom: 20px; border-bottom: 2px solid #f0f0f0; padding-bottom: 15px;">
        <h2 style="margin: 0; color: #37352f; font-size: 1.4rem;"><i class="fas fa-comments" style="color: #2383e2;"></i> <?= __('team_general_chat') ?></h2>
        <p style="margin: 5px 0 0 0; color: #787774; font-size: 0.95rem;"><?= __('team_chat_subtitle') ?></p>
    </div>

    <div id="chatBox" class="custom-scroll" style="height: 400px; overflow-y: auto; padding-right: 15px; margin-bottom: 20px; display: flex; flex-direction: column;">
        <?php if(empty($teamComments)): ?>
            <div style="text-align: center; margin: auto; color: #787774;">
                <i class="far fa-comments" style="font-size: 3rem; color: #e3e2e0; margin-bottom: 15px;"></i>
                <p><?= __('no_team_messages') ?></p>
            </div>
        <?php else: ?>
            <?php foreach($teamComments as $c): 
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
                                    <a href="/task_manager/public/uploads/teams/<?= htmlspecialchars($c['file_url']) ?>" target="_blank" style="color: #2383e2; text-decoration: none; font-weight: 500;"><i class="fas fa-paperclip"></i> <?= htmlspecialchars($c['file_url']) ?></a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <form action="index.php?action=add-team-message" method="POST" enctype="multipart/form-data" style="background: #fdfdfc; padding: 15px; border-radius: 8px; border: 1px solid #e3e2e0;">
        <input type="hidden" name="team_id" value="<?= $team['id'] ?>">
        <textarea name="content" rows="2" placeholder="<?= __('chat_input_placeholder') ?>" style="width: 100%; border: 1px solid #ccc; border-radius: 6px; padding: 10px; margin-bottom: 10px; resize: none; font-family: inherit;"></textarea>
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <input type="file" name="attachment" style="font-size: 0.85rem;">
            <button type="submit" style="background: #2383e2; color: white; border: none; padding: 8px 25px; border-radius: 6px; cursor: pointer; font-weight: 500;"><i class="fas fa-paper-plane"></i> <?= __('btn_send_message') ?></button>
        </div>
    </form>
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
                }).catch(err => console.log('Lỗi:', err));
            }, 3000); 
        }
    });
</script>

<?php require_once PROJECT_ROOT . '/views/layout/footer.php'; ?>