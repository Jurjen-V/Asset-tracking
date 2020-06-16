//login api
trackerIDArray;
startTime;
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
        var i;
        for (i = 0; i < trackerList.Data.Tracker.length; i++) {
            //these variables will be used to store in the database by php
            var trackerID = trackerList.Data.Tracker[i].ProductID;
            var gpsName = trackerList.Data.Tracker[i].Name;
            var DbID = trackerList.Data.Tracker[i].DbID;
            var PhoneNumber = trackerList.Data.Tracker[i].PhoneNumber1;
            var Longitude = trackerList.Data.Position[0].Longitude;
            var Latitude = trackerList.Data.Position[0].Latitude;
            // all the variables inside the () brackets are needed to make the websocket connection.
            //send javascript var to php page. for database
            var length= trackerIDArray.length;
            for (i = 0; i < length; i++) {
                locationHistory(Token, trackerIDArray[i], DbID, startTime);
            }
        }
        dbParam = JSON.stringify(trackerList.Data);
        xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
          }
        };
        xmlhttp.open("GET", "functions/GPS_call.php?x=" + dbParam, true);
        xmlhttp.send(); 
      }
    });
    // headers
    xhr.open("POST", "http://api.overseetracking.com/WebProcessorApi.ashx",true);  
    xhr.send(data);
}
function locationHistory(Token, trackerIDArray, DbID, startTime){
    // set start date (begin of the day )
    let start = new Date();
    start.setHours(0,0,0,0);

    let end = new Date();
    end.setHours(23,59,59,999);
    start.toISOString();
    end.toISOString();
    // set form data
    var data = new FormData();
    data.append("Token", Token); //token is a variable from the login function above.
    data.append("InformationType", "HistoricalLocation");
    data.append("OperationType", "Query");
    data.append("LanguageType", "2B72ABC6-19D7-4653-AAEE-0BE542026D46");
    data.append("Arguments", '{"Speed":"-1","ProductID":"' + trackerIDArray + '","DbID":"'+ DbID + '","StartTime":"' + startTime + '","EndTime":"'+ end.toISOString() +'"}');
    data.append("PageArguments", '{"PageSize":"500","PageIndex":"1"}');

    var xhr = new XMLHttpRequest();
    xhr.addEventListener("readystatechange", function() {
      if(this.readyState === 4) {
        // These variables will be used by connecting to websocket.
        var historyList =JSON.parse(this.responseText);
        var i;
        for (i = 0; i < historyList.Data.length; i++) {
            //these variables will be used to store in the database by php
            var DateTime = historyList.Data[i].DateTime;
            var Longitude = historyList.Data[i].Longitude;
            var Latitude = historyList.Data[i].Latitude;
            // all the variables inside the () brackets are needed to make the websocket connection.
            //send javascript var to php page. for database
        }
        if (historyList.Data[0] != undefined) {
            historyList.Data[0]['locationTrackerID'] = trackerIDArray;
            historyList['HistoryPost'] = 1;
            dbParam = JSON.stringify(historyList.Data);
            xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
              if (this.readyState == 4 && this.status == 200) {
              }
            };
            xmlhttp.open("POST", "functions/GPS_call.php", true);
            xmlhttp.setRequestHeader("Content-type", "application/json;charset=UTF-8");
            xmlhttp.send(dbParam); 
        }
    }
    });
    // headers
    xhr.open("POST", "http://api.overseetracking.com/WebProcessorApi.ashx",true);  
    xhr.send(data);
}