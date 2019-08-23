<?php
$response = array();
include 'db_connect.php';
include 'functions.php';
 
//Get the input request parameters
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, TRUE); //convert JSON into array
 
//Check for Mandatory parameters
if(isset($_POST['latitude']) && isset($_POST['longitude'])){
	$latitude = $_POST['latitude'];
	$longitude = $_POST['longitude'];
	
	//Check if user already exist 		
		//Query to register new user
		$insertQuery  = "INSERT INTO position (latitude, longitude) VALUES (?,?)";
		if($stmt = $con->prepare($insertQuery)){
			$stmt->bind_param("dd",$latitude,$longitude);
			$stmt->execute();

			$response["status"] = 0;
			$response["message"] = "position created";
			$stmt->close();
		}

}
else{
	$response["status"] = 2;
	$response["message"] = "erreur de creation";
}
echo json_encode($response);
?>