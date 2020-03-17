<?php
session_start();
$_SESSION['level'] = 0;
if ($_SESSION['level'] == 1) {
  $_SESSION['msg'] = "You belong at the admin page";
    header('location: admin.php');
}
include_once 'db.php';
$error = 0;
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
  <title>Afgelegde route</title>
  <!--Load the style stylesheet of leaflet -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.3.1/dist/leaflet.css" integrity="sha512-Rksm5RenBEKSKFjgI3a41vrjkw4EVPlJ3+OiI65vTjIdo9brlAacEuKOiQ5OFh7cOI1bkDwLqdLw3Zg0cRJAAQ==" crossorigin=""/>
  <!--Load leaflet -->
  <script src="https://unpkg.com/leaflet@1.3.1/dist/leaflet.js" integrity="sha512-/Nsx9X4HebavoBvEBuyp3I7od5tA0UzAxs+j83KgC8PU0kgB4XiK4Lfe4y4cgBtaRJQEIFCW+oC506aPT2L1zw==" crossorigin=""></script>
  <!--Load vectorGrid plugin for Leaflet -->
  <script src="https://unpkg.com/leaflet.vectorgrid@latest/dist/Leaflet.VectorGrid.bundled.js"></script>
  <!-- mapbox cdn -->
  <script src='https://api.mapbox.com/mapbox-gl-js/v1.7.0/mapbox-gl.js'></script>
  <link href='https://api.mapbox.com/mapbox-gl-js/v1.7.0/mapbox-gl.css' rel='stylesheet' />
  <!--route plugin  -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.2.0/dist/leaflet.css" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />
 

  <!-- meta tags -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

  <link rel="stylesheet" type="text/css" href="./css/style.css">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link type="text/css" rel="stylesheet" href="./css/materialize.min.css"  media="screen,projection"/>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

</head>
<body>
  <ul class="sidenav" id="mobile-demo">
    <li class="sidenav-header standard-bgcolor">
      <div class="row">
        <div class="col s4">
            <h4 class="white-text">Asset tracking</h4>
        </div>
      </div>
    </li>
    <li><a title="Home" class="modal-trigger" href="assets.php"><i class="material-icons left">home</i>Home</a></li>
    <li ><a title="Map" href="index.php"><i class="material-icons">map</i>Kaart</a></li>
    <li><a title="Uitloggen" href="?logout=1"><i class="material-icons left">exit_to_app</i>Uitloggen</a></li>
  </ul>
  <nav>
    <div class="nav-wrapper standard-bgcolor">
      <a href="#" class="brand-logo center">Asset Tracking</a>
      <a href="#" data-target="mobile-demo" class="sidenav-trigger"><i class="material-icons">menu</i></a>
      <ul id="nav-mobile" class="left hide-on-med-and-down">
        <li><a title="Home" class="modal-trigger" href="assets.php"><i class="material-icons left">home</i>Home</a></li>
        <li ><a title="Map" href="index.php"><i class="material-icons left">map</i>Kaart</a></li>
      </ul>
      <ul id="nav-mobile" class="right hide-on-med-and-down">
          <li class="right"><a title="Uitloggen" href="?logout=1"><i class="material-icons right">exit_to_app</i>Uitloggen</a></li>
      </ul>
    </div> 
  </nav>
    <div id='map'>
      <div class="leaflet-top leaflet-right button_box2 leaflet-control standard-bgcolor white-text info_box">
        <h6 class="white-text">Locaties:</h6>
        <blockquote class="blockquote_white btnStyle span3 leaflet-control Button" id="info_box"></blockquote>


      </div>
    </div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script type="text/javascript" src="js/script.js"></script>
<script type="text/javascript" src="js/materialize.min.js"></script>
<script src="https://unpkg.com/leaflet@1.2.0/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>
<script type="text/javascript">
var elem = L.DomUtil.get('info_box');
L.DomEvent.on(elem, 'mousewheel', L.DomEvent.stopPropagation);
document.getElementById("map").style.height = "calc(100% - 64px)";
  // get html map id
var current_position,
  circle,
  polyline,
  marker,
  dir,
  value,
  value2,
  clicked2,
  i,
  e,
  osmb,
  update;
var map = L.map('map')
map.locate({
  watch: true,
  setView: false,
  maxZoom: 18,
  enableHighAccuracy: true
});
// Open default map from mapbox
L.tileLayer(
    "https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}",
    {
        maxZoom: 20,
        minZoom: 5,
        zoom: 16,
        id: "mapbox/streets-v11",
        accessToken:
          "pk.eyJ1IjoianVyamVudiIsImEiOiJjazZyb2s0c2UwNXlmM2dwOWpoam1veWtvIn0.Wz1L39sbP_yOIek4zP7W9Q"
    }
).addTo(map);
var array =[
  <?php
  $result_assets = $database->prepare("SELECT asset.ID, point.ASSET_ID, asset.name, CAST(point.TS AS DATE), asset.activatiecode, asset.info, (SELECT ST_X(latlong)) AS LAT, (SELECT ST_y(latlong)) AS LON FROM asset INNER JOIN point on asset.ID = point.ASSET_ID WHERE CAST(TS AS DATE) = '".$_GET['TS']."' AND point.ASSET_ID=".$_GET['ID']);
  $result_assets->execute();
  for($i=0; $row = $result_assets->fetch(); $i++){
    // print_r($row);
    $LAT = $row['LAT'];
    $LON = $row['LON'];
    echo "'[$LAT, $LON]',";
  } 
  ?>
];
var plaatsnaam =[
  <?php
  $result_assets = $database->prepare("SELECT asset.ID, point.ASSET_ID, asset.name, CAST(point.TS AS DATE), asset.activatiecode, asset.info, (SELECT ST_X(latlong)) AS LAT, (SELECT ST_y(latlong)) AS LON FROM asset INNER JOIN point on asset.ID = point.ASSET_ID WHERE CAST(TS AS DATE) = '".$_GET['TS']."' AND point.ASSET_ID=".$_GET['ID']);
  $result_assets->execute();
  for($i=0; $row = $result_assets->fetch(); $i++){
    $lat = $row['LAT'];
    $lng = $row['LON'];

      $url = 'https://api.opencagedata.com/geocode/v1/json?q='.$lat.','.$lng.'&key=5b104f01c9434e3dad1e2d6a548445da&language=nl&pretty=1'; 
      $json = @file_get_contents($url);
      $data = json_decode($json, true);
      $results = $data['results'];
      if (is_array($data)){
        foreach($results as $results) {
          $components = $results['components'];
          $city = $components['suburb'];
      }
    }
    echo "'$city',";
  } 
  ?>
];
route = array.map(s => eval('null,' + s));
console.log(route);
console.log(plaatsnaam);
var i;
for (i = 0; i < route.length; i++) {
  document.getElementById('info_box').innerHTML +=  plaatsnaam[i] + "<br>";

}  
for (i = 0; i < route.length; i++) {
  var marker = L.marker(route[i]).addTo(map);
  marker.bindPopup(plaatsnaam[i]).openPopup();
}
var routeControl = L.Routing.control({
  createMarker: function() { return null; },
  routeWhileDragging: false,
  draggableWaypoints: false,
  addWaypoints: false,
  show: false,
  lineOptions: {
      styles: [{color: '#1c9cda', opacity: 1, weight: 5, dashArray: '20,15'}]
   }
}).addTo(map);
routeControl.setWaypoints(route);
map.setView(route[0], 10);  
// var line = L.polyline(route,{dashArray: '20,15'}).addTo(map);
</script>
</body>
</html>