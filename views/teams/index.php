<?php require_once PROJECT_ROOT . '/views/layout/header.php'; ?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <div>
        <h1 style="font-size: 1.8rem; margin: 0; color: #37352f;"><i class="fas fa-users" style="color: #2383e2; margin-right: 10px;"></i>Quản lý Đội nhóm</h1>
        <p style="color: #787774; font-size: 0.95rem; margin-top: 5px;">Tạo nhóm và cùng chạy dự án.</p>
    </div>
    <button onclick="document.getElementById('teamModal').style.display='block'" style="background: #2383e2; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: 500;">
        + Tạo nhóm mới
    </button>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 25px;">
    <?php if(empty($teams)): ?>
        <p style="color: #787774;">Bạn chưa tham gia nhóm nào. Bấm "Tạo nhóm mới" để bắt đầu</p>
    <?php else: ?>
        <?php foreach($teams as $team): ?>
            <div style="background: white; border: 1px solid #e3e2e0; border-radius: 10px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); transition: transform 0.2s;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <h3 style="margin: 0 0 10px 0; font-size: 1.2rem; color: #37352f;"><?= htmlspecialchars($team['name']) ?></h3>
                    
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <?php if($team['role'] == 'Leader'): ?>
                            <span style="background: #fde8e8; color: #eb3639; padding: 3px 8px; border-radius: 12px; font-size: 0.7rem; font-weight: bold;">Leader</span>
                            <a href="index.php?action=delete-team&id=<?= $team['id'] ?>" 
                                onclick="return confirm('Sếp có chắc chắn muốn giải tán nhóm này không? Toàn bộ dự án sẽ bay màu!');" 
                                style="color: #eb3639; font-size: 0.85rem; text-decoration: none;" title="Xóa nhóm">
                                <i class="fas fa-trash"></i>
                            </a>
                        <?php else: ?>
                            <span style="background: #e8f3fb; color: #0b6e99; padding: 3px 8px; border-radius: 12px; font-size: 0.7rem; font-weight: bold;">Member</span>
                        <?php endif; ?>
                    </div>
                    </div>
                
                <p style="color: #787774; font-size: 0.9rem; margin-bottom: 20px; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                    <?= htmlspecialchars($team['description']) ?>
                </p>
                
                <div style="border-top: 1px solid #f0f0f0; padding-top: 15px; display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 0.8rem; color: #787774;"><i class="far fa-clock"></i> Tạo ngày <?= date('d/m/Y', strtotime($team['created_at'])) ?></span>
                    <a href="index.php?action=team-detail&id=<?= $team['id'] ?>" style="background: #f7f7f5; color: #37352f; text-decoration: none; padding: 6px 12px; border-radius: 4px; font-size: 0.85rem; font-weight: 500; border: 1px solid #e3e2e0;">
                        Vào không gian làm việc &rarr;
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<div id="teamModal" style="display:none; position:fixed; z-index:100; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.4);">
    <div style="background:white; margin:10% auto; padding:30px; width:400px; border-radius:8px;">
        <h2 style="margin-top:0;">Khởi tạo nhóm mới</h2>
        <form action="index.php?action=create-team" method="POST">
            <div class="form-group" style="margin-bottom: 15px;">
                <label>Tên Đội/Nhóm</label>
                <input type="text" name="name" class="form-control" placeholder="Tên nhóm" required style="width: 100%; padding: 8px; box-sizing: border-box;">
            </div>
            <div class="form-group" style="margin-bottom: 15px;">
                <label>Mô tả ngắn</label>
                <textarea name="description" class="form-control" rows="3" placeholder="Mục tiêu của nhóm" style="width: 100%; padding: 8px; box-sizing: border-box;"></textarea>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
                <button type="button" onclick="document.getElementById('teamModal').style.display='none'" style="background:none; border:1px solid #ccc; padding:8px 15px; cursor:pointer; border-radius:4px;">Hủy</button>
                <button type="submit" style="background:#2383e2; color:white; border:none; padding:8px 15px; cursor:pointer; border-radius:4px;">Tạo nhóm</button>
            </div>
        </form>
    </div>
</div>

<?php require_once PROJECT_ROOT . '/views/layout/footer.php'; ?>