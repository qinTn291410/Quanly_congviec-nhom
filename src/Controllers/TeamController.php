<?php
namespace Tinhu\TaskManager\Controllers;

require_once PROJECT_ROOT . '/src/Models/TeamModel.php';
use Tinhu\TaskManager\Models\TeamModel;

class TeamController {
    private $teamModel;

    public function __construct() {
        $this->teamModel = new TeamModel();
    }

    // Đổ dữ liệu ra trang Quản lý nhóm
    public function index() {
        $userId = $_SESSION['user_id'];
        $teams = $this->teamModel->getTeamsByUserId($userId);
        require_once PROJECT_ROOT . '/views/teams/index.php';
    }

    // Xử lý form Tạo nhóm mới
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user_id'];
            $name = $_POST['name'] ?? '';
            $desc = $_POST['description'] ?? '';

            if (!empty($name)) {
                $this->teamModel->createTeam($name, $desc, $userId);
            }
            header('Location: index.php?action=teams');
            exit();
        }
    }

    public function detail() {
        $teamId = $_GET['id'] ?? 0;
        $team = $this->teamModel->getTeamById($teamId);
        
        if (!$team) {
            echo "Nhóm không tồn tại!"; 
            exit();
        }

        $members = $this->teamModel->getTeamMembers($teamId);
        $projects = $this->teamModel->getProjectsByTeam($teamId);
        
        // Tính % hoàn thành cho mỗi dự án để hiển thị bên ngoài
        foreach ($projects as &$p) {
            $stats = $this->teamModel->getProjectStats($p['id']);
            $total = 0;
            $done = 0;
            foreach ($stats as $s) {
                $total += $s['count'];
                if ($s['status'] == 'Done') {
                    $done = $s['count'];
                }
            }
            $p['percent'] = ($total > 0) ? round(($done / $total) * 100) : 0;
        }
        unset($p); // Xóa tham chiếu
        
        require_once PROJECT_ROOT . '/views/teams/detail.php';
    }

    public function invite() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $teamId = $_POST['team_id'];
            $email = trim($_POST['email']);
            
            $result = $this->teamModel->inviteMember($teamId, $email);
            if ($result === true) {
                $_SESSION['system_alert'] = "Đã thêm thành viên thành công!";
            } else {
                $_SESSION['system_alert'] = $result; 
            }
            header('Location: index.php?action=team-detail&id=' . $teamId);
            exit();
        }
    }

    // Xử lý nút Tạo Dự án
    public function createProject() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $teamId = $_POST['team_id'];
            $name = $_POST['name'];
            $desc = $_POST['description'];
            $startDate = $_POST['start_date'];
            $endDate = $_POST['end_date'];
            $managerId = $_POST['manager_id']; 

            $this->teamModel->createProject($teamId, $name, $desc, $startDate, $endDate, $managerId);
            $_SESSION['system_alert'] = "Tạo dự án thành công!";
            
            header('Location: index.php?action=team-detail&id=' . $teamId);
            exit();
        }
    }

    // Mở bảng Kanban của Dự án
    public function projectKanban() {
        $projectId = $_GET['id'] ?? 0;
        $project = $this->teamModel->getProjectById($projectId);
        
        if (!$project) { echo "Dự án không tồn tại!"; exit(); }

        $members = $this->teamModel->getTeamMembers($project['team_id']);

        $userId = $_SESSION['user_id'];
        $currentUserRole = 'Member';
        foreach ($members as $m) {
            if ($m['id'] == $userId) {
                $currentUserRole = $m['role'];
                break;
            }
        }
        $canEdit = ($currentUserRole == 'Leader' || $project['manager_id'] == $userId);
        $filterAssignee = $_GET['assignee'] ?? '';
        $sort = $_GET['sort'] ?? '';

        $backlogTasks = $this->teamModel->getTeamTasksByStatus($projectId, 'Backlog', $filterAssignee, $sort);
        $inProgressTasks = $this->teamModel->getTeamTasksByStatus($projectId, 'In Progress', $filterAssignee, $sort);
        $reviewTasks = $this->teamModel->getTeamTasksByStatus($projectId, 'Review', $filterAssignee, $sort);
        $doneTasks = $this->teamModel->getTeamTasksByStatus($projectId, 'Done', $filterAssignee, $sort);

        //LẤY DỮ LIỆU CHO BIỂU ĐỒ
        $stats = $this->teamModel->getProjectStats($projectId);
        $chartData = ['Backlog' => 0, 'In Progress' => 0, 'Review' => 0, 'Done' => 0];
        
        foreach ($stats as $s) {
            if (array_key_exists($s['status'], $chartData)) {
                $chartData[$s['status']] = $s['count'];
            }
        }
        
        $totalTasks = array_sum($chartData);
        $percentDone = ($totalTasks > 0) ? round(($chartData['Done'] / $totalTasks) * 100) : 0;
        $deadlineTasks = $this->teamModel->getProjectDeadlineTasks($projectId);
        $memberStats = $this->teamModel->getProjectMemberStats($projectId);
        
        require_once PROJECT_ROOT . '/views/teams/kanban.php';
    }

    public function addTeamTask() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $projectId = $_POST['project_id'];
            $data = [
                'project_id'  => $projectId,
                'assigned_to' => $_POST['assigned_to'] ?: null, // Nếu không chọn thì để null
                'title'       => $_POST['title'],
                'description' => $_POST['description'],
                'priority'    => $_POST['priority'],
                'due_date'    => !empty($_POST['due_date']) ? $_POST['due_date'] : null
            ];
            $this->teamModel->createTeamTask($data);
            header("Location: index.php?action=project-kanban&id=" . $projectId);
            exit();
        }
    }

    public function updateTeamTask() {
        $taskId = $_GET['task_id'] ?? 0;
        $projectId = $_GET['project_id'] ?? 0;
        $status = $_GET['status'] ?? 'Backlog';
        
        if ($taskId && $projectId) {
            $this->teamModel->updateTeamTaskStatus($taskId, $status);
        }
        header("Location: index.php?action=project-kanban&id=" . $projectId);
        exit();
    }

    // Hiển thị form sửa và nhận dữ liệu lưu lại
    public function editTeamTask() {
        $taskId = $_GET['id'] ?? 0;
        $projectId = $_GET['project_id'] ?? 0;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'title'       => $_POST['title'],
                'description' => $_POST['description'],
                'assigned_to' => $_POST['assigned_to'] ?: null,
                'priority'    => $_POST['priority'],
                'due_date'    => $_POST['due_date']
            ];
            $this->teamModel->updateTeamTask($taskId, $data);
            header("Location: index.php?action=project-kanban&id=" . $projectId);
            exit();
        }

        $task = $this->teamModel->getTeamTaskById($taskId);
        $project = $this->teamModel->getProjectById($projectId);
        $members = $this->teamModel->getTeamMembers($project['team_id']);
        require_once PROJECT_ROOT . '/views/teams/edit_task.php';
    }

    // Thực hiện xóa task
    public function deleteTeamTask() {
        $taskId = $_GET['id'] ?? 0;
        $projectId = $_GET['project_id'] ?? 0;
        if ($taskId) {
            $this->teamModel->deleteTeamTask($taskId);
        }
        header("Location: index.php?action=project-kanban&id=" . $projectId);
        exit();
    }

    // Mở trang Chi tiết Task (Xem bình luận)
    public function taskDetail() {
        $taskId = $_GET['task_id'] ?? 0;
        $projectId = $_GET['project_id'] ?? 0;
        
        $task = $this->teamModel->getTeamTaskById($taskId);
        $project = $this->teamModel->getProjectById($projectId);
        
        if (!$task) { echo "Công việc không tồn tại!"; exit(); }

        $comments = $this->teamModel->getTaskComments($taskId);
        require_once PROJECT_ROOT . '/views/teams/task_detail.php';
    }

    // Xử lý gửi bình luận & Upload file đính kèm
    public function addComment() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $taskId = $_POST['task_id'];
            $projectId = $_POST['project_id'];
            $userId = $_SESSION['user_id'];
            $content = trim($_POST['content']);
            $fileUrl = null;

            if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === 0) {
                // Tạo thư mục nếu chưa có
                $uploadDir = PROJECT_ROOT . '/public/uploads/teams/';
                if (!is_dir($uploadDir)) { mkdir($uploadDir, 0777, true); }
                
                $fileName = time() . '_' . basename($_FILES['attachment']['name']);
                if (move_uploaded_file($_FILES['attachment']['tmp_name'], $uploadDir . $fileName)) {
                    $fileUrl = $fileName;
                }
            }

            if (!empty($content) || $fileUrl) {
                $this->teamModel->addTaskComment($taskId, $userId, $content, $fileUrl);
            }
            
            header("Location: index.php?action=team-task-detail&task_id=$taskId&project_id=$projectId");
            exit();
        }
    }

    // Kick member ra khỏi nhóm
    public function kickMember() {
        $teamId = $_GET['team_id'] ?? 0;
        $userId = $_GET['user_id'] ?? 0;
        if ($teamId && $userId && $userId != $_SESSION['user_id']) {
            $this->teamModel->removeTeamMember($teamId, $userId);
        }
        header("Location: index.php?action=team-detail&id=" . $teamId);
        exit();
    }

    public function removeProject() {
        $projectId = $_GET['project_id'] ?? 0;
        $teamId = $_GET['team_id'] ?? 0;
        if ($projectId) {
            $this->teamModel->deleteProject($projectId);
        }
        header("Location: index.php?action=team-detail&id=" . $teamId);
        exit();
    }

    public function removeTeam() {
        $teamId = $_GET['id'] ?? 0;
        if ($teamId) {
            $this->teamModel->deleteTeam($teamId);
        }
        header("Location: index.php?action=teams");
        exit();
    }
}