<?php
namespace Tinhu\TaskManager\Controllers;

use Tinhu\TaskManager\Models\UserModel;

class UserController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    // XỬ LÝ ĐĂNG NHẬP
    public function login() {
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            $user = $this->userModel->findUserByEmail($email);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['fullname'] = $user['fullname'];
                $_SESSION['language'] = $language;
                $_SESSION['timezone'] = $timezone;
                header('Location: index.php?action=dashboard'); 
                exit();
            } else {
                $error = 'Email hoặc mật khẩu không chính xác!';
            }
        }
        require_once PROJECT_ROOT . '/views/auth/login.php';
    }

    // XỬ LÝ ĐĂNG KÝ
    public function register() {
        $error = '';
        $success = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fullname = trim($_POST['fullname'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (strlen($password) < 6) {
                $error = 'Mật khẩu phải từ 6 ký tự trở lên.';
            } else {
                if ($this->userModel->createUser($fullname, $email, $password)) {
                    $success = 'Đăng ký thành công! Hãy đăng nhập.';
                } else {
                    $error = 'Email này đã được sử dụng!';
                }
            }
        }
        require_once PROJECT_ROOT . '/views/auth/register.php';
    }

    public function forgotPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $new_password = $_POST['new_password'];

            if ($this->userModel->checkEmailExists($email)) {
                $this->userModel->resetPassword($email, $new_password);
                echo "<script>alert('Đổi mật khẩu thành công! Mời sếp đăng nhập lại.'); window.location.href='index.php?action=login';</script>";
            } else {
                echo "<script>alert('Email này chưa được đăng ký trong hệ thống!'); window.history.back();</script>";
            }
            exit();
        } else {
            require_once PROJECT_ROOT . '/views/auth/forgot_password.php';
        }
    }

    public function profile() {
        $userId = $_SESSION['user_id'];
        $message = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fullname = $_POST['fullname'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];
            $avatar = null;

            // Xử lý upload ảnh
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
                $uploadDir = PROJECT_ROOT . '/public/uploads/';
                // Đổi tên file để không bị trùng (dùng hàm time)
                $fileName = time() . '_' . basename($_FILES['avatar']['name']); 
                $targetPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetPath)) {
                    $avatar = $fileName;
                }
            }

            if ($this->userModel->updateProfile($userId, $fullname, $email, $phone, $avatar)) {
                $_SESSION['fullname'] = $fullname; 
                $message = "Cập nhật thông tin thành công!";
            } else {
                $message = "Có lỗi xảy ra!";
            }
        }

        $user = $this->userModel->getUserById($userId);
        
        require_once PROJECT_ROOT . '/views/user/profile.php';
    }

    public function settings() {
        $userId = $_SESSION['user_id'];
        $message = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $notifications = isset($_POST['notifications']) ? 1 : 0;
            $language = $_POST['language'];
            $timezone = $_POST['timezone'];

            if ($this->userModel->updateSettings($userId, $notifications, $language, $timezone)) {
                $_SESSION['language'] = $language;
                $_SESSION['timezone'] = $timezone;
                $_SESSION['notifications'] = $notifications;
                $message = "Đã lưu cài đặt hệ thống!";
            } else {
                $message = "Có lỗi xảy ra!";
            }
        }

        $settings = $this->userModel->getSettings($userId);
        
        require_once PROJECT_ROOT . '/views/user/settings.php';
    }
}