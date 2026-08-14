<?php require_once PROJECT_ROOT . '/views/layout/header.php'; ?>

<style>
    .toggle-switch { position: relative; display: inline-block; width: 46px; height: 24px; }
    .toggle-switch input { opacity: 0; width: 0; height: 0; }
    .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 34px; }
    .slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.2); }
    input:checked + .slider { background-color: #2383e2; }
    input:checked + .slider:before { transform: translateX(22px); }
</style>

<div style="max-width: 700px; margin: 0 auto; background: white; padding: 40px; border-radius: 12px; border: 1px solid #e3e2e0; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
    <div style="margin-bottom: 20px;">
        <a href="index.php?action=dashboard" style="color: #787774; text-decoration: none; font-size: 0.95rem;">
            <i class="fas fa-arrow-left"></i> <?= __('back_to_workspace') ?>
        </a>
    </div>

    <h1 style="font-size: 1.8rem; margin: 0 0 30px 0; color: #37352f; display: flex; align-items: center; gap: 10px;">
        <i class="fas fa-tools" style="color: #787774;"></i> <?= __('settings_title') ?>
    </h1>

    <?php if (isset($_SESSION['system_alert'])): ?>
        <div style="background: #dbeddb; color: #0f7b6c; padding: 12px 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 500;">
            <i class="fas fa-check-circle"></i> <?= $_SESSION['system_alert'] ?>
        </div>
        <?php unset($_SESSION['system_alert']); ?>
    <?php endif; ?>

    <form method="POST" action="index.php?action=settings">
        <div style="margin-bottom: 25px; padding-bottom: 20px; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <strong style="font-size: 1.05rem; color: #37352f;"><?= __('notify_email') ?></strong>
                <p style="margin: 5px 0 0 0; font-size: 0.85rem; color: #787774;"><?= __('notify_email_desc') ?></p>
            </div>
            
            <label class="toggle-switch">
                <input type="checkbox" name="notify_deadline" value="1" <?= (isset($nDeadline) && $nDeadline) ? 'checked' : '' ?>>
                <span class="slider"></span>
            </label>
        </div>

        <div style="margin-bottom: 25px; padding-bottom: 20px; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <strong style="font-size: 1.05rem; color: #37352f;"><?= __('notify_popup') ?></strong>
                <p style="margin: 5px 0 0 0; font-size: 0.85rem; color: #787774;"><?= __('notify_popup_desc') ?></p>
            </div>
            
            <label class="toggle-switch">
                <input type="checkbox" name="notify_alerts" value="1" <?= (isset($nAlerts) && $nAlerts) ? 'checked' : '' ?>>
                <span class="slider"></span>
            </label>
        </div>

        <div style="margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <strong style="font-size: 1.05rem; color: #37352f;"><?= __('language_lbl') ?></strong>
                <p style="margin: 5px 0 0 0; font-size: 0.85rem; color: #787774;"><?= __('language_desc') ?></p>
            </div>
            <?php $currentLang = $_SESSION['language'] ?? 'vi'; ?>
            <select name="language" style="padding: 8px 15px; border-radius: 6px; border: 1px solid #ccc; font-size: 0.95rem; outline: none; background: white;">
                <option value="vi" <?= $currentLang == 'vi' ? 'selected' : '' ?>>🇻🇳 Tiếng Việt</option>
                <option value="en" <?= $currentLang == 'en' ? 'selected' : '' ?>>🇬🇧 English</option>
            </select>
        </div>

        <div style="margin-bottom: 40px; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <strong style="font-size: 1.05rem; color: #37352f;"><?= __('timezone_lbl') ?></strong>
                <p style="margin: 5px 0 0 0; font-size: 0.85rem; color: #787774;"><?= __('timezone_desc') ?></p>
            </div>
            <?php $currentTz = $_SESSION['timezone'] ?? 'Asia/Ho_Chi_Minh'; ?>
            <select name="timezone" style="padding: 8px 15px; border-radius: 6px; border: 1px solid #ccc; font-size: 0.95rem; outline: none; background: white;">
                <option value="Asia/Ho_Chi_Minh" <?= $currentTz == 'Asia/Ho_Chi_Minh' ? 'selected' : '' ?>>GMT+7 (Hồ Chí Minh)</option>
                <option value="Asia/Tokyo" <?= $currentTz == 'Asia/Tokyo' ? 'selected' : '' ?>>GMT+9 (Tokyo)</option>
            </select>
        </div>

        <button type="submit" style="width: 100%; background: #2383e2; color: white; border: none; padding: 12px; border-radius: 6px; font-size: 1rem; font-weight: 500; cursor: pointer; transition: 0.2s;">
            <?= __('btn_save_settings') ?>
        </button>
    </form>
</div>

<?php require_once PROJECT_ROOT . '/views/layout/footer.php'; ?>