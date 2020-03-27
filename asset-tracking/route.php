<?php
session_start();
if (empty($_SESSION['email']) || $_SESSION['level'] == 1) {
	$_SESSION['msg'] = "You must log in first";
    header('location: index.php');
}
if ($_SESSION['level'] == 1) {
	$_SESSION['msg'] = "You belong at the admin page";
    header('location: admin.php');
}
if (isset($_GET['logout'])) {
	session_destroy();
    unset($_SESSION['email']);
    header("location: index.php");
}
$User_ID= $_SESSION['id'];
include_once 'db.php';
if(isset($_POST['TEST'])){
	$location = array("POINT(52.972565, 5.612297)", "POINT(52.966623, 5.590024)", "POINT(52.967554, 5.545907)", "POINT(52.991894, 5.553374)", "POINT(52.985539, 5.583587)", "POINT(52.972565, 5.612297)");
	$length = count($location);
	for($i = 0; $i<$length; $i++) {
		$ASSET_ID = $_GET['ID'];
		$sql = "INSERT INTO `point` SET `latlong` = ".$location[$i] . ", ASSET_ID = ".$ASSET_ID;
		$insert = $database->prepare($sql);
		$data = array("$location[$i]", "$ASSET_ID");
		print_r($data);
		try 
		{
		    $insert->execute($data);
			print_r($insert);
		}
		catch(PDOException $e)
		{               
	    	echo $e->getMessage();
		}
	} 
}
$result_users = $database->prepare("SELECT * FROM user WHERE ID = ".$User_ID);
$result_users->execute();
for($i=0; $row = $result_users->fetch(); $i++){
	$id = $row['ID'];
	$email = $row['email'];
	$password = $row['password'];	
}	
// if delete is pressed
if(isset($_GET['delete']) || isset($_GET['TS'])){
	$route_id= $_GET['ID'];
    $query = "DELETE FROM point WHERE ASSET_ID={$_GET['delete']} AND CAST(point.TS AS DATE) = '{$_GET['TS']}' ";
    $delete = $database->prepare($query);
    $delete->execute();
    header('location: route.php?ID='.$route_id);
}
$result_name = $database->prepare("SELECT asset.ID, point.ASSET_ID, asset.name, CAST(point.TS AS DATE), asset.activatiecode, asset.info, (SELECT ST_X(latlong)) AS LAT, (SELECT ST_y(latlong)) AS LON FROM asset INNER JOIN point on asset.ID = point.ASSET_ID WHERE point.ASSET_ID=".$_GET['ID']." GROUP BY CAST(TS AS DATE)");
$result_name->execute();
$result = $result_name->fetch(PDO::FETCH_ASSOC);
$assetName= "";
if(!$result){
	$errorMessage = "Geen afgelegde routes gevonden";
	?>
	<div class="alert">
	   	<span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
	    <strong>Let op!</strong> <?php echo $errorMessage ?>
	</div><?php
}else{
	$assetName = $result['name'];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<link rel="stylesheet" type="text/css" href="./css/style.css">
  	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  	<link type="text/css" rel="stylesheet" href="./css/materialize.min.css"  media="screen,projection"/>
  	<meta charset="UTF-8">
  	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1"/>
	<title>Route overzicht <?= $assetName?></title>
	<!-- icon of page -->
  	<link rel="icon" href="img/favicon.png">
</head>
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
	<form method="post">
		<button class="btn waves-effect standard-bgcolor" type="submit" name="TEST">send test</button>
	</form>
	  <h5 class="left-align">
      Asset naam: <?= $assetName?>
    </h5>
	<?php
	echo "
	      <table class='Assets centered highlight'>
		      <thead>
			        <tr>
			          <th>Datum</th>
			          <th>Start locatie</th>
			          <th>Actions</th>
			        </tr>
		        </thead>";

	$result_assets = $database->prepare("SELECT asset.ID, point.ASSET_ID, asset.name, CAST(point.TS AS DATE), asset.activatiecode, asset.info, (SELECT ST_X(latlong)) AS LAT, (SELECT ST_y(latlong)) AS LON FROM asset INNER JOIN point on asset.ID = point.ASSET_ID WHERE point.ASSET_ID=".$_GET['ID']." GROUP BY CAST(TS AS DATE)");
	$result_assets->execute();
	  for($i=0; $row = $result_assets->fetch(); $i++){
	    $id = $row['ID'];
	    $ASSET_ID = $row['ASSET_ID'];
	    $lat = $row['LAT'];
    	$lng = $row['LON'];

	    $url = 'https://api.opencagedata.com/geocode/v1/json?q='.$lat.','.$lng.'&key=5b104f01c9434e3dad1e2d6a548445da&language=nl&pretty=1'; 
	    $json = @file_get_contents($url);
	    $data = json_decode($json, true);
	    $results = $data['results'];
	    if (is_array($data)){
		    foreach($results as $results) {
		    	$components = $results['components'];
		    	$city = $components['suburb'];
			}
	    }
	    $DATE= $row['CAST(point.TS AS DATE)'];
	    // echo "<tbdoy>";
	    echo "<tr>";
	    echo "<td>" . $DATE . "</td>";
	    echo "<td>" . $city ."</td>";
	    echo "
   			<td>
   			<a title='Route' class='link btn-floating  btn standard-bgcolor' href='map.php?TS=$DATE&ID=$id'><i class='material-icons'>directions</i></a>
   			<a title='Delete' onclick=\"return confirm('Delete This item?')\" class='link btn-floating btn standard-bgcolor'href='?ID=".$_GET['ID']."&delete=". $ASSET_ID ."&TS=". $DATE ."'><i class='material-icons'>delete</i></a>
			</td>";	
	    ?>
	<?php } ?>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script type="text/javascript" src="js/materialize.min.js"></script>
<script type="text/javascript" src="js/script.js"></script>
</html>