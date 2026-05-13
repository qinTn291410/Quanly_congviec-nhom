<?php
namespace Tinhu\TaskManager\Controllers;
use Tinhu\TaskManager\Models\TaskModel;
use Tinhu\TaskManager\Core\MailHelper;

class TaskController {
    private $taskModel;
    public function __construct() {
        $this->taskModel = new TaskModel();
    }

    public function index() {
        $userId = $_SESSION['user_id'];
        
        // --- LOGIC NHẮC VIỆC TỰ ĐỘNG ---
        $upcomingTasks = $this->taskModel->getTasksForReminder($userId);
    if (!empty($upcomingTasks)) {
    
    $userEmail = $_SESSION['email']; 
    
    if ($userEmail) { // Kiểm tra nếu có email thì mới gửi
        $count = 0;
        foreach ($upcomingTasks as $t) {
            $msg = "<h3>Hạn chót công việc: " . htmlspecialchars($t['title']) . "</h3>"
                . "<p>Hạn cuối là ngày " . date('d/m/Y', strtotime($t['due_date'])) . ". Bạn vào xử lý ngay nhé!</p>";
            
            if (MailHelper::sendMail($userEmail, "Nhắc nhở Deadline", $msg)) {
                $this->taskModel->markAsReminded($t['id']);
                $count++;
            }
        }
        if ($count > 0) {
            $_SESSION['system_alert'] = "Đã gửi $count mail nhắc việc vào địa chỉ: " . $userEmail;
        }
    }
}

        $search = $_GET['search'] ?? '';
        $cat = $_GET['category'] ?? '';
        $pri = $_GET['priority'] ?? '';
        
        $todoTasks    = $this->taskModel->getTasksByStatus($userId, 'To-do', $search, $cat, $pri);
        $doingTasks   = $this->taskModel->getTasksByStatus($userId, 'Doing', $search, $cat, $pri);
        $doneTasks    = $this->taskModel->getTasksByStatus($userId, 'Done', $search, $cat, $pri);
        $pendingTasks = $this->taskModel->getTasksByStatus($userId, 'Pending', $search, $cat, $pri); 

        require_once PROJECT_ROOT . '/src/Models/GoalModel.php';
        $userGoals = (new \Tinhu\TaskManager\Models\GoalModel())->getGoalsWithProgress($userId);

        require_once PROJECT_ROOT . '/views/tasks/index.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $start_date = !empty($_POST['start_date']) ? $_POST['start_date'] : date('Y-m-d');
            $due_date   = !empty($_POST['due_date'])   ? $_POST['due_date']   : date('Y-m-d');

            $data = [
                'user_id'     => $_SESSION['user_id'],
                'title'       => $_POST['title'],
                'description' => $_POST['description'],
                'start_date'  => $start_date,
                'due_date'    => $due_date,
                'priority'    => $_POST['priority'],
                'status'      => 'To-do',
                'category'    => $_POST['category'] ?? 'Khác',
                'goal_id'     => !empty($_POST['goal_id']) ? $_POST['goal_id'] : null
            ];

            if ($this->taskModel->createTask($data)) {
                header('Location: index.php?action=tasks');
                exit();
            }
        }
    }

    public function update() {
        $id = $_GET['id'] ?? 0;
        $status = $_GET['status'] ?? 'To-do';
        if ($id) {
            $this->taskModel->updateStatus($id, $status);
        }
        header('Location: index.php?action=tasks');
        exit();
    }

    public function edit() {
        $id = $_GET['id'] ?? 0;
        $userId = $_SESSION['user_id']; 

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'title'       => $_POST['title'],
                'description' => $_POST['description'],
                'start_date'  => $_POST['start_date'],
                'due_date'    => $_POST['due_date'],
                'priority'    => $_POST['priority'],
                'category'    => $_POST['category'],
                'goal_id'     => !empty($_POST['goal_id']) ? $_POST['goal_id'] : null 
            ];
            $this->taskModel->updateTask($id, $data);
            header('Location: index.php?action=tasks');
            exit();
        }
        
        $task = $this->taskModel->getTaskById($id);
        
        require_once PROJECT_ROOT . '/src/Models/GoalModel.php';
        $userGoals = (new \Tinhu\TaskManager\Models\GoalModel())->getGoalsWithProgress($userId);

        require_once PROJECT_ROOT . '/views/tasks/edit.php';
    }

    public function delete() {
    $id = $_GET['id'] ?? 0;
    if ($id) {
        $this->taskModel->deleteTask($id);
    }
    header('Location: index.php?action=tasks');
    exit();
    }

    public function calendar() {
        $userId = $_SESSION['user_id'];
        $tasks = $this->taskModel->getAllTasks($userId);
        
        $events = [];
        foreach ($tasks as $t) {
            // 1. CHO VIỆC DONE "CÚT"
            if ($t['status'] == 'Done') {
                continue; 
            }

            // Mặc định là màu xanh dương nhạt (To-do)
            $bg = '#e8f3fb'; 
            $text = '#0b6e99'; 

            // LỖI 1 ĐÃ FIX: Đổi dấu < thành <= để đồng bộ chuẩn "Quá hạn" với Kanban
            $isOverdue = (strtotime($t['due_date']) <= strtotime(date('Y-m-d'))) && !empty($t['due_date']) && $t['due_date'] != '0000-00-00';

            if ($isOverdue) {
                $bg = '#fde8e8'; $text = '#eb3639'; // Đỏ nhạt (Quá hạn) 
            } elseif ($t['status'] == 'Doing') {
                $bg = '#fdf3c0'; $text = '#d9730d'; // Vàng cam nhạt (Đang tiến hành)
            } elseif ($t['status'] == 'Pending') {
                // LỖI 2 ĐÃ FIX: Thêm quy định màu Cam Đất cho các task Pending
                $bg = '#fdecc8'; $text = '#ad7f11'; 
            }

            // Xử lý Lịch: Ngày kết thúc phải cộng thêm 1 ngày
            $endDate = $t['due_date'];
            if ($endDate && $endDate != '0000-00-00') {
                $endDate = date('Y-m-d', strtotime($endDate . ' +1 day'));
            } else {
                $endDate = $t['start_date'];
            }

            $events[] = [
                'id' => $t['id'],
                'title' => $t['title'],
                'start' => $t['start_date'],
                'end' => $endDate,
                'backgroundColor' => $bg,   
                'borderColor' => $bg,       
                'textColor' => $text,       
                'url' => 'index.php?action=edit-task&id=' . $t['id']
            ];
        }
        $eventsJson = json_encode($events);
        
        require_once PROJECT_ROOT . '/views/tasks/calendar.php';
    }
}