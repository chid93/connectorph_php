<?php

error_reporting(E_ALL ^ E_DEPRECATED);

/*
 * Following code will create a new user row
 * All user details are read from HTTP Post Request
 */

// array for JSON response
$response = array();

// check for required fields
if (!(empty($_POST['email']) )) {

    $email = $_POST['email'];
    $donationid = $_POST['donationid'];

    // include db connect class
    require_once __DIR__ . '/db_connect.php';
    require '/Mailer.php';

    // connecting to db
    $db = new DB_CONNECT();
    $mailIt = new Mailer();

    //Get oid from the email
    $result = mysql_query("SELECT oid from orphanages WHERE email = '$email'");
    $result = mysql_fetch_array($result);
    $oid = $result["oid"];
    $claimed = 1;
    $claim_code = $mailIt->random_code();


    // mysql inserting a new row
    $result = mysql_query("UPDATE `donations` SET `claimed` = '$claimed', `claimed_at` = CURRENT_TIMESTAMP, `claim_code` = '$claim_code',
                        `oid` = '$oid' WHERE `donationid` = '$donationid'");

    // check if row inserted or not
    if ($result) {
        // successfully inserted into database
        $response["success"] = 1;
        $response["message"] = "Donation successfully claimed.";

        // echoing JSON response
        echo json_encode($response);
    } else {
        // failed to insert row
        $response["success"] = 0;
        $response["message"] = "Oops! An error occurred.";

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
