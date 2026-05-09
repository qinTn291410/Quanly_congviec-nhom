<?php
session_start();
define('PROJECT_ROOT', dirname(__DIR__));
require_once PROJECT_ROOT . '/vendor/autoload.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Tinhu\TaskManager\Controllers\UserController;
$userController = new UserController(); 

$action = $_GET['action'] ?? 'login';

// BỘ ĐỊNH TUYẾN CHÍNH THỨC VÀ DUY NHẤT
switch ($action) {
    case 'login':
        $userController->login();
        break;
        
    case 'register':
        $userController->register();
        break;

    case 'dashboard':
        require_once PROJECT_ROOT . '/views/dashboard.php';
        break;

    case 'logout':
        session_destroy();
        header('Location: index.php?action=login');
        exit(); 
        break;  
    
    case 'forgot-password':
        $userController->forgotPassword();
        break;

    case 'profile':
        $userController = new \Tinhu\TaskManager\Controllers\UserController();
        $userController->profile();
        break;

    case 'settings':
        $userController = new \Tinhu\TaskManager\Controllers\UserController();
        $userController->settings();
        break;
    
    case 'tasks':
        $taskController = new \Tinhu\TaskManager\Controllers\TaskController();
        $taskController->index();
        break;

    case 'add-task':
        $taskController = new \Tinhu\TaskManager\Controllers\TaskController();
        $taskController->create();
        break;

    case 'update-task':
        $taskController = new \Tinhu\TaskManager\Controllers\TaskController();
        $taskController->update();
        break;

    case 'edit-task':
        $taskController = new \Tinhu\TaskManager\Controllers\TaskController();
        $taskController->edit();
        break;
    
    case 'delete-task':
        $taskController = new \Tinhu\TaskManager\Controllers\TaskController();
        $taskController->delete();
        break;

    case 'goals':
        require_once PROJECT_ROOT . '/views/goals/index.php';
        break;
    
    case 'add-goal':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once PROJECT_ROOT . '/src/Models/GoalModel.php';
            $goalModel = new \Tinhu\TaskManager\Models\GoalModel();
            $data = [
                'user_id'    => $_SESSION['user_id'],
                'title'      => $_POST['title'],
                'type'       => $_POST['type'],
                'start_date' => $_POST['start_date'],
                'end_date'   => $_POST['end_date']
            ];
            $goalModel->createGoal($data);
            header('Location: index.php?action=goals');
            exit();
        }
        break;

    case 'delete-goal':
        if (isset($_GET['id'])) {
            require_once PROJECT_ROOT . '/src/Models/GoalModel.php';
            $goalModel = new \Tinhu\TaskManager\Models\GoalModel();
            $goalModel->deleteGoal($_GET['id'], $_SESSION['user_id']);
        }
        header('Location: index.php?action=goals');
        exit();
        break;

    case 'calendar':
    $taskController = new \Tinhu\TaskManager\Controllers\TaskController();
    $taskController->calendar();
    break;

    case 'teams':
        require_once PROJECT_ROOT . '/src/Controllers/TeamController.php';
        $teamController = new \Tinhu\TaskManager\Controllers\TeamController();
        $teamController->index();
        break;

    case 'create-team':
        require_once PROJECT_ROOT . '/src/Controllers/TeamController.php';
        $teamController = new \Tinhu\TaskManager\Controllers\TeamController();
        $teamController->create();
        break;
    
    case 'team-detail':
        require_once PROJECT_ROOT . '/src/Controllers/TeamController.php';
        $teamController = new \Tinhu\TaskManager\Controllers\TeamController();
        $teamController->detail();
        break;

    case 'invite-member':
        require_once PROJECT_ROOT . '/src/Controllers/TeamController.php';
        $teamController = new \Tinhu\TaskManager\Controllers\TeamController();
        $teamController->invite();
        break;

    default:    
        echo "404 - Trang không tồn tại!";
        break;
}