<?php
 
/*
 * Following code will list all the products
 */
 
// array for JSON response
$response = array();
 
// include db connect class
include 'db_connect.php';
include 'functions.php'; 
 
// get all products from products table
$query = "SELECT *FROM appareil" or die(mysqli_error());
$result = $con->query($query);
 
// check for empty result
if (mysqli_num_rows($result) > 0) {
    // looping through all results
    // products node
    $response["appareils"] = array();
 
    while ($row = mysqli_fetch_array($result)) {
        // temp user array
        $appareil = array();
        $appareil["id"] = $row["id"];
        $appareil["proprietaire"] = doubleval($row["proprietaire"]);
        $appareil["code"] = doubleval($row["code"]);
 
        // push single product into final response array
        array_push($response["appareils"], $appareil);
    }
    // success
    $response["success"] = 1;
 
    // echoing JSON response
    echo json_encode($response);
} else {
    // no products found
    $response["success"] = 0;
    $response["message"] = "No appareils found";
 
    // echo no users JSON
    echo json_encode($response);
}
?>