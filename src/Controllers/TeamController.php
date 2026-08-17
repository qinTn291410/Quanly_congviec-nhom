<?php
namespace Tinhu\TaskManager\Controllers;

require_once PROJECT_ROOT . '/src/Models/TeamModel.php';
use Tinhu\TaskManager\Models\TeamModel;
use Tinhu\TaskManager\Core\Database;

class TeamController {
    private $teamModel;

    public function __construct() {
        $this->teamModel = new TeamModel();
    }

    private function getUserRole($teamId) {
        if (!$teamId) return 'Viewer';
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT role FROM team_members WHERE team_id = ? AND user_id = ?");
        $stmt->execute([$teamId, $_SESSION['user_id']]);
        return $stmt->fetchColumn() ?: 'Viewer';
    }

    private function getTeamIdByProject($projectId) {
        if (!$projectId) return 0;
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT team_id FROM projects WHERE id = ?");
        $stmt->execute([$projectId]);
        return $stmt->fetchColumn();
    }

    private function logActivity($projectId, $action) {
        $db = Database::getInstance()->getConnection();
        $userId = $_SESSION['user_id'];
        $stmt = $db->prepare("INSERT INTO activity_logs (project_id, user_id, action) VALUES (?, ?, ?)");
        $stmt->execute([$projectId, $userId, $action]);
    }

    private function handleMentions($content, $teamId, $locationText) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT u.id, u.fullname FROM users u JOIN team_members tm ON u.id = tm.user_id WHERE tm.team_id = ?");
        $stmt->execute([$teamId]);
        $members = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($members as $m) {
            $mentionStr = '@' . $m['fullname'];
            if (strpos($content, $mentionStr) !== false && $m['id'] != $_SESSION['user_id']) {
                $msg = "TAG: " . $_SESSION['fullname'] . " vừa nhắc đến bạn $locationText. Vào xem ngay!";
                $db->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)")->execute([$m['id'], $msg]);
            }
        }
    }

    public function index() {
        $userId = $_SESSION['user_id'];
        $teams = $this->teamModel->getTeamsByUserId($userId);
        require_once PROJECT_ROOT . '/views/teams/index.php';
    }

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
        if (!$team) { echo "Nhóm không tồn tại!"; exit(); }
        $members = $this->teamModel->getTeamMembers($teamId);
        $projects = $this->teamModel->getProjectsByTeam($teamId);
        $teamComments = $this->teamModel->getTeamComments($teamId);
        foreach ($projects as &$p) {
            $stats = $this->teamModel->getProjectStats($p['id']);
            $total = 0; $done = 0;
            foreach ($stats as $s) {
                $total += $s['count'];
                if ($s['status'] == 'Done') $done = $s['count'];
            }
            $p['percent'] = ($total > 0) ? round(($done / $total) * 100) : 0;
        }
        unset($p);
        require_once PROJECT_ROOT . '/views/teams/detail.php';
    }

    public function invite() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $teamId = $_POST['team_id'];
            $email = trim($_POST['email']);
            $roleToAssign = $_POST['role'] ?? 'Member';

            $myRole = $this->getUserRole($teamId);
            if (!in_array($myRole, ['Leader', 'Manager'])) {
                $_SESSION['system_alert'] = "Lỗi bảo mật: Bạn không có quyền mời thành viên!";
                header('Location: index.php?action=team-detail&id=' . $teamId); exit();
            }
            if ($myRole === 'Manager' && in_array($roleToAssign, ['Leader', 'Manager'])) {
                $_SESSION['system_alert'] = "Lỗi bảo mật: Manager chỉ có thể mời Member hoặc Viewer!";
                header('Location: index.php?action=team-detail&id=' . $teamId); exit();
            }

            $result = $this->teamModel->inviteMember($teamId, $email);
            if ($result === true) {
                $db = Database::getInstance()->getConnection();
                $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$email]);
                $invitedUserId = $stmt->fetchColumn();
                
                if ($invitedUserId) {
                    $stmtUpdate = $db->prepare("UPDATE team_members SET role = ? WHERE team_id = ? AND user_id = ?");
                    $stmtUpdate->execute([$roleToAssign, $teamId, $invitedUserId]);
                }
                $_SESSION['system_alert'] = "Đã thêm thành viên và cấp quyền $roleToAssign thành công!";
            } else {
                $_SESSION['system_alert'] = $result; 
            }
            header('Location: index.php?action=team-detail&id=' . $teamId);
            exit();
        }
    }

    public function createProject() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $teamId = $_POST['team_id'];
            $myRole = $this->getUserRole($teamId);
            if (!in_array($myRole, ['Leader', 'Manager'])) {
                $_SESSION['system_alert'] = "Lỗi bảo mật: Chỉ Leader/Manager mới được tạo dự án!";
                header('Location: index.php?action=team-detail&id=' . $teamId); exit();
            }

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
        $canEdit = ($currentUserRole == 'Leader' || $currentUserRole == 'Manager' || $project['manager_id'] == $userId);
        
        $filterAssignee = $_GET['assignee'] ?? '';
        $sort = $_GET['sort'] ?? '';

        $backlogTasks = $this->teamModel->getTeamTasksByStatus($projectId, 'Backlog', $filterAssignee, $sort);
        $inProgressTasks = $this->teamModel->getTeamTasksByStatus($projectId, 'In Progress', $filterAssignee, $sort);
        $reviewTasks = $this->teamModel->getTeamTasksByStatus($projectId, 'Review', $filterAssignee, $sort);
        $doneTasks = $this->teamModel->getTeamTasksByStatus($projectId, 'Done', $filterAssignee, $sort);

        $stats = $this->teamModel->getProjectStats($projectId);
        $chartData = ['Backlog' => 0, 'In Progress' => 0, 'Review' => 0, 'Done' => 0];
        foreach ($stats as $s) {
            if (array_key_exists($s['status'], $chartData)) $chartData[$s['status']] = $s['count'];
        }
        $totalTasks = array_sum($chartData);
        $percentDone = ($totalTasks > 0) ? round(($chartData['Done'] / $totalTasks) * 100) : 0;
        $deadlineTasks = $this->teamModel->getProjectDeadlineTasks($projectId);
        $memberStats = $this->teamModel->getProjectMemberStats($projectId);
        $projectComments = $this->teamModel->getProjectComments($projectId);

        $logs = [];
        if (isset($_GET['view']) && $_GET['view'] == 'log') {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT a.*, u.fullname FROM activity_logs a JOIN users u ON a.user_id = u.id WHERE a.project_id = ? ORDER BY a.created_at DESC");
            $stmt->execute([$projectId]);
            $logs = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        require_once PROJECT_ROOT . '/views/teams/kanban.php';
    }

    public function addTeamTask() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $projectId = $_POST['project_id'];
            $teamId = $this->getTeamIdByProject($projectId);
            
            if ($this->getUserRole($teamId) === 'Viewer') die("Lỗi bảo mật: Viewer không có quyền!");

            $data = [
                'project_id'  => $projectId,
                'assigned_to' => $_POST['assigned_to'] ?: null, 
                'title'       => $_POST['title'],
                'description' => $_POST['description'],
                'priority'    => $_POST['priority'],
                'due_date'    => !empty($_POST['due_date']) ? $_POST['due_date'] : null
            ];
            $this->teamModel->createTeamTask($data);
            
            $this->logActivity($projectId, "đã thêm một công việc mới: <b>" . htmlspecialchars($data['title']) . "</b>");

            $_SESSION['system_alert'] = "Đã phân công việc thành công!";
            header("Location: index.php?action=project-kanban&id=" . $projectId);
            exit();
        }
    }

    public function updateTeamTask() {
        $taskId = $_GET['task_id'] ?? 0;
        $projectId = $_GET['project_id'] ?? 0;
        $status = $_GET['status'] ?? 'Backlog';
        $teamId = $this->getTeamIdByProject($projectId);
        
        if ($this->getUserRole($teamId) === 'Viewer') die("Lỗi bảo mật!");

        if ($taskId && $projectId) {
            $taskInfo = $this->teamModel->getTeamTaskById($taskId);
            $this->teamModel->updateTeamTaskStatus($taskId, $status);
            
            // Ghi LOG
            $this->logActivity($projectId, "đã chuyển trạng thái công việc <b>" . htmlspecialchars($taskInfo['title']) . "</b> sang <b>" . strtoupper($status) . "</b>");

            $_SESSION['system_alert'] = "Đã cập nhật tiến độ sang: " . strtoupper($status);
        }
        header("Location: index.php?action=project-kanban&id=" . $projectId);
        exit();
    }

    public function editTeamTask() {
        $taskId = $_GET['id'] ?? 0;
        $projectId = $_GET['project_id'] ?? 0;
        $teamId = $this->getTeamIdByProject($projectId);
        
        if ($this->getUserRole($teamId) === 'Viewer') die("Lỗi bảo mật!");
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'title'       => $_POST['title'],
                'description' => $_POST['description'],
                'assigned_to' => $_POST['assigned_to'] ?: null,
                'priority'    => $_POST['priority'],
                'due_date'    => $_POST['due_date']
            ];
            $this->teamModel->updateTeamTask($taskId, $data);
            
            // Ghi LOG
            $this->logActivity($projectId, "đã chỉnh sửa nội dung công việc <b>" . htmlspecialchars($data['title']) . "</b>");

            header("Location: index.php?action=project-kanban&id=" . $projectId);
            exit();
        }
        $task = $this->teamModel->getTeamTaskById($taskId);
        $project = $this->teamModel->getProjectById($projectId);
        $members = $this->teamModel->getTeamMembers($project['team_id']);
        require_once PROJECT_ROOT . '/views/teams/edit_task.php';
    }

    public function deleteTeamTask() {
        $taskId = $_GET['id'] ?? 0;
        $projectId = $_GET['project_id'] ?? 0;
        $teamId = $this->getTeamIdByProject($projectId);
        
        if ($this->getUserRole($teamId) === 'Viewer') die("Lỗi bảo mật!");

        if ($taskId) {
            $taskInfo = $this->teamModel->getTeamTaskById($taskId);
            $this->teamModel->deleteTeamTask($taskId);
            
            // Ghi LOG
            $this->logActivity($projectId, "đã xóa công việc <b>" . htmlspecialchars($taskInfo['title']) . "</b> khỏi hệ thống");
        }
        header("Location: index.php?action=project-kanban&id=" . $projectId);
        exit();
    }

    public function taskDetail() {
        $taskId = $_GET['task_id'] ?? 0;
        $projectId = $_GET['project_id'] ?? 0;
        
        $task = $this->teamModel->getTeamTaskById($taskId);
        $project = $this->teamModel->getProjectById($projectId);
        if (!$task) { echo "Công việc không tồn tại!"; exit(); }

        $comments = $this->teamModel->getTaskComments($taskId);
        
        $members = $this->teamModel->getTeamMembers($project['team_id']);
        require_once PROJECT_ROOT . '/views/teams/task_detail.php';
    }

    public function addComment() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $taskId = $_POST['task_id'];
            $projectId = $_POST['project_id'];
            $teamId = $this->getTeamIdByProject($projectId);
            
            if ($this->getUserRole($teamId) === 'Viewer') die("Lỗi bảo mật: Cấm chat!");

            $userId = $_SESSION['user_id'];
            $content = trim($_POST['content']);
            $fileUrl = null;

            if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === 0) {
                $uploadDir = PROJECT_ROOT . '/public/uploads/teams/';
                if (!is_dir($uploadDir)) { mkdir($uploadDir, 0777, true); }
                $fileName = time() . '_' . basename($_FILES['attachment']['name']);
                if (move_uploaded_file($_FILES['attachment']['tmp_name'], $uploadDir . $fileName)) {
                    $fileUrl = $fileName;
                }
            }

            if (!empty($content) || $fileUrl) {
                $this->teamModel->addTaskComment($taskId, $userId, $content, $fileUrl);
                // GỌI HÀM QUÉT MENTION
                $this->handleMentions($content, $teamId, "bên trong một công việc");
            }
            header("Location: index.php?action=team-task-detail&task_id=$taskId&project_id=$projectId#chatBox");
            exit();
        }
    }

    public function addProjectComment() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $projectId = $_POST['project_id'];
            $teamId = $this->getTeamIdByProject($projectId);
            
            if ($this->getUserRole($teamId) === 'Viewer') die("Lỗi bảo mật!");

            $userId = $_SESSION['user_id'];
            $content = trim($_POST['content']);
            $fileUrl = null;

            if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === 0) {
                $uploadDir = PROJECT_ROOT . '/public/uploads/teams/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                $fileName = time() . '_' . basename($_FILES['attachment']['name']);
                if (move_uploaded_file($_FILES['attachment']['tmp_name'], $uploadDir . $fileName)) {
                    $fileUrl = $fileName;
                }
            }
            if (!empty($content) || $fileUrl) {
                $this->teamModel->addProjectComment($projectId, $userId, $content, $fileUrl);
                // GỌI HÀM QUÉT MENTION
                $this->handleMentions($content, $teamId, "trong kênh thảo luận dự án");
            }
            header("Location: index.php?action=project-kanban&id=$projectId&view=chat#chatBox");
            exit();
        }
    }

    public function addTeamMessage() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $teamId = $_POST['team_id'];
            if ($this->getUserRole($teamId) === 'Viewer') die("Lỗi bảo mật!");

            $userId = $_SESSION['user_id'];
            $content = trim($_POST['content']);
            $fileUrl = null;

            if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === 0) {
                $uploadDir = PROJECT_ROOT . '/public/uploads/teams/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                $fileName = time() . '_' . basename($_FILES['attachment']['name']);
                if (move_uploaded_file($_FILES['attachment']['tmp_name'], $uploadDir . $fileName)) {
                    $fileUrl = $fileName;
                }
            }
            if (!empty($content) || $fileUrl) {
                $this->teamModel->addTeamComment($teamId, $userId, $content, $fileUrl);
                // GỌI HÀM QUÉT MENTION
                $this->handleMentions($content, $teamId, "trong kênh chat chung của Nhóm");
            }
            header("Location: index.php?action=team-detail&id=$teamId#chatBox");
            exit();
        }
    }

    public function kickMember() {
        $teamId = $_GET['team_id'] ?? 0;
        $userId = $_GET['user_id'] ?? 0;
        if ($this->getUserRole($teamId) !== 'Leader') {
            $_SESSION['system_alert'] = "Lỗi bảo mật: Chỉ Leader mới có quyền đuổi thành viên!";
            header("Location: index.php?action=team-detail&id=" . $teamId); exit();
        }
        if ($teamId && $userId && $userId != $_SESSION['user_id']) {
            $this->teamModel->removeTeamMember($teamId, $userId);
        }
        header("Location: index.php?action=team-detail&id=" . $teamId); exit();
    }

    public function removeProject() {
        $projectId = $_GET['project_id'] ?? 0;
        $teamId = $_GET['team_id'] ?? 0;
        if ($this->getUserRole($teamId) !== 'Leader') {
            $_SESSION['system_alert'] = "Lỗi bảo mật: Chỉ Leader mới có quyền xóa dự án!";
            header("Location: index.php?action=team-detail&id=" . $teamId); exit();
        }
        if ($projectId) {
            $this->teamModel->deleteProject($projectId);
        }
        header("Location: index.php?action=team-detail&id=" . $teamId); exit();
    }

    public function removeTeam() {
        $teamId = $_GET['id'] ?? 0;
        if ($this->getUserRole($teamId) !== 'Leader') {
            $_SESSION['system_alert'] = "Lỗi bảo mật: Chỉ Leader mới có quyền xóa nhóm!";
            header("Location: index.php?action=teams"); exit();
        }
        if ($teamId) {
            $this->teamModel->deleteTeam($teamId);
        }
        header("Location: index.php?action=teams"); exit();
    }

    public function exportExcel() {
        $projectId = $_GET['project_id'] ?? 0;
        $db = Database::getInstance()->getConnection();
        $stmtProj = $db->prepare("SELECT name FROM projects WHERE id = ?");
        $stmtProj->execute([$projectId]);
        $projectName = $stmtProj->fetchColumn();

        $stmt = $db->prepare("SELECT t.title, u.fullname, t.status, t.priority, t.due_date 
                            FROM team_tasks t LEFT JOIN users u ON t.assigned_to = u.id 
                            WHERE t.project_id = ? ORDER BY t.status, t.due_date");
        $stmt->execute([$projectId]);
        $tasks = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $filename = "Bao_Cao_Tien_Do_" . preg_replace('/[^a-zA-Z0-9_ -]/s', '', $projectName) . ".csv";
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        fputcsv($output, ['Tên công việc', 'Người phụ trách', 'Trạng thái', 'Độ ưu tiên', 'Hạn chót']);
        foreach ($tasks as $row) {
            fputcsv($output, [
                $row['title'], $row['fullname'] ?: 'Chưa phân công', $row['status'], $row['priority'],
                (!empty($row['due_date']) && $row['due_date'] != '0000-00-00') ? date('d/m/Y', strtotime($row['due_date'])) : 'Không có'
            ]);
        }
        fclose($output); exit();
    }
}