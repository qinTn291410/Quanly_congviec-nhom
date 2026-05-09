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
}