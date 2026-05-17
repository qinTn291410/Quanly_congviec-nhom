<?php
namespace Tinhu\TaskManager\Core;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailHelper {
    public static function sendMail($to, $subject, $body) {
        $db = \Tinhu\TaskManager\Core\Database::getInstance()->getConnection();

        $stmt = $db->query("SELECT config_key, config_value FROM system_configs");
        $configs = $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = $configs['smtp_host'] ?? 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = $configs['smtp_user'] ?? '';
            $mail->Password   = $configs['smtp_pass'] ?? '';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = $configs['smtp_port'] ?? 587;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom($configs['smtp_user'], 'Task Manager System');

            $mail->addAddress($to); 

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Lỗi gửi mail: " . $mail->ErrorInfo);
            return false;
        }
    }
}