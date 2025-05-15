<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/PHPMailer-master/src/SMTP.php';
require 'PHPMailer-master/PHPMailer-master/src/Exception.php';


if (isset($_POST['send'])) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;

        // 🔒 Use your Gmail & App Password here
        $mail->Username = 'eaglebeatsadiii@gmail.com';
        $mail->Password = 'suavzltdxktlfkbd';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Recipients
        $mail->setFrom($_POST['email'], $_POST['name']);
        $mail->addAddress('eaglebeatsadiii@gmail.com'); // Your Gmail

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'New Contact Message from Cleanify';
        $mail->Body = "
            <h4>New Message Received</h4>
            <p><strong>Name:</strong> {$_POST['name']}</p>
            <p><strong>Email:</strong> {$_POST['email']}</p>
            <p><strong>Message:</strong><br>{$_POST['message']}</p>
        ";

        $mail->send();
        // After sending email successfully
        header("Location: contact.php?mail=sent");
        exit();

    } catch (Exception $e) {
        echo "Error sending message: {$mail->ErrorInfo}";
    }
}
?>
