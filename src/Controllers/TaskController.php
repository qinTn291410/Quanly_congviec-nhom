<?php
namespace Tinhu\TaskManager\Controllers;
use Tinhu\TaskManager\Models\TaskModel;

class TaskController {
    private $taskModel;
    public function __construct() {
        $this->taskModel = new TaskModel();
    }

    public function index() {
        $userId = $_SESSION['user_id'];
        
        // Phân loại việc vào 3 cột Kanban
        $todoTasks = $this->taskModel->getTasksByStatus($userId, 'To-do');
        $doingTasks = $this->taskModel->getTasksByStatus($userId, 'Doing');
        $doneTasks = $this->taskModel->getTasksByStatus($userId, 'Done');
        $pendingTasks = $this->taskModel->getTasksByStatus($userId, 'Pending'); 

        require_once PROJECT_ROOT . '/views/tasks/index.php';
    }

    public function create() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Tự động lấy ngày hôm nay nếu sếp để trống
        $start_date = !empty($_POST['start_date']) ? $_POST['start_date'] : date('Y-m-d');
        $due_date   = !empty($_POST['due_date'])   ? $_POST['due_date']   : date('Y-m-d');

        $data = [
            'user_id'     => $_SESSION['user_id'],
            'title'       => $_POST['title'],
            'description' => $_POST['description'],
            'start_date'  => $start_date,
            'due_date'    => $due_date,
            'priority'    => $_POST['priority'],
            'status'      => 'To-do'
        ];

        if ($this->taskModel->createTask($data)) {
            header('Location: index.php?action=tasks');
            exit();
        }
    }
}

    public function update() {
        $id = $_GET['id'] ?? 0;
        $status = $_GET['status'] ?? 'To-do';
        if ($id) {
            $this->taskModel->updateStatus($id, $status);
        }
        header('Location: index.php?action=tasks');
        exit();
    }

    public function delete() {
    $id = $_GET['id'] ?? 0;
    if ($id) {
        $this->taskModel->deleteTask($id);
    }
    header('Location: index.php?action=tasks');
    exit();
}
}