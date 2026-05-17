<?php
namespace Tinhu\TaskManager\Controllers;

use Tinhu\TaskManager\Models\UserModel;
use Tinhu\TaskManager\Core\MailHelper; 

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
                
                if (isset($user['is_locked']) && $user['is_locked'] == 1) {
                    $error = 'Tài khoản của bạn đã bị Admin khóa!';
                } else {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['fullname'] = $user['fullname'];
                    $_SESSION['role'] = $user['role'] ?? 'user'; 
                    $_SESSION['language'] = $language ?? 'vi'; 
                    $_SESSION['timezone'] = $timezone ?? 'Asia/Ho_Chi_Minh';
                    $_SESSION['email'] = $user['email'];
                    header('Location: index.php?action=dashboard'); 
                    exit();
                }

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
                    
                    // --- GỌI LỆNH GỬI MAIL CHÀO MỪNG ---
                    MailHelper::sendMail(
                        $email, 
                        "Chào mừng bạn gia nhập Task Manager!", 
                        "<h3>Chào bạn $fullname,</h3><p>Tài khoản của bạn đã được khởi tạo thành công trên hệ thống. Bắt đầu dọn dẹp task ngay thôi!</p>"
                    );
                    
                } else {
                    $error = 'Email này đã được sử dụng!';
                }
            }
        }
        require_once PROJECT_ROOT . '/views/auth/register.php';
    }

    // XỬ LÝ QUÊN / ĐỔI MẬT KHẨU
    public function forgotPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $new_password = $_POST['new_password'];

            if ($this->userModel->checkEmailExists($email)) {
                $this->userModel->resetPassword($email, $new_password);
                
                //GỌI LỆNH GỬI MAIL CẢNH BÁO BẢO MẬT
                MailHelper::sendMail(
                    $email, 
                    "Cảnh báo bảo mật: Mật khẩu đã thay đổi", 
                    "<h3>Cảnh báo,</h3><p>Mật khẩu tài khoản Task Manager của bạn vừa được thay đổi thành công. Nếu bạn không thực hiện hành động này, vui lòng liên hệ Admin ngay lập tức!</p>"
                );

                echo "<script>alert('Đổi mật khẩu thành công! Mời bạn đăng nhập lại.'); window.location.href='index.php?action=login';</script>";
            } else {
                echo "<script>alert('Email này chưa được đăng ký trong hệ thống!'); window.history.back();</script>";
            }
            exit();
        } else {
            require_once PROJECT_ROOT . '/views/auth/forgot_password.php';
        }
    }

    // XỬ LÝ CẬP NHẬT HỒ SƠ
    public function profile() {
        $userId = $_SESSION['user_id'];
        $message = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fullname = $_POST['fullname'];
            $email = $_POST['email'];
            $phone = $_POST['phone'] ?? null;
            $dob = $_POST['dob'] ?? null;
            $address = $_POST['address'] ?? null;
            $bio = $_POST['bio'] ?? null;
            $avatar = null;

            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
                $uploadDir = PROJECT_ROOT . '/public/uploads/';
                $fileName = time() . '_' . basename($_FILES['avatar']['name']); 
                $targetPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetPath)) {
                    $avatar = $fileName;
                }
            }

            if ($this->userModel->updateProfile($userId, $fullname, $email, $phone, $avatar, $dob, $address, $bio)) {
                $_SESSION['fullname'] = $fullname; 
                $message = "Cập nhật hồ sơ thành công!";
            } else {
                $message = "Có lỗi xảy ra!";
            }
        }

        $user = $this->userModel->getUserById($userId);
        require_once PROJECT_ROOT . '/views/user/profile.php';
    }

    // XỬ LÝ CÀI ĐẶT
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