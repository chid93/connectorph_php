<?php

/*
 * Following code will create a new user row
 * All user details are read from HTTP Post Request
 */

// array for JSON response
$response = array();

// check for required fields
if (!(empty($_POST['email']) || empty($_POST['password']))) {

    $password = $_POST['password'];
    $email = $_POST['email'];

    // include db connect class
    require_once __DIR__ . '/db_connect.php';

    // connecting to db
    $db = new DB_CONNECT();

    // mysql inserting a new row
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
        }
        else{
            $response["success"] = 0;
            $response["message"] = "Incorrect Email/Password";
            // echoing JSON response
            echo json_encode($response);
        }
    } else {
        // user not found
        $response["success"] = 0;
        $response["message"] = "Incorrect Email/Password.";
        // echoing JSON response
        echo json_encode($response);
    }

} else {
// required field is missing
$response["success"] = 0;
$response["message"] = "Required field(s) is missing";

// echoing JSON response
echo json_encode($response);
}
?>
