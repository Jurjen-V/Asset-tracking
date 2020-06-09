<?php
// start session
session_start();
// include function file

include 'functions/global.php';
include 'functions/edit.php';

// if there is no session or level is 1 redirect user to login page
// if session level is 1 redirect user to admin page
checkSessionUser();

// if logout is pressed
if (isset($_GET['logout'])) {
	logOut();
}

// set user id 
// user id is being used to identify wich items in the database are connected to this account.
$User_ID= $_SESSION['id'];
// check if $_GET['ID'] is set else because it is needed to edit asset data.
if(empty($_GET['ID'])){
	//send user to assets page if id is empty
	header("location: assets.php");
}else{
	//else set the $_get['ID] to a variable
	// the variable will be used to select the asset and update it
	$ID = $_GET['ID'];
	$row = getAsset($database, $ID);
}

// if update asset is pressed
if(isset($_POST['Save'])) {
	editAsset($database, $ID, $User_ID);
}	
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  	<script src='https://kit.fontawesome.com/a076d05399.js'></script>
	<link type="text/css" rel="stylesheet" href="css/materialize.min.css"  media="screen,projection"/>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<!-- Title of page -->
	<title>Edit <?= $row['name'] ?></title>
	<!-- icon of page -->
  	<link rel="icon" href="img/favicon.png">
</head>
<!-- nav for mobile -->
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
  <!-- nav for desktop -->
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
  <body class="login_body">
	<div class="row edit_form" id="mobile">
		<form class="col s6" id="form_full" action="" method="post">
		<h4 class="standard-color">Bewerk asset</h4>
			<div class="row">
				<div class="input-field col s12" id="name">
					<input class="validate" type="text" value="<?=$row['name']?>" required name="name">
	          		<label for="Name">Asset name</label>
	          		<span class="helper-text" data-error="Veld mag niet leeg zijn" data-success="correct">Geef de GPS tracker een naam</span>
				</div>
			</div>
			<div class="row">
				<div class="input-field col s12" id="activatiecode">
					<input class="validate" type="text" value="<?=$row['activatiecode']?>" required name="activatiecode">
	          		<label for="activatiecode ">GPS tracker ID</label>
	          		<span class="helper-text" data-error="Moet uniek zijn" data-success="correct">activatiecode  van de tracker (IMEI + korte activatie string)</span>
				</div>
			</div>
			<div class="row">
				<div class="input-field col s12" id="info">
					<input class="validate" type="text" value="<?=$row['info']?>" required name="info">
	          		<label for="Info">Other gps info</label>
	          		<span data-error="Veld mag niet leeg zijn" data-success="correct" class="helper-text">Extra info over de GPS</span>
				</div>
			</div>
			<div class="row">
				<div class="input-field col s12" id="info">
					    <p class="range-field">
					      <input type="range" name="seconden" id="test5" value="<?=$row['seconden']?>" min="30" max="600" />
					    </p>
	          		<span data-error="Veld mag niet leeg zijn" data-success="correct" class="helper-text">Aantal seconden tot nieuwe locatie?..</span>
				</div>
			</div>
		     <div class="input-group">
	      		<button id="Sign-up" class="btn waves-effect standard-bgcolor" type="submit" name="Save">Edit</button>
	    		<h2 class="Cancel"><a href="assets.php" id="edit_Cancel" class="btn waves-effect modal-close">Cancel</a></h2>
	    	</div>
		  </form>
	</div>
	 <!-- include footer -->
	<?php include('objects/footer.php'); ?>
</body>
<!-- script link -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script type="text/javascript" src="js/materialize.min.js"></script>
<script src="js/script.js" type="text/javascript"></script>
</html>