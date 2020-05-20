<?php
// start session
session_start();
// if there is no session or level is 1 redirect user to login page
if (!isset($_SESSION['email'])) {
	$_SESSION['msg'] = "You must log in first";
    header('location: index.php');
}
// if session level is 1 redirect user to admin page
if ($_SESSION['level'] == 1) {
	$_SESSION['msg'] = "You belong at the admin page";
    header('location: admin.php');
}
// if logout is pressed
if (isset($_GET['logout'])) {
	// destroy session
	session_destroy();
    unset($_SESSION['email']);
    // redirect to login page
    header("location: index.php");
}
// include db file
include_once 'db.php';
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
}
// Make a select call to the database to get the asset variables
// the ID is used to get the correct asset
$result_software = $database->prepare("SELECT * FROM asset WHERE ID = " . $ID);
$result_software->execute();
for($i=0; $row = $result_software->fetch(); $i++){
	// Set all the needed variables.
	// the variables will be filled in the form so the user has a better experience editing the asset.
	$name = $row['name'];
	$activatiecode = $row['activatiecode'];
	$info = $row['info'];
	$seconden = $row['seconden'];
}	
// if update asset is pressed
if(isset($_POST['Save'])) {
	// set error to 0 if a if statement is not succes the error var will increase by one. There will also be a specific errormessage assigned to the error.
	// in the end there will be a check if error is 0 if not show error message.
	$error = 0;
	// check in database if the activationkey is already in use
	$activatiecode = htmlspecialchars($_POST['activatiecode']);
	$query = "SELECT * FROM asset WHERE ID != :ID AND activatiecode = :activatiecode LIMIT 1";
	$stmt = $database->prepare($query);
	$results = $stmt->execute(array(":ID" => $ID, ":activatiecode" => $activatiecode));
	$asset = $stmt->fetch(PDO::FETCH_ASSOC);
	if ($asset) { // if tracker exists
	    if ($asset['activatiecode'] == $activatiecode ) {
		    $error++;
	        $errorMessage= "Die GPS is al geactiveerd";
	    }
	}
	//check in database if the name is already in use
	$name = htmlspecialchars($_POST['name']);
	$query = "SELECT * FROM asset WHERE ID != :ID AND name= :name AND user_ID =:user_ID LIMIT 1";
	$stmt = $database->prepare($query);
	$results = $stmt->execute(array(":name" => $name, ":user_ID" => $User_ID, ":ID" => $ID));
	$asset_naam = $stmt->fetch(PDO::FETCH_ASSOC);
	if ($asset_naam) { // if user exists
	    if ($asset_naam['name'] == $name) {
		    $error++;
	        $errorMessage= "That name is already used";
	    }
	}		
	// check if input is empty
	if (!empty($_POST['name'])){ // if asset name is not empty
	    $name = htmlspecialchars($_POST['name']);

	}else{
	    $error++;
	    $errorMessage = "Naam is leeg";
	}
	if (!empty($_POST['activatiecode'])){ // if asset activationkey is not empty
	    $activatiecode  = htmlspecialchars($_POST['activatiecode']);

	}else{
	    $error++;
	    $errorMessage = "activatiecode  is leeg";
	}
	if (!empty($_POST['info'])){ // if asset info is not empty
	    $info = htmlspecialchars($_POST['info']);

	}else{
	    $error++;
	    $errorMessage = "info is leeg";
	}
	if(!empty($_POST['seconden'])){ // if asset seconds is not empty
		$seconden = htmlspecialchars($_POST['seconden']);
	}else{
	    $error++;
	    $errorMessage = "Seconden is leeg";
	}
    if ($error == 0) { // if error = 0 update the asset
    	// update asset into database
	    $query = "UPDATE asset SET name=:name, activatiecode =:activatiecode , info=:info, seconden=:seconden WHERE ID= :ID";
	    $stmt = $database->prepare($query);
	    // all the variables that will be inserted to the database.
	    $stmt->bindValue(":ID", $ID, PDO::PARAM_STR);
		$stmt->bindValue(":name", $name, PDO::PARAM_STR);
		$stmt->bindValue(":activatiecode", $activatiecode , PDO::PARAM_STR);
		$stmt->bindValue(":info", $info, PDO::PARAM_STR);
		$stmt->bindValue(":seconden", $seconden, PDO::PARAM_STR);

	    try {
	        $stmt->execute();
	    }
	    catch (PDOException $e) {
	        echo $e->getMessage();
	    }
	    // all the data is handled succesfully send user to assets.php.
	    header('location: assets.php');	
	}else{// else show alert box?> 
		<!-- error was not 0 so there was a error -->
		<!-- show a html box that will contain the specified erromessage -->
	   	<div class="alert">
	        <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
	        <strong>Let op!</strong> <?php echo $errorMessage ?>
	    </div><?php
	}
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
	<title>Edit <?= $name ?></title>
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
					<input class="validate" type="text" value="<?=$name?>" required name="name">
	          		<label for="Name">Asset name</label>
	          		<span class="helper-text" data-error="Veld mag niet leeg zijn" data-success="correct">Geef de GPS tracker een naam</span>
				</div>
			</div>
			<div class="row">
				<div class="input-field col s12" id="activatiecode">
					<input class="validate" type="text" value="<?=$activatiecode?>" required name="activatiecode">
	          		<label for="activatiecode ">GPS tracker ID</label>
	          		<span class="helper-text" data-error="Moet uniek zijn" data-success="correct">activatiecode  van de tracker (IMEI + korte activatie string)</span>
				</div>
			</div>
			<div class="row">
				<div class="input-field col s12" id="info">
					<input class="validate" type="text" value="<?=$info?>" required name="info">
	          		<label for="Info">Other gps info</label>
	          		<span data-error="Veld mag niet leeg zijn" data-success="correct" class="helper-text">Extra info over de GPS</span>
				</div>
			</div>
			<div class="row">
				<div class="input-field col s12" id="info">
					    <p class="range-field">
					      <input type="range" name="seconden" id="test5" value="<?=$seconden?>" min="30" max="600" />
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