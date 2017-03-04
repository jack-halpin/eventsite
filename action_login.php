<?php
//Class for accessing database informatino
class DatabaseHelper{

	private $connection;
	private $cursor;

	//Constructor method
	function __construct(){
		$servername = "localhost";
		$username = "root";
		$password = "root";

		// Create connection
		$this->connection = new mysqli($servername, $username, $password);

		// Check connection
		if ($this->connection->connect_error)  {
    		die("Connection failed: " . $this->connection->connect_error);
		}	
		
		echo "Connected successfully";
	}

	//Function to add a new user to the database, if the creation was a succcess return 1, else return 0
	function createUser($username, $password, $email){
		//Need to encrypt the password for storage into the datbase
		

		$hash = password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);
		echo ' ';
		echo $hash;


		//Create the prepared statement and bind the parameters
		$stmt = $this->connection->prepare("INSERT INTO eventdb.users (username, password, email) VALUES (?, ?, ?)");
		$stmt->bind_param("sss", $username, $hash, $email);
		$stmt->execute();

		
	}

	//Function for verifying the identity of a user by comparing their supplied password to the hash in the databse
	function verifyUser($username, $password){
		//Query the database for the user with the supplied username
		$stmt = $this->connection->prepare("SELECT password FROM eventdb.users WHERE username = ?");
		
		$stmt->bind_param("s", $username);
		$stmt->execute();

		//Get the hash from the databse and compare it to the supplied password
		$user = $stmt->get_result();
		$row = $user->fetch_assoc();
		if (password_verify($password, $row["password"])){
			return true;
		}
		return false;

	}

	function __destruct() {
		//When the object is destroyed close the connection to the database
		$this->connection->close();
	}
}

//When user asks to login verity that the user belongs to the database
$db = new DatabaseHelper();

//Get the username and password
$user = $_POST['uname'];
$password = $_POST['psw'];
echo $user;
echo $password;
if ($db->verifyUser($user, $password)){
	echo "You have logged in!";
}
else{
	echo "No user exists with those details.";
}
?>