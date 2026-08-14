<?php require_once PROJECT_ROOT . '/views/layout/header.php'; ?>

<style>
    .setting-group { display: flex; justify-content: space-between; align-items: center; padding: 15px 0; border-bottom: 1px solid #f1f1f0; }
    .setting-label { font-weight: 500; color: #37352f; font-size: 0.95rem; display: block; margin-bottom: 3px; }
    .setting-desc { font-size: 0.8rem; color: #787774; }
    .setting-select { padding: 8px 12px; border: 1px solid #e3e2e0; border-radius: 6px; font-family: inherit; font-size: 0.9rem; color: #37352f; background: white; cursor: pointer; outline: none; transition: border-color 0.2s; }
    .setting-select:focus { border-color: #2383e2; }
    
    .switch { position: relative; display: inline-block; width: 40px; height: 22px; }
    .switch input { opacity: 0; width: 0; height: 0; }
    .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .3s; border-radius: 22px; }
    .slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 2px; bottom: 2px; background-color: white; transition: .3s; border-radius: 50%; box-shadow: 0 1px 3px rgba(0,0,0,0.2); }
    input:checked + .slider { background-color: #2383e2; }
    input:checked + .slider:before { transform: translateX(18px); }
</style>

<div style="max-width: 500px; margin: 20px auto; background: white; padding: 40px; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
    
    <div style="margin-bottom: 25px;">
        <a href="index.php?action=tasks" style="color: #787774; text-decoration: none; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 5px;">
            <i class="fas fa-arrow-left"></i> <?= __('back_to_workspace') ?? 'Quay lại Workspace' ?>
        </a>
    </div>

    <h2 style="margin-top: 0; font-size: 1.5rem; color: #37352f; margin-bottom: 20px;"><?= __('system_settings_title') ?></h2>
    
    <?php if(!empty($message)): ?>
        <div style="padding: 12px; background: #dbeddb; color: #0f7b6c; border-radius: 6px; margin-bottom: 25px; font-size: 0.9rem; text-align: center; border: 1px solid #c3e2c3;">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <form action="index.php?action=settings" method="POST">
        
        <div class="setting-group">
            <div>
                <span class="setting-label"><?= __('sys_notif_label') ?></span>
                <span class="setting-desc"><?= __('sys_notif_desc') ?></span>
            </div>
            <label class="switch">
                <input type="checkbox" name="notifications" <?= (!isset($settings['notifications']) || $settings['notifications'] == 1) ? 'checked' : '' ?>>
                <span class="slider"></span>
            </label>
        </div>

        <div class="setting-group">
            <div>
                <span class="setting-label"><?= __('language_lbl') ?></span>
                <span class="setting-desc"><?= __('language_desc') ?></span>
            </div>
            <select name="language" class="setting-select">
                <option value="vi" <?= (isset($settings['language']) && $settings['language'] == 'vi') ? 'selected' : '' ?>>🇻🇳 Tiếng Việt</option>
                <option value="en" <?= (isset($settings['language']) && $settings['language'] == 'en') ? 'selected' : '' ?>>🇬🇧 English</option>
            </select>
        </div>

        <div class="setting-group" style="border-bottom: none; margin-bottom: 20px;">
            <div>
                <span class="setting-label"><?= __('timezone_lbl') ?></span>
                <span class="setting-desc"><?= __('timezone_desc') ?></span>
            </div>
            <select name="timezone" class="setting-select">
                <option value="Asia/Ho_Chi_Minh" <?= (isset($settings['timezone']) && $settings['timezone'] == 'Asia/Ho_Chi_Minh') ? 'selected' : '' ?>>GMT+7 (Hồ Chí Minh)</option>
                <option value="Asia/Tokyo" <?= (isset($settings['timezone']) && $settings['timezone'] == 'Asia/Tokyo') ? 'selected' : '' ?>>GMT+9 (Tokyo)</option>
                <option value="America/New_York" <?= (isset($settings['timezone']) && $settings['timezone'] == 'America/New_York') ? 'selected' : '' ?>>GMT-5 (New York)</option>
            </select>
        </div>

        <button type="submit" style="background: #2383e2; color: white; border: none; padding: 12px 20px; border-radius: 6px; cursor: pointer; width: 100%; font-weight: 500; font-size: 0.95rem; transition: background 0.2s;">
            <?= __('btn_save_settings') ?>
        </button>
    </form>
</div>

<?php require_once PROJECT_ROOT . '/views/layout/footer.php'; ?>