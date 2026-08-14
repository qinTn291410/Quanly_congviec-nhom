<?php require_once PROJECT_ROOT . '/views/layout/header.php'; ?>

<div style="margin-bottom: 20px;">
    <a href="index.php?action=project-kanban&id=<?= $project['id'] ?>" style="color: #787774; text-decoration: none; font-size: 0.95rem;">
        <i class="fas fa-arrow-left"></i> <?= __('back_to_kanban_link') ?>
    </a>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
    
    <div style="background: white; padding: 25px; border-radius: 10px; border: 1px solid #e3e2e0; box-shadow: 0 2px 4px rgba(0,0,0,0.02); display: flex; flex-direction: column;">
        <h2 style="font-size: 1.3rem; margin: 0 0 20px 0; color: #37352f; border-bottom: 2px solid #f0f0f0; padding-bottom: 15px;">
            <i class="fas fa-comments" style="color: #2383e2;"></i> <?= __('task_discussion_title') ?>
        </h2>

        <div id="chatBox" style="flex-grow: 1; max-height: 500px; overflow-y: auto; padding-right: 15px; margin-bottom: 20px;" class="custom-scroll">
            <?php if(empty($comments)): ?>
                <div style="text-align: center; padding: 30px 0; color: #787774;">
                    <i class="far fa-comment-dots" style="font-size: 2.5rem; color: #e3e2e0; margin-bottom: 10px;"></i>
                    <p style="font-style: italic;"><?= __('empty_task_comments') ?></p>
                </div>
            <?php else: ?>
                <?php foreach($comments as $c): 
                    $isMe = ($c['user_id'] == $_SESSION['user_id']);
                    // HIỂN THỊ AVATAR NGƯỜI CHAT
                    $chatAvatar = $c['avatar'] ?? '';
                    $chatAvatarUrl = (!empty($chatAvatar) && $chatAvatar !== 'default.png') 
                        ? '/task_manager/public/uploads/' . htmlspecialchars($chatAvatar) 
                        : 'https://ui-avatars.com/api/?name=' . urlencode($c['fullname']) . '&background=random';
                ?>
                    <div style="margin-bottom: 20px; display: flex; gap: 15px; <?= $isMe ? 'flex-direction: row-reverse;' : '' ?>">
                        <img src="<?= $chatAvatarUrl ?>" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; flex-shrink: 0; border: 1px solid <?= $isMe ? '#b9d5e5' : '#e3e2e0' ?>; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        
                        <div style="max-width: 75%;">
                            <div style="font-size: 0.8rem; color: #787774; margin-bottom: 5px; <?= $isMe ? 'text-align: right;' : '' ?>">
                                <strong><?= $isMe ? __('you_tag') : htmlspecialchars($c['fullname']) ?></strong> • <?= date('d/m/Y H:i', strtotime($c['created_at'])) ?>
                            </div>
                            <div style="background: <?= $isMe ? '#e8f3fb' : '#f7f7f5' ?>; padding: 12px 15px; border-radius: 12px; font-size: 0.95rem; color: #37352f; line-height: 1.5; border: 1px solid <?= $isMe ? '#b9d5e5' : '#e3e2e0' ?>;">
                                <?php if(!empty($c['content'])): ?>
                                    <?= nl2br(htmlspecialchars($c['content'])) ?>
                                <?php endif; ?>
                                
                                <?php if(!empty($c['file_url'])): ?>
                                    <div style="margin-top: <?= !empty($c['content']) ? '10px' : '0' ?>; padding-top: <?= !empty($c['content']) ? '10px' : '0' ?>; border-top: <?= !empty($c['content']) ? '1px dashed rgba(0,0,0,0.1)' : 'none' ?>;">
                                        <a href="/task_manager/public/uploads/teams/<?= htmlspecialchars($c['file_url']) ?>" target="_blank" style="color: #2383e2; text-decoration: none; font-size: 0.85rem; display: flex; align-items: center; gap: 5px;">
                                            <i class="fas fa-paperclip"></i> <?= __('attachment_label') ?> <?= htmlspecialchars($c['file_url']) ?>
                                        </a>
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

        <form action="index.php?action=add-team-comment" method="POST" enctype="multipart/form-data" style="background: #fdfdfc; padding: 15px; border-radius: 8px; border: 1px solid #e3e2e0;">
            <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
            <input type="hidden" name="project_id" value="<?= $project['id'] ?>">
            
            <textarea name="content" rows="3" placeholder="<?= __('comment_input_placeholder') ?>" style="width: 100%; border: 1px solid #ccc; border-radius: 6px; padding: 10px; margin-bottom: 10px; box-sizing: border-box; resize: vertical;"></textarea>
            
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <label for="file-upload" style="cursor: pointer; color: #787774; font-size: 0.9rem; display: flex; align-items: center; gap: 5px;">
                        <i class="fas fa-paperclip"></i> <?= __('btn_attach_file') ?>
                    </label>
                    <input id="file-upload" type="file" name="attachment" style="display: none;" onchange="document.getElementById('file-name').textContent = this.files[0].name">
                    <span id="file-name" style="font-size: 0.8rem; color: #0b6e99; margin-left: 10px;"></span>
                </div>
                <button type="submit" style="background: #2383e2; color: white; border: none; padding: 8px 20px; border-radius: 6px; cursor: pointer; font-weight: 500;">
                    <i class="fas fa-paper-plane"></i> <?= __('btn_send_msg') ?>
                </button>
            </div>
        </form>
    </div>

    <div style="background: white; padding: 25px; border-radius: 10px; border: 1px solid #e3e2e0; box-shadow: 0 2px 4px rgba(0,0,0,0.02); height: fit-content;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
            <h1 style="font-size: 1.5rem; margin: 0; color: #37352f;"><?= htmlspecialchars($task['title']) ?></h1>
            <span style="background: #e3e2e0; padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: bold;"><?= $task['status'] ?></span>
        </div>
        
        <div style="background: #f7f7f5; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <p style="margin: 0 0 10px 0; font-size: 0.9rem; color: #787774;"><strong><?= __('task_desc_label') ?></strong></p>
            <p style="margin: 0; color: #37352f; line-height: 1.5; font-size: 0.95rem;"><?= nl2br(htmlspecialchars($task['description'] ?: __('no_task_desc'))) ?></p>
        </div>

        <div style="margin-bottom: 15px; display: flex; align-items: center; gap: 10px; font-size: 0.9rem;">
            <i class="fas fa-user-circle" style="color: #ccc; font-size: 1.2rem;"></i>
            <span style="color: #787774;"><?= __('assignee_label') ?></span>
            <strong><?= isset($task['assignee_name']) && $task['assignee_name'] ? htmlspecialchars($task['assignee_name']) : '<span style="color:#eb3639;">' . __('unassigned_label') . '</span>' ?></strong>
        </div>

        <div style="margin-bottom: 15px; display: flex; align-items: center; gap: 10px; font-size: 0.9rem;">
            <i class="fas fa-flag" style="color: #d9730d;"></i>
            <span style="color: #787774;"><?= __('priority_label') ?></span>
            <strong><?= $task['priority'] ?></strong>
        </div>

        <div style="display: flex; align-items: center; gap: 10px; font-size: 0.9rem;">
            <i class="far fa-calendar-alt" style="color: #0f7b6c;"></i>
            <span style="color: #787774;"><?= __('due_date_label') ?></span>
            <?php 
                $isOverdue = (!empty($task['due_date']) && strtotime($task['due_date']) < strtotime(date('Y-m-d')) && $task['status'] != 'Done');
            ?>
            <strong style="color: <?= $isOverdue ? '#eb3639' : '#37352f' ?>;">
                <?= (!empty($task['due_date']) && $task['due_date'] != '0000-00-00') ? date('d/m/Y', strtotime($task['due_date'])) : __('no_due_date') ?>
                <?= $isOverdue ? __('overdue_label') : '' ?>
            </strong>
        </div>
    </div>
</div>

<style>
    .custom-scroll::-webkit-scrollbar { width: 6px; }
    .custom-scroll::-webkit-scrollbar-track { background: transparent; }
    .custom-scroll::-webkit-scrollbar-thumb { background: #e3e2e0; border-radius: 4px; }
    .custom-scroll::-webkit-scrollbar-thumb:hover { background: #cfcecc; }
</style>

<?php require_once PROJECT_ROOT . '/views/layout/footer.php'; ?>