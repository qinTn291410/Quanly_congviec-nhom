<?php require_once PROJECT_ROOT . '/views/layout/header.php'; ?>

<div style="max-width: 600px; margin: 20px auto; background: white; padding: 30px; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
    
    <div style="margin-bottom: 20px;">
        <a href="index.php?action=tasks" style="color: #787774; text-decoration: none; font-size: 0.9rem;">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <h2 style="margin-top: 0; color: #37352f;">Sửa công việc</h2>
    
    <form action="index.php?action=edit-task&id=<?= $task['id'] ?>" method="POST">
        <div class="form-group">
            <label>Tiêu đề</label>
            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($task['title']) ?>" required>
        </div>
        
        <div class="form-group">
            <label>Mô tả</label>
            <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($task['description']) ?></textarea>
        </div>
        
        <div style="display: flex; gap: 15px;">
            <div class="form-group" style="flex: 1;">
                <label>Ngày bắt đầu</label>
                <input type="date" name="start_date" class="form-control" value="<?= $task['start_date'] ?>">
            </div>
            <div class="form-group" style="flex: 1;">
                <label>Hạn chót (Deadline)</label>
                <input type="date" name="due_date" class="form-control" value="<?= $task['due_date'] ?>">
            </div>
        </div>

        <div style="display: flex; gap: 15px; margin-bottom: 15px;">
            <div class="form-group" style="flex: 1;">
                <label>Danh mục</label>
                <select name="category" class="form-control">
                    <option value="Công việc" <?= $task['category'] == 'Công việc' ? 'selected' : '' ?>>Công việc</option>
                    <option value="Học tập" <?= $task['category'] == 'Học tập' ? 'selected' : '' ?>>Học tập</option>
                    <option value="Sức khỏe" <?= $task['category'] == 'Sức khỏe' ? 'selected' : '' ?>>Sức khỏe</option>
                    <option value="Tài chính" <?= $task['category'] == 'Tài chính' ? 'selected' : '' ?>>Tài chính</option>
                    <option value="Khác" <?= $task['category'] == 'Khác' ? 'selected' : '' ?>>Khác</option>
                </select>
            </div>
            <div class="form-group">
            <label>Mục tiêu</label>
            <select name="goal_id" class="form-control">
                <option value="">-- Không gắn mục tiêu --</option>
                <?php foreach($userGoals as $g): ?>
                    <option value="<?= $g['id'] ?>" <?= (isset($task['goal_id']) && $task['goal_id'] == $g['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($g['title']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        </div>

        <div class="form-group">
            <label>Mức độ ưu tiên</label>
            <select name="priority" class="form-control">
                <option value="High" <?= $task['priority'] == 'High' ? 'selected' : '' ?>>Cao (High)</option>
                <option value="Medium" <?= $task['priority'] == 'Medium' ? 'selected' : '' ?>>Trung bình (Medium)</option>
                <option value="Low" <?= $task['priority'] == 'Low' ? 'selected' : '' ?>>Thấp (Low)</option>
            </select>
        </div>

        <button type="submit" style="background: #2383e2; color: white; border: none; padding: 12px 20px; border-radius: 6px; cursor: pointer; width: 100%; font-weight: 500; font-size: 0.95rem; margin-top: 10px;">
            Lưu thay đổi
        </button>
    </form>
</div>

<?php require_once PROJECT_ROOT . '/views/layout/footer.php'; ?>