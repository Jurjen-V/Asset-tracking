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
            console.log(this.responseText);
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
        console.log(this.responseText);
        // These variables will be used by connecting to websocket.
        var trackerList = JSON.parse(this.responseText);
        // These variables will be used to make a connection to the websocket
        var IP = trackerList.Transfer.ServerIP;
        var port = trackerList.Transfer.WsOutputPort;

        // all the variables inside the () brackets are needed to make the websocket connection.
        websocketLogin(Token, ClientID, UserName, Password, IP, Port);
      }
    });
    // headers
    xhr.open("POST", "http://api.overseetracking.com/WebProcessorApi.ashx",true);  
    xhr.send(data);
}
function websocketLogin(Token, ClientID, UserName, Password, IP, Port){
    // this function uses variables from Login function and getTrackers function.
    // The credentials to send to the web socket
	var Credential = "{'ClientID':'" + ClientID + "','SignalName':'00','LoginType':'0','UserID':'{UserName}','Password':'{Password}','ClientType':'4','DataIP':'','DataTypeReq':[]}#";
    // Fill in the username and password form the login function above.
	Credential = Credential.replace("{UserName}", UserName).replace("{Password}", Password)
    // use the IP and port variable from getTrackers function to make connection to websocket
	 ws = new WebSocket("ws://" + IP + ":" + Port);
     // to this when system receives a message
	ws.onmessage = function (event) { 
		if (event.data) { 
		   console.log(event.data.toString());
		   //write your code logic here
		}
	};
    // when webscoket connection is closed
	ws.onclose = function (event) {
		setTimeout(function () {
			ConnectToServer();//connect again when the socket closed
		}, 5000);
	};
    // When there is a error console.log the error.
	ws.onerror = function (event) {
		if ($rootScope.IsDebug) {
			console.log(event);
		}
	};
    // when the connection is started send the credentials to the websocket
	ws.onopen = function (event) {
		//send credential when open the socket
		ws.send(Credential); 
	};
}