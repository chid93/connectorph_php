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

    $desc = str_replace("'","''",$desc);
    $categories = str_replace("'","''",$categories);
    $subCategories = str_replace("'","''",$subCategories);
    $address1 = str_replace("'","''",$address1);
    $address2 = str_replace("'","''",$address2);

    // include db connect class
    require_once './db_Connect.php';

    // connecting to db
    $db = new DB_CONNECT();

    //Check if User already exists
    $result = mysql_query("SELECT uid, phoneno from users WHERE email = '$email'");
    $result = mysql_fetch_array($result);
    $phoneNumber = $result["phoneno"];
    $uid = $result["uid"];
    $claimed = 0;
    $currentTime = (new \DateTime())->format('Y-m-d H:i:s');


    // mysql inserting a new row
    $result = mysql_query("INSERT INTO donations( uid, claimed, category, subCategory, numberOfItems, description,
        phoneNumber, caddress1, caddress2, cstate, ccity, created_at ) VALUES( '$uid', '$claimed', '$categories' , '$subCategories', '$itemCount',
        '$desc', '$phoneNumber', '$address1','$address2','$state','$city','$currentTime')");

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
