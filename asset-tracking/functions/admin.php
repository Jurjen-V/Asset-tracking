<?php
include_once 'db.php';

$database = db_connect();

// if delete is pressed
function deleteUser($database){
	// $_Get delete is the id of the asset the id is used to identify the correct asset and delete it
    $query = "DELETE FROM user WHERE ID={$_GET['delete']}";
    $insert = $database->prepare($query);
    $insert->execute();
    ?>
    	<script>window.location.href = "admin.php";</script>
    <?php
}
function submitUser($database){
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