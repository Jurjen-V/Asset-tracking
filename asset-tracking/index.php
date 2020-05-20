<?php
session_start();
// If a admin account tries to get acces to regular user page
// send the admin accounts to the admin page.
if (!empty($_SESSION['level'])){ //if session is not set 
  if($_SESSION['level'] == 1) {
    $_SESSION['msg'] = "You belong at the admin page";
    header('location: admin.php');
  }
}else{
  $_SESSION['level'] = 0;
}

//Detect special conditions devices
// Get browser http user agent and check if user is visiting web page from apple device
// if true and it runs ios < 13 send user to other login page because user can not use regular login page.
$version = preg_replace("/(.*) OS ([0-9]*)_(.*)/","$2", $_SERVER['HTTP_USER_AGENT']);
$iPod    = stripos($_SERVER['HTTP_USER_AGENT'],"iPod");
$iPhone  = stripos($_SERVER['HTTP_USER_AGENT'],"iPhone");
$iPad    = stripos($_SERVER['HTTP_USER_AGENT'],"iPad");
$webOS   = stripos($_SERVER['HTTP_USER_AGENT'],"webOS");
if (!isset($_SESSION['email'])){
  if( $iPod || $iPhone || $iPad || $webOS){ // als de device een ipod iphone of ipad is.
    // for example you use it this way
    if ($version < 13){ // als de ios versie lager is dan 13 kan de gebruiker niet inloggen.
     header('location: login.php'); //stuur gebruiker door naar andere login pagina.
    }
  }
}
// include db file
include_once 'db.php';
// login validation
if (isset($_POST['login_user'])) {
  // set error to 0 if a if statement is not succes the error var will increase by one. There will also be a specific errormessage assigned to the error.
  // in the end there will be a check if error is 0 if not show error message.
  $error = 0;
  // check if input fields are filled in
  if(!empty($_POST['email'])){ // check if email is not empty
    $email = htmlspecialchars($_POST['email']); 
  }else{
    $error++;
    $errorMessage = "Email is leeg";
  }
  if(!empty($_POST['psw'])){ // check if password is not empty
    $enterd_password = htmlspecialchars($_POST['psw']);
  }else{
    $error++;
    $errorMessage = "Wachtwoord is leeg";
  }
  //check if password is correct.
  // Get Old Password from Database using the email addres to get the password form database
  $query ="SELECT * FROM user WHERE email =:email";
  $stmt = $database->prepare($query);
  $results = $stmt->execute(array(":email" => $email));
  $user = $stmt->fetch(PDO::FETCH_ASSOC);
  // variables that the user account with the email addres has.
  $id = $user['ID'];
  $email = $user['email'];
  $Level = $user['level'];
  $password= $user['password'];
  // compare the filled in password with the database password.
  if(password_verify($enterd_password, $password)){
  }else{
    $error++;
    $errorMessage= "Wachtwoorden zijn niet juist";
  }
  if($error == 0){
    // These variables will be used to make sure the user is logged in.
    $_SESSION['email'] = $email;
    $_SESSION['id'] = $id;
    $_SESSION['level'] = $Level;
    $_SESSION['msg'] = "You are now logged in";
    if($Level == 0){
    }else{
      // If the user has a admin account 
      // send the user to admin page.
      header('location: admin.php');
    }
  }else{?>
    <!-- error was not 0 so there was a error -->
    <!-- show a html box that will contain the specified erromessage -->
    <div style="display: block" class="alert" id="alert">
      <span class="closebtn" onclick="Close()">&times;</span> 
      <strong>Let op!</strong> <?php echo $errorMessage ?>
    </div>
    <?php
  }
}
// if $_GET['logout'] is set destroy session and send user to index page.
if (isset($_GET['logout'])) {
  session_destroy();
  unset($_SESSION['email']);
  header("location: index.php");
}
?>
<!DOCTYPE html>
<html>
<head>
  <!-- Title document -->
  <title>Kaartpagina</title>
  <!--Load the style stylesheet of leaflet -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.3.1/dist/leaflet.css" integrity="sha512-Rksm5RenBEKSKFjgI3a41vrjkw4EVPlJ3+OiI65vTjIdo9brlAacEuKOiQ5OFh7cOI1bkDwLqdLw3Zg0cRJAAQ==" crossorigin=""/>
  <!--Load leaflet -->
  <script src="https://unpkg.com/leaflet@1.3.1/dist/leaflet.js" integrity="sha512-/Nsx9X4HebavoBvEBuyp3I7od5tA0UzAxs+j83KgC8PU0kgB4XiK4Lfe4y4cgBtaRJQEIFCW+oC506aPT2L1zw==" crossorigin=""></script>
  <!--Load vectorGrid plugin for Leaflet -->
  <script src="https://unpkg.com/leaflet.vectorgrid@latest/dist/Leaflet.VectorGrid.bundled.js"></script>
  <!-- mapbox cdn -->
  <script src='https://api.mapbox.com/mapbox-gl-js/v1.7.0/mapbox-gl.js'></script>
  <link href='https://api.mapbox.com/mapbox-gl-js/v1.7.0/mapbox-gl.css' rel='stylesheet' />
  <!-- boat icon -->
  <script src="https://unpkg.com/leaflet.boatmarker/leaflet.boatmarker.min.js"></script>
  <!-- meta tags -->
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

  <link rel="stylesheet" type="text/css" href="./css/style.css">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link type="text/css" rel="stylesheet" href="./css/materialize.min.css"  media="screen,projection"/>
  <!-- icon of page -->
  <link rel="icon" href="img/favicon.png">
</head>
<body>
  <div id="alert" style="display: none"></div>
  <!-- if the user is logged in show nav bar etc -->
  <!-- show below html code if user is logged in -->
  <?php if (isset($_SESSION['email'])) : ?>
  <ul class="sidenav" id="mobile-demo">
  <li class="sidenav-header standard-bgcolor">
          <div class="row">
            <div class="col s4">
                <h4 class="white-text">Asset-tracking</h4>
            </div>
          </div>
        </li>
        <li><a title="Home" class="modal-trigger" href="assets.php"><i class="material-icons left">home</i>Home</a></li>
        <li class="active"><a title="Map" href="index.php"><i class="material-icons">map</i>Kaart</a></li>
        <li><a title="Uitloggen" href="?logout=1"><i class="material-icons left">exit_to_app</i>Uitloggen</a></li>
  </ul>
  <nav>
    <div class="nav-wrapper standard-bgcolor">
      <a href="#" class="brand-logo center">Asset-Tracking</a>
      <a href="#" data-target="mobile-demo" class="sidenav-trigger"><i class="material-icons">menu</i></a>
      <ul id="nav-mobile" class="left hide-on-med-and-down">
        <li><a title="Home" class="modal-trigger" href="assets.php"><i class="material-icons left">home</i>Home</a></li>
        <li class="active"><a title="Map" href="index.php"><i class="material-icons left">map</i>Kaart</a></li>
      </ul>
      <ul id="nav-mobile" class="right hide-on-med-and-down">
          <li class="right"><a title="Uitloggen" href="?logout=1"><i class="material-icons right">exit_to_app</i>Uitloggen</a></li>
      </ul>
    </div> 
  </nav>
  <?php endif ?>
  <!-- show below html when user is not logged in and when user is logged in -->
    <div id='map'>
      <div class="leaflet-top leaflet-right button_box2 leaflet-control standard-bgcolor white-text info_box">
        <h6 class="white-text">Locaties:</h6>
        <blockquote class="blockquote_white btnStyle span3 leaflet-control Button" id="info_box"></blockquote>
      </div>
      <div class="leaflet-bottom leaflet-right button_box2">
            <button type="button" id="Btn" class="open-button btn waves-effect standard-bgcolor btnStyle span3 leaflet-control Button" onclick="openForm()">Login</button>
      <!-- login form -->
      <div class="form-popup row" id="myForm">
        <form action="" method="post" id="form" class="form-container col s12">
          <h4 class="login standard-color">Login</h4>

            <div class="row">
              <!-- email input -->
            <div class="input-field col s12" id="email">
              <input required class="leaflet-control validate" type="email" name="email">
              <label for="Email">Email</label>
              <span class="helper-text" data-error="Geen correct e-mailadres" data-success="correct">voorbeeld@voorbeeld.nl</span>
            </div>
          </div>
          <div class="row">
            <!-- password input -->
            <div class="input-field col s12" id="psw">
              <input minlength="10" required class="leaflet-control validate" type="password" name="psw">
              <label for="Password">Password</label>
              <span class="helper-text" data-error="Wachtwoord is te kort" data-success="correct">10 karakters lang</span>
            </div>
          </div>
          <div class="row"  id="row_link">
            <h2 class="not-a-user">not a user? <a href="sign-up.php" class="Sign-up leaflet-control standard-color ">Sign up</a></h2>
          </div>
          <!-- login button and close button -->
          <button type="submit" id="form_btn" name="login_user" class="btn waves-effect standard-bgcolor btnStyle leaflet-control Button">Login</button>
          <button type="button" id="form_btn" class="cancel btn waves-effect standard-bgcolor btnStyle leaflet-control Button" onclick="closeForm()">Close</button>
        </form>
      </div>
    </div>
  </div>
  <!-- script links -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

<script type="text/javascript">
  var alert = document.getElementById('alert');
  var map = document.getElementById('map')
  if (alert.style.display === 'block') {
      map.style.height = "calc(100% - 52.5px)";
  }
</script>
<script type="text/javascript" src="js/script.js"></script>
<script type="text/javascript" src="js/materialize.min.js"></script>
<script type="text/javascript" src="script.php"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
</body>
</html>