<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer files
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $message = htmlspecialchars(trim($_POST['message']));

    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';       // SMTP server
        $mail->SMTPAuth   = true;
        $mail->Username   = 'eaglebeatsadiii@gmail.com';  // Your Gmail
        $mail->Password   = 'suavzltdxktlfkbd ';    // Gmail App Password (not your Gmail login)
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom($email, $name);                    // From user
        $mail->addAddress('yourgmail@gmail.com', 'You');  // To yourself

        // Content
        $mail->isHTML(true);
        $mail->Subject = "New Contact Message from Cleanify";
        $mail->Body    = "
            <h3>New Message Received</h3>
            <p><strong>Name:</strong> $name</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>Message:</strong><br>$message</p>
        ";

        $mail->send();
        echo "<script>alert('Message sent successfully!'); window.location.href='contact.html';</script>";
    } catch (Exception $e) {
        echo "<script>alert('Message failed to send. Error: {$mail->ErrorInfo}'); window.location.href='contact.html';</script>";
    }
}
?>
