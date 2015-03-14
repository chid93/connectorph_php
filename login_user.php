<?php

error_reporting(E_ALL ^ E_DEPRECATED);

/*
 * Following code will create a new user row
 * All user details are read from HTTP Post Request
 */

// array for JSON response
$response = array();

// check for required fields
if (!(empty($_POST['email']) || empty($_POST['tag']))) {

    $tag = $_POST['tag'];
    // include db connect class
    require_once __DIR__ . '/db_connect.php';
    require '/Mailer.php';

    // connecting to db
    $db = new DB_CONNECT();
    $mailIt = new Mailer();
    $response = array("tag" => $tag, "success" => 0, "error" => 0);

    if ($tag == 'login' && !empty($_POST['password'])) {

        $password = $_POST['password'];
        $email = $_POST['email'];

        // mysql retrieve record from email
        $result = mysql_query("SELECT * FROM users WHERE email = '$email'") or die(mysql_error());

        // check for result
        $no_of_rows = mysql_num_rows($result);
        if ($no_of_rows > 0) {
            $result = mysql_fetch_array($result);
            $salt = $result['salt'];
            $encrypted_password = $result['encrypted_password'];
            //Decrypting password
            $hash = base64_encode(sha1($password . $salt, true) . $salt);

            // check for password equality
            if ($encrypted_password == $hash) {
                // user authentication details are correct
                $response["success"] = 1;
                $response["message"] = "User Authentication Successful";

                // echoing JSON response
                echo json_encode($response);
                exit;
            }
            else{
                //Password does not match
                $response["success"] = 0;
                $response["error"] = 1;
                $response["message"] = "Incorrect Email/Password";
                // echoing JSON response
                echo json_encode($response);
                exit;
            }
        } else {
            // user not found
            $response["success"] = 0;
            $response["error"] = 1;
            $response["message"] = "Incorrect Email/Password.";
            // echoing JSON response
            echo json_encode($response);
            exit;
        }
    }
    if ($tag == 'forgotPassword') {

        $email = $_POST['email'];
        $result = mysql_query("SELECT * FROM users WHERE email = '$email'") or die(mysql_error());

        // check for result
        $no_of_rows = mysql_num_rows($result);
        if ($no_of_rows > 0) {
            // user found
            $user=mysql_fetch_array($result);
            $subject="Reset your password";
            $randomCode=$mailIt->random_string();

            $updatePassResult = mysql_query("UPDATE `users` SET `verificationCode` = '$randomCode' WHERE `email` = '$email'");

            $user_name=$user["name"];
            $body=nl2br("Hi $user_name,\r\n\r\nYou requested to reset the password for your ConnectOrph account with the e-mail address ($email). Kindly use the code below to reset your password!\r\n\r\n$randomCode \r\n\r\nThanks, The ConnectOrph Team\r\n ");
            //$mailIt->mailMe("chidambaram.pl.2011.it@rajalakshmi.edu.in",$user_name,$subject,$body);
            $response["success"] = 1;
            $response["error"] = 0;
            $response["message"] = "Mail instructions to reset password sent";
            // echoing JSON response
            echo json_encode($response);
            exit;

        } else {
            // user not found
            $response["success"] = 0;
            $response["error"] = 1;
            $response["message"] = "This Username is not yet registered";
            // echoing JSON response
            echo json_encode($response);
            exit;
        }

    }
    if ($tag == 'verificationCode') {
        $email = $_POST['email'];
        $vCode = $_POST['vCode'];
        $result = mysql_query("SELECT * FROM users WHERE email = '$email'") or die(mysql_error());
        $user=mysql_fetch_array($result);
        $user_verificationCode = $user["verificationCode"];
        if($vCode == $user_verificationCode){
            //Verification Code matches
            $response["success"] = 1;
            $response["error"] = 0;
            $response["message"] = "Verification Success";
            // echoing JSON response
            echo json_encode($response);
            exit;
        }
        else{
            //Verification Code is Wrong
            $response["success"] = 0;
            $response["error"] = 1;
            $response["message"] = "Invalid Verification Code";
            // echoing JSON response
            echo json_encode($response);
            exit;
        }


    }
    if ($tag == 'resetPassword') {
        $email = $_POST['email'];
        $password = $_POST['password'];
        /**
         * Encrypting password
         * returns salt and encrypted password
         */
        $salt = sha1(rand());
        $salt = substr($salt, 0, 10);
        $encrypted = base64_encode(sha1($password . $salt, true) . $salt);
        $hash = array("salt" => $salt, "encrypted" => $encrypted);
        $encrypted_password = $hash["encrypted"]; // encrypted password
        $salt = $hash["salt"]; // salt

        $result = mysql_query("UPDATE `users` SET `encrypted_password` = '$encrypted_password', `salt` = '$salt', `verificationCode` = '' WHERE `email` = '$email'");
        if($result){
            //Succesfully updated table
            $response["success"] = 1;
            $response["error"] = 0;
            $response["message"] = "Password has been successfully changed";
            // echoing JSON response
            echo json_encode($response);
            exit;
        }
        else{
            //Unable to Update table
            $response["success"] = 0;
            $response["error"] = 1;
            $response["message"] = "Oops some error occured while accessing server!";
            // echoing JSON response
            echo json_encode($response);
            exit;
        }


    }



    // password is missing
    $response["success"] = 0;
    $response["error"] = 1;
    $response["message"] = "Required field(s) is missing";
    // echoing JSON response
    echo json_encode($response);
    exit;



} else {
// required field is missing
$response["success"] = 0;
$response["error"] = 1;

if($_POST['tag'] == 'login')
    $response["message"] = "Required field(s) is missing";
else if($_POST['tag'] == 'changePassword')
    $response["message"] = "Enter your email address";

// echoing JSON response
echo json_encode($response);
}
?>
