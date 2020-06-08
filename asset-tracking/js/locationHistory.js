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
        }
        locationHistory(Token, trackerID, DbID);
      }
    });
    // headers
    xhr.open("POST", "http://api.overseetracking.com/WebProcessorApi.ashx",true);  
    xhr.send(data);
}
function locationHistory(Token, trackerID, DbID){
    // set form data
    var data = new FormData();
    data.append("Token", Token); //token is a variable from the login function above.
    data.append("InformationType", "HistoricalLocation");
    data.append("OperationType", "Query");
    data.append("LanguageType", "2B72ABC6-19D7-4653-AAEE-0BE542026D46");
    data.append("Arguments", '{"Speed":"-1","ProductID":"' + trackerID + '","DbID":"'+ DbID + '","StartTime":"2020-03-25 00:00:00","EndTime":"2020-06-26 23:59:59"}');
    data.append("PageArguments", '{"PageSize":"20","PageIndex":"1"}');

    var xhr = new XMLHttpRequest();
    xhr.addEventListener("readystatechange", function() {
      if(this.readyState === 4) {
        // console.log(this.responseText.Data);
        // These variables will be used by connecting to websocket.
        var historyList =JSON.parse(this.responseText);
        var i;
        // console.log(historyList.Data);
        console.log(historyList.Data.length);
        for (i = 0; i < historyList.Data.length; i++) {
            //these variables will be used to store in the database by php
            var DateTime = historyList.Data[i].DateTime;
            var Longitude = historyList.Data[i].Longitude;
            var Latitude = historyList.Data[i].Latitude;
            // all the variables inside the () brackets are needed to make the websocket connection.
            //send javascript var to php page. for database
        }
    }
    });
    // headers
    xhr.open("POST", "http://api.overseetracking.com/WebProcessorApi.ashx",true);  
    xhr.send(data);
}