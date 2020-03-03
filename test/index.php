<!DOCTYPE html>
<html>
    <head>
        <!-- Title document -->
        <title> leaflet webviewer with Geoserver</title>
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
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    </head>
<div id='map'></div>
<style type="text/css">
html, body {
    margin: 0;
    height: 100%;
}
#map{
    height: 100%;
    width: 100%;
}
</style>
<!-- <script src="script.php"></script> -->
</html>
<?php 
$REMOTE_ADDR= $_SERVER['REMOTE_ADDR'];
?>
<script>
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
var count = 4;
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

// // Add dutch buidlings as layer from Geoserver
// var Buidlings= L.tileLayer.wms("http://localhost:8080/geoserver/Netherlands/wms", {
//     layers: 'Netherlands:NL buildings',
//     transparent: true,
//     format: 'image/png',
// });
// map.addLayer(Buidlings);
// // Add dutch railways as layer from Geoserver
// var Railways= L.tileLayer.wms("http://localhost:8080/geoserver/Netherlands/wms", {
//     layers: 'Netherlands:railways',
//     transparent: true,
//     format: 'image/png',
// });
// map.addLayer(Railways);

// var Points= L.tileLayer.wms("http://localhost:8080/geoserver/Netherlands/wms", {
//     layers: 'Netherlands:points',
//     transparent: true,
//     format: 'image/png',
// });
// map.addLayer(Points);

// Set our initial location and zoomlevel
map.setView([52.132633, 5.291266], 6);

var array = [
  <?php
    $dbhost = 'localhost';
    $dbname = 'leaflet';
    $user = 'root';
    $pass = '';
    // $dbhost = 'localhost:3306';
    // $dbname = 'Leaflet';
    // $user = 'Leaflet';
    // $pass = 'leFr530$';

    try {
      $database = new PDO('mysql:host='.$dbhost.';dbname='.$dbname, $user, $pass);
      $database->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    }
    catch (PDOException $e) {
      echo $e->getMessage();
    }

    $result_users = $database->prepare("SELECT * FROM location");
    $result_users->execute();
    for($i=0; $row = $result_users->fetch(); $i++){
      $id = $row['ID'];
      $latitude = $row["latitude"];
      $longitude = $row["longitude"]; 
      echo "'[$latitude, $longitude]',";
    }   
  ?>
];
console.log(array);
assets = array.map(s => eval('null,' + s));
var i;
for (i = 0; i < assets.length; i++) {
    L.circleMarker(assets[i], {
        color: "#4285F4",
        weight: 0,
        fillColor: "#4285F4",
        fillOpacity: 0.5,
        radius: 20,
    }).addTo(map);
    marker = L.circleMarker(assets[i], {
        color: "white",
        opacity: 1,
        weight: 2,
        fillColor: "#4285F4",
        radius: 10,
        opacity: 1,
        fillOpacity: 1,
    }).addTo(map);
}
// functie om afgelde route te tekenen


function onLocationFound(e) {
  if (i == 0) {
    map.panTo(new L.LatLng(e.latitude, e.longitude));
    i = i + 1;
  }
  var latlngs = Array();
  var radius = e.accuracy;
  if (current_position) {
    map.removeLayer(current_position);
    map.removeLayer(circle);
  }
  //set location

  circle = new L.circleMarker(e.latlng, {
    color: "#4285F4",
    weight: 0,
    fillColor: "#4285F4",
    fillOpacity: 0.5,
    radius: 20,
  }).addTo(map);
  current_position = new L.circleMarker(e.latlng, {
    color: "white",
    weight: 2,
    fillColor: "#4285F4",
    radius: 10,
    opacity: 1,
    fillOpacity: 1,
  }).addTo(map);
  map.addLayer(circle);
  current_position.bindPopup("Latitude: " + e.latitude +"<br>" + "Longitude: " + e.longitude);
  map.addLayer(current_position);
  var REMOTE_ADDR = <?php echo json_encode($REMOTE_ADDR);?>;
  window.history.replaceState(null, null, "index.php?latitude=" + e.latitude + "&longitude=" + e.longitude + "&REMOTE_ADDR=" + REMOTE_ADDR); 
  <?php
    $error = 0;
    $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    if(strpos($actual_link, 'longitude') && strpos($actual_link, 'latitude') && strpos($actual_link, 'REMOTE_ADDR')){
      if (!empty($_GET['REMOTE_ADDR'])){
        $succes= "succes 1";
      }else{
          $error++;
      }
      if (!empty($_GET['longitude'])){
        $longitude = $_GET["longitude"];
        $succes= "succes 2";
      }else{
        $error++;
      }
      if (!empty($_GET['latitude'])){
        $latitude = $_GET["latitude"];
        $succes= "succes 3";
      }else{
        $error++;
      }
      $result = $database->prepare("SELECT * FROM location WHERE REMOTE_ADDR= :parameter  LIMIT 1");
      $result->bindParam(':parameter', $REMOTE_ADDR, PDO::PARAM_STR);
      $result->execute();
      for($i=0; $row = $result->fetch(); $i++){
        $ID = $row['ID'];
        $row_REMOTE_ADDR = $row["REMOTE_ADDR"];
        $row_latitude = $row['latitude'];
        $row_longitude = $row['longitude'];
      } 
      if ($error === 0 && $result) {
          if ($row_REMOTE_ADDR == $REMOTE_ADDR) {
            if($_GET['longitude'] != $row_longitude || $_GET['latitude'] != $row_latitude){
              $query = "UPDATE location SET latitude=:latitude, longitude=:longitude WHERE REMOTE_ADDR= :REMOTE_ADDR";
              $stmt = $database->prepare($query);
              $stmt->bindValue(":REMOTE_ADDR", $row_REMOTE_ADDR, PDO::PARAM_STR);
              $stmt->bindValue(":latitude", $latitude, PDO::PARAM_STR);
              $stmt->bindValue(":longitude", $longitude, PDO::PARAM_STR);
              try {
                $stmt->execute();
              }
              catch (PDOException $e) {
                echo $e->getMessage();
              }
            }
          }else{
            $query = "INSERT INTO location (latitude, longitude, REMOTE_ADDR) VALUES (?, ?, ?)";
            $insert = $database->prepare($query);
            $data = array("$longitude", "$latitude", "$REMOTE_ADDR");
            try {
              $insert->execute($data);
              $succes = "succes 4";
            }
            catch (PDOException $e) {
              throw $e;
            }
          }
        }
    }
  ?>
  var latitude = <?php echo json_encode($row_latitude);?>;
var longitude = <?php echo json_encode($row_longitude);?>;
console.log(latitude);
console.log(longitude);
  if(count == 0){
    setTimeout(update(e), 1000);
  }else{
    console.log(count);
    count -= 1;   
  }
}
//set vieuw on yourlocation
function setview(e) {
  var latview = e._latlng.lat;
  var lngview = e._latlng.lng;
  map.panTo(new L.LatLng(latview, lngview));
}
map.on("locationfound", onLocationFound);
function onLocationError(e) {
  alert(e.message);
}
var line = L.polyline([],{dashArray: '20,15'}).addTo(map);

function redraw(point) {
    line.addLatLng(point);
}
function update(e) {
    if(e != null){
        var loc = [e.latlng];
        if (loc.length) {
            redraw(loc.shift());
            setTimeout(update, 1000);
        }
    }
}
</script> 