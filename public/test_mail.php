//http://localhost/task_manager/public/test_mail.php
<?php
session_start();
define('PROJECT_ROOT', dirname(__DIR__));
require_once PROJECT_ROOT . '/vendor/autoload.php';

$db = \Tinhu\TaskManager\Core\Database::getInstance()->getConnection();
$stmt = $db->query("SELECT config_key, config_value FROM system_configs");
$configs = $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);

echo "<h3>THÔNG TIN ĐANG DÙNG ĐỂ GỬI:</h3>";
echo "Host: " . ($configs['smtp_host'] ?? 'Trống') . "<br>";
echo "User: " . ($configs['smtp_user'] ?? 'Trống') . "<br>";
echo "Pass: " . (!empty($configs['smtp_pass']) ? 'ĐÃ CÓ MẬT KHẨU (***)' : '<b style="color:red">CHƯA CÓ MẬT KHẨU TRONG DATABASE!</b>') . "<br><hr>";

$mail = new \PHPMailer\PHPMailer\PHPMailer(true);
try {
    $mail->SMTPDebug = 2; 
    $mail->Debugoutput = 'html';

    $mail->isSMTP();
    $mail->Host       = $configs['smtp_host'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $configs['smtp_user'];
    $mail->Password   = str_replace(' ', '', $configs['smtp_pass'] ?? ''); 
    $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = $configs['smtp_port'];
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom($configs['smtp_user'], 'Task Manager Test');
    
    $mail->addAddress('EMAIL_CUA_SEP_VAO_DAY@gmail.com'); 

    $mail->isHTML(true);
    $mail->Subject = 'Test moi lỗi Email';
    $mail->Body    = 'Nếu đọc được dòng này thì Mail đã thông!';

    $mail->send();
    echo "<h2 style='color:green;'>GỬI THÀNH CÔNG!</h2>";
} catch (Exception $e) {
    echo "<h2 style='color:red;'>LỖI TỪ PHẨM CHẤT GOOGLE: " . $mail->ErrorInfo . "</h2>";
}
?>