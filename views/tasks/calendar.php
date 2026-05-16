<?php require_once PROJECT_ROOT . '/views/layout/header.php'; ?>

<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/vi.js'></script> <div style="background: white; padding: 25px; border-radius: 10px; border: 1px solid #e3e2e0; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1 style="font-size: 1.8rem; margin: 0; color: #37352f;">Lịch làm việc</h1>
        <a href="index.php?action=tasks" style="background: #2383e2; color: white; padding: 8px 15px; border-radius: 4px; text-decoration: none; font-weight: 500;">
            Quản lý Kanban &rarr;
        </a>
    </div>
    <div style="background: white; padding: 15px 25px; border-radius: 10px; border: 1px solid #e3e2e0; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 style="margin: 0; font-size: 1.5rem; color: #37352f;"><i class="far fa-calendar-alt" style="color: #2383e2;"></i> Lịch Tổng Hợp</h1>
        <p style="margin: 5px 0 0 0; font-size: 0.9rem; color: #787774;">Theo dõi thời gian của cả việc cá nhân lẫn dự án nhóm.</p>
    </div>
    
    <form action="index.php" method="GET" style="display: flex; gap: 15px; margin: 0;">
        <input type="hidden" name="action" value="calendar">
        <select name="filter_type" onchange="this.form.submit()" style="padding: 8px 15px; border-radius: 6px; border: 1px solid #ccc; font-weight: 500;">
            <option value="all" <?= (isset($_GET['filter_type']) && $_GET['filter_type'] == 'all') ? 'selected' : '' ?>>Hiển thị tất cả</option>
            <option value="personal" <?= (isset($_GET['filter_type']) && $_GET['filter_type'] == 'personal') ? 'selected' : '' ?>>Chỉ việc cá nhân</option>
            <option value="team" <?= (isset($_GET['filter_type']) && $_GET['filter_type'] == 'team') ? 'selected' : '' ?>>Chỉ việc nhóm</option>
        </select>
    </form>
</div>
    <div id='calendar'></div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      locale: 'vi',
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
      },
      buttonText: {
        today: 'Hôm nay', month: 'Tháng', week: 'Tuần', day: 'Ngày', list: 'Danh sách'
      },
      events: <?= $eventsJson ?>
    });
    calendar.render();
  });
</script>

<style>
    .fc-toolbar-title { font-size: 1.5rem !important; color: #37352f; text-transform: capitalize; }
    .fc-button-primary { background-color: white !important; color: #37352f !important; border-color: #e3e2e0 !important; box-shadow: none !important; }
    .fc-button-primary:hover { background-color: #f7f7f5 !important; }
    .fc-button-active { background-color: #e3e2e0 !important; }
    .fc-event { cursor: pointer; border: none; padding: 2px 4px; border-radius: 4px; font-weight: 500;}
    .fc-daygrid-day-number { color: #37352f; text-decoration: none; }
    .fc-col-header-cell-cushion { color: #787774; text-decoration: none; }
</style>

<?php require_once PROJECT_ROOT . '/views/layout/footer.php'; ?>