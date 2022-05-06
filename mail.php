<?php
// Файлы phpmailer
require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';
require 'phpmailer/Exception.php';

$mail = new PHPMailer\PHPMailer\PHPMailer();
$mail->CharSet = 'UTF-8';
 
// Настройки SMTP
$mail->isSMTP();
$mail->SMTPAuth = true;
$mail->SMTPDebug = 0;
 
$mail->Host = 'ssl://smtp.gmail.com';
$mail->Port = 465;
$mail->Username = 'chance20022@gmail.com';
$mail->Password = 'gymocnayknhjeibz';
 
// От кого
$mail->setFrom('chance20022@gmail.com', 'Test');		
 
// Кому
$mail->addAddress('sora200222@gmail.com');
 
// Тема письма
$mail->Subject = $subject;
 
// Тело письма
$body = '<p><strong>«Hello, world!» </strong></p>';
$mail->msgHTML($body);
 
// Приложение
//$mail->addAttachment(__DIR__ . '/image.jpg');
 
$mail->send();
?>