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

	//Developer function for setting up the database
	function createDB(){
		//Drop existing schema if it already exists
		$stmt = $this->connection->prepare("DROP SCHEMA eventdb");
		$stmt->execute();
		//Create the database
		$stmt = $this->connection->prepare("CREATE DATABASE IF NOT EXISTS eventdb");
		$stmt->execute();

		//Create the events table
		$stmt = $this->connection->prepare("CREATE TABLE eventdb.Events (
				id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
				event_name VARCHAR(30) NOT NULL,
				event_date DATETIME,
				event_location VARCHAR(30) NOT NULL,
				user_created VARCHAR(30) NOT NULL,
				creation_date TIMESTAMP)");
		$stmt->execute();

		//Create the user table
		$stmt = $this->connection->prepare("CREATE TABLE eventdb.users (
				id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
				username VARCHAR(30) NOT NULL,
				password VARCHAR(60) NOT NULL,
				email VARCHAR(30) NOT NULL,
				creation_date TIMESTAMP
)");

		$stmt->execute();

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
			//Set the session variables for the user and return true
			session_start();
			$_SESSION["username"] = $username;
			return true;
		}
		return false;
	}

	//Descructor when the class object has been destroyed
	function __destruct() {
		//When the object is destroyed close the connection to the database
		$this->connection->close();
	}
}




?>