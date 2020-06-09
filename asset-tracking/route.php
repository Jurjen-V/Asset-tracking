<?php
include 'functions/global.php';
include 'functions/route.php';
// start session
session_start();

checkSessionUser();
// if logout is pressed
if (isset($_GET['logout'])) {
	// destroy session
    // redirect to login page
    logOut();
}
// set user id 
// user id is being used to identify wich items in the database are connected to this account.
$User_ID= $_SESSION['id'];

getUser($database, $User_ID);

// if delete is pressed
if(isset($_GET['delete']) || isset($_GET['TS'])){
	deleteRoute($database, $_GET['delete'], $_GET['TS']);
}

$assetName = getRoute($database);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<link rel="stylesheet" type="text/css" href="./css/style.css">
  	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  	<link type="text/css" rel="stylesheet" href="./css/materialize.min.css"  media="screen,projection"/>
  	<meta charset="UTF-8">
  	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1"/>
  	<!-- title of page -->
	<title>Route overzicht <?= $assetName?></title>
	<!-- icon of page -->
  	<link rel="icon" href="img/favicon.png">
</head>
<!-- nav bar mobile -->
<ul class="sidenav" id="mobile-demo">
 	<li class="sidenav-header standard-bgcolor">
        <div class="row">
        	<div class="col s4">
                <h4 class="white-text">Asset tracking</h4>
            </div>
          </div>
    </li>
    <li><a title="Home" class="modal-trigger" href="assets.php"><i class="material-icons left">home</i>Home</a></li>
    <li><a title="Map" href="index.php"><i class="material-icons">map</i>Kaart</a></li>
    <li><a title="Uitloggen" href="?logout=1"><i class="material-icons left">exit_to_app</i>Uitloggen</a></li>
</ul>
<!-- nav desktop -->
<nav>
    <div class="nav-wrapper standard-bgcolor">
    	<a href="#" class="brand-logo center">Asset Tracking</a>
    	<a href="#" data-target="mobile-demo" class="sidenav-trigger"><i class="material-icons">menu</i></a>
      	<ul id="nav-mobile" class="left hide-on-med-and-down">
	      	<li><a title="Home" class="modal-trigger" href="assets.php"><i class="material-icons left">home</i>Home</a></li>
	      	<li><a title="Map" href="index.php"><i class="material-icons left">map</i>Kaart</a></li>
      	</ul>
      	<ul id="nav-mobile" class="right hide-on-med-and-down">
      		<li class="right"><a title="Uitloggen" href="?logout=1"><i class="material-icons right">exit_to_app</i>Uitloggen</a></li>
      	</ul>
    </div> 
  </nav>
<body>
	  <h5 class="left-align">
	  	<!-- show the asset name where we are looking at -->
      Asset naam: <?= $assetName?>
    </h5>
	<?php
	// create a table to show all the traveled routes 
	echo "
	      <table class='Assets responsive-table centered highlight'>
		      <thead>
			        <tr>
			          <th>ASSET_ID</th>
			          <th>latitude</th>
			          <th>longitude</th>
			          <th>DATE</th>
			          <th>Actions</th>
			        </tr>
		        </thead>";
    //select statement to get users assets  his travelled route
    //the select statement is selecting by date and asset id
	$ID = $_GET['ID'];

	$result_assets = $database->prepare("SELECT asset.ID, point.ASSET_ID, point.latitude, point.longitude, asset.name, CAST(TS as DATE) FROM asset INNER JOIN point on asset.trackerID = point.ASSET_ID WHERE point.ASSET_ID='".$_GET['ID']."' GROUP BY CAST(TS AS DATE)");
	$result_assets->execute();
	echo "<tbody>";
	  for($i=0; $row = $result_assets->fetch(); $i++){
	  	// all the variables that we need from the select statement
	    $id = $row['ID'];
	    $ASSET_ID = $row['ASSET_ID'];
	    $latitude = $row['latitude'];
    	$longitude = $row['longitude'];
    	$DATE = $row['CAST(TS as DATE)'];
    	
	    // echo the date and city name in the table
	    // echo "<tbdoy>";
	    echo "<tr>";
	    echo "<td>" . $ASSET_ID . "</td>";
	    echo "<td>" . $latitude ."</td>";
	    echo "<td>" . $longitude ."</td>";
	    echo "<td>" . $DATE ."</td>";
	    echo "
   			<td>
   			<a title='Route' class='link btn-floating  btn standard-bgcolor' href='map.php?TS=$DATE&ID=$ASSET_ID'><i class='material-icons'>directions</i></a>
   			<a title='Delete' onclick=\"return confirm('Delete This item?')\" class='link btn-floating btn standard-bgcolor'href='?ID=".$ASSET_ID."&delete=". $ASSET_ID ."&TS=". $DATE ."'><i class='material-icons'>delete</i></a>
			</td>";	
	    ?>
	<?php } 
	echo "</tbody>";
	echo "</table>"?>
</body>
<!-- script links -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script type="text/javascript" src="js/materialize.min.js"></script>
<script type="text/javascript" src="js/script.js"></script>
<!-- <script type="text/javascript" src="js/locationHistory.js"></script> -->
</html>