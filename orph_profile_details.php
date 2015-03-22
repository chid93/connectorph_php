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
    $result = mysql_query("SELECT * from orphanages WHERE email = '$email'");

    // check for empty result
    if ($row = mysql_fetch_array($result)) {
        // success
        $response["success"] = 1;
        // temp user array
        $response["orphanageName"] = $row["name"];
        $response["orphanageEmail"] = $row["email"];
        $response["orphanageMission"] = $row["mission"];
        $response["orphanageWebsite"] = $row["website"];
        $response["orphanagePhoneNumber"] = $row["phoneno"];
        $response["orphanageAddress1"] = $row["address1"];
        $response["orphanageAddress2"] = $row["address2"];
        $response["orphanageState"] = $row["state"];
        $response["orphanageCity"] = $row["city"];

        // echoing JSON response
        echo json_encode($response);
    } else {
        // no orphanage with that email id found (Which is impossible given the
        // way ConnectOrph is designed)
        $response["success"] = 0;
        $response["message"] = "Oops! An error occurred.";
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
