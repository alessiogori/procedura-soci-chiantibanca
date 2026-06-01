<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';


// Create a new PHPMailer instance
$mail = new PHPMailer(true);

try {
    // SMTP configuration
    $mail->SMTPDebug = 3;
    $mail->isSMTP();
    $mail->Host = 'ssl://smtp.pec.actalis.it';
    //$mail->SMTPAuth = false;
    $mail->Username = 'soci@pecchiantibanca.it';
    $mail->Password = 'ChiantiBanca!S@ci23';
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;
    //$mail->Timeout = 10;

    // Sender and recipient
    $mail->setFrom('soci@pecchiantibanca.it', 'ChiantiBanca Soci');
    $mail->addAddress('alessio.fedi@chiantibanca.it', 'Recipient Name');

    // Email content
    $mail->isHTML(true);
    $mail->Subject = 'Test Email';
    $mail->Body = 'This is a test email sent via PHPMailer with SSL and SMTP.';

    // Send the email
    $mail->send();
    echo 'Email sent successfully.';
} catch (Exception $e) {
    echo 'Email could not be sent. Error: ', $mail->ErrorInfo;
}

?>