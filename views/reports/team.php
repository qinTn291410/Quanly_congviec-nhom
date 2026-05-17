<?php require_once PROJECT_ROOT . '/views/layout/header.php'; ?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <h1 style="font-size: 1.8rem; margin: 0; color: #37352f;"><i class="fas fa-chart-line" style="color: #2383e2;"></i> Báo cáo Nhóm / Dự án</h1>
</div>

<?php if (isset($error)): ?>
    <div style="background: #fde8e8; color: #eb3639; padding: 15px 20px; border-radius: 8px; margin-bottom: 25px; border: 1px solid #f9c2c2;">
        <i class="fas fa-info-circle"></i> <?= $error ?>
    </div>
    <div style="margin-top: 25px;">
        <a href="index.php?action=teams" style="background: #2383e2; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; display: inline-block; font-weight: 500;">
            <i class="fas fa-arrow-left"></i> Quay lại Nhóm
        </a>
    </div>
<?php else: ?>
    <!-- CHỌN NHÓM -->
    <div style="background: white; padding: 20px; border-radius: 12px; border: 1px solid #e3e2e0; margin-bottom: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
        <label style="font-weight: 600; margin-right: 15px; color: #37352f;">Chọn Nhóm:</label>
        <select id="teamSelect" style="padding: 10px 15px; border-radius: 6px; border: 1px solid #ccc; font-size: 0.95rem; cursor: pointer;">
            <?php foreach ($teams as $team): ?>
                <option value="<?= $team['id'] ?>" <?= ($team['id'] == $selectedTeamId) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($team['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <script>
        document.getElementById('teamSelect').addEventListener('change', function() {
            window.location.href = 'index.php?action=report-team&team_id=' + this.value;
        });
    </script>

    <?php if ($selectedTeamId && isset($teamStats)): ?>
        <!-- THỐNG KÊ NHÓM -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
            <div style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #e3e2e0; box-shadow: 0 4px 15px rgba(0,0,0,0.03); text-align: center;">
                <div style="font-size: 2.5rem; font-weight: bold; color: #2383e2;">
                    <?= $teamStats['total_tasks'] ?? 0 ?>
                </div>
                <div style="color: #787774; margin-top: 10px; font-size: 0.95rem;">Tổng Công việc</div>
            </div>

            <div style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #e3e2e0; box-shadow: 0 4px 15px rgba(0,0,0,0.03); text-align: center;">
                <div style="font-size: 2.5rem; font-weight: bold; color: #0f7b6c;">
                    <?= $teamStats['completed_tasks'] ?? 0 ?>
                </div>
                <div style="color: #787774; margin-top: 10px; font-size: 0.95rem;">Hoàn thành</div>
            </div>

            <div style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #e3e2e0; box-shadow: 0 4px 15px rgba(0,0,0,0.03); text-align: center;">
                <div style="font-size: 2.5rem; font-weight: bold; color: #f59e0b;">
                    <?= $teamStats['pending_tasks'] ?? 0 ?>
                </div>
                <div style="color: #787774; margin-top: 10px; font-size: 0.95rem;">Đang xử lý</div>
            </div>

            <div style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #e3e2e0; box-shadow: 0 4px 15px rgba(0,0,0,0.03); text-align: center;">
                <div style="font-size: 2.5rem; font-weight: bold; color: #eb3639;">
                    <?= $teamStats['overdue_tasks'] ?? 0 ?>
                </div>
                <div style="color: #787774; margin-top: 10px; font-size: 0.95rem;">Quá hạn</div>
            </div>
        </div>

        <!-- TIẾN ĐỘ NHÓM -->
        <?php if ($teamStats['total_tasks'] > 0): ?>
        <div style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #e3e2e0; box-shadow: 0 4px 15px rgba(0,0,0,0.03); margin-bottom: 30px;">
            <h3 style="margin: 0 0 20px 0; color: #37352f; font-size: 1.1rem;"><i class="fas fa-chart-pie"></i> Tiến độ Nhóm</h3>
            <?php $teamProgress = round(($teamStats['completed_tasks'] / $teamStats['total_tasks']) * 100); ?>
            <div style="padding: 20px 0;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <span style="font-weight: 500;">Hoàn thành</span>
                    <span style="font-weight: bold; color: #2383e2;"><?= $teamProgress ?>%</span>
                </div>
                <div style="background: #f0f0f0; height: 20px; border-radius: 10px; overflow: hidden;">
                    <div style="background: linear-gradient(to right, #0f7b6c, #2383e2); height: 100%; width: <?= $teamProgress ?>%;"></div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- THỰC hiện của từng THÀNH VIÊN -->
        <div style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #e3e2e0; box-shadow: 0 4px 15px rgba(0,0,0,0.03); margin-bottom: 30px;">
            <h3 style="margin: 0 0 20px 0; color: #37352f; font-size: 1.1rem;"><i class="fas fa-users"></i> Hiệu suất Thành viên</h3>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid #f0f0f0; color: #787774;">
                        <th style="padding: 15px 10px; text-align: left;">Tên Thành viên</th>
                        <th style="padding: 15px 10px; text-align: center;">Giao việc</th>
                        <th style="padding: 15px 10px; text-align: center;">Hoàn thành</th>
                        <th style="padding: 15px 10px; text-align: center;">Đang xử lý</th>
                        <th style="padding: 15px 10px; text-align: center;">Tỉ lệ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($memberStats as $member): ?>
                    <tr style="border-bottom: 1px solid #f0f0f0;">
                        <td style="padding: 15px 10px; font-weight: 500;"><?= htmlspecialchars($member['fullname']) ?></td>
                        <td style="padding: 15px 10px; text-align: center; color: #2383e2; font-weight: bold;">
                            <?= $member['assigned_tasks'] ?? 0 ?>
                        </td>
                        <td style="padding: 15px 10px; text-align: center; color: #0f7b6c; font-weight: bold;">
                            <?= $member['completed_tasks'] ?? 0 ?>
                        </td>
                        <td style="padding: 15px 10px; text-align: center; color: #f59e0b; font-weight: bold;">
                            <?= $member['pending_tasks'] ?? 0 ?>
                        </td>
                        <td style="padding: 15px 10px; text-align: center;">
                            <?php
                                $memberRate = ($member['assigned_tasks'] > 0)
                                    ? round(($member['completed_tasks'] / $member['assigned_tasks']) * 100)
                                    : 0;
                            ?>
                            <div style="background: #f0f0f0; height: 6px; border-radius: 3px; overflow: hidden; margin-bottom: 5px;">
                                <div style="background: #0f7b6c; height: 100%; width: <?= $memberRate ?>%;"></div>
                            </div>
                            <span style="font-size: 0.85rem; color: #787774;"><?= $memberRate ?>%</span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- THỐNG KÊ DỰ ÁN -->
        <?php if (!empty($projectStats)): ?>
        <div style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #e3e2e0; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
            <h3 style="margin: 0 0 20px 0; color: #37352f; font-size: 1.1rem;"><i class="fas fa-project-diagram"></i> Tiến độ Dự án</h3>
            <div style="display: grid; gap: 15px;">
                <?php foreach ($projectStats as $project): ?>
                    <div style="padding: 15px; background: #f5f5f5; border-radius: 8px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <span style="font-weight: 600; color: #37352f;"><?= htmlspecialchars($project['title']) ?></span>
                            <span style="font-size: 0.85rem; color: #787774;">
                                <?= $project['completed_tasks'] ?? 0 ?>/<?= $project['total_tasks'] ?? 0 ?>
                            </span>
                        </div>
                        <div style="background: white; height: 8px; border-radius: 4px; overflow: hidden;">
                            <?php
                                $projectRate = ($project['total_tasks'] > 0)
                                    ? round(($project['completed_tasks'] / $project['total_tasks']) * 100)
                                    : 0;
                            ?>
                            <div style="background: #2383e2; height: 100%; width: <?= $projectRate ?>%;"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    <?php endif; ?>

    <div style="margin-top: 25px;">
        <a href="index.php?action=teams" style="background: #2383e2; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; display: inline-block; font-weight: 500;">
            <i class="fas fa-arrow-left"></i> Quay lại Nhóm
        </a>
    </div>

<?php endif; ?>

<?php require_once PROJECT_ROOT . '/views/layout/footer.php'; ?>
