<?php require_once PROJECT_ROOT . '/views/layout/header.php'; ?>

<style>
    .profile-input {
        width: 100%; 
        padding: 10px 12px; 
        border: 1px solid #e3e2e0; 
        border-radius: 6px; 
        box-sizing: border-box; 
        font-size: 0.95rem; 
        font-family: inherit;
        transition: all 0.2s ease;
    }
    .profile-input:focus {
        border-color: #2383e2; 
        outline: none; 
        box-shadow: 0 0 0 3px rgba(35, 131, 226, 0.15);
    }
    .profile-label {
        display: block; 
        margin-bottom: 6px; 
        font-weight: 500; 
        font-size: 0.85rem; 
        color: #787774;
    }
    .btn-upload {
        display: inline-block;
        cursor: pointer;
        background: white;
        border: 1px solid #d1d1d1;
        padding: 6px 15px;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 500;
        color: #37352f;
        transition: background 0.2s;
    }
    .btn-upload:hover { background: #f1f1f0; }
</style>

<div style="max-width: 500px; margin: 20px auto; background: white; padding: 40px; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
    
    <div style="margin-bottom: 25px;">
        <a href="index.php?action=tasks" style="color: #787774; text-decoration: none; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 5px; transition: color 0.2s;">
            <i class="fas fa-arrow-left"></i> Quay lại Workspace
        </a>
    </div>

    <h2 style="margin-top: 0; font-size: 1.5rem; color: #37352f; margin-bottom: 20px;">⚙️ Cài đặt hồ sơ</h2>
    
    <?php if(!empty($message)): ?>
        <div style="padding: 12px; background: #dbeddb; color: #28453c; border-radius: 6px; margin-bottom: 25px; font-size: 0.9rem; text-align: center;">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <form action="index.php?action=profile" method="POST" enctype="multipart/form-data">
        
        <div style="text-align: center; margin-bottom: 35px;">
            <?php $avatarPath = !empty($user['avatar']) && $user['avatar'] !== 'default.png' ? 'uploads/' . $user['avatar'] : 'https://ui-avatars.com/api/?name=' . urlencode($user['fullname']) . '&background=random'; ?>
            
            <div style="position: relative; display: inline-block;">
                <img src="<?= $avatarPath ?>" style="width: 110px; height: 110px; border-radius: 50%; object-fit: cover; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            </div>
            
            <div style="margin-top: 15px;">
                <label for="avatar-upload" class="btn-upload">
                    <i class="fas fa-camera" style="margin-right: 5px; color: #787774;"></i> Đổi ảnh đại diện
                </label>
                <input id="avatar-upload" type="file" name="avatar" accept="image/*" style="display: none;" onchange="document.getElementById('file-name').textContent = this.files[0].name;">
                <div id="file-name" style="font-size: 0.75rem; color: #787774; margin-top: 8px;"></div>
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label class="profile-label">Họ và tên</label>
            <input type="text" name="fullname" class="profile-input" value="<?= htmlspecialchars($user['fullname']) ?>" required>
        </div>

        <div style="margin-bottom: 20px;">
            <label class="profile-label">Email</label>
            <input type="email" name="email" class="profile-input" value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>

        <div style="display: flex; gap: 15px; margin-bottom: 20px;">
            <div style="flex: 1;">
                <label class="profile-label">Số điện thoại</label>
                <input type="text" name="phone" class="profile-input" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="Ví dụ: 0987654321">
            </div>
            
            <div style="flex: 1;">
                <label class="profile-label">Ngày sinh</label>
                <input type="date" name="dob" class="profile-input" value="<?= htmlspecialchars($user['dob'] ?? '') ?>">
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label class="profile-label">Địa chỉ</label>
            <input type="text" name="address" class="profile-input" value="<?= htmlspecialchars($user['address'] ?? '') ?>" placeholder="Ví dụ: Quận 1, TP. HCM...">
        </div>

        <div style="margin-bottom: 30px;">
            <label class="profile-label">Giới thiệu bản thân (Bio)</label>
            <textarea name="bio" class="profile-input" rows="3" placeholder="Viết vài dòng giới thiệu về bản thân..." style="resize: vertical;"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
        </div>

        <button type="submit" style="background: #2383e2; color: white; border: none; padding: 12px 20px; border-radius: 6px; cursor: pointer; width: 100%; font-weight: 500; font-size: 0.95rem; transition: background 0.2s;">
            Lưu thay đổi
        </button>
    </form>
</div>

<?php require_once PROJECT_ROOT . '/views/layout/footer.php'; ?>