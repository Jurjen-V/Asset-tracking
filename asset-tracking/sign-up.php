<?php 
include 'functions/sign-up.php';
  if(isset($_POST['Sign-up'])) {
    signUp($database);
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