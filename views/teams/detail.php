<?php require_once PROJECT_ROOT . '/views/layout/header.php'; ?>

<?php if (isset($_SESSION['system_alert'])): ?>
    <script>alert("THÔNG BÁO:\n\n<?= $_SESSION['system_alert'] ?>");</script>
    <?php unset($_SESSION['system_alert']); ?>
<?php endif; ?>

<div style="margin-bottom: 20px;">
    <a href="index.php?action=teams" style="color: #787774; text-decoration: none; font-size: 0.95rem;">
        <i class="fas fa-arrow-left"></i> Quay lại danh sách nhóm
    </a>
</div>

<div style="background: white; padding: 25px; border-radius: 10px; border: 1px solid #e3e2e0; margin-bottom: 30px;">
    <h1 style="margin: 0 0 10px 0; font-size: 1.8rem; color: #37352f;"><?= htmlspecialchars($team['name']) ?></h1>
    <p style="color: #787774; margin: 0;"><?= htmlspecialchars($team['description']) ?></p>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
    <div style="background: white; padding: 25px; border-radius: 10px; border: 1px solid #e3e2e0;">
        <h2 style="font-size: 1.2rem; margin: 0 0 20px 0; color: #37352f;"><i class="fas fa-users" style="color: #2383e2;"></i> Thành viên (<?= count($members) ?>)</h2>
        
        <table style="width: 100%; border-collapse: collapse;">
            <tr style="border-bottom: 2px solid #f0f0f0; text-align: left;">
                <th style="padding: 10px; color: #787774; font-weight: 500;">Họ tên</th>
                <th style="padding: 10px; color: #787774; font-weight: 500;">Email</th>
                <th style="padding: 10px; color: #787774; font-weight: 500;">Vai trò</th>
            </tr>
            <?php foreach($members as $m): ?>
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td style="padding: 15px 10px; font-weight: 500; color: #37352f;">
                        <?= htmlspecialchars($m['fullname']) ?>
                        <?= ($m['id'] == $_SESSION['user_id']) ? '<span style="color:#787774; font-size:0.8rem; font-weight:normal;">(Bạn)</span>' : '' ?>
                    </td>
                    <td style="padding: 15px 10px; color: #787774;"><?= htmlspecialchars($m['email']) ?></td>
                    <td style="padding: 15px 10px;">
                        <?php if($m['role'] == 'Leader'): ?>
                            <span style="background: #fde8e8; color: #eb3639; padding: 4px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: bold;">Leader</span>
                        <?php else: ?>
                            <span style="background: #e8f3fb; color: #0b6e99; padding: 4px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: bold;">Member</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div style="background: white; padding: 25px; border-radius: 10px; border: 1px solid #e3e2e0; height: fit-content;">
        <h2 style="font-size: 1.1rem; margin: 0 0 15px 0; color: #37352f;"><i class="fas fa-user-plus"></i> Thêm đồng đội</h2>
        <p style="color: #787774; font-size: 0.9rem; margin-bottom: 20px;">Nhập email tài khoản của người bạn muốn mời vào nhóm.</p>
        
        <form action="index.php?action=invite-member" method="POST">
            <input type="hidden" name="team_id" value="<?= $team['id'] ?>">
            <input type="email" name="email" placeholder="Nhập địa chỉ email..." required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; margin-bottom: 15px; box-sizing: border-box;">
            <button type="submit" style="width: 100%; background: #2383e2; color: white; border: none; padding: 10px; border-radius: 4px; font-weight: 500; cursor: pointer;">
                Gửi lời mời
            </button>
        </form>
    </div>
</div>

<?php require_once PROJECT_ROOT . '/views/layout/footer.php'; ?>