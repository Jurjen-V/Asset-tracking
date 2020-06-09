<?php 
// start session
  session_start();
  // make this file a javascript file
  header("Content-type: application/javascript");
  // include db file
  include_once 'functions/db.php';
  $database = db_connect();
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
//login api
function login(){
  //set form data
    var data = new FormData();
    data.append("Token", "");
    data.append("InformationType", "User");
    data.append("OperationType", "SignIn");
    data.append("LanguageType", "2B72ABC6-19D7-4653-AAEE-0BE542026D46");
    data.append("Arguments", "{\"UserName\":\"HawarITInternGPS\",\"Password\":\"tTa7r3KZ\"}");

    var xhr = new XMLHttpRequest(); 
    xhr.addEventListener("readystatechange", function() {
        if(this.readyState === 4) {
            //get responsetext
            // these variables will be used by getting the tracker list and making the websocket login.
            var obj = JSON.parse(this.responseText);
            var Token = obj.Token; // token will be used to call to api
            // These variables will be used to make a connection to the websocket
            var ClientID = obj.Data.SessionID;
            var UserName = obj.Data.UserName;
            var Password = obj.Data.Password;
            // excecute getTrackers() with token
            getTrackers(Token, ClientID, UserName, Password);
        }
    });
    //headers
    xhr.open("POST", "http://api.overseetracking.com/WebProcessorApi.ashx",true);  
    xhr.send(data);
}
login();
// getTrakcers form api
function getTrackers(Token, ClientID, UserName, Password){
  // set form data
    var data = new FormData();
    data.append("Token", Token); //token is a variable from the login function above.
    data.append("InformationType", "Product");
    data.append("OperationType", "GetMyTracker");
    data.append("LanguageType", "2B72ABC6-19D7-4653-AAEE-0BE542026D46");
    data.append("Arguments", "{\"TrackerType\":\"1\"}");

    var xhr = new XMLHttpRequest();
    xhr.addEventListener("readystatechange", function() {
      if(this.readyState === 4) {
        
        // These variables will be used by connecting to websocket.
        var trackerList =JSON.parse(this.responseText);
        // These variables will be used to make a connection to the websocket
        var IP = trackerList.Data.Transfer[0].ServerIP;
        var Port = trackerList.Data.Transfer[0].WsOutputPort;
        var SystemNo = trackerList.Data.Tracker[0].SystemNo;
        //these variables will be used to store in the database by php
        for (i = 0; i < trackerList.Data.Tracker.length; i++) {
          var trackerID = trackerList.Data.Tracker[i].ProductID;
          var gpsName = trackerList.Data.Tracker[i].Name;
          var PhoneNumber = trackerList.Data.Tracker[i].PhoneNumber1;
          // all the variables inside the () brackets are needed to make the websocket connection.
          <?php 
            $User_ID= $_SESSION['id'];
            $query = "SELECT * FROM asset WHERE user_ID= :User_ID";
            $stmt = $database->prepare($query);
            $results = $stmt->execute(array(":User_ID" => $User_ID));
            $asset = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($asset) :
              $trackerID = $asset["trackerID"]; 
            ?>
              var userTrackers = "<?= $trackerID;?>";
              if (userTrackers === trackerID){
                websocketLogin(Token, ClientID, UserName, Password, IP, Port, gpsName, PhoneNumber,SystemNo);
              }
            <?php endif ?>
        }
      }
    });
    // headers
    xhr.open("POST", "http://api.overseetracking.com/WebProcessorApi.ashx",true);  
    xhr.send(data);
}
function websocketLogin(Token, ClientID, UserName, Password, IP, Port, gpsName, PhoneNumber,SystemNo){
    // this function uses variables from Login function and getTrackers function.
    // The credentials to send to the web socket
  var Credential = "{'ClientID':'" + ClientID + "','SignalName':'00','LoginType':'0','UserID':'{UserName}','Password':'{Password}','ClientType':'4','DataIP':'','DataTypeReq':[]}";
    // Fill in the username and password form the login function above.
  Credential = Credential.replace("{UserName}", UserName).replace("{Password}", Password)
    // use the IP and port variable from getTrackers function to make connection to websocket
   ws = new WebSocket("ws://" + IP + ":" + Port);
     // to this when system receives a message
  ws.onmessage = function (event) { 
    if (event.data) { 
      let str = event.data.toString();
      str = str.slice(0, -1); 
      var jsonData = JSON.parse(str);
      if (jsonData.SignalName === "80" || jsonData.SignalName === "81"){
        var Latitude = jsonData.Latitude;
        var Longitude = jsonData.Longitude;
        var DateTime = jsonData.DateTime;
        var Livelatlong = [Latitude, Longitude];
        addlayer(Livelatlong, gpsName, PhoneNumber, DateTime);        
      }
    } 
  };
    // when webscoket connection is closed
  ws.onclose = function (event) {
    setTimeout(function () {
      ws.send(Credential);//connect again when the socket closed
    }, 5000);
        console.log("close");
  };
    // When there is a error console.log the error.
  ws.onerror = function (event) {
    if ($rootScope.IsDebug) {
      console.log(event);
            console.log("error");
    }
  };
  var location = "{'SignalName':'30','SystemNo':" + SystemNo + "}#";
    // when the connection is started send the credentials to the websocket
  ws.onopen = function (event) {
    //send credential when open the socket
    ws.send(Credential); 
    ws.send(location);
  };
}

// set the height of the map so there is space for the nav bar
document.getElementById("map").style.height = "calc(100% - 64px)";
// hide the login form
document.getElementById("Btn").style.display = "none";
var layerGroup = L.layerGroup().addTo(map);

function addlayer(Livelatlong, gpsName, PhoneNumber, DateTime) {
  // set the popup data for the gps trackers
  // if you click on a gps tracker it will show his name and his activationkey
  var popup_data= ["Name: " + gpsName + " activatiecode: " + PhoneNumber + " DateTime: " + DateTime ];  
  var i;
  layerGroup.clearLayers();
  // style the gps trackers as google maps circles.
  for (i = 0; i < Livelatlong.length; i++) {
    // data = popup_data.map(s => eval('null,' + s));
      L.circleMarker(Livelatlong, {
          color: "#4285F4",
          weight: 0,
          fillColor: "#4285F4",
          fillOpacity: 0.5,
          radius: 20,
          'className': 'pulse'
      }).addTo(layerGroup);
      marker = L.circleMarker(Livelatlong, {
          color: "white",
          opacity: 1,
          weight: 2,
          fillColor: "#4285F4",
          radius: 10,
          opacity: 1,
          fillOpacity: 1,
          'className': 'pulse'
      }).addTo(layerGroup);
      marker.bindPopup(popup_data[0]); 
  }
}
<?php endif ?>
