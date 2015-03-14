<?php

error_reporting(E_ALL ^ E_DEPRECATED);

/*
 * Following code will create a new user row
 * All user details are read from HTTP Post Request
 */

// array for JSON response
$response = array();
// include db connect class
require_once __DIR__ . '/db_connect.php';
// connecting to db
$db = new DB_CONNECT();

$result = mysql_query("SELECT *FROM donations ORDER BY created_at DESC") or die(mysql_error());

// check for empty result
if (mysql_num_rows($result) > 0) {
    // looping through all results
    // products node
    $response["donations"] = array();

    while ($row = mysql_fetch_array($result)) {
        // temp user array
        $donation = array();
        $donation["donationid"] = $row["donationid"];
        $time = $row["created_at"];
        $timestamp = strtotime( $time );
        $donation["created_at"] = $timestamp;
        $donation["category"] = $row["category"];
        $donation["subCategory"] = $row["subCategory"];
        $donation["description"] = $row["description"];
        $donation["numberOfItems"] = $row["numberOfItems"];
        $donation["caddress1"] = $row["caddress1"];
        $donation["caddress2"] = $row["caddress2"];
        $donation["cstate"] = $row["cstate"];
        $donation["ccity"] = $row["ccity"];

        // push single product into final response array
        array_push($response["donations"], $donation);
    }
    // success
    $response["success"] = 1;

    // echoing JSON response
    echo json_encode($response);
} else {
    // no products found
    $response["success"] = 0;
    $response["message"] = "No donations found";

    // echo no users JSON
    echo json_encode($response);
}
?>
