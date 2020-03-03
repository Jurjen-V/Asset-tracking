<?php
$dbhost ="localhost";
$dbname ="leaflet";
$user = "root";
$pass = "";

// hosting database conn

// $dbhost ="localhost:3306";
// $dbname ="Leaflet";
// $user = "Leaflet";
// $pass = "leFr530$";
try{
	$database = new PDO('mysql:host='.$dbhost.';dbname='.$dbname, $user, $pass);
}catch(PDOException $e){
	echo $e->getMessage();
}


?>