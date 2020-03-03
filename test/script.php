<?php 
$REMOTE_ADDR= $_SERVER['REMOTE_ADDR'];
?>
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

// Add dutch buidlings as layer from Geoserver
var Buidlings= L.tileLayer.wms("http://localhost:8080/geoserver/Netherlands/wms", {
    layers: 'Netherlands:NL buildings',
    transparent: true,
    format: 'image/png',
});
map.addLayer(Buidlings);
// Add dutch railways as layer from Geoserver
var Railways= L.tileLayer.wms("http://localhost:8080/geoserver/Netherlands/wms", {
    layers: 'Netherlands:railways',
    transparent: true,
    format: 'image/png',
});
map.addLayer(Railways);

var Points= L.tileLayer.wms("http://localhost:8080/geoserver/Netherlands/wms", {
    layers: 'Netherlands:points',
    transparent: true,
    format: 'image/png',
});
map.addLayer(Points);

// Set our initial location and zoomlevel
map.setView([52.132633, 5.291266], 6);

var array = [
  <?php
    $dbhost = 'localhost';
    $dbname = 'leaflet';
    $user = 'root';
    $pass = '';

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
    if (!empty($REMOTE_ADDR)){
      $succes= "succes 1";
      ?><script>console.log("succes1");</script><?php
    }else{
        $error++;
    }
    $result = $database->prepare("SELECT * FROM location WHERE REMOTE_ADDR= :parameter  LIMIT 1");
    $result->bindParam(':parameter', $REMOTE_ADDR, PDO::PARAM_STR);
    $result->execute();
    for($i=0; $row = $result->fetch(); $i++){
      $ID = $row['ID'];
      $latitude = $row['latitude'];
      $longitude = $row['longitude'];
      $row_REMOTE_ADDR = $row["REMOTE_ADDR"];
    } 
    echo "<br>". $row_REMOTE_ADDR ."<br>";
    // if ($row) { // if user exists
      if ($row_REMOTE_ADDR == $REMOTE_ADDR) {
        $query = "UPDATE location SET latitude=:latitude, longitude=:longitude WHERE REMOTE_ADDR= :REMOTE_ADDR";
        $stmt = $database->prepare($query);
        $stmt->bindValue(":REMOTE_ADDR", $REMOTE_ADDR, PDO::PARAM_STR);
        $stmt->bindValue(":latitude", $latitude, PDO::PARAM_STR);
        $stmt->bindValue(":longitude", $longitude, PDO::PARAM_STR);
        var_dump($stmt);
        try {
            $stmt->execute();
            ?><script>console.log("update");</script><?php
        }
        catch (PDOException $e) {
            echo $e->getMessage();
        }
        $error++;
      // }
    }
       if (!empty($_GET['longitude'])){
          $longitude = htmlspecialchars($_GET["longitude"]);
          $succes= "succes 2";
                ?><script>console.log("succes2");</script><?php
      }else{
          $error++;
      }
      if (!empty($_GET['latitude'])){
          $latitude = htmlspecialchars($_GET['latitude']);
          $succes= "succes 3";
                ?><script>console.log("succes3");</script><?php
      }else{
          $error++;
      }

      if ($error === 0) {
        $query = "INSERT INTO location (latitude, longitude, REMOTE_ADDR) VALUES (?, ?, ?)";
        $insert = $database->prepare($query);

        $data = array("$longitude", "$latitude", "$REMOTE_ADDR");
        try {
          $insert->execute($data);
          $succes = "succes 4";
          ?><script>console.log("insert ");</script><?php
        }
        catch (PDOException $e) {
         throw $e;
        }
      }
  ?>
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