<?php
namespace Tinhu\TaskManager\Models;
use Tinhu\TaskManager\Core\Database;
use PDO;

class TaskModel {
    private $conn;
    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    // Lấy tất cả việc của một người dùng theo trạng thái và các bộ lọc
    public function getTasksByStatus($userId, $status, $search = '', $category = '', $priority = '') {
        $sql = "SELECT * FROM tasks WHERE user_id = :user_id AND status = :status";
        $params = ['user_id' => $userId, 'status' => $status];

        if (!empty($search)) {
            $sql .= " AND (title LIKE :search OR description LIKE :search)";
            $params['search'] = "%$search%";
        }
        if (!empty($category)) {
            $sql .= " AND category = :category";
            $params['category'] = $category;
        }
        if (!empty($priority)) {
            $sql .= " AND priority = :priority";
            $params['priority'] = $priority;
        }

        $sql .= " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy các việc sắp đến hạn (Hôm nay hoặc ngày mai) mà chưa gửi nhắc nhở
    public function getTasksForReminder($userId) {
        $sql = "SELECT * FROM tasks 
                WHERE user_id = :user_id 
                AND status != 'Done' 
                AND due_date <= DATE_ADD(CURDATE(), INTERVAL 1 DAY) 
                AND due_date != '0000-00-00'
                AND is_reminded = 0";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Đánh dấu là "Đã gửi mail" để lần sau không spam nữa
    public function markAsReminded($taskId) {
        $stmt = $this->conn->prepare("UPDATE tasks SET is_reminded = 1 WHERE id = :id");
        return $stmt->execute(['id' => $taskId]);
    }

    // Thêm công việc mới
    public function createTask($data) {
        $sql = "INSERT INTO tasks (user_id, title, description, start_date, due_date, priority, status, category, goal) 
                VALUES (:user_id, :title, :description, :start_date, :due_date, :priority, :status, :category, :goal)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($data);
    }

    public function updateStatus($taskId, $newStatus) {
    $stmt = $this->conn->prepare("UPDATE tasks SET status = :status WHERE id = :id");
    return $stmt->execute(['status' => $newStatus, 'id' => $taskId]);
    }

    // Lấy chi tiết 1 công việc để sửa
    public function getTaskById($taskId) {
        $stmt = $this->conn->prepare("SELECT * FROM tasks WHERE id = :id");
        $stmt->execute(['id' => $taskId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Cập nhật toàn bộ thông tin công việc
    public function updateTask($taskId, $data) {
        $sql = "UPDATE tasks SET title = :title, description = :description, start_date = :start_date, 
                due_date = :due_date, priority = :priority, category = :category, goal = :goal 
                WHERE id = :id";
        $data['id'] = $taskId;
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($data);
    }

    // Hàm xóa công việc
public function deleteTask($taskId) {
    $stmt = $this->conn->prepare("DELETE FROM tasks WHERE id = :id");
    return $stmt->execute(['id' => $taskId]);
}   
}