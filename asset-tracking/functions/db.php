<?php
/*In this document the function db_connect is defined. 
db_connect is used to make a connection to database by pdo */

// this function is used to open a connection to the database. the function is used in all files that talk with the database.
function db_connect(){
	// wachtwoorden db lokaal
	$dbhost ="localhost";
	$dbname ="leaflet";
	$user = "root";
	$pass = "";

	// hosting database conn
	// wachtwoorden db hosting
	// $dbhost ="localhost:3306";
	// $dbname ="Leaflet";
	// $user = "Leaflet";
	// $pass = "leFr530$";

	// start connection with db
	try{
		$database = new PDO('mysql:host='.$dbhost.';dbname='.$dbname, $user, $pass, array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));
	}catch(PDOException $e){
		echo $e->getMessage();
	}
	return $database;
}
?>