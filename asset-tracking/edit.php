<?php
session_start();
if (!isset($_SESSION['email'])) {
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
include_once 'db.php';
$User_ID= $_SESSION['id'];
if(empty($_GET['ID'])){
	header("loation: assets.php");
}else{
	$ID = $_GET['ID'];
}
$result_users = $database->prepare("SELECT * FROM user");
$result_users->execute();
for($i=0; $row = $result_users->fetch(); $i++){
	$id = $row['ID'];
}	
$result_software = $database->prepare("SELECT * FROM asset WHERE ID = " . $ID);
$result_software->execute();
for($i=0; $row = $result_software->fetch(); $i++){
	$name = $row['name'];
	$activatiecode = $row['activatiecode'];
	$info = $row['info'];
}	
if(isset($_POST['Save'])) {
	$error = 0;
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
	if (!empty($_POST['name'])){
	    $name = htmlspecialchars($_POST['name']);

	}else{
	    $error++;
	    $errorMessage = "Naam is leeg";
	}
	if (!empty($_POST['activatiecode'])){
	    $activatiecode  = htmlspecialchars($_POST['activatiecode']);

	}else{
	    $error++;
	    $errorMessage = "activatiecode  is leeg";
	}
	if (!empty($_POST['info'])){
	    $info = htmlspecialchars($_POST['info']);

	}else{
	    $error++;
	    $errorMessage = "info is leeg";
	}
    if ($error == 0) {
	    $query = "UPDATE asset SET name=:name, activatiecode =:activatiecode , info=:info WHERE ID= :ID";
	    $stmt = $database->prepare($query);

	    $stmt->bindValue(":ID", $ID, PDO::PARAM_STR);
		$stmt->bindValue(":name", $name, PDO::PARAM_STR);
		$stmt->bindValue(":activatiecode", $activatiecode , PDO::PARAM_STR);
		$stmt->bindValue(":info", $info, PDO::PARAM_STR);

	    try {
	        $stmt->execute();
	    }
	    catch (PDOException $e) {
	        echo $e->getMessage();
	    }
	    header('location: assets.php');	
	}else{?>
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
	<link type="text/css" rel="stylesheet" href="css/materialize.min.css"  media="screen,projection"/>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<title>Edit <?= $name ?></title>
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
  <body class="login_body">
	<div class="row">
		<form class="col s12" id="form_full" action="" method="post">
		<h4 class="standard-color">Bewerk asset</h4>
			<div class="row">
				<div class="input-field col s6" id="name">
					<input class="validate" type="text" value="<?=$name?>" required name="name">
	          		<label for="Name">Asset name</label>
	          		<span class="helper-text" data-error="Veld mag niet leeg zijn" data-success="correct">Geef de GPS tracker een naam</span>
				</div>
			</div>
			<div class="row">
				<div class="input-field col s6" id="activatiecode">
					<input class="validate" type="text" value="<?=$activatiecode?>" required name="activatiecode">
	          		<label for="activatiecode">GPS tracker ID</label>
	          		<span class="helper-text" data-error="Moet uniek zijn" data-success="correct">activatiecode  van de tracker (IMEI + korte activatie string)</span>
				</div>
			</div>
			<div class="row">
				<div class="input-field col s6" id="info">
					<input class="validate" type="text" value="<?=$info?>" required name="info">
	          		<label for="Info">Other gps info</label>
	          		<span data-error="Veld mag niet leeg zijn" data-success="correct" class="helper-text">Extra info over de GPS</span>
				</div>
			</div>
		     <div class="input-group">
	      		<button id="Sign-up" class="btn waves-effect standard-bgcolor" type="submit" name="Save">Edit</button>
	    		<h2 class="Cancel"><a href="assets.php" class="Sign-up_Cancel standard-color">Cancel</a></h2>
	    	</div>
	    	</div>
		  </form>
	</div>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script type="text/javascript" src="js/materialize.min.js"></script>
<script src="js/script.js" type="text/javascript"></script>
</html>