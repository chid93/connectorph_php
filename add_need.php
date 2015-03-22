<?php

error_reporting(E_ALL ^ E_DEPRECATED);

// array for JSON response
$response = array();

// check for required fields
if (!(empty($_POST['categories']) || empty($_POST['desc']) || empty($_POST['email']) )) {

    $desc = $_POST['desc'];
    $categories= $_POST['categories'];
    $email = $_POST['email'];



    // include db connect class
    require_once __DIR__ . '/db_connect.php';

    // connecting to db
    $db = new DB_CONNECT();

    //Check if User already exists
    $result = mysql_query("SELECT oid from orphanages WHERE email = '$email'");
    $result = mysql_fetch_array($result);
    $oid = $result["oid"];

    // mysql inserting a new row
    $result = mysql_query("INSERT INTO needs( oid, category, description) VALUES('$oid', '$categories', '$desc')");

    // check if row inserted or not
    if ($result) {
        // successfully inserted into database
        $response["success"] = 1;
        $response["message"] = "Need successfully added.";

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
