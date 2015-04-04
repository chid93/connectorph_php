<?php

error_reporting(E_ALL ^ E_DEPRECATED);
// array for JSON response
$response = array();

if (!(empty($_POST['email']))) {
    // include db connect class
    require_once './db_Connect.php';
    // connecting to db
    $db = new DB_CONNECT();

    $email = $_POST['email'];
    $tag = $_POST['tag'];

    //Get oid from the email
    $result = mysql_query("SELECT oid from orphanages WHERE email = '$email'");
    $result = mysql_fetch_array($result);
    $oid = $result["oid"];
    if($tag == "MyClaimedDonations")
        $result = mysql_query("SELECT *FROM donations WHERE oid = $oid AND delivered = 0 ORDER BY claimed_at DESC") or die(mysql_error());
    else if($tag == "markAsDelivered"){
        $did = $_POST['donationid'];
        $currentTime = (new \DateTime())->format('Y-m-d H:i:s');
        $result = mysql_query("UPDATE donations SET delivered = 1, delivered_at = '$currentTime' WHERE donationid = $did");
        // check if row inserted or not
        if ($result) {
            // successfully updated database
            $response["success"] = 1;
            $response["message"] = "Donation successfully marked as received!";
            // echoing JSON response
            echo json_encode($response);
        } else {
            // failed to insert row
            $response["success"] = 0;
            $response["message"] = "Oops! An error occurred while claiming the donation.";
            // echoing JSON response
            echo json_encode($response);
        }
        exit;
    }
    else
        $result = mysql_query("SELECT *FROM donations WHERE oid = $oid AND delivered = 1 ORDER BY delivered_at DESC") or die(mysql_error());
    // check for empty result
    if (mysql_num_rows($result) > 0 ) {
        // looping through all results
        // products node
        $response["SelectedDonationsFeed"] = array();

        while ($row = mysql_fetch_array($result)) {
            // temp user array
            $donation = array();
            $donation["donationid"] = $row["donationid"];

            $time = $row["created_at"];
            $timestamp = strtotime( $time );
            $donation["created_at"] = $timestamp;

            $timeClaim = $row["claimed_at"];
            $timestampClaim = strtotime( $timeClaim );
            $donation["claimed_at"] = $timestampClaim;

            $timeDelivered = $row["delivered_at"];
            $timestampDelivered = strtotime( $timeDelivered );
            $donation["delivered_time"]=$timeDelivered;
            $donation["delivered_at"] = $timestampDelivered;

            $donation["claim_code"] = $row["claim_code"];
            $donation["category"] = $row["category"];
            $donation["subCategory"] = $row["subCategory"];
            $donation["description"] = $row["description"];
            $donation["numberOfItems"] = $row["numberOfItems"];
            $donation["phoneNumber"] = $row["phoneNumber"];
            $donation["caddress1"] = $row["caddress1"];
            $donation["caddress2"] = $row["caddress2"];
            $donation["cstate"] = $row["cstate"];
            $donation["ccity"] = $row["ccity"];

            $uid = $row["uid"];
            $fetchDonorName = mysql_query("SELECT name FROM users WHERE uid = $uid") or die(mysql_error());
            $fetchDonorName = mysql_fetch_array($fetchDonorName);

            $donation["donorName"] = $fetchDonorName["name"];


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
            $response["message"] = "You have not claimed any donations yet!";
        else
            $response["message"] = "You have not received any donations yet!";

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

