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
        
        // LOGIC KIỂM TRA CÔNG TẮC THÔNG BÁO 
        $db = \Tinhu\TaskManager\Core\Database::getInstance()->getConnection();
        try {
            $stmt = $db->prepare("SELECT notify_deadline FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $userSettings = $stmt->fetch(\PDO::FETCH_ASSOC);
            $canNotify = ($userSettings && $userSettings['notify_deadline'] == 1);
        } catch (\Exception $e) {
            $canNotify = true;
        }

        // Chỉ gửi mail nhắc nhở nếu người dùng BẬT thông báo
        if ($canNotify) {
            $upcomingTasks = $this->taskModel->getTasksForReminder($userId);
            if (!empty($upcomingTasks)) {
                $userEmail = $_SESSION['email']; 
                if ($userEmail) {
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
        }

        $search = $_GET['search'] ?? '';
        $cat = $_GET['category'] ?? '';
        $pri = $_GET['priority'] ?? '';
        
        $todoTasks    = $this->taskModel->getTasksByStatus($userId, 'To-do', $search, $cat, $pri);
        $doingTasks   = $this->taskModel->getTasksByStatus($userId, 'Doing', $search, $cat, $pri);
        $doneTasks    = $this->taskModel->getTasksByStatus($userId, 'Done', $search, $cat, $pri);
        $pendingTasks = $this->taskModel->getTasksByStatus($userId, 'Pending', $search, $cat, $pri);
        
        $chartData = [
            'To-do' => count($todoTasks),
            'Doing' => count($doingTasks),
            'Pending' => count($pendingTasks),
            'Done' => count($doneTasks)
        ];
        $totalPersonalTasks = array_sum($chartData);
        $percentPersonalDone = ($totalPersonalTasks > 0) ? round(($chartData['Done'] / $totalPersonalTasks) * 100) : 0;

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
        $db = \Tinhu\TaskManager\Core\Database::getInstance()->getConnection();
        
        $filterType = $_GET['filter_type'] ?? 'all'; 

        $events = [];

        // Kéo việc cá nhân
        if ($filterType === 'all' || $filterType === 'personal') {
            $stmt = $db->prepare("SELECT id, title, start_date, due_date, status FROM tasks WHERE user_id = ? AND status != 'Done'");
            $stmt->execute([$userId]);
            $pTasks = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            foreach ($pTasks as $t) {
                $bg = '#fdfdfc'; $text = '#787774'; 
                if ($t['status'] == 'To-do') { $bg = '#e8f3fb'; $text = '#0b6e99'; }
                elseif ($t['status'] == 'Doing') { $bg = '#fdf3c0'; $text = '#d9730d'; }
                elseif ($t['status'] == 'Pending') { $bg = '#fdecc8'; $text = '#ad7f11'; }

                $isOverdue = (strtotime($t['due_date']) < strtotime(date('Y-m-d'))) && !empty($t['due_date']) && $t['due_date'] != '0000-00-00';
                if ($isOverdue) { $bg = '#fde8e8'; $text = '#eb3639'; }

                $endDate = (!empty($t['due_date']) && $t['due_date'] != '0000-00-00') ? date('Y-m-d', strtotime($t['due_date'] . ' +1 day')) : $t['start_date'];

                $events[] = [
                    'id' => 'p_'.$t['id'],
                    'title' => '[Cá nhân] ' . $t['title'],
                    'start' => $t['start_date'] ?: date('Y-m-d'),
                    'end' => $endDate,
                    'backgroundColor' => $bg,
                    'borderColor' => $bg,
                    'textColor' => $text,
                    'url' => 'index.php?action=edit-task&id=' . $t['id']
                ];
            }
        }

        // Kéo việc nhóm
        if ($filterType === 'all' || $filterType === 'team') {
            $stmt = $db->prepare("SELECT tt.id, tt.title, tt.due_date, tt.status, tt.project_id, p.name as project_name 
                                FROM team_tasks tt 
                                JOIN projects p ON tt.project_id = p.id 
                                WHERE tt.assigned_to = ? AND tt.status != 'Done'");
            $stmt->execute([$userId]);
            $tTasks = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            foreach ($tTasks as $t) {
                $bg = '#f4e0f9'; $text = '#8f24b2'; 
                if ($t['status'] == 'Review') { $bg = '#fdecc8'; $text = '#ad7f11'; }

                $isOverdue = (strtotime($t['due_date']) < strtotime(date('Y-m-d'))) && !empty($t['due_date']) && $t['due_date'] != '0000-00-00';
                if ($isOverdue) { $bg = '#fde8e8'; $text = '#eb3639'; } 

                $endDate = (!empty($t['due_date']) && $t['due_date'] != '0000-00-00') ? date('Y-m-d', strtotime($t['due_date'] . ' +1 day')) : null;
                $startDate = (!empty($t['due_date']) && $t['due_date'] != '0000-00-00') ? $t['due_date'] : date('Y-m-d');

                $events[] = [
                    'id' => 't_'.$t['id'],
                    'title' => '[' . htmlspecialchars($t['project_name']) . '] ' . $t['title'],
                    'start' => $startDate,
                    'end' => $endDate,
                    'backgroundColor' => $bg,
                    'borderColor' => $bg,
                    'textColor' => $text,
                    'url' => 'index.php?action=team-task-detail&task_id=' . $t['id'] . '&project_id=' . $t['project_id']
                ];
            }
        }

        $eventsJson = json_encode($events);
        require_once PROJECT_ROOT . '/views/tasks/calendar.php';
    }
}