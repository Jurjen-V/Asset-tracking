<?php
// start session
session_start();
// if there is no session or level is 1 redirect user to login page
if (empty($_SESSION['email']) || $_SESSION['level'] == 1) {
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
// set user id 
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
    $query = "DELETE FROM asset WHERE ID={$_GET['delete']}";
    $insert = $database->prepare($query);
    $insert->execute();

    // delete from json file
    //get all your data on file
	$string = file_get_contents("json/GPS-tracker.json");
	if ($string === false) {
		// deal with error...
	}

	$json_a = json_decode($string, true);

	if ($json_a === null) {
		// deal with error...
	}
	// get array index to delete
	$arr_index = array();
	foreach ($json_a as $GPS => $GPS_data) {
	    if ($json_a[$GPS]['ID'] == $_GET['delete']) {
	        $arr_index[] = $GPS;
	    }
	}

	// delete data
	foreach ($arr_index as $i) {
	    unset($json_a[$i]);
	}

	// rebase array
	$json_a = array_values($json_a);

	// encode array to json and save to file
	file_put_contents('json/GPS-tracker.json', json_encode($json_a, JSON_PRETTY_PRINT));

    ?>
    	<script>
    	// window.location.href = "assets.php";</script>
    <?php
}
// if add asset is pressed
if(isset($_POST['submit'])) {
	$error = 0;
	$activatiecode = htmlspecialchars($_POST['activatiecode']);
	$query = "SELECT * FROM asset WHERE activatiecode= :activatiecode LIMIT 1";
	$stmt = $database->prepare($query);
	$results = $stmt->execute(array(":activatiecode" => $activatiecode));
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
	if (!empty($_POST['name'])){ //if asset name is not empty
	    $name = htmlspecialchars($_POST['name']);
	}else{
	    $error++;
	    $errorMessage = "Naam is leeg";
	}
	if (!empty($_POST['activatiecode'])){ // if activatiecode is not empty
	    $activatiecode = htmlspecialchars($_POST['activatiecode']);
	}else{
	    $error++;
	    $errorMessage = "Activatiecode is empty";
	}
	if (!empty($_POST['info'])){ //if info is not empty
	    $info = htmlspecialchars($_POST['info']);

	}else{
	    $error++;
	    $errorMessage = "Info is leeg";
	}
	if(!empty($_POST['seconden'])){ //if seconden is not empty
		$seconden = htmlspecialchars($_POST['seconden']);
	}else{
	    $error++;
	    $errorMessage = "Seconden is leeg";
	}
	if ($error == 0) { //if error is 0 proceed
		// insert asset into database
	    $query = "INSERT INTO asset (name, activatiecode, info, user_ID, seconden) VALUES (?,?,?,?,?)";
	    $insert = $database->prepare($query);
	    $data = array("$name", "$activatiecode", "$info" ,"$User_ID", "$seconden");

	    try {
	        $insert->execute($data);
	    }
	    catch (PDOException $e) {
	        echo $e->getMessage();
	    }

	 //    // add asset to api
		// $ch = curl_init();

		// curl_setopt($ch, CURLOPT_URL,"http://www.example.com/tester.phtml");
		// curl_setopt($ch, CURLOPT_POST, 1);

		// // In real life you should use something like:
		// curl_setopt($ch, CURLOPT_POSTFIELDS, 
		//          http_build_query(array('name' => '$name', 'activatiecode' => '$activatiecode', 'info' => '$info', 'user_ID' => '$User_ID', 'seconden' => '$seconden')));

		// Receive server response ...
		// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		// $server_output = curl_exec($ch);

		// curl_close ($ch);

		// Further processing ...
		// if ($server_output == "OK") {
		// }

		// get the id of the gps 
		$last_id = $database->lastInsertId();

		// add data to local json file for testing
		$temp_array = array();
	    $temp_array = json_decode(file_get_contents('json/GPS-tracker.json'));
	    $upload_info = array("ID" => "$last_id" ,"name" => "$name", "activatiecode" => "$activatiecode", "info" => "$info" , "user_ID" => "$User_ID", "seconden" => "$seconden");
	    array_push($temp_array, $upload_info);
	    file_put_contents('json/GPS-tracker.json', json_encode($temp_array, JSON_PRETTY_PRINT));

	    header('location: assets.php');	

	}else{?>
		<!-- else show error message -->
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
	// if email is empty
	if (!empty($_POST['email'])){
	    $email = htmlspecialchars($_POST['email']);

	}else{
	    $error++;
	    $errorMessage = "E-mail is leeg";
	}
	// if password_1 is empty
	if (!empty($_POST['password_1'])){
	    $password_1 = htmlspecialchars($_POST['password_1']);

	}else{
	    $error++;
	    $errorMessage = "Password is empty";
	}
	// if password_2 is empty
	if (!empty($_POST['password_2'])){
	    $password_2 = htmlspecialchars($_POST['password_2']);

	}else{
	    $error++;
	    $errorMessage = "Please confirm the password";
	}
	// if password_1 and password_2 is not 10 characters
	if(strlen($password_1) < 10 || strlen($password_2) < 10){
      $error++;
      $errorMessage= "Password needs to me longer than 10 characters.";
    }
    // if password_1 and password_2 are the same
    if($password_1 == $password_2){
      $password_3 = $password_3 = password_hash($password_1, PASSWORD_DEFAULT);
    }else{
      $error++;
      $errorMessage= "Password needs to be the same";
    }
    // if there are no errors proceed
	if ($error == 0) {
		// update user data
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
		<!-- else show error message -->
	   	<div class="alert">
	        <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
	        <strong>Let op!</strong> <?php echo $errorMessage ?>
	    </div><?php
	}
}
// test json file as api
$string = file_get_contents("json/GPS-tracker.json");
if ($string === false) {
    // deal with error...
}

$json_a = json_decode($string, true);

if ($json_a === null) {
    // deal with error...
}
$array_length = count($json_a);
for ($i=0; $i < $array_length; $i++) { 
	echo $json_a[$i]["ID"];
    echo $json_a[$i]["name"];
    echo $json_a[$i]['activatiecode'];
    echo $json_a[$i]["info"];
    echo $json_a[$i]['user_ID'];
    echo $json_a[$i]["seconden"] ."<br>";
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
			    	<th>Asset name</th>
			    	<th>activatiecode</th>
			        <th>Info</th>
			        <th>Seconden</th>			
			        <th>Actions</th>
			    </tr>
		    </thead>";
	//select statement to get users assets  
	$result_assets = $database->prepare("SELECT * FROM asset WHERE user_ID=".$User_ID);

	$result_assets->execute();
	// loop database results
	for($i=0; $row = $result_assets->fetch(); $i++){
	    $id = $row['ID'];
	    // the TR row is clickable it will redirect to view asset routes page.	    
	    echo "<tr class='clickable-row' data-href='route.php?ID=". $id. "'>";
	    echo "<td>" . $row['name'] . "</td>";
	    echo "<td>" . $row['activatiecode'] . "</td>";
	   	echo "<td>" . $row['info'] . "</td>";
	   	echo "<td>" . $row['seconden'] . "</td>";
	    echo "
   			<td>
   			<a title='Route' class='link btn-floating  btn standard-bgcolor' href=route.php?ID=". $id."><i class='material-icons'>visibility</i></a>
			<a title='Edit' class='link btn-floating  btn standard-bgcolor' href=edit.php?ID=". $id."><i class='material-icons'>edit</i></a>
   			<a title='Delete' onclick=\"return confirm('Delete This item?')\" class='link btn-floating btn standard-bgcolor'href='?delete=". $id ."'><i class='material-icons'>delete</i></a>
			</td>";
	    ?>
	<?php }  ?>

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
				<div class="input-field col s12" id="activatiecode">
					<input class="validate" type="text" required name="activatiecode">
	          		<label for="activatiecode">Activatiecode</label>
	          		<span class="helper-text" data-error="Moet uniek zijn" data-success="correct">Activatiecode van de tracker (IMEI + korte activatie string)</span>
				</div>
				<div class="input-field col s12" id="info">
					<input class="validate" type="text" required name="info">
	          		<label for="Info">Other gps info</label>
	          		<span data-error="Veld mag niet leeg zijn" data-success="correct" class="helper-text">Extra info over de GPS</span>
				</div>
				<div class="input-field col s12" id="seconden">
					<p class="range-field">
						<input type="range" name="seconden" required id="test5" value="60" min="30" max="600" />
					</p>
					<label for="Info">Aantal seconden tot nieuwe locatie?</label>
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
include('objects/update-profile.php');
// styling van footer moet nog beter
//include('objects/footer.php'); 
?>
</body>
<!-- script links -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script type="text/javascript" src="js/materialize.min.js"></script>
<script type="text/javascript" src="js/script.js"></script>
</html>