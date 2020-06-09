<?php
include 'functions/global.php';
session_start();
$IOS = 0;
checkSessionLevel();
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


// login validation
if (isset($_POST['login_user'])) {
  loginUser($database, $IOS);
}
// if $_GET['logout'] is set destroy session and send user to index page.
if (isset($_GET['logout'])) {
  logOut();
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