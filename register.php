<?php
$response = array();
include 'db_connect.php';
include 'functions.php';
 
//Get the input request parameters
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, TRUE); //convert JSON into array
 
//Check for Mandatory parameters
if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['full_name'])){
	$username = $_POST['username'];
	$password = $_POST['password'];
	$fullName = $_POST['full_name'];
	
	//Check if user already exist
	if(!userExists($username)){
 
		//Get a unique Salt
		$salt         = getSalt();
		
		//Generate a unique password Hash
		$passwordHash = password_hash(concatPasswordWithSalt($password,$salt),PASSWORD_DEFAULT);

		//Query to register new user
		$insertQuery  = "INSERT INTO member(username, full_name, password_hash, salt) VALUES (?,?,?,?)";
		if($stmt = $con->prepare($insertQuery)){
			$stmt->bind_param("ssss",$username,$fullName,$passwordHash,$salt);
			$stmt->execute();

			$response["status"] = 0;
			$response["message"] = "User created";
			$stmt->close();
			$con->close();
		}
	}
	else{
		$response["status"] = 1;
		$response["message"] = "User exists";
	}
}
else{
	$response["status"] = 2;
	$response["message"] = "y as un blem";
}
echo json_encode($response);
?>