<?php require_once PROJECT_ROOT . '/views/layout/header.php'; ?>

<?php
// TỰ ĐỘNG KIỂM TRA QUYỀN LEADER CỦA SẾP
$isLeader = false;
foreach($members as $m) {
    if($m['id'] == $_SESSION['user_id'] && $m['role'] == 'Leader') {
        $isLeader = true;
        break;
    }
}
?>

<?php if (isset($_SESSION['system_alert'])): ?>
    <script>alert("THÔNG BÁO:\n\n<?= $_SESSION['system_alert'] ?>");</script>
    <?php unset($_SESSION['system_alert']); ?>
<?php endif; ?>

<div style="margin-bottom: 20px;">
    <a href="index.php?action=teams" style="color: #787774; text-decoration: none; font-size: 0.95rem;">
        <i class="fas fa-arrow-left"></i> Quay lại danh sách nhóm
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
                        <?= htmlspecialchars($m['fullname']) ?> <?= ($m['id'] == $_SESSION['user_id']) ? '<span style="color:#787774; font-size:0.8rem; font-weight:normal;">(Bạn)</span>' : '' ?>
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
                               onclick="return confirm('Đá thanh niên này ra khỏi nhóm?');" 
                               style="color: #eb3639; font-size: 0.8rem; text-decoration: none; margin-left: 15px;" title="Đuổi khỏi nhóm">
                               <i class="fas fa-user-times"></i> Đuổi
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <?php if ($isLeader): ?>
    <div style="background: white; padding: 25px; border-radius: 10px; border: 1px solid #e3e2e0; height: fit-content;">
        <h2 style="font-size: 1.1rem; margin: 0 0 15px 0; color: #37352f;"><i class="fas fa-user-plus"></i> Thêm đồng đội</h2>
        <form action="index.php?action=invite-member" method="POST">
            <input type="hidden" name="team_id" value="<?= $team['id'] ?>">
            <input type="email" name="email" placeholder="Nhập địa chỉ email..." required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; margin-bottom: 15px; box-sizing: border-box;">
            <button type="submit" style="width: 100%; background: #2383e2; color: white; border: none; padding: 10px; border-radius: 4px; font-weight: 500; cursor: pointer;">Gửi lời mời</button>
        </form>
    </div>
    <?php endif; ?>
</div>

<div style="background: white; padding: 25px; border-radius: 10px; border: 1px solid #e3e2e0;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="font-size: 1.2rem; margin: 0; color: #37352f;"><i class="fas fa-project-diagram" style="color: #0f7b6c;"></i> Các Dự án đang chạy</h2>
        <?php if ($isLeader): ?>
        <button onclick="document.getElementById('projectModal').style.display='block'" style="background: #0f7b6c; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-weight: 500;">
            + Tạo Dự án mới
        </button>
        <?php endif; ?>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
        <?php if(empty($projects)): ?>
            <p style="color: #787774;">Chưa có dự án nào được tạo.</p>
        <?php else: ?>
            <?php foreach($projects as $p): ?>
                <div style="position: relative; border: 1px solid #e3e2e0; border-radius: 8px; padding: 20px; transition: 0.2s;">
                    
                    <?php if ($isLeader): ?>
                        <a href="index.php?action=delete-project&project_id=<?= $p['id'] ?>&team_id=<?= $team['id'] ?>" 
                           onclick="return confirm('Xóa dự án này? Các task bên trong cũng sẽ bốc hơi!');" 
                           style="position: absolute; top: 15px; right: 15px; color: #eb3639; text-decoration: none;" title="Xóa Dự án">
                           <i class="fas fa-trash"></i>
                        </a>
                    <?php endif; ?>

                    <h3 style="margin: 0 0 10px 0; color: #37352f; font-size: 1.1rem; padding-right: 25px;"><?= htmlspecialchars($p['name']) ?></h3>
                    <p style="color: #787774; font-size: 0.85rem; margin-bottom: 15px; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?= htmlspecialchars($p['description']) ?></p>
                    
                    <div style="font-size: 0.8rem; color: #37352f; margin-bottom: 5px;">
                        <i class="fas fa-user-tie" style="color: #d9730d;"></i> Quản lý: <strong><?= htmlspecialchars($p['manager_name']) ?></strong>
                    </div>
                    <div style="font-size: 0.8rem; color: #787774; margin-bottom: 15px;">
                        <i class="far fa-calendar-alt"></i> <?= date('d/m/Y', strtotime($p['start_date'])) ?> - <?= date('d/m/Y', strtotime($p['end_date'])) ?>
                    </div>
                    
                    <a href="index.php?action=project-kanban&id=<?= $p['id'] ?>" style="display: block; text-align: center; background: #f7f7f5; color: #37352f; text-decoration: none; padding: 8px; border-radius: 4px; font-size: 0.85rem; font-weight: 500; border: 1px solid #e3e2e0;">
                        Vào Kanban Nhóm &rarr;
                    </a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<div id="projectModal" style="display:none; position:fixed; z-index:100; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.4);">
    <div style="background:white; margin:5% auto; padding:30px; width:500px; border-radius:8px;">
        <h2 style="margin-top:0;">Tạo Dự án mới</h2>
        <form action="index.php?action=create-project" method="POST">
            <input type="hidden" name="team_id" value="<?= $team['id'] ?>">
            
            <div class="form-group" style="margin-bottom: 15px;">
                <label>Tên Dự án</label>
                <input type="text" name="name" class="form-control" required style="width: 100%; padding: 8px; box-sizing: border-box;">
            </div>
            
            <div class="form-group" style="margin-bottom: 15px;">
                <label>Mô tả công việc</label>
                <textarea name="description" class="form-control" rows="3" style="width: 100%; padding: 8px; box-sizing: border-box;"></textarea>
            </div>
            
            <div style="display:flex; gap:10px; margin-bottom: 15px;">
                <div style="flex:1;">
                    <label>Ngày bắt đầu</label>
                    <input type="date" name="start_date" required class="form-control" style="width: 100%; padding: 8px; box-sizing: border-box;">
                </div>
                <div style="flex:1;">
                    <label>Ngày kết thúc</label>
                    <input type="date" name="end_date" required class="form-control" style="width: 100%; padding: 8px; box-sizing: border-box;">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label>Người quản lý (Project Manager)</label>
                <select name="manager_id" required class="form-control" style="width: 100%; padding: 8px; box-sizing: border-box;">
                    <?php foreach($members as $m): ?>
                        <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['fullname']) ?> <?= ($m['role'] == 'Leader') ? '(Leader)' : '' ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
                <button type="button" onclick="document.getElementById('projectModal').style.display='none'" style="background:none; border:1px solid #ccc; padding:8px 15px; cursor:pointer; border-radius:4px;">Hủy</button>
                <button type="submit" style="background:#0f7b6c; color:white; border:none; padding:8px 15px; cursor:pointer; border-radius:4px;">Lưu Dự án</button>
            </div>
        </form>
    </div>
</div>

<?php require_once PROJECT_ROOT . '/views/layout/footer.php'; ?>