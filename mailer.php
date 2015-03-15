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
        $mail->FromName = 'ConnectOrph';
        $mail->addAddress($to,$nick);
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

    public function random_string()
    {
        $character_set_array = array();
        $character_set_array[] = array('count' => 7, 'characters' => 'abcdefghijklmnopqrstuvwxyz');
        $character_set_array[] = array('count' => 1, 'characters' => '0123456789');
        $temp_array = array();
        foreach ($character_set_array as $character_set) {
            for ($i = 0; $i < $character_set['count']; $i++) {
                $temp_array[] = $character_set['characters'][rand(0, strlen($character_set['characters']) - 1)];
            }
        }
        shuffle($temp_array);
        return implode('', $temp_array);
    }

    public function random_code()
    {
        return rand(pow(10, 4 - 1) - 1, pow(10, 4) - 1);
    }



}
?>
