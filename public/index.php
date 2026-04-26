<?php
session_start();
define('PROJECT_ROOT', dirname(__DIR__));
require_once PROJECT_ROOT . '/vendor/autoload.php';

use Tinhu\TaskManager\Controllers\UserController;
$userController = new UserController(); 

// Lấy action từ URL
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
        // Gọi file giao diện Dashboard (có chứa Sidebar)
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
    
    case 'delete-task':
        $taskController = new \Tinhu\TaskManager\Controllers\TaskController();
        $taskController->delete();
        break;

    default:    
        echo "404 - Trang không tồn tại!";
        break;
}