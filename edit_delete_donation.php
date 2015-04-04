<?php

error_reporting(E_ALL ^ E_DEPRECATED);

/*
 * Following code will create a new user row
 * All user details are read from HTTP Post Request
 */

// array for JSON response
$response = array();

// check for required fields
if (!(empty($_POST['donationid']))) {

    $did = $_POST['donationid'];

    // include db connect class
    require_once './db_Connect.php';

    // connecting to db
    $db = new DB_CONNECT();

    if($_POST['tag'] == 'delete_donation'){
        $result = mysql_query("DELETE from donations WHERE donationid = $did");
        // check if row inserted or not
        if ($result) {
            // successfully inserted into database
            $response["success"] = 1;
            $response["message"] = "Donation Deleted.";
            // echoing JSON response
            echo json_encode($response);
        } else {
            // failed to insert row
            $response["success"] = 0;
            $response["message"] = "Oops! An error occurred.";
            // echoing JSON response
            echo json_encode($response);
        }
    }
    else if($_POST['tag'] == 'edit_donation'){
        if (!(empty($_POST['itemCount']) || empty($_POST['desc']))) {

            $desc = $_POST['desc'];
            $itemCount= $_POST['itemCount'];
            $categories= $_POST['categories'];
            $subCategories= $_POST['subCategories'];
            $address1 = $_POST['address1'];
            $address2 = $_POST['address2'];
            $state = $_POST['state'];
            $city = $_POST['city'];
            $phoneNumber = $_POST['phoneNumber'];

            $result = mysql_query("UPDATE donations SET category =
                '$categories', subCategory = '$subCategories', numberOfItems =
                '$itemCount', description = '$desc', phoneNumber =
                '$phoneNumber', caddress1 = '$address1', caddress2 =
                '$address2', cstate = '$state', ccity = '$city' WHERE
                 donationid = $did");
            // check if row inserted or not
            if ($result) {
                // successfully inserted into database
                $response["success"] = 1;
                $response["message"] = "Donation successfully edited.";
                // echoing JSON response
                echo json_encode($response);
            } else {
                // failed to insert row
                $response["success"] = 0;
                $response["message"] = "Oops! An error occurred.";
                // echoing JSON response
                echo json_encode($response);
            }
        }
    } else {
        // required field is missing
        $response["success"] = 0;
        $response["message"] = "Required field(s) is missing";

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
