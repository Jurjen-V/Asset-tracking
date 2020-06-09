<?php
include 'functions/global.php';
session_start();

checkSessionLevel();
$IOS = 1;
// login validation
if (isset($_POST['login_user'])) {
  loginUser($database, $IOS);
}
?>
<!DOCTYPE html>
<html>
<head>
  <!-- Title document -->
  <title>Login</title>
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
      <!-- login form -->
      <div class="row col s12">
        <h2 class="standard-color center">Asset tracking <img class="logo" src="img/favicon.png"></h2>
        <div class="col s2 m1 l2"></div>
        <div class="col m4 l4 login hide-on-small-only">
            <div class="row login-box" >
              <br>
              <h5 class="white-text center-align">Powered by</h5>
              <br>
                  <div class="col s12">
                    <!-- link to hawarit -->
                    <a href="https://www.hawarit.com/"><img class="footer-img" src="img/HIT-logo-150.png"></a>
                  </div>
                  <br><br>
                  <h5 class="white-text center-align">And</h5>
                  <br>
                  <div class="col s12">
                    <!-- footer link to chaloIS -->
                    <a href="https://www.chalois.com/"><img class="footer-img" src="img/chalois_logo.png"></a>
                    <br>
                  </div>
              </div>
          </div>
        <form action="" method="post" class="form-container login col s12 m6 l4">
          <h4 class="login center standard-color">Login</h4>

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
          <button type="submit" id="submit" name="login_user" class="btn waves-effect standard-bgcolor right">Login</button>
        </form>
            <div class="col s12 m1 l2"></div>
      </div>
    </div>
  <!-- script links -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script type="text/javascript" src="js/script.js"></script>
<script type="text/javascript" src="js/materialize.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
</body>
<style type="text/css">
  .login-box{
    background-color: #1c9cda;
  }
  .login{
    margin-top: 5%;
 } 
  .page-footer{
    position: absolute !important;
    bottom: 0px;
  }
  #submit{
    position: relative;
    right: 0px;
    bottom: 0px;
    width: auto;
  }
  .login{
    display: block;
  }
  .logo{
    height: 53px;
  }
  html, body{
    height: auto;
  }
</style>
</html>