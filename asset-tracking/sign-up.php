<?php 
// include db connection 
include_once 'db.php';
  if(isset($_POST['Sign-up'])) {
    // set error to 0 if a if statement is not succes the error var will increase by one. There will also be a specific errormessage assigned to the error.
    // in the end there will be a check if error is 0 if not show error message.
    $error = 0;
    // check in database if the email is already in use
    $email = $_POST['email'];
    $query = "SELECT * FROM user WHERE email= :email LIMIT 1";
    $stmt = $database->prepare($query);
    $results = $stmt->execute(array(":email" => $email));
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) { // if user exists
      if ($user['email'] == $email) {
        $error++;
        $errorMessage= "Email already excist";
      }
    }
    // check if input fields are filled in
    if (!empty($_POST['email'])){ // check if email is empty
        $email = htmlspecialchars($_POST['email']);
    }else{
        $error++;
        $errorMessage= "Email is empty";
    }
    if (!empty($_POST['password_1'])){ // check if password_1 is empty
        $password_1 = htmlspecialchars($_POST['password_1']);

    }else{
        $error++;
        $errorMessage= "Password is empty";
    }
    if (!empty($_POST['password_2'])){ // check if password_2 is empty 
        $password_2 = htmlspecialchars($_POST['password_2']);
    }else{
        $error++;
        $errorMessage= "Please confirm the password";
    }
    // if the passwords are shorter than 10 
    if(strlen($password_1) < 10 || strlen($password_2) < 10){
      $error++;
      $errorMessage= "Password needs to me longer than 10 characters.";
    }
    // if password_1 and password_2 are the same
    // make variable $password_3 (hash variant of $password_1) 
    // from here $password_3 will be used.
    if($password_1 == $password_2){ // check if passwords are the same hash the password
      $password_3 = password_hash($password_1, PASSWORD_DEFAULT);
    }else{
      $error++;
      $errorMessage= "Password needs to be the same";
    }
    if ($error === 0) { //if error = 0 insert the user 
      $query = "INSERT INTO user (email, Password) VALUES (?, ?)";
      $insert = $database->prepare($query);

      $data = array("$email", "$password_3");
      try {
        $insert->execute($data);
      }
      catch (PDOException $e) {
        throw $e;
      }
      // start session and send user to his home page
      session_start();
      $user_id =$database->lastInsertId();
      // These variables will be used to make sure the user is logged in.
      $_SESSION['id'] = $user_id;
      $_SESSION['email'] = $email;
      $_SESSION['level'] = $Level;
      $_SESSION['msg'] = "You are now logged in";
      // all the data is handled succesfully send user to assets.php.
      header('Location:assets.php');
    }else{// else show alert box
      ?>    
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
  <link rel="icon" href="/img/favicon.ico">
  <link rel="stylesheet" type="text/css" href="css/style.css">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link type="text/css" rel="stylesheet" href="css/materialize.min.css"  media="screen,projection"/>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Sign-up</title>
  <!-- icon of page -->
  <link rel="icon" href="img/favicon.png">
</head>
  <nav>
    <div class="nav-wrapper standard-bgcolor">
      <a href="#" class="brand-logo center">Asset Tracking</a>
      <ul id="nav-mobile" class="right hide-on-med-and-down">
      </ul>
    </div>
  </nav>
  <!-- login form -->
<body class="login_body">
	<div class="row">
	<form class="col s6" id="form_full" action="" method="post">
    <h4 class="standard-color">Maak account</h4>
		<div class="row">
      <!-- email input -->
			<div class="input-field col s12" id="e-mail">
				<input class="validate" type="email" required name="email">
        <label for="E-mail">E-mail address</label>
        <span class="helper-text" data-error="Geen correct e-mailadres" data-success="correct">voorbeeld@voorbeeld.nl</span>
			</div>
		</div>
		<div class="row">
      <!-- password input -->
			<div class="input-field col s12" id="password">
				<input minlength="10" required type="password" class="validate" name="password_1">
        <label for="Password">Password</label>
        <span class="helper-text" data-error="Wachtwoord is te kort" data-success="correct">10 karakters lang</span>
			</div>
		</div>
		<div class="row">
      <!-- password input -->
			<div class="input-field col s12" id="password">
				<input minlength="10" required type="password" class="validate" name="password_2">
        <label for="Password">Confirm password</label>
        <span class="helper-text" data-error="Wachtwoord is te kort" data-success="correct">10 karakters lang</span>
			</div>
		</div>
      <div class="row">
        <!-- sign up and cancel button -->
    <div class="input-group">
      <button id="Sign-up" class="btn waves-effect standard-bgcolor" type="submit" name="Sign-up">Sign-up</button>
    </div>
      <h2 class="Cancel"><a href="index.php" class="Sign-up_Cancel standard-color">Cancel</a></h2>
    </div>
	</form>
	</div>
  <!-- include footer -->
  <?php include('objects/footer.php'); ?>
</body>
<!-- script links -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script type="text/javascript" src="js/materialize.min.js"></script>
</html>