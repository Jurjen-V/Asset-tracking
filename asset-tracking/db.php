<?php
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
	$database = new PDO('mysql:host='.$dbhost.';dbname='.$dbname, $user, $pass);
}catch(PDOException $e){
	echo $e->getMessage();
}


?>