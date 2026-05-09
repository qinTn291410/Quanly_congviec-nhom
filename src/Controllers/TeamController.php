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
}