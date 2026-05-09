<?php require_once PROJECT_ROOT . '/views/layout/header.php'; ?>

<div style="max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; border: 1px solid #e3e2e0;">
    <h2>Sửa công việc dự án</h2>
    <form action="index.php?action=edit-team-task&id=<?= $task['id'] ?>&project_id=<?= $project['id'] ?>" method="POST">
        <div class="form-group" style="margin-bottom: 15px;">
            <label>Tên công việc</label>
            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($task['title']) ?>" required style="width: 100%; padding: 8px; box-sizing: border-box;">
        </div>
        <div class="form-group" style="margin-bottom: 15px;">
            <label>Giao cho ai?</label>
            <select name="assigned_to" class="form-control" style="width: 100%; padding: 8px; box-sizing: border-box;">
                <option value="">-- Chưa giao ai --</option>
                <?php foreach($members as $m): ?>
                    <option value="<?= $m['id'] ?>" <?= ($m['id'] == $task['assigned_to']) ? 'selected' : '' ?>><?= htmlspecialchars($m['fullname']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group" style="margin-bottom: 15px;">
            <label>Mô tả chi tiết</label>
            <textarea name="description" class="form-control" rows="4" style="width: 100%; padding: 8px; box-sizing: border-box;"><?= htmlspecialchars($task['description']) ?></textarea>
        </div>
        <div style="display: flex; gap: 10px; margin-bottom: 20px;">
            <div style="flex: 1;">
                <label>Deadline</label>
                <input type="date" name="due_date" class="form-control" value="<?= $task['due_date'] ?>" style="width: 100%; padding: 8px; box-sizing: border-box;">
            </div>
            <div style="flex: 1;">
                <label>Ưu tiên</label>
                <select name="priority" class="form-control" style="width: 100%; padding: 8px; box-sizing: border-box;">
                    <option value="Low" <?= $task['priority'] == 'Low' ? 'selected' : '' ?>>Thấp</option>
                    <option value="Medium" <?= $task['priority'] == 'Medium' ? 'selected' : '' ?>>Trung bình</option>
                    <option value="High" <?= $task['priority'] == 'High' ? 'selected' : '' ?>>Cao</option>
                </select>
            </div>
        </div>
        <div style="display:flex; justify-content:flex-end; gap:10px;">
            <a href="index.php?action=project-kanban&id=<?= $project['id'] ?>" style="padding: 8px 15px; color: #787774; text-decoration: none;">Hủy</a>
            <button type="submit" style="background:#2383e2; color:white; border:none; padding:8px 20px; border-radius:4px; cursor:pointer;">Cập nhật</button>
        </div>
    </form>
</div>

<?php require_once PROJECT_ROOT . '/views/layout/footer.php'; ?>
