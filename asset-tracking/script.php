<?php 
// start session
  session_start();
  // make this file a javascript file
  header("Content-type: application/javascript");
  // include db file
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
// set map
var map = L.map('map');
document.getElementsByClassName("info_box")[0].style.display = "none";
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

// Set our initial location and zoomlevel
map.setView([52.132633, 5.291266], 6);
// if the user i signed in
<?php if (isset($_SESSION['email'])) : ?>
// set the height of the map so there is space for the nav bar
document.getElementById("map").style.height = "calc(100% - 64px)";
// hide the login form
document.getElementById("Btn").style.display = "none";
var layerGroup = L.layerGroup().addTo(map);
// get last location of the gps trackers that are connected to the account and put them in a javascript array
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
assets = array.map(s => eval('null,' + s));
function addlayer() {
  // set the popup data for the gps trackers
  // if you click on a gps tracker it will show his name and his activationkey
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
  var i;
  layerGroup.clearLayers();
  document.getElementById("info_box").innerHTML = ""; 
  // style the gps trackers as google maps circles.
  for (i = 0; i < assets.length; i++) {
    // data = popup_data.map(s => eval('null,' + s));
      L.circleMarker(assets[i], {
          color: "#4285F4",
          weight: 0,
          fillColor: "#4285F4",
          fillOpacity: 0.5,
          radius: 20,
          'className': 'pulse'
      }).addTo(layerGroup);
      marker = L.circleMarker(assets[i], {
          color: "white",
          opacity: 1,
          weight: 2,
          fillColor: "#4285F4",
          radius: 10,
          opacity: 1,
          fillOpacity: 1,
          'className': 'pulse'
      }).addTo(layerGroup);
      marker.bindPopup(popup_data[i]); 
    // get location name from API
    // convert the lat lon data to city names for the info box
    document.getElementsByClassName('info_box')[0].style.display = "block";
    fetch('https://api.opencagedata.com/geocode/v1/json?key=5b104f01c9434e3dad1e2d6a548445da&language&q=' + assets[i] + '&pretty=1&no_annotations=1')
    .then(response => {
      if(response.ok) return response.json();
      throw new Error(response.statusText)
    })
    .then(function handleData(data){
      data = data.results[0].components['suburb'];
      console.log(data);
      // insert the city names into the info box
      document.getElementById('info_box').innerHTML +=  data + "<br>"; 
    })
    .catch(function handleError(error){
    })
  }
}
addlayer();
// excecute the function everey min to make sure it has the last location of the gps trackers
setInterval(addlayer, 60000);
<?php endif ?>
