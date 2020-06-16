<?php
// start session
session_start();
// include function file
include 'functions/global.php';
include 'functions/edit.php';
// if there is no session redirect user to login page
// if session level is 0 redirect user to user page
checkSessionAdmin();

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
	$row = getUserID($database, $ID);
}

// if update user is pressed
if(isset($_POST['Save'])) {
	editUser($database, $ID, $User_ID);
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
	<title>Edit <?= $row['email'] ?></title>
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
					<input class="validate" type="email" value="<?=$row['email']?>" required name="email">
	          		<label for="E-mail address">E-mail address</label>
	          		<span class="helper-text" data-error="Veld mag niet leeg zijn" data-success="correct">voorbeeld@voorbeeld.nl</span>
				</div>
			</div>
			<a class="waves-effect waves-light btn modal-trigger help-bgcolor" href="#modal1">Uitleg wachtwoorden</a>
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
				        <input name="level" type="checkbox" value="1" <?= $row['level'] == 1 ? 'checked' : ''?>  />
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
	  <!-- Modal Structure -->
  <div id="modal1" class="modal">
    <div class="modal-content">
      <h4>Uitleg wachtwoorden</h4>
      <p>Als admin kun je de wachtwoord velden leeg laten.</p>
      <p>Het syteem gebruikt in dat geval het oude wachtwoord van het account</p>
    </div>
    <div class="modal-footer">
      <a href="#!" class="modal-close waves-effect waves-green btn-flat">Sluit</a>
    </div>
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