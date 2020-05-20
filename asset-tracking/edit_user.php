<?php
// start session
session_start();
// if there is no session redirect user to login page
if (!isset($_SESSION['email'])) {
	$_SESSION['msg'] = "You must log in first";
    header('location: index.php');
}
// if session level is 0 redirect user to user page
if($_SESSION['level'] == 0){
	$_SESSION['msg'] = "You must log in first";
    header('location: index.php');
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
$result_software = $database->prepare("SELECT * FROM user WHERE ID = " . $ID);
$result_software->execute();
for($i=0; $row = $result_software->fetch(); $i++){
	// Set all the needed variables.
	// the variables will be filled in the form so the user has a better experience editing the asset.
	$email = $row['email'];
	$password = $row['password'];
	$level = $row['level'];
}	
// if update user is pressed
if(isset($_POST['Save'])) {
	// set error to 0 if a if statement is not succes the error var will increase by one. There will also be a specific errormessage assigned to the error.
	// in the end there will be a check if error is 0 if not show error message.
	$error = 0;
	// check in database if the activationkey is already in use
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
	// check if input is empty
	if (!empty($_POST['email'])){ //check if email is not empty
	    $email = htmlspecialchars($_POST['email']);
	}else{
	    $error++;
	    $errorMessage = "E-mailadres is leeg";
	}
	if (!empty($_POST['password_1'])){ //check if password_1 is not empty
	    $password_1 = htmlspecialchars($_POST['password_1']);
	}else{
		// There is no error ++ because the admin does not need the password of the user to update the account.
	}
	if (!empty($_POST['password_2'])){ // check if password_2 is not empty
	    $password_2 = htmlspecialchars($_POST['password_2']);
	}else{
		// There is no error ++ because the admin does not need the password of the user to update the account.
	}
	// if the admin is filling in the password then the system checks if they are not empty
	// if they are longer than 10 characters
	// and if they are the same
	if(!empty($_POST['password_1']) && !empty($_POST['password_2'])){
		// if password_1 and password_2 is not 10 characters
		if(strlen($password_1) < 10 || strlen($password_2) < 10){
	      $error++;
	      $errorMessage= "Password needs to me longer than 10 characters.";
	    }
	    // if password_1 and password_2 are the same
	    // make variable $password_3 (hash variant of $password_1) 
	    // from here $password_3 will be used.
	    if($password_1 == $password_2){
	      $password = $password = password_hash($password_1, PASSWORD_DEFAULT);
	    }else{
	      $error++;
	      $errorMessage= "Password needs to be the same";
	    }
	}
    if(!empty($_POST['level'])){ // check if level is not empty
    	//if the level is filled in the user his account will become a admin account aka level 1.
    	$level = htmlspecialchars($_POST['level']);
    }else{
    	// if level is left unfilled the standard value will be filled in and that is 0 aka user level
    	$level = 0;
    }
    // if there are no errors proceed
    if ($error == 0) {
    	// update user data
	    $query = "UPDATE user SET email=:email, password =:password , level=:level WHERE ID= :ID";
	    $stmt = $database->prepare($query);
	    // all the variables that will be updated
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
	    // all the data is handled succesfully send user to assets.php.
	    header('Location: admin.php');	
	}else{?>
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
	<link type="text/css" rel="stylesheet" href="css/materialize.min.css"  media="screen,projection"/>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<!-- title of page -->
	<title>Edit <?= $email ?></title>
	<!-- icon of page -->
  	<link rel="icon" href="img/favicon.png">
</head>
<!-- nav mobile -->
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
  <!-- nav desktop -->
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
  <!-- edit user form -->
  <body class="login_body">
	<div class="row edit_form"  id="mobile">
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
	<!-- include footer -->
	<?php include('objects/footer.php'); ?>
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