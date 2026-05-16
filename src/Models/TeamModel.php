<?php
namespace Tinhu\TaskManager\Models;
require_once PROJECT_ROOT . '/src/Core/Database.php';
use Tinhu\TaskManager\Core\Database;

class TeamModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    // Lấy danh sách các nhóm mà user này đang tham gia
    public function getTeamsByUserId($userId) {
        $sql = "SELECT t.*, tm.role 
                FROM teams t 
                JOIN team_members tm ON t.id = tm.team_id 
                WHERE tm.user_id = :user_id 
                ORDER BY t.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Tạo nhóm mới và tự động gán quyền Leader
    public function createTeam($name, $description, $userId) {
        try {
            $this->conn->beginTransaction();
            $sqlTeam = "INSERT INTO teams (name, description, created_by) VALUES (:name, :description, :created_by)";
            $stmtTeam = $this->conn->prepare($sqlTeam);
            $stmtTeam->execute([
                'name' => $name,
                'description' => $description,
                'created_by' => $userId
            ]);
            $teamId = $this->conn->lastInsertId();

            $sqlMember = "INSERT INTO team_members (team_id, user_id, role) VALUES (:team_id, :user_id, 'Leader')";
            $stmtMember = $this->conn->prepare($sqlMember);
            $stmtMember->execute([
                'team_id' => $teamId,
                'user_id' => $userId
            ]);

            $this->conn->commit();
            return $teamId;
        } catch (\Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }


    // Lấy thông tin chi tiết 1 nhóm
    public function getTeamById($teamId) {
        $stmt = $this->conn->prepare("SELECT * FROM teams WHERE id = :id");
        $stmt->execute(['id' => $teamId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // Lấy danh sách thành viên trong nhóm
    public function getTeamMembers($teamId) {
        $sql = "SELECT u.id, u.fullname, u.email, tm.role, tm.joined_at 
                FROM team_members tm 
                JOIN users u ON tm.user_id = u.id 
                WHERE tm.team_id = :team_id 
                ORDER BY tm.role ASC, tm.joined_at ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['team_id' => $teamId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Mời thành viên mới bằng Email
    public function inviteMember($teamId, $email) {
        $stmtUser = $this->conn->prepare("SELECT id FROM users WHERE email = :email");
        $stmtUser->execute(['email' => $email]);
        $user = $stmtUser->fetch(\PDO::FETCH_ASSOC);

        if (!$user) return "Email này chưa đăng ký tài khoản trên hệ thống!";

        $stmtCheck = $this->conn->prepare("SELECT * FROM team_members WHERE team_id = :team_id AND user_id = :user_id");
        $stmtCheck->execute(['team_id' => $teamId, 'user_id' => $user['id']]);
        if ($stmtCheck->fetch()) return "Thành viên này đã có mặt trong nhóm rồi!";

        $stmtAdd = $this->conn->prepare("INSERT INTO team_members (team_id, user_id, role) VALUES (:team_id, :user_id, 'Member')");
        $stmtAdd->execute(['team_id' => $teamId, 'user_id' => $user['id']]);
        return true;
    }

    // Lấy danh sách dự án của một nhóm
    public function getProjectsByTeam($teamId) {
        $stmt = $this->conn->prepare("SELECT p.*, u.fullname as manager_name 
                                    FROM projects p 
                                    LEFT JOIN users u ON p.manager_id = u.id 
                                    WHERE p.team_id = :team_id 
                                    ORDER BY p.created_at DESC");
        $stmt->execute(['team_id' => $teamId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Tạo dự án mới cho nhóm
    public function createProject($teamId, $name, $description, $startDate, $endDate, $managerId) {
        $sql = "INSERT INTO projects (team_id, name, description, start_date, end_date, manager_id) 
                VALUES (:team_id, :name, :description, :start_date, :end_date, :manager_id)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            'team_id' => $teamId,
            'name' => $name,
            'description' => $description,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'manager_id' => $managerId
        ]);
    }

    public function getProjectById($projectId) {
        $stmt = $this->conn->prepare("SELECT * FROM projects WHERE id = :id");
        $stmt->execute(['id' => $projectId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function getTeamTasksByStatus($projectId, $status, $assigneeId = '', $sort = '') {
        $sql = "SELECT tt.*, u.fullname as assignee_name 
                FROM team_tasks tt 
                LEFT JOIN users u ON tt.assigned_to = u.id 
                WHERE tt.project_id = :project_id AND tt.status = :status";
        
        $params = ['project_id' => $projectId, 'status' => $status];

        if ($assigneeId !== '') {
            if ($assigneeId === 'unassigned') {
                $sql .= " AND tt.assigned_to IS NULL";
            } else {
                $sql .= " AND tt.assigned_to = :assignee_id";
                $params['assignee_id'] = $assigneeId;
            }
        }

        if ($sort === 'deadline_asc') {
            $sql .= " ORDER BY tt.due_date ASC";
        } elseif ($sort === 'deadline_desc') {
            $sql .= " ORDER BY tt.due_date DESC";
        } else {
            $sql .= " ORDER BY tt.created_at DESC";
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Tạo task nhóm mới
    public function createTeamTask($data) {
        $sql = "INSERT INTO team_tasks (project_id, assigned_to, title, description, status, priority, due_date) 
                VALUES (:project_id, :assigned_to, :title, :description, 'Backlog', :priority, :due_date)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($data);
    }

    public function updateTeamTaskStatus($taskId, $newStatus) {
        $stmt = $this->conn->prepare("UPDATE team_tasks SET status = :status WHERE id = :id");
        return $stmt->execute(['status' => $newStatus, 'id' => $taskId]);
    }

    public function getTeamTaskById($taskId) {
        $stmt = $this->conn->prepare("SELECT * FROM team_tasks WHERE id = :id");
        $stmt->execute(['id' => $taskId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function updateTeamTask($taskId, $data) {
        $sql = "UPDATE team_tasks SET title = :title, description = :description, 
                assigned_to = :assigned_to, priority = :priority, due_date = :due_date 
                WHERE id = :id";
        $data['id'] = $taskId;
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($data);
    }

    public function deleteTeamTask($taskId) {
        $stmt = $this->conn->prepare("DELETE FROM team_tasks WHERE id = :id");
        return $stmt->execute(['id' => $taskId]);
    }

    public function getTaskComments($taskId) {
        $sql = "SELECT c.*, u.fullname 
                FROM team_task_comments c 
                JOIN users u ON c.user_id = u.id 
                WHERE c.task_id = :task_id 
                ORDER BY c.created_at ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['task_id' => $taskId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function addTaskComment($taskId, $userId, $content, $fileUrl = null) {
        $sql = "INSERT INTO team_task_comments (task_id, user_id, content, file_url) 
                VALUES (:task_id, :user_id, :content, :file_url)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            'task_id' => $taskId,
            'user_id' => $userId,
            'content' => $content,
            'file_url' => $fileUrl
        ]);
    }

    public function removeTeamMember($teamId, $userId) {
        $stmt = $this->conn->prepare("DELETE FROM team_members WHERE team_id = :team_id AND user_id = :user_id");
        return $stmt->execute(['team_id' => $teamId, 'user_id' => $userId]);
    }

    public function deleteProject($projectId) {
        $this->conn->prepare("DELETE FROM team_tasks WHERE project_id = :id")->execute(['id' => $projectId]);
        $stmt = $this->conn->prepare("DELETE FROM projects WHERE id = :id");
        return $stmt->execute(['id' => $projectId]);
    }

    public function deleteTeam($teamId) {
        $this->conn->prepare("DELETE FROM team_members WHERE team_id = :id")->execute(['id' => $teamId]);
        $this->conn->prepare("DELETE FROM projects WHERE team_id = :id")->execute(['id' => $teamId]);
        $stmt = $this->conn->prepare("DELETE FROM teams WHERE id = :id");
        return $stmt->execute(['id' => $teamId]);
    }

    //THỐNG KÊ TIẾN ĐỘ DỰ ÁN
    public function getProjectStats($projectId) {
        $sql = "SELECT status, COUNT(*) as count 
                FROM team_tasks 
                WHERE project_id = :project_id 
                GROUP BY status";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['project_id' => $projectId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    //Lấy danh sách công việc trễ hạn / sắp đến hạn (chưa Done)
    public function getProjectDeadlineTasks($projectId) {
        $sql = "SELECT tt.*, u.fullname as assignee_name 
                FROM team_tasks tt 
                LEFT JOIN users u ON tt.assigned_to = u.id 
                WHERE tt.project_id = :project_id 
                AND tt.status != 'Done' 
                AND tt.due_date IS NOT NULL 
                AND tt.due_date != '0000-00-00'
                ORDER BY tt.due_date ASC 
                LIMIT 5"; // Lấy 5 task gấp nhất
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['project_id' => $projectId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    //Báo cáo thống kê tiến độ theo từng thành viên
    public function getProjectMemberStats($projectId) {
        $sql = "SELECT u.fullname, 
                    COUNT(tt.id) as total_tasks,
                    SUM(CASE WHEN tt.status = 'Done' THEN 1 ELSE 0 END) as done_tasks
                FROM team_tasks tt
                LEFT JOIN users u ON tt.assigned_to = u.id
                WHERE tt.project_id = :project_id
                GROUP BY tt.assigned_to, u.fullname
                ORDER BY total_tasks DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['project_id' => $projectId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getProjectComments($projectId) {
        $sql = "SELECT pc.*, u.fullname FROM project_comments pc LEFT JOIN users u ON pc.user_id = u.id WHERE pc.project_id = ? ORDER BY pc.created_at ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$projectId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function addProjectComment($projectId, $userId, $content, $fileUrl = null) {
        $sql = "INSERT INTO project_comments (project_id, user_id, content, file_url) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$projectId, $userId, $content, $fileUrl]);
    }

    public function getTeamComments($teamId) {
        $sql = "SELECT tc.*, u.fullname FROM team_comments tc LEFT JOIN users u ON tc.user_id = u.id WHERE tc.team_id = ? ORDER BY tc.created_at ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$teamId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function addTeamComment($teamId, $userId, $content, $fileUrl = null) {
        $sql = "INSERT INTO team_comments (team_id, user_id, content, file_url) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$teamId, $userId, $content, $fileUrl]);
    }
}