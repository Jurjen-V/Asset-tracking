<?php
include_once 'db.php';

$database = db_connect();

function getUser($database, $User_ID){
	// request basic info from user.
	$result_users = $database->prepare("SELECT * FROM user WHERE ID = ".$User_ID);
	$result_users->execute();
	for($i=0; $row = $result_users->fetch(); $i++){
		$id = $row['ID'];
		$email = $row['email'];
		$password = $row['password'];	
	}	
	return $email;
}
function getTS($database){
	$query = "SELECT * FROM `point` ORDER BY TS DESC LIMIT 1";
	$stmt = $database->prepare($query);
	$results = $stmt->execute();
	$point = $stmt->fetch(PDO::FETCH_ASSOC);
	$startTime= $point['TS'];
	return $startTime;
}
// if delete is pressed
function deleteAsset($database){
	// $_Get delete is the id of the asset the id is used to identify the correct asset and delete it
	$query = "DELETE FROM asset WHERE ID={$_GET['delete']}";
	$insert = $database->prepare($query);
	$insert->execute();
	?>
	<script>window.location.href = "assets.php";</script>
	<?php
}
function submitAsset($database, $User_ID){
	// set error to 0 if a if statement is not succes the error var will increase by one. There will also be a specific errormessage assigned to the error.
	// in the end there will be a check if error is 0 if not show error message.
	$error = 0;
	// check in database if the activationkey is already in use
	$activatiecode = htmlspecialchars($_POST['activatiecode']);
	$query = "SELECT * FROM asset WHERE activatiecode= :activatiecode AND user_ID = 0 LIMIT 1";
	$stmt = $database->prepare($query);
	$results = $stmt->execute(array(":activatiecode" => $activatiecode));
	$asset = $stmt->fetch(PDO::FETCH_ASSOC);
	$trackerID = $asset['trackerID'];
	if ($asset) { // if asset exists
	    if ($asset['activatiecode'] == $activatiecode) {
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
		}else{
			$error++;
			$errorMessage= "De gps tracker moet nog geactiveerd worden.";
		}
		if ($error == 0) { //if error is 0 proceed
			// insert asset into database
			$query = "UPDATE asset SET name=:gpsName, user_ID=:user_ID, info=:info WHERE trackerID =:trackerID";
			$stmt = $database->prepare($query);

			$stmt->bindValue(":trackerID", $trackerID, PDO::PARAM_STR);
			$stmt->bindValue(":gpsName", $name, PDO::PARAM_STR);
			$stmt->bindValue(":info", $info, PDO::PARAM_STR);
			$stmt->bindValue(":user_ID", $User_ID, PDO::PARAM_STR);

			try {
			    $stmt->execute();
			}
			catch (PDOException $e) {
			    echo $e->getMessage();
			}
			// all the data is handled succesfully send user to assets.php.
			// header('location: assets.php');	

		}else{?>
			<!-- error was not 0 so there was a error -->
			<!-- show a html box that will contain the specified erromessage -->
			<div class="alert">
			    <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
			    <strong>Let op!</strong> <?php echo $errorMessage ?>
			</div><?php
		}
	}	
}
function updateProfile($database, $User_ID){
	// set error to 0 if a if statement is not succes the error var will increase by one. There will also be a specific errormessage assigned to the error.
	// in the end there will be a check if error is 0 if not show error message.
	$error = 0;
	// check in database if the email is already in use
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
    // make variable $password_3 (hash variant of $password_1) 
    // from here $password_3 will be used.
    if($password_1 == $password_2){
      $password_3 = password_hash($password_1, PASSWORD_DEFAULT);
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
	    // all the data is handled succesfully send user to assets.php.
	    header('location: assets.php');

	}else{?>
		<!-- error was not 0 so there was a error -->
		<!-- show a html box that will contain the specified erromessage -->
	   	<div class="alert">
	        <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
	        <strong>Let op!</strong> <?php echo $errorMessage ?>
	    </div><?php
	}
}