<?php
// start session
session_start();

// if there is no session or level is 1 redirect user to login page
if (empty($_SESSION['email']) || $_SESSION['level'] == 1) {
	$_SESSION['msg'] = "You must log in first";
    header('location: index.php');
}
// if session level is 0 redirect user to user page
if ($_SESSION['level'] == 0) {
	$_SESSION['msg'] = "You belong at the user page";
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
// set user id 
// user id is being used to identify wich items in the database are connected to this account.
$User_ID= $_SESSION['id'];
// include db file
include_once 'db.php';
// request basic info from user.
$result_users = $database->prepare("SELECT * FROM user WHERE ID = ".$User_ID);
$result_users->execute();
for($i=0; $row = $result_users->fetch(); $i++){
	$id = $row['ID'];
	$email = $row['email'];
	$password = $row['password'];	
}	
// if delete is pressed
if(isset($_GET['delete'])){
	// $_Get delete is the id of the asset the id is used to identify the correct asset and delete it
    $query = "DELETE FROM user WHERE ID={$_GET['delete']}";
    $insert = $database->prepare($query);
    $insert->execute();
    ?>
    	<script>window.location.href = "admin.php";</script>
    <?php
}
// if add user is pressed
if(isset($_POST['submit'])) {
	// set error to 0 if a if statement is not succes the error var will increase by one. There will also be a specific errormessage assigned to the error.
	// in the end there will be a check if error is 0 if not show error message.
	$error = 0;
	// check in database if the email is already in use
	$email = htmlspecialchars($_POST['email']);
	$query = "SELECT * FROM user WHERE email = :email LIMIT 1";
	$stmt = $database->prepare($query);
	$results = $stmt->execute(array(":email" => $email));
	$user = $stmt->fetch(PDO::FETCH_ASSOC);
	if ($user) { // if user exists
	    if ($user['email'] == $email) {
		    $error++;
	        $errorMessage= "User already excist";
	    }
	}	
	// check if input data is all filled in
	// check if email is not empty
	if (!empty($_POST['email'])){ 
	    $email = htmlspecialchars($_POST['email']);
	}else{
	    $error++;
	    $errorMessage = "E-mailadres is leeg";
	}
	// check if password_1 is filled in
	if (!empty($_POST['password_1'])){ 
	    $password_1 = htmlspecialchars($_POST['password_1']);
	}else{
	    $error++;
	    $errorMessage = "Password is empty";
	}
	// check if password_2 is filled in
	if (!empty($_POST['password_2'])){
	    $password_2 = htmlspecialchars($_POST['password_2']);
	}else{
	    $error++;
	    $errorMessage = "Please confirm the password";
	}
	//check if passwords are longer than 10 
	if(strlen($password_1) < 10 || strlen($password_2) < 10){ 
      $error++;
      $errorMessage= "Password needs to me longer than 10 characters.";
    }
    // if password_1 and password_2 are the same
    // make variable $password_3 (hash variant of $password_1) 
    // from here $password_3 will be used.
    if($password_1 == $password_2){
      $password_3 = $password_3 = password_hash($password_1, PASSWORD_DEFAULT);
    }else{
      $error++;
      $errorMessage= "Password needs to be the same";
    }
    //check if the level is empty
    if(!empty($_POST['level'])){ 
    	//if the level is filled in the user his account will become a admin account aka level 1.
    	$level = htmlspecialchars($_POST['level']);
    }else{
    	// if level is left unfilled the standard value will be filled in and that is 0 aka user level
    	$level = 0;
    }
    //if error is 0 proceed
	if ($error == 0) { 
		// insert user into database
	    $query = "INSERT INTO user (email, password, level) VALUES (?,?,?)";
	    $insert = $database->prepare($query);
	    // all the variables that will be inserted
	    $data = array("$email", "$password_3", "$level");

	    try {
	        $insert->execute($data);
	    }
	    catch (PDOException $e) {
	        echo $e->getMessage();
	    }
	    // all the data is handled succesfully send user to admin.php.
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
// if update profile is pressed
if(isset($_POST['update'])) {
	// set error to 0 if a if statement is not succes the error var will increase by one. There will also be a specific errormessage assigned to the error.
	// in the end there will be a check if error is 0 if not show error message.
	$error = 0;
	// check in database if the activationkey is already in use
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
	// check if input fields are filled in
	// check if email is not empty
	if (!empty($_POST['email'])){
	    $email = htmlspecialchars($_POST['email']);
	}else{
	    $error++;
	    $errorMessage = "E-mail is leeg";
	}
	// check if password_1 is not empty
	if (!empty($_POST['password_1'])){
	    $password_1 = htmlspecialchars($_POST['password_1']);
	}else{
	    $error++;
	    $errorMessage = "Password is empty";
	}
	// Check if password_2 is not empty
	if (!empty($_POST['password_2'])){
	    $password_2 = htmlspecialchars($_POST['password_2']);
	}else{
	    $error++;
	    $errorMessage = "Please confirm the password";
	}
	//check if passwords are longer than 10 
	if(strlen($password_1) < 10 || strlen($password_2) < 10){ 
      $error++;
      $errorMSG= "Password needs to me longer than 10 characters.";
    }
    // if password_1 and password_2 are the same
	// make variable $password_3 (hash variant of $password_1) 
	// from here $password_3 will be used.
    if($password_1 == $password_2){ // check if passwords are the same
      $password_3 = $password_3 = password_hash($password_1, PASSWORD_DEFAULT);
    }else{
      $error++;
      $errorMSG= "Password needs to be the same";
    }
    // if there are no errors proceed
	if ($error == 0) { 
		// update the user settings in database
	    $query = "UPDATE user SET email=:email, password=:password_3 WHERE ID =:ID";
	    
	   	$stmt = $database->prepare($query);
		// all the variables that will be updated
	    $stmt->bindValue(":ID", $User_ID, PDO::PARAM_STR);
		$stmt->bindValue(":email", $email, PDO::PARAM_STR);
		$stmt->bindValue(":password_3", $password_3, PDO::PARAM_STR);

	    try {
	        $stmt->execute();
	    }
	    catch (PDOException $e) {
	        echo $e->getMessage();
	    }
	    // all the data is handled succesfully send user to admin.php.
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
	<link rel="stylesheet" type="text/css" href="./css/style.css">
  	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  	<link type="text/css" rel="stylesheet" href="./css/materialize.min.css"  media="screen,projection"/>
  	<meta charset="UTF-8">
  	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1"/>
  	<!-- title of page -->
	<title>Home</title>
	<!-- icon of page -->
  	<link rel="icon" href="img/favicon.png">
</head>
<!-- dropdown nav bar desktop -->
<ul id="dropdown1" class="dropdown-content">
    <li><a title="Add Asset" class="modal-trigger grey-text text-darken-1" href="#modal1"><i class="material-icons">add</i>Voeg gebruiker toe</a></li>
    <li><a title="Edit profile" class="modal-trigger grey-text text-darken-1" href="#modal2"><i class="material-icons left ">person</i>Edit profile</a></li>
</ul>
<!-- side nav mobile -->
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
    					<li><a title="Add Asset" class="modal-trigger grey-text text-darken-1" href="#modal1"><i class="material-icons">add</i>Voeg gebruiker toe</a></li>
    					<li><a title="Edit profile" class="modal-trigger grey-text text-darken-1" href="#modal2"><i class="material-icons left ">person</i>Edit profile</a></li>
				    </ul>
		        </div>
	        </li>
	    </ul>
	</li>
    <li><a class="nav" title="Uitloggen" href="?logout=1"><i class="material-icons left">exit_to_app</i>Uitloggen</a></li>  	
  </ul>
  <!-- nav desktop -->
  <nav>
    <div class="nav-wrapper standard-bgcolor">
    	<a href="#" data-target="mobile-demo" class="sidenav-trigger"><i class="material-icons">menu</i></a>
    	<a href="#" class="brand-logo center">Asset Tracking</a>
      <ul id="nav-mobile" class="left hide-on-med-and-down">
		<li class="active"><a title="Home" class="dropdown-trigger" data-target="dropdown1" href="#!"><i class="material-icons left">home</i>Home<i class="material-icons right">arrow_drop_down</i></a></li>
      </ul>
      <ul id="nav-mobile" class="right hide-on-med-and-down">
      		<li class="right"><a title="Uitloggen" href="?logout=1"><i class="material-icons right">exit_to_app</i>Uitloggen</a></li>
      </ul>
    </div> 
  </nav>
<body>
	<?php
	// table to show all users
	echo "
	      <table class='Assets responsive-table centered highlight'>
		      <thead>
			        <tr>
			          <th>E-mailadres</th>
			          <th>Aantal GPS trackers</th>
			          <th>Level</th>
			          <th>Actions</th>
			        </tr>
		        </thead>";
	//Select all the users that are in the database with a count that counts the amount of gps trackers that are registerd to their account 
	$result_assets = $database->prepare("SELECT user.ID, user.email, user.level, (SELECT COUNT(*) FROM asset WHERE asset.user_ID = user.ID) as GPScount FROM user");

	  $result_assets->execute();
	  echo "<tbody>";
	  for($i=0; $row = $result_assets->fetch(); $i++){
	    $id = $row['ID'];
	    // if level = 0 replace it with text "gebruiker"
	    if($row['level'] == 0 ){
	    	$level = "Gebruiker";
	    }elseif($row['level'] == 1){ //if level = 1 replace it with text "Admin"
	    	$level = "Admin";
	    }else{ //if it is a nother number just show the number
	    	$level = $row['level'];
	    }
	    //  echo all the user data in the table 
	    // data like email amount of gps trackers and the user his level.
	   	echo "<tr data-href='edit_user.php?ID=". $id. "'>";
	    echo "<td>" . $row['email'] . "</td>";
	    echo "<td>" . $row['GPScount'] ."</td>";
	    echo "<td>" . $level . "</td>";
	    // edit and delete button
	    echo "
   			<td>
			<a title='Edit' class='link btn-floating  btn standard-bgcolor' href=edit_user.php?ID=". $id."><i class='material-icons'>edit</i></a>
   			<a title='Delete' onclick=\"return confirm('Delete This item?')\" class='link btn-floating btn standard-bgcolor'href='?delete=". $id ."'><i class='material-icons'>delete</i></a>
			</td>";
		echo "</tr>";
	    ?>
	<?php }
		echo "</tbody>";
		echo "</table>"; 
	?>
	<!-- add user modal form -->
	<div id="modal1" class="modal add_assets modal2">
	  <div class="modal-content">
	  	<h4 class="standard-color">Voeg gebruiker toe</h4>
		  <form class="col s12 animate" action="" method="post">
		   	<div class="row">
				<div class="input-field col s12" id="email">
					<input class="validate" type="email" required name="email">
	          		<label for="e-mailadres">E-mailaddress</label>
	          		<span class="helper-text" data-error="Geen correct e-mailadres" data-success="correct">voorbeeld@voorbeeld.nl</span>
				</div>
			<div class="row">
				<div class="input-field col s12" id="password">
					<input minlength="10" required class="validate" type="password" name="password_1">
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
			<div class="row">
				<div class="input-field col s12" id="password">
					<p>
				      <label>
				        <input value="1" name="level" type="checkbox" />
				        <span>Admin?</span>
				      </label>
				    </p>
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
</html>