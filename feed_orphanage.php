<?php

error_reporting(E_ALL ^ E_DEPRECATED);

// array for JSON response
$response = array();

// include db connect class
require_once __DIR__ . '/db_connect.php';
// connecting to db
$db = new DB_CONNECT();

if (!(empty($_POST['state']) || empty($_POST['city']))){
    $state = $_POST['state'];
    $city = $_POST['city'];
    $result = mysql_query("SELECT *FROM orphanages WHERE state = '$state' AND city = '$city' ORDER BY name") or die(mysql_error());
}
else
    $result = mysql_query("SELECT *FROM orphanages ORDER BY name") or die(mysql_error());

// check for empty result
if (mysql_num_rows($result) > 0) {
    // looping through all results
    // products node
    $response["orphanagesArray"] = array();

    while ($row = mysql_fetch_array($result)) {
        // temp user array
        $orphanage = array();
        //$orphanage["orphanageID"] = $row["oid"];
        $orphanage["orphanageEmail"] = $row["email"];
        $orphanage["orphanageName"] = $row["name"];
        $orphanage["orphanageMission"] = $row["mission"];
        $orphanage["orphanageAddress1"] = $row["address1"];
        $orphanage["orphanageAddress2"] = $row["address2"];
        $orphanage["orphanageState"] = $row["state"];
        $orphanage["orphanageCity"] = $row["city"];
        // push single product into final response array
        array_push($response["orphanagesArray"], $orphanage);
    }
    // success
    $response["success"] = 1;

    // echoing JSON response
    echo json_encode($response);
} else {
    // no products found
    $response["success"] = 0;
    $response["message"] = "No orphanages found";
    // echo no users JSON
    echo json_encode($response);
}
?>
