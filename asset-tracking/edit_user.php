<?php
session_start();
if (!isset($_SESSION['email'])) {
	$_SESSION['msg'] = "You must log in first";
    header('location: index.php');
}
if($_SESSION['level'] == 0){
	$_SESSION['msg'] = "You must log in first";
    header('location: index.php');
}
if (isset($_GET['logout'])) {
	session_destroy();
    unset($_SESSION['email']);
    header("location: index.php");
}
include_once 'db.php';
$User_ID= $_SESSION['id'];
if(empty($_GET['ID'])){
	header("location: assets.php");
}else{
	$ID = $_GET['ID'];
}
$result_users = $database->prepare("SELECT * FROM user");
$result_users->execute();
for($i=0; $row = $result_users->fetch(); $i++){
	$id = $row['ID'];
}	
$result_software = $database->prepare("SELECT * FROM user WHERE ID = " . $ID);
$result_software->execute();
for($i=0; $row = $result_software->fetch(); $i++){
	$email = $row['email'];
	$password = $row['password'];
	$level = $row['level'];
}	
if(isset($_POST['Save'])) {
	$error = 0;
	$email = htmlspecialchars($_POST['email']);
	$query = "SELECT * FROM user WHERE email = :email AND ID !=:ID LIMIT 1";
	$stmt = $database->prepare($query);
	$results = $stmt->execute(array(":email" => $email, ":ID" => $ID));
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
	    $errorMessage = "E-mailadres is leeg";
	}
	if (!empty($_POST['password_1'])){
	    $password_1 = htmlspecialchars($_POST['password_1']);
	}else{
	}
	if (!empty($_POST['password_2'])){
	    $password_2 = htmlspecialchars($_POST['password_2']);

	}else{
	}
	if(empty($password)){
		if(strlen($password_1) < 10 || strlen($password_2) < 10){
	      $error++;
	      $errorMSG= "Password needs to me longer than 10 characters.";
	    }
	    if($password_1 == $password_2){
	      $password = $password = password_hash($password_1, PASSWORD_DEFAULT);
	    }else{
	      $error++;
	      $errorMSG= "Password needs to be the same";
	    }
	}
    if(!empty($_POST['level'])){
    	$level = htmlspecialchars($_POST['level']);
    }else{
    	$level = 0;
    }
    if ($error == 0) {
	    $query = "UPDATE user SET email=:email, password =:password , level=:level WHERE ID= :ID";
	    $stmt = $database->prepare($query);

	    $stmt->bindValue(":ID", $ID, PDO::PARAM_STR);
		$stmt->bindValue(":email", $email, PDO::PARAM_STR);
		$stmt->bindValue(":password", $password , PDO::PARAM_STR);
		$stmt->bindValue(":level", $level, PDO::PARAM_STR);

	    try {
	        $stmt->execute();
	    }
	    catch (PDOException $e) {
	        echo $e->getMessage();
	    }
	    header('Location: admin.php');	
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
	<title>Edit <?= $email ?></title>
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
      	<li><a title="Home" class="modal-trigger" href="admin.php"><i class="material-icons left">home</i>Home</a></li>
      	<li><a title="Uitloggen" href="?logout=1"><i class="material-icons left">exit_to_app</i>Uitloggen</a></li>
  </ul>
  <nav>
    <div class="nav-wrapper standard-bgcolor">
    	<a href="#" class="brand-logo center">Asset Tracking</a>
    	<a href="#" data-target="mobile-demo" class="sidenav-trigger"><i class="material-icons">menu</i></a>
      <ul id="nav-mobile" class="left hide-on-med-and-down">
      	<li><a title="Home" class="modal-trigger" href="admin.php"><i class="material-icons left">home</i>Home</a></li>
      </ul>
      <ul id="nav-mobile" class="right hide-on-med-and-down">
      		<li class="right"><a title="Uitloggen" href="?logout=1"><i class="material-icons right">exit_to_app</i>Uitloggen</a></li>
      </ul>
    </div> 
  </nav>
  <body class="login_body">
	<div class="row"  id="mobile">
		<form class="col s6" id="form_full" action="" method="post">
		<h4 class="standard-color">Bewerk gebruiker</h4>
			<div class="row">
				<div class="input-field col s12" id="email">
					<input class="validate" type="email" value="<?=$email?>" required name="email">
	          		<label for="E-mail address">E-mail address</label>
	          		<span class="helper-text" data-error="Veld mag niet leeg zijn" data-success="correct">voorbeeld@voorbeeld.nl</span>
				</div>
			</div>
			<div class="row">
				<div class="input-field col s12" id="password">
					<input minlength="10" type="password" class="validate" name="password_1">
			        <label for="Password">Password</label>
			        <span class="helper-text" data-error="Wachtwoord is te kort" data-success="correct">10 karakters lang</span>
				</div>
			</div>
			<div class="row">
				<div class="input-field col s12" id="password">
					<input minlength="10" type="password" class="validate" name="password_2">
			        <label for="Password">Password</label>
			        <span class="helper-text" data-error="Wachtwoord is te kort" data-success="correct">10 karakters lang</span>
				</div>
			</div>
			<div class="row">
				<div class="input-field col s12" id="password">
					<p>
				      <label>
				        <input name="level" type="checkbox" value="1" <?= $level == 1 ? 'checked' : ''?>  />
				        <span>Admin?</span>
				      </label>
				    </p>
				</div>
			</div>
		     <div class="input-group">
	      		<button id="Sign-up" class="btn waves-effect standard-bgcolor" type="submit" name="Save">Edit</button>
	    		<h2 class="Cancel"><a href="admin.php" id="edit_Cancel" class="btn waves-effect modal-close">Cancel</a></h2>
	    	</div>
	    	</div>
		  </form>
	</div>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script type="text/javascript" src="js/materialize.min.js"></script>
<script src="js/script.js" type="text/javascript"></script>
<script>
$(document).ready(function(){
	$('select').formSelect();
});
</script>
</html>