<?php

error_reporting(E_ALL ^ E_DEPRECATED);

// array for JSON response
$response = array();

if (!(empty($_POST['email']))) {
    // include db connect class
    require_once __DIR__ . '/db_connect.php';
    // connecting to db
    $db = new DB_CONNECT();

    $email = $_POST['email'];

    //Get oid from the email
    $result = mysql_query("SELECT oid from orphanages WHERE email = '$email'");
    $result = mysql_fetch_array($result);
    $oid = $result["oid"];

    $result = mysql_query("SELECT *FROM donations WHERE oid = $oid ORDER BY claimed_at DESC") or die(mysql_error());

    // check for empty result
    if (mysql_num_rows($result) > 0) {
        // looping through all results
        // products node
        $response["claimedDonations"] = array();

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
            array_push($response["claimedDonations"], $donation);
        }
        // success
        $response["success"] = 1;

        // echoing JSON response
        echo json_encode($response);
    } else {
        // no products found
        $response["success"] = 0;
        $response["message"] = "You have not claimed any donations yet!";

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
