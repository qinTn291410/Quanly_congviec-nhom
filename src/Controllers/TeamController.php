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
        // ------------------------------------------------

        $backlogTasks = $this->teamModel->getTeamTasksByStatus($projectId, 'Backlog');
        $inProgressTasks = $this->teamModel->getTeamTasksByStatus($projectId, 'In Progress');
        $reviewTasks = $this->teamModel->getTeamTasksByStatus($projectId, 'Review');
        $doneTasks = $this->teamModel->getTeamTasksByStatus($projectId, 'Done');

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
}