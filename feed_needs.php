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

    $result = mysql_query("SELECT *FROM needs where oid = $oid ORDER BY created_at DESC") or die(mysql_error());

    // check for empty result
    if (mysql_num_rows($result) > 0) {
        // looping through all results
        // products node
        $response["needs"] = array();

        while ($row = mysql_fetch_array($result)) {
            // temp user array
            $donation = array();
            $donation["needsid"] = $row["needsid"];
            $time = $row["created_at"];
            $timestamp = strtotime( $time );
            $donation["created_at"] = $timestamp;
            $donation["category"] = $row["category"];
            $donation["description"] = $row["description"];
            // push single product into final response array
            array_push($response["needs"], $donation);
        }
        // success
        $response["success"] = 1;

        // echoing JSON response
        echo json_encode($response);
    } else {
        // no products found
        $response["success"] = 0;
        $response["message"] = "Needs list is empty";

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
