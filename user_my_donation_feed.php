<?php

error_reporting(E_ALL ^ E_DEPRECATED);

/*
 * Following code will create a new user row
 * All user details are read from HTTP Post Request
 */

// array for JSON response
$response = array();
$email = $_POST['email'];

if (!(empty($_POST['email']))) {
    // include db connect class
    require_once './db_Connect.php';
    // connecting to db
    $db = new DB_CONNECT();

    $email = $_POST['email'];
    $tag = $_POST['tag'];

    //Get oid from the email
    $result = mysql_query("SELECT uid from users WHERE email = '$email'");
    $result = mysql_fetch_array($result);
    $uid = $result["uid"];

    if($tag == "MyClaimedDonations")
        $result = mysql_query("SELECT *FROM donations WHERE uid = $uid AND claimed = 1 ORDER BY claimed_at DESC") or die(mysql_error());
    else if($tag == "MyUnclaimedDonations")
        $result = mysql_query("SELECT *FROM donations WHERE uid = $uid AND claimed = 0 ORDER BY created_at DESC") or die(mysql_error());
    else
        $result = mysql_query("SELECT *FROM donations WHERE uid = $uid AND claimed = 1 AND delivered = 1 ORDER BY delivered_at DESC") or die(mysql_error());

    // check for empty result
    if (mysql_num_rows($result) > 0) {
        // looping through all results
        // products node
        $response["SelectedDonationsFeed"] = array();
        while ($row = mysql_fetch_array($result)) {
            // temp user array
            $donation = array();
            $donation["donationid"] = $row["donationid"];
            $time = $row["created_at"];
            $timestamp = strtotime($time);
            $donation["created_at"] = $timestamp;
            $donation["category"] = $row["category"];
            $donation["subCategory"] = $row["subCategory"];
            $donation["description"] = $row["description"];
            $donation["numberOfItems"] = $row["numberOfItems"];
            if($tag != "MyUnclaimedDonations"){
                $timeClaim = $row["claimed_at"];
                $timestampClaim = strtotime( $timeClaim );
                $donation["claimed_at"] = $timestampClaim;

                $timeDelivered = $row["delivered_at"];
                $timestampDelivered = strtotime( $timeDelivered );
                $donation["delivered_at"] = $timestampDelivered;

                $donation["claim_code"] = $row["claim_code"];
                $oid = $row["oid"];
                $donation["oid"] = $oid;
                $resultOrph = mysql_query("SELECT *FROM orphanages WHERE oid = $oid") or die(mysql_error());
                $resultOrph = mysql_fetch_array($resultOrph);
                $donation["orphanageName"] = $resultOrph["name"];
                $donation["orphanagePhoneNumber"] = $resultOrph["phoneno"];
                $donation["orphanageAddress1"] = $resultOrph["address1"];
                $donation["orphanageAddress2"] = $resultOrph["address2"];
                $donation["orphanageState"] = $resultOrph["state"];
                $donation["orphanageCity"] = $resultOrph["city"];
            }
            else{
            $donation["phoneNumber"] = $row["phoneNumber"];
            $donation["caddress1"] = $row["caddress1"];
            $donation["caddress2"] = $row["caddress2"];
            $donation["cstate"] = $row["cstate"];
            $donation["ccity"] = $row["ccity"];
            }
            // push single product into final response array
            array_push($response["SelectedDonationsFeed"], $donation);
        }
        // success
        $response["success"] = 1;
        // echoing JSON response
        echo json_encode($response);
    } else {
        // no products found
        $response["success"] = 0;
        if($tag == "MyClaimedDonations")
            $response["message"] = "You have no claimed donations yet!";
        else if($tag == "MyUnclaimedDonations")
            $response["message"] = "You have not made any donations yet!";
        else
            $response["message"] = "You have no delivered donations yet!";
        // echo no users JSON
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
