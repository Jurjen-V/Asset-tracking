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

$result_users = $database->prepare("SELECT * FROM user WHERE ID = ".$User_ID);
$result_users->execute();
for($i=0; $row = $result_users->fetch(); $i++){
	$id = $row['ID'];
	$email = $row['email'];
	$password = $row['password'];	
}	
// if delete is pressed
if(isset($_GET['delete'])){
    $query = "DELETE FROM asset WHERE ID={$_GET['delete']}";
    $insert = $database->prepare($query);
    $insert->execute();
    ?>
    	<script>window.location.href = "assets.php";</script>
    <?php
}
// if add asset is pressed
if(isset($_POST['submit'])) {
	$error = 0;
	$activatiecode = htmlspecialchars($_POST['activatiecode']);
	$query = "SELECT * FROM asset WHERE activatiecode= :activatiecode AND user_ID !=:user_ID LIMIT 1";
	$stmt = $database->prepare($query);
	$results = $stmt->execute(array(":activatiecode" => $activatiecode, ":user_ID" => $User_ID));
	$asset = $stmt->fetch(PDO::FETCH_ASSOC);
	if ($asset) { // if user exists
	    if ($asset['activatiecode'] == $activatiecode) {
		    $error++;
	        $errorMessage= "GPS already excist";
	    }
	}	
	$name = htmlspecialchars($_POST['name']);
	$query = "SELECT * FROM asset WHERE name= :name AND user_ID =:user_ID LIMIT 1";
	$stmt = $database->prepare($query);
	$results = $stmt->execute(array(":name" => $name, ":user_ID" => $User_ID));
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
	    $activatiecode = htmlspecialchars($_POST['activatiecode']);
	}else{
	    $error++;
	    $errorMessage = "Activatiecode is empty";
	}
	if (!empty($_POST['info'])){
	    $info = htmlspecialchars($_POST['info']);

	}else{
	    $error++;
	    $errorMessage = "Info is leeg";
	}
	if ($error == 0) {
	    $query = "INSERT INTO asset (name, activatiecode, info, user_ID) VALUES (?,?,?,?)";
	    $insert = $database->prepare($query);
	    $data = array("$name", "$activatiecode", "$info" ,"$User_ID");

	    try {
	        $insert->execute($data);
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
// if update profile is pressed
if(isset($_POST['update'])) {
	$error = 0;
	$email = htmlspecialchars($_POST['email']);
	$query = "SELECT * FROM user WHERE email = :email AND ID !=:ID LIMIT 1";
	$stmt = $database->prepare($query);
	$results = $stmt->execute(array(":email" => $email, ":ID" => $User_ID));
	$user = $stmt->fetch(PDO::FETCH_ASSOC);
	if ($user) { // if user exists
	    if ($user['email'] == $email) {
		    $error++;
	        $errorMessage= "User already excist";
	    }
	}	

	if (!empty($_POST['email'])){
	    $email = htmlspecialchars($_POST['email']);

	}else{
	    $error++;
	    $errorMessage = "E-mail is leeg";
	}
	if (!empty($_POST['password_1'])){
	    $password_1 = htmlspecialchars($_POST['password_1']);

	}else{
	    $error++;
	    $errorMessage = "Password is empty";
	}
	if (!empty($_POST['password_2'])){
	    $password_2 = htmlspecialchars($_POST['password_2']);

	}else{
	    $error++;
	    $errorMessage = "Please confirm the password";
	}
	if(strlen($password_1) < 10 || strlen($password_2) < 10){
      $error++;
      $errorMSG= "Password needs to me longer than 10 characters.";
    }
    if($password_1 == $password_2){
      $password_3 = $password_3 = password_hash($password_1, PASSWORD_DEFAULT);
    }else{
      $error++;
      $errorMSG= "Password needs to be the same";
    }
	if ($error == 0) {
	    $query = "UPDATE user SET email=:email, password=:password_3 WHERE ID =:ID";
	    
	   	$stmt = $database->prepare($query);

	    $stmt->bindValue(":ID", $User_ID, PDO::PARAM_STR);
		$stmt->bindValue(":email", $email, PDO::PARAM_STR);
		$stmt->bindValue(":password_3", $password_3, PDO::PARAM_STR);

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
	<link rel="stylesheet" type="text/css" href="./css/style.css">
  	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  	<link type="text/css" rel="stylesheet" href="./css/materialize.min.css"  media="screen,projection"/>
  	<meta charset="UTF-8">
  	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
	<title>Homepagina van <?= $email?></title>
</head>
<ul id="dropdown1" class="dropdown-content">
    <li><a title="Add Asset" class="modal-trigger grey-text text-darken-1" href="#modal1"><i class="material-icons">add</i>Voeg asset toe</a></li>
    <li><a title="Edit profile" class="modal-trigger grey-text text-darken-1" href="#modal2"><i class="material-icons left ">person</i>Edit profile</a></li>
</ul>
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
<body>
	<?php
	echo "
	      <table class='Assets responsive-table centered highlight'>
		      <thead>
			        <tr>
			          <th>Asset name</th>
			          <th>activatiecode</th>
			          <th>Info</th>
			          <th>Actions</th>
			        </tr>
		        </thead>";
	$result_assets = $database->prepare("SELECT * FROM asset WHERE user_ID=".$User_ID);

	  $result_assets->execute();
	  for($i=0; $row = $result_assets->fetch(); $i++){
	    $id = $row['ID'];
	    // echo "<tbdoy>";
	    echo "<tr>";
	    echo "<td>" . $row['name'] . "</td>";
	    echo "<td>" . $row['activatiecode'] . "</td>";
	   	echo "<td>" . $row['info'] . "</td>";
	    echo "
   			<td>
   			<a title='Route' class='link btn-floating  btn standard-bgcolor' href=route.php?ID=". $id."><i class='material-icons'>visibility</i></a>
			<a title='Edit' class='link btn-floating  btn standard-bgcolor' href=edit.php?ID=". $id."><i class='material-icons'>edit</i></a>
   			<a title='Delete' onclick=\"return confirm('Delete This item?')\" class='link btn-floating btn standard-bgcolor'href='?delete=". $id ."'><i class='material-icons'>delete</i></a>
			</td>";
		// echo "</tbody>";
	    ?>
	<?php } ?>

	<!-- add asset form -->
	<div id="modal1" class="modal add_assets modal2">
	  <div class="modal-content">
	  	<h4 class="standard-color">Voeg asset toe</h4>
		  <form class="col s12 animate" action="" method="post">
		   	<div class="row">
				<div class="input-field col s12" id="name">
					<input class="validate" type="text" required name="name">
	          		<label for="Name">Asset name</label>
	          		<span class="helper-text" data-error="Veld mag niet leeg zijn" data-success="correct">Geef de GPS tracker een naam</span>
				</div>
			<div class="row">
				<div class="input-field col s12" id="activatiecode">
					<input class="validate" type="text" required name="activatiecode">
	          		<label for="activatiecode">Activatiecode</label>
	          		<span class="helper-text" data-error="Moet uniek zijn" data-success="correct">Activatiecode van de tracker (IMEI + korte activatie string)</span>
				</div>
			</div>
			<div class="row">
				<div class="input-field col s12" id="info">
					<input class="validate" type="text" required name="info">
	          		<label for="Info">Other gps info</label>
	          		<span data-error="Veld mag niet leeg zijn" data-success="correct" class="helper-text">Extra info over de GPS</span>
				</div>
			</div>
		     <div class="input-group">
	      		<button id="submit" class="btn waves-effect standard-bgcolor" type="submit" name="submit">Add</button>
	    	</div>
	      		<button id="Cancel_add" type="button" class="btn waves-effect  modal-close" >Cancel</button>
	    	</div>
		  </form>
		</div>
	</div>
		<!-- Update profile form -->
	<div id="modal2" class="modal add_assets modal2">
	  <div class="modal-content">
	  	<h4 class="standard-color">Update account</h4>
		  <form class="col s12 animate" action="" method="post">
		   	<div class="row">
				<div class="input-field col s12" id="name">
					<input value="<?=$email?>" class="validate" type="email" required name="email">
	          		<label for="Name">E-mail Address</label>
	          		<span class="helper-text" data-error="Geen correct e-mailadres" data-success="correct">voorbeeld@voorbeeld.nl</span>
				</div>
			<div class="row">
				<div class="input-field col s12" id="password">
					<input minlength="10" required type="password" class="validate" name="password_1">
			        <label for="Password">Password</label>
			        <span class="helper-text" data-error="Wachtwoord is te kort" data-success="correct">10 karakters lang</span>
				</div>
			</div>
			<div class="row">
				<div class="input-field col s12" id="password">
					<input minlength="10" required type="password" class="validate" name="password_2">
	        		<label for="Password">Confrim password</label>
	        		<span class="helper-text" data-error="Wachtwoord is te kort" data-success="correct">10 karakters lang</span>
				</div>
			</div>
		     <div class="input-group">
	      		<button id="submit" class="btn waves-effect standard-bgcolor" type="submit" name="update">Update</button>
	    	</div>
	      		<button id="Cancel_add" type="button" class="btn waves-effect  modal-close">Cancel</button>
	    	</div>
		  </form>
		</div>
	</div>

</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script type="text/javascript" src="js/materialize.min.js"></script>
<script type="text/javascript" src="js/script.js"></script>
</html>