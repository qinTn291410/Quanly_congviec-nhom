<?php
namespace Tinhu\TaskManager\Models;
require_once PROJECT_ROOT . '/src/Core/Database.php';

use Tinhu\TaskManager\Core\Database;

class GoalModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    // Lấy danh sách mục tiêu kèm tiến độ %
    public function getGoalsWithProgress($userId) {
        $sql = "SELECT g.*, 
                COUNT(t.id) as total_tasks,
                SUM(CASE WHEN t.status = 'Done' THEN 1 ELSE 0 END) as completed_tasks
                FROM goals g
                LEFT JOIN tasks t ON g.id = t.goal_id
                WHERE g.user_id = :user_id
                GROUP BY g.id
                ORDER BY g.end_date ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Tạo mục tiêu mới
    public function createGoal($data) {
        $sql = "INSERT INTO goals (user_id, title, type, start_date, end_date) 
                VALUES (:user_id, :title, :type, :start_date, :end_date)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            'user_id'    => $data['user_id'],
            'title'      => $data['title'],
            'type'       => $data['type'],
            'start_date' => $data['start_date'],
            'end_date'   => $data['end_date']
        ]);
    }

    // Xóa mục tiêu (và gỡ liên kết công việc cũ)
    public function deleteGoal($goalId, $userId) {
        $stmt1 = $this->conn->prepare("UPDATE tasks SET goal_id = NULL WHERE goal_id = :goal_id AND user_id = :user_id");
        $stmt1->execute(['goal_id' => $goalId, 'user_id' => $userId]);

        $stmt2 = $this->conn->prepare("DELETE FROM goals WHERE id = :id AND user_id = :user_id");
        return $stmt2->execute(['id' => $goalId, 'user_id' => $userId]);
    }
}