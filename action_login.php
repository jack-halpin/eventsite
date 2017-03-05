<?php
	require "DatabaseHelper.php";
	//Connect to the database
	$db = new DatabaseHelper();

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$username = test_input($_POST["uname"]);
	  	$password = test_input($_POST["psw"]);
	}

	function test_input($data) {
	  $data = trim($data);
	  $data = stripslashes($data);
	  $data = htmlspecialchars($data);
	  return $data;
	}

	//Next we have to validate that the username is correct
	if($db->verifyUser($username, $password)){
		//Either redirect to users profile page/main page or render the page here
		echo "Logged in succesfully!";

	}
	else{
		//Display an error message
	}

?>