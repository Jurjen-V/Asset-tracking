<?php 
include_once 'db.php';
  if(isset($_POST['Sign-up'])) {
    $error = 0;
    $email = $_POST['email'];
    $query = "SELECT * FROM user WHERE email= :email LIMIT 1";
    $stmt = $database->prepare($query);
    $results = $stmt->execute(array(":email" => $email));
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) { // if user exists
      if ($user['email'] == $email) {
        $error++;
        $errorMSG= "Email already excist";
      }
    }
    if (!empty($_POST['email'])){
        $email = htmlspecialchars($_POST['email']);

    }else{
        $error++;
        $errorMSG= "Email is empty";
    }
    if (!empty($_POST['password_1'])){
        $password_1 = htmlspecialchars($_POST['password_1']);

    }else{
        $error++;
        $errorMSG= "Password is empty";
    }
    if (!empty($_POST['password_2'])){
        $password_2 = htmlspecialchars($_POST['password_2']);

    }else{
        $error++;
        $errorMSG= "Please confirm the password";
    }
    if(strlen($password_1) < 10 || strlen($password_2) < 10){
      $error++;
      $errorMSG= "Password needs to me longer than 10 characters.";
    }
    if($password_1 == $password_2){
      $password_3 = $password_3 = password_hash($password_1, PASSWORD_DEFAULT);
    }else{
      $error++;
      $errorMSG= "Password needs to be the same";
    }

      if ($error === 0) {
        $query = "INSERT INTO user (email, Password) VALUES (?, ?)";
        $insert = $database->prepare($query);

        $data = array("$email", "$password_3");
        try {
            $insert->execute($data);
        }
        catch (PDOException $e) {
            throw $e;
        }

        session_start();
        $user_id =$database->lastInsertId();
        $_SESSION['id'] = $user_id;
        $_SESSION['email'] = $email;
        $_SESSION['level'] = $Level;
        $_SESSION['msg'] = "You are now logged in";
        header('Location:assets.php');
      }else{
        ?>
        <div class="alert">
          <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
          <strong>Let op!</strong> <?php echo $errorMSG ?>
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
</head>
  <nav>
    <div class="nav-wrapper standard-bgcolor">
      <a href="#" class="brand-logo center">Asset Tracking</a>
      <ul id="nav-mobile" class="right hide-on-med-and-down">
      </ul>
    </div>
  </nav>
<body class="login_body">
	<div class="row">
	<form class="col s12" id="form_full" action="" method="post">
    <h4 class="standard-color">Maak account</h4>
		<div class="row">
			<div class="input-field col s6" id="e-mail">
				<input class="validate" type="email" required name="email">
        <label for="E-mail">E-mail address</label>
        <span class="helper-text" data-error="Geen correct e-mailadres" data-success="correct">voorbeeld@voorbeeld.nl</span>
			</div>
		</div>
		<div class="row">
			<div class="input-field col s6" id="password">
				<input minlength="10" required type="password" class="validate" name="password_1">
        <label for="Password">Password</label>
        <span class="helper-text" data-error="Wachtwoord is te kort" data-success="correct">10 karakters lang</span>
			</div>
		</div>
		<div class="row">
			<div class="input-field col s6" id="password">
				<input minlength="10" required type="password" class="validate" name="password_2">
        <label for="Password">Confirm password</label>
        <span class="helper-text" data-error="Wachtwoord is te kort" data-success="correct">10 karakters lang</span>
			</div>
		</div>
      <div class="row">
    <div class="input-group">
      <button id="Sign-up" class="btn waves-effect standard-bgcolor" type="submit" name="Sign-up">Sign-up</button>
    </div>
      <h2 class="Cancel"><a href="index.php" class="Sign-up_Cancel standard-color">Cancel</a></h2>
    </div>
	</form>
	</div>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script type="text/javascript" src="js/materialize.min.js"></script>
</html>