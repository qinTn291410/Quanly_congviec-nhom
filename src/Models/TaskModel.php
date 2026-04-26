<?php
namespace Tinhu\TaskManager\Models;
use Tinhu\TaskManager\Core\Database;
use PDO;

class TaskModel {
    private $conn;
    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    // Lấy tất cả việc của một người dùng theo trạng thái
    public function getTasksByStatus($userId, $status) {
        $stmt = $this->conn->prepare("SELECT * FROM tasks WHERE user_id = :user_id AND status = :status ORDER BY created_at DESC");
        $stmt->execute(['user_id' => $userId, 'status' => $status]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Thêm công việc mới
    public function createTask($data) {
        $sql = "INSERT INTO tasks (user_id, title, description, start_date, due_date, priority, status) 
                VALUES (:user_id, :title, :description, :start_date, :due_date, :priority, :status)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($data);
    }

    public function updateStatus($taskId, $newStatus) {
    $stmt = $this->conn->prepare("UPDATE tasks SET status = :status WHERE id = :id");
    return $stmt->execute(['status' => $newStatus, 'id' => $taskId]);
    }

    // Hàm xóa công việc
public function deleteTask($taskId) {
    $stmt = $this->conn->prepare("DELETE FROM tasks WHERE id = :id");
    return $stmt->execute(['id' => $taskId]);
}   
}