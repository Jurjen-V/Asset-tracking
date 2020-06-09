<?php
include_once 'db.php';

$database = db_connect();

function getAsset($database, $ID){
	// Make a select call to the database to get the asset variables
	// the ID is used to get the correct asset
	$result_software = $database->prepare("SELECT * FROM asset WHERE ID = " . $ID);
	$result_software->execute();
	for($i=0; $row = $result_software->fetch(); $i++){
		// Set all the needed variables.
		// the variables will be filled in the form so the user has a better experience editing the asset.
		$row['name'];
		$row['activatiecode'];
		$row['info'];
		$row['seconden'];
		return $row;
	}	
}
function editAsset($database, $ID, $User_ID){
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
function getUserID($database, $ID){
	// Make a select call to the database to get the asset variables
	// the ID is used to get the correct asset
	$result_software = $database->prepare("SELECT * FROM user WHERE ID = " . $ID);
	$result_software->execute();
	for($i=0; $row = $result_software->fetch(); $i++){
		// Set all the needed variables.
		// the variables will be filled in the form so the user has a better experience editing the asset.
		$row['email'];
		$row['password'];
		$row['level'];
		return $row;
	}	
}
function editUser($database, $ID, $User_ID){
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