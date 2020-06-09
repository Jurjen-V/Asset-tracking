<?php
// start session
session_start();
// include function file
include 'functions/global.php';
include 'functions/admin.php';

checkSessionAdmin();
// set user id 
// user id is being used to identify wich items in the database are connected to this account.
$User_ID= $_SESSION['id'];

// request basic info from user.
$email = getUser($database, $User_ID);
// if delete is pressed
if(isset($_GET['delete'])){
	deleteUser($database, $_GET['delete']);
}
// if add user is pressed
if(isset($_POST['submit'])) {
	submitUser($database);
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