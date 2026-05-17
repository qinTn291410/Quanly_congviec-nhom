<?php
namespace Tinhu\TaskManager\Controllers;

use Tinhu\TaskManager\Core\Database;

class ReportController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function personalReport() {
        $userId = $_SESSION['user_id'];

        $stats = $this->getPersonalStats($userId);
        $tasksByStatus = $this->getTasksByStatusCount($userId);
        $tasksByPriority = $this->getTasksByPriorityCount($userId);
        $tasksByCategory = $this->getTasksByCategoryCount($userId);
        $progressChart = $this->getProgressOverTime($userId);

        require_once PROJECT_ROOT . '/views/reports/personal.php';
    }

    public function teamReport() {
        $teams = $this->getTeamsForReport();

        if (!$teams) {
            $error = "Bạn chưa tham gia nhóm nào!";
            require_once PROJECT_ROOT . '/views/reports/team.php';
            return;
        }

        $selectedTeamId = $_GET['team_id'] ?? ($teams[0]['id'] ?? null);

        if ($selectedTeamId) {
            $teamStats = $this->getTeamStats($selectedTeamId);
            $memberStats = $this->getMemberStats($selectedTeamId);
            $projectStats = $this->getProjectStats($selectedTeamId);
        }

        require_once PROJECT_ROOT . '/views/reports/team.php';
    }

    private function getPersonalStats($userId) {
        $stmt = $this->db->prepare("
            SELECT
                COUNT(*) as total_tasks,
                SUM(CASE WHEN status = 'Done' THEN 1 ELSE 0 END) as completed_tasks,
                SUM(CASE WHEN status != 'Done' THEN 1 ELSE 0 END) as pending_tasks,
                SUM(CASE WHEN status != 'Done' AND due_date <= CURDATE() AND due_date != '0000-00-00' THEN 1 ELSE 0 END) as overdue_tasks
            FROM tasks
            WHERE user_id = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    private function getTasksByStatusCount($userId) {
        $stmt = $this->db->prepare("
            SELECT status, COUNT(*) as count
            FROM tasks
            WHERE user_id = ?
            GROUP BY status
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getTasksByPriorityCount($userId) {
        $stmt = $this->db->prepare("
            SELECT priority, COUNT(*) as count
            FROM tasks
            WHERE user_id = ? AND priority IS NOT NULL AND priority != ''
            GROUP BY priority
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getTasksByCategoryCount($userId) {
        $stmt = $this->db->prepare("
            SELECT category, COUNT(*) as count
            FROM tasks
            WHERE user_id = ? AND category IS NOT NULL AND category != ''
            GROUP BY category
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getProgressOverTime($userId) {
        $stmt = $this->db->prepare("
            SELECT DATE(created_at) as date, COUNT(*) as tasks_created
            FROM tasks
            WHERE user_id = ?
            GROUP BY DATE(created_at)
            ORDER BY created_at DESC
            LIMIT 30
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getTeamsForReport() {
        $userId = $_SESSION['user_id'];
        $stmt = $this->db->prepare("
            SELECT DISTINCT t.id, t.name
            FROM teams t
            JOIN team_members tm ON t.id = tm.team_id
            WHERE tm.user_id = ?
            ORDER BY t.name
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getTeamStats($teamId) {
        $stmt = $this->db->prepare("
            SELECT
                COUNT(*) as total_tasks,
                SUM(CASE WHEN status = 'Done' THEN 1 ELSE 0 END) as completed_tasks,
                SUM(CASE WHEN status != 'Done' THEN 1 ELSE 0 END) as pending_tasks,
                SUM(CASE WHEN status != 'Done' AND due_date <= CURDATE() AND due_date != '0000-00-00' THEN 1 ELSE 0 END) as overdue_tasks
            FROM team_tasks
            WHERE team_id = ?
        ");
        $stmt->execute([$teamId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    private function getMemberStats($teamId) {
        $stmt = $this->db->prepare("
            SELECT
                u.id, u.fullname,
                COUNT(tt.id) as assigned_tasks,
                SUM(CASE WHEN tt.status = 'Done' THEN 1 ELSE 0 END) as completed_tasks,
                SUM(CASE WHEN tt.status != 'Done' THEN 1 ELSE 0 END) as pending_tasks
            FROM team_members tm
            JOIN users u ON tm.user_id = u.id
            LEFT JOIN team_tasks tt ON u.id = tt.assigned_to AND tt.team_id = ?
            WHERE tm.team_id = ?
            GROUP BY u.id, u.fullname
            ORDER BY assigned_tasks DESC
        ");
        $stmt->execute([$teamId, $teamId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getProjectStats($teamId) {
        $stmt = $this->db->prepare("
            SELECT
                p.id, p.title,
                COUNT(tt.id) as total_tasks,
                SUM(CASE WHEN tt.status = 'Done' THEN 1 ELSE 0 END) as completed_tasks
            FROM projects p
            LEFT JOIN team_tasks tt ON p.id = tt.project_id
            WHERE p.team_id = ?
            GROUP BY p.id, p.title
            ORDER BY p.created_at DESC
        ");
        $stmt->execute([$teamId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
