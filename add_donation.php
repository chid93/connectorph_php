<?php

error_reporting(E_ALL ^ E_DEPRECATED);

/*
 * Following code will create a new user row
 * All user details are read from HTTP Post Request
 */

// array for JSON response
$response = array();

// check for required fields
if (!(empty($_POST['itemCount']) || empty($_POST['desc']) || empty($_POST['email']) )) {

    $desc = $_POST['desc'];
    $itemCount= $_POST['itemCount'];
    $categories= $_POST['categories'];
    $subCategories= $_POST['subCategories'];
    $address1 = $_POST['address1'];
	$address2 = $_POST['address2'];
    $email = $_POST['email'];
	$state = $_POST['state'];
	$city = $_POST['city'];



    // include db connect class
    require_once __DIR__ . '/db_connect.php';

    // connecting to db
    $db = new DB_CONNECT();

    //Check if User already exists
    $result = mysql_query("SELECT uid, phoneno from users WHERE email = '$email'");
    $result = mysql_fetch_array($result);
    $phoneNumber = $result["phoneno"];
    $uid = $result["uid"];
    $claimed = 0;


    // mysql inserting a new row
    $result = mysql_query("INSERT INTO donations( uid, claimed, category, subCategory, numberOfItems, description,
        phoneNumber, caddress1, caddress2, cstate, ccity ) VALUES( '$uid', '$claimed', '$categories' , '$subCategories', '$itemCount',
        '$desc', '$phoneNumber', '$address1','$address2','$state','$city')");

    // check if row inserted or not
    if ($result) {
        // successfully inserted into database
        $response["success"] = 1;
        $response["message"] = "Donation successfully submitted.";

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
