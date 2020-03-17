<?php 
  session_start();
  header("Content-type: application/javascript");
  include_once './db.php';
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

// Set our initial location and zoomlevel
map.setView([52.132633, 5.291266], 6);
<?php if (isset($_SESSION['email'])) : ?>
document.getElementById("map").style.height = "calc(100% - 64px)";
document.getElementById("Btn").style.display = "none";
var array = [
  <?php
      $result_users = $database->prepare("SELECT * FROM asset WHERE latitude != '' AND longitude != '' AND user_ID =". $_SESSION['id']);
      $result_users->execute();
      for($i=0; $row = $result_users->fetch(); $i++){
        $id = $row['ID'];
        $latitude = $row["latitude"];
        $longitude = $row["longitude"]; 
        echo "'[$latitude, $longitude]',";

      }
  ?>
];
var popup_data= [
  <?php
  $result_users = $database->prepare("SELECT * FROM asset WHERE latitude != '' AND longitude != '' AND  user_ID =". $_SESSION['id']);
  $result_users->execute();
  for($i=0; $row = $result_users->fetch(); $i++){
    $name = $row['name'];
    $activatiecode =$row['activatiecode']; 
    echo "'Name: $name <br> activatiecode: $activatiecode',";
  }
  ?>
];
assets = array.map(s => eval('null,' + s));
console.log(assets);
console.log(popup_data);
var i;
for (i = 0; i < assets.length; i++) {
  document.createElement("a");

  // data = popup_data.map(s => eval('null,' + s));
    L.circleMarker(assets[i], {
        color: "#4285F4",
        weight: 0,
        fillColor: "#4285F4",
        fillOpacity: 0.5,
        radius: 20,
        'className': 'pulse'
    }).addTo(map);
    marker = L.circleMarker(assets[i], {
        color: "white",
        opacity: 1,
        weight: 2,
        fillColor: "#4285F4",
        radius: 10,
        opacity: 1,
        fillOpacity: 1,
        'className': 'pulse'
    }).addTo(map);
    marker.bindPopup(popup_data[i]);
}
<?php endif ?>
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