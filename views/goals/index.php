<?php 
require_once PROJECT_ROOT . '/views/layout/header.php'; 
require_once PROJECT_ROOT . '/src/Models/GoalModel.php';

$goalModel = new \Tinhu\TaskManager\Models\GoalModel();
$userId = $_SESSION['user_id'];
$goals = $goalModel->getGoalsWithProgress($userId);

$activeGoals = [];
$completedGoals = [];

foreach($goals as $g) {
    $total = $g['total_tasks'] ?? 0;
    $completed = $g['completed_tasks'] ?? 0;
    $percent = $total > 0 ? round(($completed / $total) * 100) : 0;
    
    // Thêm key percent vào mảng để dùng lại ở dưới cho tiện
    $g['percent'] = $percent;
    
    if($total > 0 && $percent == 100) {
        $completedGoals[] = $g;
    } else {
        $activeGoals[] = $g;
    }
}

// Màu sắc cho từng loại mục tiêu
$typeColors = [
    'Tuần' => ['bg' => '#e8f3fb', 'text' => '#0b6e99'],
    'Tháng' => ['bg' => '#f4e0f9', 'text' => '#8f24b2'],
    'Quý' => ['bg' => '#fdecc8', 'text' => '#ad7f11']
];

// Hàm render thẻ Goal (tạo hàm để tái sử dụng cho gọn code)
function renderGoalCard($g, $typeColors, $isCompleted = false) {
    $color = $typeColors[$g['type']] ?? $typeColors['Tuần'];
    $progressBarColor = $isCompleted ? '#0f7b6c' : '#2383e2';
    $opacity = $isCompleted ? '0.7' : '1'; 
    $border = $isCompleted ? 'border: 1px solid #0f7b6c;' : 'border: 1px solid #e3e2e0;';
    
    echo "
    <div style='background: white; border-radius: 8px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); opacity: {$opacity}; {$border}'>
        <div style='display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;'>
            <h3 style='margin: 0; font-size: 1.1rem; color: #37352f;'>" . htmlspecialchars($g['title']) . "</h3>
            
            <div style='display: flex; gap: 8px; align-items: center;'>
                <span style='background: {$color['bg']}; color: {$color['text']}; padding: 3px 8px; border-radius: 12px; font-size: 0.7rem; font-weight: bold;'>
                    {$g['type']}
                </span>
                <a href='index.php?action=delete-goal&id={$g['id']}' onclick=\"return confirm('Sếp có chắc muốn xóa mục tiêu này? Các công việc bên trong sẽ không bị xóa mà chỉ mất liên kết mục tiêu.')\" style='color: #eb3639; font-size: 0.95rem; text-decoration: none;' title='Xóa mục tiêu'>
                    <i class='fas fa-trash-alt'></i>
                </a>
            </div>
            </div>
        
        <div style='font-size: 0.8rem; color: #787774; margin-bottom: 15px;'>
            <i class='far fa-calendar-alt'></i> " . date('d/m/Y', strtotime($g['start_date'])) . " - " . date('d/m/Y', strtotime($g['end_date'])) . "
        </div>

        <div style='margin-bottom: 8px; display: flex; justify-content: space-between; font-size: 0.85rem; font-weight: 600;'>
            <span style='color: {$progressBarColor};'>Tiến độ</span>
            <span style='color: {$progressBarColor};'>{$g['percent']}%</span>
        </div>
        <div style='background: #e3e2e0; height: 10px; border-radius: 10px; overflow: hidden; margin-bottom: 10px;'>
            <div style='background: {$progressBarColor}; height: 100%; width: {$g['percent']}%; transition: width 0.5s ease;'></div>
        </div>
        
        <div style='font-size: 0.8rem; color: #787774; text-align: right;'>
            Hoàn thành: <strong>{$g['completed_tasks']}/{$g['total_tasks']}</strong> công việc
        </div>
    </div>";
}
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h1 style="font-size: 1.8rem; margin: 0;">Mục tiêu của tôi</h1>
    <button onclick="document.getElementById('goalModal').style.display='block'" style="background: #2383e2; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-weight: 500;">
        + Thêm Mục tiêu
    </button>
</div>

<h2 style="font-size: 1.2rem; color: #37352f; margin-top: 30px; margin-bottom: 15px;"><i class="fas fa-spinner fa-spin" style="margin-right: 8px; color: #2383e2;"></i>Đang thực hiện</h2>
<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
    <?php 
    if(empty($activeGoals)) {
        echo "<p style='color: #787774;'>Không có mục tiêu nào đang thực hiện.</p>";
    } else {
        foreach($activeGoals as $g) renderGoalCard($g, $typeColors, false);
    }
    ?>
</div>

<?php if(!empty($completedGoals)): ?>
    <h2 style="font-size: 1.2rem; color: #0f7b6c; margin-top: 40px; margin-bottom: 15px; padding-top: 20px; border-top: 1px solid #eee;"><i class="fas fa-trophy" style="margin-right: 8px;"></i>Đã hoàn thành</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
        <?php foreach($completedGoals as $g) renderGoalCard($g, $typeColors, true); ?>
    </div>
<?php endif; ?>

<div id="goalModal" style="display:none; position:fixed; z-index:100; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.4);">
    <div style="background:white; margin:10% auto; padding:30px; width:400px; border-radius:8px;">
        <h2 style="margin-top:0;">Thiết lập mục tiêu mới</h2>
        <form action="index.php?action=add-goal" method="POST">
            <div class="form-group" style="margin-bottom: 15px;">
                <label>Tên mục tiêu</label>
                <input type="text" name="title" class="form-control" placeholder="VD: Hoàn thành Đồ án PHP..." required style="width: 100%; padding: 8px; box-sizing: border-box;">
            </div>
            <div class="form-group" style="margin-bottom: 15px;">
                <label>Loại mục tiêu</label>
                <select name="type" class="form-control" style="width: 100%; padding: 8px; box-sizing: border-box;">
                    <option value="Tuần">Mục tiêu Tuần</option>
                    <option value="Tháng">Mục tiêu Tháng</option>
                    <option value="Quý">Mục tiêu Quý</option>
                </select>
            </div>
            <div style="display:flex; gap:10px; margin-bottom: 15px;">
                <div style="flex:1;">
                    <label>Từ ngày</label>
                    <input type="date" name="start_date" required class="form-control" style="width: 100%; padding: 8px; box-sizing: border-box;">
                </div>
                <div style="flex:1;">
                    <label>Đến ngày</label>
                    <input type="date" name="end_date" required class="form-control" style="width: 100%; padding: 8px; box-sizing: border-box;">
                </div>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
                <button type="button" onclick="document.getElementById('goalModal').style.display='none'" style="background:none; border:1px solid #ccc; padding:8px 15px; cursor:pointer; border-radius:4px;">Hủy</button>
                <button type="submit" style="background:#2383e2; color:white; border:none; padding:8px 15px; cursor:pointer; border-radius:4px;">Lưu mục tiêu</button>
            </div>
        </form>
    </div>
</div>

<?php require_once PROJECT_ROOT . '/views/layout/footer.php'; ?>