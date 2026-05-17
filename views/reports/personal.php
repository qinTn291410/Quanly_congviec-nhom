<?php require_once PROJECT_ROOT . '/views/layout/header.php'; ?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <h1 style="font-size: 1.8rem; margin: 0; color: #37352f;"><i class="fas fa-chart-bar" style="color: #2383e2;"></i> Báo cáo Công việc Cá nhân</h1>
</div>

<!-- THỐNG KÊ TỔNG QUAN -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
    <div style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #e3e2e0; box-shadow: 0 4px 15px rgba(0,0,0,0.03); text-align: center;">
        <div style="font-size: 2.5rem; font-weight: bold; color: #2383e2;">
            <?= $stats['total_tasks'] ?? 0 ?>
        </div>
        <div style="color: #787774; margin-top: 10px; font-size: 0.95rem;">Tổng số Công việc</div>
    </div>

    <div style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #e3e2e0; box-shadow: 0 4px 15px rgba(0,0,0,0.03); text-align: center;">
        <div style="font-size: 2.5rem; font-weight: bold; color: #0f7b6c;">
            <?= $stats['completed_tasks'] ?? 0 ?>
        </div>
        <div style="color: #787774; margin-top: 10px; font-size: 0.95rem;">Hoàn thành</div>
    </div>

    <div style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #e3e2e0; box-shadow: 0 4px 15px rgba(0,0,0,0.03); text-align: center;">
        <div style="font-size: 2.5rem; font-weight: bold; color: #f59e0b;">
            <?= $stats['pending_tasks'] ?? 0 ?>
        </div>
        <div style="color: #787774; margin-top: 10px; font-size: 0.95rem;">Đang xử lý</div>
    </div>

    <div style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #e3e2e0; box-shadow: 0 4px 15px rgba(0,0,0,0.03); text-align: center;">
        <div style="font-size: 2.5rem; font-weight: bold; color: #eb3639;">
            <?= $stats['overdue_tasks'] ?? 0 ?>
        </div>
        <div style="color: #787774; margin-top: 10px; font-size: 0.95rem;">Quá hạn</div>
    </div>
</div>

<!-- CHART SECTION -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 25px; margin-bottom: 30px;">
    <!-- TRẠNG THÁI -->
    <div style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #e3e2e0; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
        <h3 style="margin: 0 0 20px 0; color: #37352f; font-size: 1.1rem;"><i class="fas fa-list-check"></i> Phân bố theo Trạng thái</h3>
        <div style="padding: 20px 0;">
            <?php foreach ($tasksByStatus as $item): ?>
                <?php
                    $status = $item['status'];
                    $count = $item['count'];
                    $total = $stats['total_tasks'];
                    $percentage = $total > 0 ? round(($count / $total) * 100) : 0;

                    $statusColor = match($status) {
                        'Done' => '#0f7b6c',
                        'In Progress' => '#2383e2',
                        'To Do' => '#f59e0b',
                        default => '#787774'
                    };
                ?>
                <div style="margin-bottom: 15px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 0.9rem;">
                        <span><?= htmlspecialchars($status) ?></span>
                        <span style="font-weight: bold;"><?= $count ?> (<?= $percentage ?>%)</span>
                    </div>
                    <div style="background: #f0f0f0; height: 8px; border-radius: 4px; overflow: hidden;">
                        <div style="background: <?= $statusColor ?>; height: 100%; width: <?= $percentage ?>%;"></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- MỨC ĐỘ ƯU TIÊN -->
    <div style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #e3e2e0; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
        <h3 style="margin: 0 0 20px 0; color: #37352f; font-size: 1.1rem;"><i class="fas fa-flag"></i> Phân bố theo Mức độ ưu tiên</h3>
        <div style="padding: 20px 0;">
            <?php foreach ($tasksByPriority as $item): ?>
                <?php
                    $priority = $item['priority'];
                    $count = $item['count'];
                    $total = $stats['total_tasks'];
                    $percentage = $total > 0 ? round(($count / $total) * 100) : 0;

                    $priorityColor = match($priority) {
                        'High' => '#eb3639',
                        'Medium' => '#f59e0b',
                        'Low' => '#0f7b6c',
                        default => '#787774'
                    };
                ?>
                <div style="margin-bottom: 15px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 0.9rem;">
                        <span><?= htmlspecialchars($priority) ?></span>
                        <span style="font-weight: bold;"><?= $count ?> (<?= $percentage ?>%)</span>
                    </div>
                    <div style="background: #f0f0f0; height: 8px; border-radius: 4px; overflow: hidden;">
                        <div style="background: <?= $priorityColor ?>; height: 100%; width: <?= $percentage ?>%;"></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- DANH MỤC & TIMELINE -->
<?php if (!empty($tasksByCategory)): ?>
<div style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #e3e2e0; box-shadow: 0 4px 15px rgba(0,0,0,0.03); margin-bottom: 30px;">
    <h3 style="margin: 0 0 20px 0; color: #37352f; font-size: 1.1rem;"><i class="fas fa-tags"></i> Phân bố theo Danh mục</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px;">
        <?php foreach ($tasksByCategory as $item): ?>
            <div style="background: #f5f5f5; padding: 15px; border-radius: 8px; text-align: center;">
                <div style="font-size: 1.5rem; font-weight: bold; color: #2383e2;"><?= $item['count'] ?></div>
                <div style="font-size: 0.85rem; color: #787774; margin-top: 5px;"><?= htmlspecialchars($item['category']) ?></div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- COMPLETION RATE -->
<?php if ($stats['total_tasks'] > 0): ?>
<div style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #e3e2e0; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
    <h3 style="margin: 0 0 20px 0; color: #37352f; font-size: 1.1rem;"><i class="fas fa-chart-pie"></i> Tỉ lệ hoàn thành</h3>
    <?php $completionRate = round(($stats['completed_tasks'] / $stats['total_tasks']) * 100); ?>
    <div style="padding: 20px 0;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
            <span style="font-weight: 500;">Tiến độ hoàn thành</span>
            <span style="font-weight: bold; color: #2383e2;"><?= $completionRate ?>%</span>
        </div>
        <div style="background: #f0f0f0; height: 20px; border-radius: 10px; overflow: hidden;">
            <div style="background: linear-gradient(to right, #0f7b6c, #2383e2); height: 100%; width: <?= $completionRate ?>%;"></div>
        </div>
    </div>
</div>
<?php endif; ?>

<div style="margin-top: 25px;">
    <a href="index.php?action=tasks" style="background: #2383e2; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; display: inline-block; font-weight: 500;">
        <i class="fas fa-arrow-left"></i> Quay lại Danh sách Công việc
    </a>
</div>

<?php require_once PROJECT_ROOT . '/views/layout/footer.php'; ?>
