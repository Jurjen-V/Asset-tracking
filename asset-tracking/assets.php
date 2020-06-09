 <?php
// start session
session_start();
// include function file
include 'functions/AssetsFunctions.php';
include 'functions/global.php';

checkSessionUser();

// if logout is pressed
if (isset($_GET['logout'])) {
	logOut();
}
// set user id 
// user id is being used to identify wich items in the database are connected to this account.
$User_ID= $_SESSION['id'];

$email = getUser($database, $User_ID);

$startTime = getTS($database);

if(isset($_GET['delete'])){
	deleteAsset($database, $_GET['delete']);
}

// if add asset is pressed
if(isset($_POST['submit'])) {
	submitAsset($database, $User_ID);
}


// if update profile is pressed
if(isset($_POST['update'])) {
	updateProfile($database, $User_ID);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<link rel="stylesheet" type="text/css" href="./css/style.css">
  	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  	<link type="text/css" rel="stylesheet" href="./css/materialize.min.css"  media="screen,projection"/>
  	<meta charset="UTF-8">
  	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
	<!-- title pagina -->
	<title>Homepagina van <?= $email?></title>
	<!-- icon pagina -->
	<link rel="icon" href="img/favicon.png">
</head>
<body>
<header>
<!-- dropdown for desktop nav -->
<ul id="dropdown1" class="dropdown-content">
    <li><a title="Add Asset" class="modal-trigger grey-text text-darken-1" href="#modal1"><i class="material-icons">add</i>Voeg asset toe</a></li>
    <li><a title="Edit profile" class="modal-trigger grey-text text-darken-1" href="#modal2"><i class="material-icons left ">person</i>Edit profile</a></li>
</ul>
<!-- sidenav mobile -->
 <ul class="sidenav" id="mobile-demo">
 	<li class="sidenav-header standard-bgcolor ">
        <div class="row">
        	<div class="col s4">
            	<h4 class="white-text">Asset tracking</h4>
            </div>
        </div>
    </li> 
    <li class="no-padding nav">
	    <ul class="collapsible collapsible-accordion">
	    	<li>
		      	<a class="collapsible-header"><i class="material-icons left">home</i>Home<i class="material-icons right">arrow_drop_down</i></a>
		  		<div class="collapsible-body">
		            <ul>
				        <li><a title="Add Asset" class="modal-trigger grey-text text-darken-1" href="#modal1"><i class="material-icons">add</i>Voeg asset toe</a></li>
    					<li><a title="Edit profile" class="modal-trigger grey-text text-darken-1" href="#modal2"><i class="material-icons left ">person</i>Edit profile</a></li>
				    </ul>
		        </div>
	        </li>
	    </ul>
	</li>
	<li><a class="nav" title="Map" href="index.php"><i class="material-icons">map</i>Kaart</a></li>
    <li><a class="nav" title="Uitloggen" href="?logout=1"><i class="material-icons left">exit_to_app</i>Uitloggen</a></li>  	
  </ul>
  <!-- nav desktop -->
  <nav>
    <div class="nav-wrapper standard-bgcolor">
    	<a href="#" data-target="mobile-demo" class="sidenav-trigger"><i class="material-icons">menu</i></a>
    	<a href="#" class="brand-logo center">Asset Tracking</a>
      <ul id="nav-mobile" class="left hide-on-med-and-down">
		<li class="active"><a title="Home" class="dropdown-trigger" data-target="dropdown1" href="#!"><i class="material-icons left">home</i>Home<i class="material-icons right">arrow_drop_down</i></a></li>
      	<li><a title="Map" href="index.php"><i class="material-icons left">map</i>Kaart</a></li>
      </ul>
      <ul id="nav-mobile" class="right hide-on-med-and-down">
      		<li class="right"><a title="Uitloggen" href="?logout=1"><i class="material-icons right">exit_to_app</i>Uitloggen</a></li>
      </ul>
    </div> 
  </nav>
</header>
<main>
	<?php
	// table to show all assets of user
	echo "
		<table class='Assets responsive-table centered highlight'>
		    <thead>
			    <tr>
			        <th>TrackerID</th>			
			    	<th>Asset name</th>
			    	<th>activatiecode</th>
			        <th>Info</th>
			        <th>Seconden</th>
			        <th>Actions</th>
			    </tr>
		    </thead>";
	//select statement to get users assets  
	// Variable $User_ID is used to select the correct data that is connected to the user his account
	// User_ID is created from $_session['id'];
	$result_assets = $database->prepare("SELECT * FROM asset WHERE user_ID=".$User_ID);

	$result_assets->execute();
	// loop database results
	echo "<tbody>";
	for($i=0; $row = $result_assets->fetch(); $i++){
	    $id = $row['ID'];
	    $trackerID = $row['trackerID'];
	    // the TR row is clickable it will redirect to view asset routes page.	    

	    echo "<tr data-href='route.php?ID=". $id. "'>";
	   	echo "<td>" . $row['trackerID'] . "</td>";
	    echo "<td>" . $row['name'] . "</td>";
	    echo "<td>" . $row['activatiecode'] . "</td>";
	   	echo "<td>" . $row['info'] . "</td>";
	   	echo "<td>" . $row['seconden'] . "</td>";
	    echo "
   			<td>
   			<a title='Route' class='link btn-floating  btn standard-bgcolor' href=route.php?ID=". $trackerID."><i class='material-icons'>visibility</i></a>
			<a title='Edit' class='link btn-floating  btn standard-bgcolor' href=edit.php?ID=". $id."><i class='material-icons'>edit</i></a>
   			<a title='Delete' onclick=\"return confirm('Delete This item?')\" class='link btn-floating btn standard-bgcolor'href='?delete=". $id ."'><i class='material-icons'>delete</i></a>
			</td>";
			echo "</tr>";
	    ?>
	<?php }
		echo "</tbody>";
		echo "</table>";
	  ?>

	<!-- add asset form -->
	<div id="modal1" class="modal add_assets modal2">
	  <div class="modal-content">
	  	<h4 class="standard-color">Voeg asset toe</h4>
		  <form class="col s12 animate" action="" method="post">
		   	<div class="row">
				<div class="input-field col s12" id="name">
					<input id="Name" class="validate" type="text" required name="name">
	          		<label for="Name">Asset name</label>
	          		<span class="helper-text" data-error="Veld mag niet leeg zijn" data-success="correct">Geef de GPS tracker een naam</span>
				</div>
				<div class="input-field col s12" id="activatiecode">
					<input id="Activatiecode" class="validate" type="text" required name="activatiecode">
	          		<label for="Activatiecode">Activatiecode</label>
	          		<span class="helper-text" data-error="Moet uniek zijn" data-success="correct">Activatiecode van de tracker (IMEI + korte activatie string)</span>
				</div>
				<div class="input-field col s12" id="info">
					<input id="Info" class="validate" type="text" required name="info">
	          		<label for="Info">Other gps info</label>
	          		<span data-error="Veld mag niet leeg zijn" data-success="correct" class="helper-text">Extra info over de GPS</span>
				</div>
				<div class="input-field col s12" id="seconden">
					<p class="range-field">
						<input type="range" name="seconden" required id="test5" value="60" min="30" max="600" />
					</p>
	          		<span data-error="Veld mag niet leeg zijn" data-success="correct" class="helper-text">Aantal seconden tot nieuwe locatie?.</span>
				</div>
			     <div class="input-group">
		      		<button id="submit" class="btn waves-effect standard-bgcolor" type="submit" name="submit">Add</button>
		      		<button id="Cancel_add" type="button" class="btn waves-effect  modal-close" >Cancel</button>
		    	</div>
			</div>
	    	</div>
		  </form>
		</div>
	</div>
</main>
<?php 
// include update profile modal and footer
include('objects/update-profile.php');
// include('objects/footer.php'); 
?>
</body>
<!-- script links -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script type="text/javascript" src="js/materialize.min.js"></script>
<script type="text/javascript" src="js/script.js"></script>
<?php 
if(empty($_GET['GpsUpdated'])){?>
	<script type="text/javascript">
		var locationTrackerID = "<?= $trackerID?>";
		var startTime = "<?=$startTime?>";
	</script>
	<script type="text/javascript" src="js/GPS.js"></script>
<?php
}
?>
<p id="demo"></p>
</html>