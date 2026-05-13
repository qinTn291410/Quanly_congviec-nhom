<?php
namespace Tinhu\TaskManager\Core;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailHelper {
    public static function sendMail($toEmail, $subject, $body) {
        $mail = new PHPMailer(true);

        try {
            // Cấu hình Server gửi mail (Gmail)
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            
            // TÀI KHOẢN VÀ MẬT KHẨU CỦA SẾP
            $mail->Username   = 'tinhuynhba1289@gmail.com';
            $mail->Password   = 'uldy pplc xjel ercz';
            
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; 
            $mail->Port       = 465;

            $mail->setFrom('tinhuynhba1289@gmail.com', 'Task Manager System');
            $mail->addAddress($toEmail);
            $mail->CharSet = 'UTF-8';

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}