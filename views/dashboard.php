<?php require_once PROJECT_ROOT . '/views/layout/header.php'; ?>

<h1 style="font-size: 2rem; margin-top: 0; border-bottom: 1px solid var(--border-color); padding-bottom: 20px;">
    Chào buổi sáng, <?php echo htmlspecialchars($_SESSION['fullname']); ?>!
</h1>

<p style="color: var(--text-muted); margin-top: 20px;">
    Hãy chọn "Việc cá nhân" ở thanh bên trái để bắt đầu tạo bảng Kanban quản lý công việc nhé.
</p>

<?php require_once PROJECT_ROOT . '/views/layout/footer.php'; ?>