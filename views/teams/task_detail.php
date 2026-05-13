<?php require_once PROJECT_ROOT . '/views/layout/header.php'; ?>

<div style="max-width: 800px; margin: 0 auto;">
    <div style="margin-bottom: 20px;">
        <a href="index.php?action=project-kanban&id=<?= $project['id'] ?>" style="color: #787774; text-decoration: none;"><i class="fas fa-arrow-left"></i> Về bảng Kanban</a>
    </div>

    <div style="background: white; padding: 25px; border-radius: 10px; border: 1px solid #e3e2e0; margin-bottom: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
            <h1 style="margin: 0; font-size: 1.5rem; color: #37352f;"><?= htmlspecialchars($task['title']) ?></h1>
            <span style="background: #e3e2e0; padding: 4px 10px; border-radius: 4px; font-size: 0.8rem; font-weight: bold;"><?= $task['status'] ?></span>
        </div>
        <p style="color: #37352f; line-height: 1.6; margin-bottom: 20px;"><?= nl2br(htmlspecialchars($task['description'])) ?></p>
        
        <div style="display: flex; gap: 20px; font-size: 0.85rem; color: #787774;">
            <span><i class="fas fa-flag" style="color: #d9730d;"></i> Ưu tiên: <strong><?= $task['priority'] ?></strong></span>
            <?php if($task['due_date'] && $task['due_date'] != '0000-00-00'): ?>
                <span><i class="far fa-calendar-alt"></i> Deadline: <strong><?= date('d/m/Y', strtotime($task['due_date'])) ?></strong></span>
            <?php endif; ?>
        </div>
    </div>

    <div style="background: white; padding: 25px; border-radius: 10px; border: 1px solid #e3e2e0;">
        <h2 style="font-size: 1.2rem; margin: 0 0 20px 0; border-bottom: 1px solid #f0f0f0; padding-bottom: 10px;">
            <i class="far fa-comments" style="color: #2383e2;"></i> Trao đổi & Ghi chú tiến độ
        </h2>
        
        <div style="max-height: 400px; overflow-y: auto; margin-bottom: 20px; padding-right: 10px;">
            <?php if(empty($comments)): ?>
                <p style="color: #787774; text-align: center; font-style: italic;">Chưa có bình luận nào. Hãy là người đầu tiên!</p>
            <?php else: ?>
                <?php foreach($comments as $c): ?>
                    <div style="margin-bottom: 15px; background: #f7f7f5; padding: 15px; border-radius: 8px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                            <strong style="color: #37352f; font-size: 0.9rem;"><?= htmlspecialchars($c['fullname']) ?></strong>
                            <span style="color: #787774; font-size: 0.75rem;"><?= date('H:i d/m/Y', strtotime($c['created_at'])) ?></span>
                        </div>
                        <?php if(!empty($c['content'])): ?>
                            <p style="margin: 0 0 10px 0; font-size: 0.95rem; color: #37352f; line-height: 1.5;"><?= nl2br(htmlspecialchars($c['content'])) ?></p>
                        <?php endif; ?>
                        
                        <?php if(!empty($c['file_url'])): ?>
                            <a href="/task_manager/public/uploads/teams/<?= $c['file_url'] ?>" target="_blank" style="display: inline-block; background: #e8f3fb; color: #0b6e99; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 0.8rem; border: 1px solid #b6d4ea;">
                                <i class="fas fa-paperclip"></i> Đính kèm: <?= htmlspecialchars($c['file_url']) ?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <form action="index.php?action=add-team-comment" method="POST" enctype="multipart/form-data" style="border-top: 1px solid #f0f0f0; padding-top: 15px;">
            <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
            <input type="hidden" name="project_id" value="<?= $project['id'] ?>">
            
            <textarea name="content" rows="3" placeholder="Gõ nội dung trao đổi, báo cáo tiến độ..." style="width: 100%; padding: 10px; box-sizing: border-box; border: 1px solid #e3e2e0; border-radius: 6px; margin-bottom: 10px; resize: vertical;"></textarea>
            
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <input type="file" name="attachment" style="font-size: 0.85rem;">
                <button type="submit" style="background: #2383e2; color: white; border: none; padding: 8px 20px; border-radius: 6px; cursor: pointer; font-weight: bold;">
                    <i class="fas fa-paper-plane"></i> Gửi
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once PROJECT_ROOT . '/views/layout/footer.php'; ?>