<?php
class Mailer {

    // constructor
    function __construct() {
        require 'PHPMailer/PHPMailerAutoload.php';
    }

    // destructor
    function __destruct() {

    }

    public function mailMe($to, $nick, $subject, $body){
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'chidu93@gmail.com';
        $mail->Password = 'wElcome to my WORLD3T';
        $mail->SMTPSecure = 'tls';
        $mail->From = 'chidu93@gmail.com';
        $mail->FromName = 'ConnectOrph Support';
        $mail->addAddress($to);
        $mail->addReplyTo('chidu93@gmail.com', 'Chid');
        $mail->WordWrap = 50;
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        if(!$mail->send()) {
           echo 'Message could not be sent.'."\r\n";
           echo 'Mailer Error: ' . $mail->ErrorInfo;
           echo "\r\n";
           exit;
        }
        echo 'Message has been sent'."\r\n";
    }
}
?>
