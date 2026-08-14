<?php require_once PROJECT_ROOT . '/views/layout/header.php'; ?>
<?php $view = $_GET['view'] ?? 'users'; ?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <h1 style="font-size: 1.8rem; margin: 0; color: #37352f;"><i class="fas fa-shield-alt" style="color: #eb3639;"></i> <?= __('admin_title') ?></h1>
</div>

<div style="display: flex; gap: 35px; border-bottom: 2px solid #e3e2e0; margin-bottom: 30px;">
    <a href="index.php?action=admin&view=users" style="text-decoration: none; font-size: 1.05rem; font-weight: 500; padding-bottom: 12px; margin-bottom: -2px; transition: 0.2s; color: <?= ($view == 'users') ? '#37352f' : '#787774' ?>; border-bottom: <?= ($view == 'users') ? '3px solid #eb3639' : '3px solid transparent' ?>;">
        <i class="fas fa-users-cog"></i> <?= __('tab_manage_users') ?>
    </a>
    <a href="index.php?action=admin&view=config" style="text-decoration: none; font-size: 1.05rem; font-weight: 500; padding-bottom: 12px; margin-bottom: -2px; transition: 0.2s; color: <?= ($view == 'config') ? '#37352f' : '#787774' ?>; border-bottom: <?= ($view == 'config') ? '3px solid #eb3639' : '3px solid transparent' ?>;">
        <i class="fas fa-cogs"></i> <?= __('tab_system_config') ?>
    </a>
</div>

<?php if (isset($_SESSION['system_alert'])): ?>
    <div style="background: #dbeddb; color: #0f7b6c; padding: 12px 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 500;">
        <i class="fas fa-check-circle"></i> <?= $_SESSION['system_alert'] ?>
    </div>
    <?php unset($_SESSION['system_alert']); ?>
<?php endif; ?>

<?php if ($view == 'users'): ?>
    <div style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #e3e2e0; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 2px solid #f0f0f0; color: #787774;">
                    <th style="padding: 15px 10px;"><?= __('col_id') ?></th>
                    <th style="padding: 15px 10px;"><?= __('col_fullname') ?></th>
                    <th style="padding: 15px 10px;"><?= __('col_email') ?></th>
                    <th style="padding: 15px 10px;"><?= __('col_role') ?></th>
                    <th style="padding: 15px 10px;"><?= __('col_status') ?></th>
                    <th style="padding: 15px 10px; text-align: right;"><?= __('col_action') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr style="border-bottom: 1px solid #f0f0f0;">
                        <td style="padding: 15px 10px; font-weight: bold;">#<?= $u['id'] ?></td>
                        <td style="padding: 15px 10px;"><?= htmlspecialchars($u['fullname']) ?></td>
                        <td style="padding: 15px 10px; color: #787774;"><?= htmlspecialchars($u['email']) ?></td>
                        <td style="padding: 15px 10px;">
                            <?php if ($u['id'] == $_SESSION['user_id']): ?>
                                <span style="background: #fde8e8; color: #eb3639; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: bold;"><?= __('admin_you') ?></span>
                            <?php else: ?>
                                <form action="index.php?action=admin-change-role" method="POST" style="margin:0;">
                                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                    <select name="role" onchange="this.form.submit()" style="padding: 4px 8px; border-radius: 4px; border: 1px solid #ccc; font-size: 0.85rem;">
                                        <option value="user" <?= $u['role'] == 'user' ? 'selected' : '' ?>>User</option>
                                        <option value="admin" <?= $u['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                                    </select>
                                </form>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 15px 10px;">
                            <?php if ($u['is_locked']): ?>
                                <span style="background: #f1f1f0; color: #787774; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: bold;"><i class="fas fa-lock"></i> <?= __('btn_lock_account') ?></span>
                            <?php else: ?>
                                <span style="background: #dbeddb; color: #0f7b6c; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: bold;"><i class="fas fa-check"></i> <?= __('status_active') ?></span>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 15px 10px; text-align: right;">
                            <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                <a href="index.php?action=admin-toggle-lock&id=<?= $u['id'] ?>" style="background: <?= $u['is_locked'] ? '#0f7b6c' : '#eb3639' ?>; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 0.85rem; font-weight: 500;">
                                    <?= $u['is_locked'] ? 'Mở khóa' : __('btn_lock_account') ?>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

<?php else: ?>
    <div style="background: white; padding: 30px; border-radius: 12px; border: 1px solid #e3e2e0; box-shadow: 0 4px 15px rgba(0,0,0,0.03); max-width: 800px;">
        <form action="index.php?action=admin-save-config" method="POST">
            <h3 style="margin: 0 0 20px 0; color: #0f7b6c;"><i class="fas fa-tags"></i> <?= __('config_cat_label') ?></h3>
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: 500; margin-bottom: 5px;"><?= __('config_statuses') ?></label>
                <input type="text" name="configs[task_statuses]" value="<?= htmlspecialchars($configs['task_statuses'] ?? '') ?>" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc; box-sizing: border-box;">
            </div>
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: 500; margin-bottom: 5px;"><?= __('config_priorities') ?></label>
                <input type="text" name="configs[task_priorities]" value="<?= htmlspecialchars($configs['task_priorities'] ?? '') ?>" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc; box-sizing: border-box;">
            </div>
            
            <div style="margin-bottom: 30px;">
                <label style="display: block; font-weight: 500; margin-bottom: 5px;"><?= __('config_labels') ?></label>
                <input type="text" name="configs[task_labels]" value="<?= htmlspecialchars($configs['task_labels'] ?? '') ?>" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc; box-sizing: border-box;">
            </div>

            <h3 style="margin: 0 0 20px 0; color: #2383e2;"><i class="fas fa-envelope"></i> <?= __('config_email_smtp') ?></h3>
            
            <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                <div style="flex: 2;">
                    <label style="display: block; font-weight: 500; margin-bottom: 5px;"><?= __('config_host') ?></label>
                    <input type="text" name="configs[smtp_host]" value="<?= htmlspecialchars($configs['smtp_host'] ?? '') ?>" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc; box-sizing: border-box;">
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-weight: 500; margin-bottom: 5px;"><?= __('config_port') ?></label>
                    <input type="text" name="configs[smtp_port]" value="<?= htmlspecialchars($configs['smtp_port'] ?? '') ?>" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc; box-sizing: border-box;">
                </div>
            </div>

            <div style="display: flex; gap: 15px; margin-bottom: 30px;">
                <div style="flex: 1;">
                    <label style="display: block; font-weight: 500; margin-bottom: 5px;"><?= __('config_email_sender') ?></label>
                    <input type="email" name="configs[smtp_user]" value="<?= htmlspecialchars($configs['smtp_user'] ?? '') ?>" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc;">
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-weight: 500; margin-bottom: 5px;"><?= __('config_app_password') ?></label>
                    <input type="password" name="configs[smtp_pass]" value="<?= htmlspecialchars($configs['smtp_pass'] ?? '') ?>" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc;" placeholder="16 ký tự từ Google">
                </div>
            </div>
            <button type="submit" style="background: #eb3639; color: white; border: none; padding: 12px 25px; border-radius: 6px; cursor: pointer; font-size: 1rem; font-weight: 600;"><i class="fas fa-save"></i> <?= __('btn_save_config') ?></button>
        </form>
    </div>
<?php endif; ?>

<?php require_once PROJECT_ROOT . '/views/layout/footer.php'; ?>