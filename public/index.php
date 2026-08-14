<?php
session_start();
$user_timezone = $_SESSION['timezone'] ?? 'Asia/Ho_Chi_Minh';
date_default_timezone_set($user_timezone);
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
        require_once PROJECT_ROOT . '/src/Controllers/SettingsController.php';
        $settingsController = new \Tinhu\TaskManager\Controllers\SettingsController();
        $settingsController->index();
        break;
    
    case 'tasks':
        require_once PROJECT_ROOT . '/src/Controllers/TaskController.php';
        $taskController = new \Tinhu\TaskManager\Controllers\TaskController();
        $taskController->index();
        break;

    case 'add-task':
        require_once PROJECT_ROOT . '/src/Controllers/TaskController.php';
        $taskController = new \Tinhu\TaskManager\Controllers\TaskController();
        $taskController->create();
        break;

    case 'update-task':
        require_once PROJECT_ROOT . '/src/Controllers/TaskController.php';
        $taskController = new \Tinhu\TaskManager\Controllers\TaskController();
        $taskController->update();
        break;

    case 'edit-task':
        require_once PROJECT_ROOT . '/src/Controllers/TaskController.php';
        $taskController = new \Tinhu\TaskManager\Controllers\TaskController();
        $taskController->edit();
        break;
    
    case 'delete-task':
        require_once PROJECT_ROOT . '/src/Controllers/TaskController.php';
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
        require_once PROJECT_ROOT . '/src/Controllers/TaskController.php';
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

    case 'create-project':
        require_once PROJECT_ROOT . '/src/Controllers/TeamController.php';
        $teamController = new \Tinhu\TaskManager\Controllers\TeamController();
        $teamController->createProject();
        break;

    case 'project-kanban':
        require_once PROJECT_ROOT . '/src/Controllers/TeamController.php';
        $teamController = new \Tinhu\TaskManager\Controllers\TeamController();
        $teamController->projectKanban();
        break;

    case 'add-team-task':
        require_once PROJECT_ROOT . '/src/Controllers/TeamController.php';
        $teamController = new \Tinhu\TaskManager\Controllers\TeamController();
        $teamController->addTeamTask();
        break;

    case 'update-team-task':
        require_once PROJECT_ROOT . '/src/Controllers/TeamController.php';
        $teamController = new \Tinhu\TaskManager\Controllers\TeamController();
        $teamController->updateTeamTask();
        break;

    case 'edit-team-task':
        require_once PROJECT_ROOT . '/src/Controllers/TeamController.php';
        $teamController = new \Tinhu\TaskManager\Controllers\TeamController();
        $teamController->editTeamTask();
        break;

    case 'delete-team-task':
        require_once PROJECT_ROOT . '/src/Controllers/TeamController.php';
        $teamController = new \Tinhu\TaskManager\Controllers\TeamController();
        $teamController->deleteTeamTask();
        break;
    
    case 'team-task-detail':
        require_once PROJECT_ROOT . '/src/Controllers/TeamController.php';
        $teamController = new \Tinhu\TaskManager\Controllers\TeamController();
        $teamController->taskDetail();
        break;

    case 'add-project-comment':
        require_once PROJECT_ROOT . '/src/Controllers/TeamController.php';
        $chatController = new \Tinhu\TaskManager\Controllers\TeamController();
        $chatController->addProjectComment();
        break;

    case 'add-team-comment':
        require_once PROJECT_ROOT . '/src/Controllers/TeamController.php';
        $teamController = new \Tinhu\TaskManager\Controllers\TeamController();
        $teamController->addComment();
        break;

    case 'add-team-message':
        require_once PROJECT_ROOT . '/src/Controllers/TeamController.php';
        $teamMsgController = new \Tinhu\TaskManager\Controllers\TeamController();
        $teamMsgController->addTeamMessage();
        break;

    case 'kick-member':
        require_once PROJECT_ROOT . '/src/Controllers/TeamController.php';
        (new \Tinhu\TaskManager\Controllers\TeamController())->kickMember();
        break;

    case 'delete-project':
        require_once PROJECT_ROOT . '/src/Controllers/TeamController.php';
        (new \Tinhu\TaskManager\Controllers\TeamController())->removeProject();
        break;

    case 'delete-team':
        require_once PROJECT_ROOT . '/src/Controllers/TeamController.php';
        (new \Tinhu\TaskManager\Controllers\TeamController())->removeTeam();
        break;
    
    case 'api-check-notifications':
        header('Content-Type: application/json');
        $db = \Tinhu\TaskManager\Core\Database::getInstance()->getConnection();
        $userId = $_SESSION['user_id'] ?? 0;
        
        $stmtSet = $db->prepare("SELECT notify_alerts FROM users WHERE id = ?");
        $stmtSet->execute([$userId]);
        $uSet = $stmtSet->fetch(\PDO::FETCH_ASSOC);
        
        if ($userId && (!isset($uSet['notify_alerts']) || $uSet['notify_alerts'] == 1)) {
            $stmt = $db->prepare("SELECT id, message FROM notifications WHERE user_id = ? AND is_read = 0");
            $stmt->execute([$userId]);
            $notifs = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if (count($notifs) > 0) {
                $ids = array_column($notifs, 'id');
                $inQuery = implode(',', array_fill(0, count($ids), '?'));
                $stmtUpdate = $db->prepare("UPDATE notifications SET is_read = 1 WHERE id IN ($inQuery)");
                $stmtUpdate->execute($ids);
                
                echo json_encode(['status' => 'success', 'data' => $notifs]);
                exit;
            }
        }
        echo json_encode(['status' => 'empty']);
        exit;

    case 'export-excel':
        require_once PROJECT_ROOT . '/src/Controllers/ExportController.php';
        (new \Tinhu\TaskManager\Controllers\ExportController())->exportExcel();
        break;

    case 'export-personal-report':
        require_once PROJECT_ROOT . '/src/Controllers/ExportController.php';
        (new \Tinhu\TaskManager\Controllers\ExportController())->exportPersonalReport();
        break;

    case 'admin':
        require_once PROJECT_ROOT . '/src/Controllers/AdminController.php';
        (new \Tinhu\TaskManager\Controllers\AdminController())->index();
        break;
        
    case 'admin-change-role':
        require_once PROJECT_ROOT . '/src/Controllers/AdminController.php';
        (new \Tinhu\TaskManager\Controllers\AdminController())->updateRole();
        break;
        
    case 'admin-toggle-lock':
        require_once PROJECT_ROOT . '/src/Controllers/AdminController.php';
        (new \Tinhu\TaskManager\Controllers\AdminController())->toggleLock();
        break;
        
    case 'admin-save-config':
        require_once PROJECT_ROOT . '/src/Controllers/AdminController.php';
        (new \Tinhu\TaskManager\Controllers\AdminController())->saveConfig();
        break;

    case 'report-personal':
        require_once PROJECT_ROOT . '/src/Controllers/ReportController.php';
        (new \Tinhu\TaskManager\Controllers\ReportController())->personalReport();
        break;

    case 'report-team':
        require_once PROJECT_ROOT . '/src/Controllers/ReportController.php';
        (new \Tinhu\TaskManager\Controllers\ReportController())->teamReport();
        break;

    default:
        echo "404 - Trang không tồn tại!";
        break;
}