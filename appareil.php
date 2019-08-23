<?php
$response = array();
include 'db_connect.php';
include 'functions.php';
 
//Get the input request parameters
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, TRUE); //convert JSON into array
 
//Check for Mandatory parameters
if(isset($_POST['proprietaire']) && isset($_POST['code'])){
	$proprietaire = $_POST['proprietaire'];
	$code = $_POST['code'];
	
	//Check if user already exist 		
		//Query to register new user
		$insertQuery  = "INSERT INTO appareil (proprietaire, code) VALUES (?,?)";
		if($stmt = $con->prepare($insertQuery)){
			$stmt->bind_param("sd",$proprietaire,$code);
			$stmt->execute();

			$response["status"] = 0;
			$response["message"] = "creation reussi";
			$stmt->close();
		}

}
else{
	$response["status"] = 2;
	$response["message"] ="erreur de creation";
}
echo json_encode($response);
?>