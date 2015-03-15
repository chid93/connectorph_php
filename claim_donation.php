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
    $result_getOid = mysql_query("SELECT oid, name,phoneno from orphanages WHERE email = '$email'");
    $result_getOid = mysql_fetch_array($result_getOid);
    $oid = $result_getOid["oid"];
    $orphanageName =$result_getOid["name"];
    $phoneNumber = $result_getOid["phoneno"];
    $claimed = 1;
    $claim_code = $mailIt->random_code();


    // mysql updating a row
    $result_update_donation = mysql_query("UPDATE `donations` SET `claimed` = '$claimed', `claimed_at` = CURRENT_TIMESTAMP, `claim_code` = '$claim_code',
                        `oid` = '$oid' WHERE `donationid` = '$donationid'");

    // check if row inserted or not
    if ($result_update_donation) {
        // successfully updated database
        $response["success"] = 1;
        $response["message"] = "Donation successfully claimed.";
    } else {
        // failed to insert row
        $response["success"] = 0;
        $response["message"] = "Oops! An error occurred while claiming the donation.";

        // echoing JSON response
        echo json_encode($response);
        exit;
    }

    //Get phonenumber from the donationid
    $result_getPNO = mysql_query("SELECT phoneNumber, created_at FROM donations WHERE donationid = $donationid") or die(mysql_error());
    $result_getPNO = mysql_fetch_array($result_getPNO);
    $phoneNumberDonor = $result_getPNO["phoneNumber"];
    $created_at = $result_getPNO["created_at"];

    // check if row inserted or not
    if ($result_getPNO) {
        // successfully inserted into database
        $response["phoneNumber"] = $phoneNumberDonor;

        $body="Claim Code: $claim_code.\nYour donation which was created at $created_at was claimed by the following Orphanage.\nOrphanage Name: $orphanageName\nPhone Number: $phoneNumber";
        $response["body"] = $body;

        // echoing JSON response
        echo json_encode($response);
    } else {
        // failed to insert row
        $response["success"] = 0;
        $response["message"] = "Oops! An error occurred while claiming the donation.";

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
