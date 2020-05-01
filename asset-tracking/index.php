<?php
session_start();
$_SESSION['level'] = 0;
if ($_SESSION['level'] == 1) {
  $_SESSION['msg'] = "You belong at the admin page";
    header('location: admin.php');
}
include_once 'db.php';
$error = 0;
// login validation
if (isset($_POST['login_user'])) {
  if(!empty($_POST['email'])){
    $email = htmlspecialchars($_POST['email']);
  }else{
    $error++;
    $errorMessage = "Email is leeg";
  }
  if(!empty($_POST['psw'])){
    $enterd_password = htmlspecialchars($_POST['psw']);
  }else{
    $error++;
    $errorMessage = "Password is leeg";
  }
  // Get Old Password from Database which is having unique userName
  $query ="SELECT * FROM user WHERE email =:email";
  $stmt = $database->prepare($query);
  $results = $stmt->execute(array(":email" => $email));
  $user = $stmt->fetch(PDO::FETCH_ASSOC);
  $id = $user['ID'];
  $email = $user['email'];
  $Level = $user['level'];
  $password= $user['password'];
  if(password_verify($enterd_password, $password)){
  }else{
    $error++;
    $errorMessage= "Wachtwoorden is niet juist";
  }
  if($error == 0){
    $_SESSION['email'] = $email;
    $_SESSION['id'] = $id;
    $_SESSION['level'] = $Level;
    $_SESSION['msg'] = "You are now logged in";
    if($Level == 0){
    }else{
      header('location: admin.php');
    }
  }else{?>
    <div class="alert">
      <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
      <strong>Let op!</strong> <?php echo $errorMessage ?>
    </div><?php
  }
}
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
  <?php 
  // $string = file_get_contents("json/locatie.json");
  // if ($string === false) {
  //     // deal with error...
  // }

  // $json_a = json_decode($string, true);

  // if ($json_a === null) {
  //     // deal with error...
  // }
  // $array_length = count($json_a);
  // for ($i=0; $i < $array_length; $i++) { 
  //   echo $json_a[$i]["POINT_ID"];
  //     echo $json_a[$i]["ASSET_ID"];
  //     echo $json_a[$i]['latlong'];
  //     echo $json_a[$i]["TS"]."<br>";
  // }
endif ?>
      <div id="info" style="display: none" class="alert info">
        <span class="closebtn">&times;</span>  
        <strong>Let op!</strong> Als u niet kunt inloggen klik dan <strong><a href="login.php" class="white-text">hier</a></strong>
      </div>
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

<script type="text/javascript" src="js/script.js"></script>
<script type="text/javascript" src="js/materialize.min.js"></script>
<script type="text/javascript" src="script.php"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
</body>
</html>
<?php
// include_once 'js/script.php';
    // $error = 0;
    // $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    // if(strpos($actual_link, 'longitude') && strpos($actual_link, 'latitude') && strpos($actual_link, 'REMOTE_ADDR')){
    //   if (!empty($_GET['REMOTE_ADDR'])){
    //     $succes= "succes 1";
    //   }else{
    //       $error++;
    //   }
    //   if (!empty($_GET['longitude'])){
    //     $longitude = $_GET["longitude"];
    //     $succes= "succes 2";
    //   }else{
    //     $error++;
    //   }
    //   if (!empty($_GET['latitude'])){
    //     $latitude = $_GET["latitude"];
    //     $succes= "succes 3";
    //   }else{
    //     $error++;
    //   }
    //   $result = $database->prepare("SELECT * FROM location WHERE REMOTE_ADDR= :parameter  LIMIT 1");
    //   $result->bindParam(':parameter', $REMOTE_ADDR, PDO::PARAM_STR);
    //   $result->execute();
    //   for($i=0; $row = $result->fetch(); $i++){
    //     $ID = $row['ID'];
    //     $row_REMOTE_ADDR = $row["REMOTE_ADDR"];
    //     $row_latitude = $row['latitude'];
    //     $row_longitude = $row['longitude'];
    //   } 
    //   if ($error === 0 && $result) {
    //       if ($row_REMOTE_ADDR == $REMOTE_ADDR) {
    //         if($_GET['longitude'] != $row_longitude || $_GET['latitude'] != $row_latitude){
    //           $query = "UPDATE location SET latitude=:latitude, longitude=:longitude WHERE REMOTE_ADDR= :REMOTE_ADDR";
    //           $stmt = $database->prepare($query);
    //           $stmt->bindValue(":REMOTE_ADDR", $row_REMOTE_ADDR, PDO::PARAM_STR);
    //           $stmt->bindValue(":latitude", $latitude, PDO::PARAM_STR);
    //           $stmt->bindValue(":longitude", $longitude, PDO::PARAM_STR);
    //           try {
    //             $stmt->execute();
    //           }
    //           catch (PDOException $e) {
    //             echo $e->getMessage();
    //           }
    //         }
    //       }else{
    //         $query = "INSERT INTO location (latitude, longitude, REMOTE_ADDR) VALUES (?, ?, ?)";
    //         $insert = $database->prepare($query);
    //         $data = array("$longitude", "$latitude", "$REMOTE_ADDR");
    //         try {
    //           $insert->execute($data);
    //           $succes = "succes 4";
    //         }
    //         catch (PDOException $e) {
    //           throw $e;
    //         }
    //       }
    //     }
    // }
  ?>