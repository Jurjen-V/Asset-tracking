<?php
include 'db.php';
$database = db_connect();

// update gps to database
$dbParam = file_get_contents('php://input');
$history = json_decode($dbParam, true); // decoding received JSON to array
if(isset($history)){
	updateToDb($history, $database);
}
function updateToDb($history, $database){
	// update gps to database
	$error = 0;
	$length = count($history);
	for ($i = 0; $i < $length; $i++) {
		$trackerID = $history[0]['locationTrackerID'];
		$DateTime = $history[$i]['DateTime'];
		$Longitude = $history[$i]['Longitude'];
		$Latitude = $history[$i]['Latitude'];

			$query = "INSERT INTO `point` (Asset_ID, latitude, longitude, TS) VALUES (?,?,?,?)";
			$insert = $database->prepare($query);
			$data = array("$trackerID", "$Latitude", "$Longitude","$DateTime");
			try {
				$insert->execute($data);
			}
			catch (PDOException $e) {
				echo $e->getMessage();
			}
			header('location: assets.php?GpsUpdated=1');
	}
}
// updateToDb();
$obj = json_decode($_GET["x"], true);
if(isset($obj)){
	trackerList($obj, $database);
}
function trackerList($obj, $database){
	// db_connect();
	$error = 0;
	$length = count($obj['Tracker']);
	for ($i = 0; $i < $length; $i++) {
		$trackerID = $obj['Tracker'][$i]['ProductID'];
		$gpsName = $obj['Tracker'][$i]['Name'];
		$PhoneNumber = $obj['Tracker'][$i]['PhoneNumber1'];
		echo $PhoneNumber;
		$Latitude = $obj['Position'][$i]['Latitude'];
		$Longitude = $obj['Position'][$i]['Longitude'];
		// check in database if the gps is already in use
		// check if nothing is empty
		if (!empty($gpsName)){ //if asset name is not empty
			$gpsName = $gpsName;
		}else{
			$error++;
			$errorMessage = "Naam is leeg";
		}
		if (!empty($PhoneNumber)){ // if activatiecode is not empty
			$PhoneNumber = $PhoneNumber;
		}else{
			$error++;
			$errorMessage = "Activatiecode is empty";
		}
		$query = "SELECT * FROM asset WHERE trackerID= :trackerID LIMIT 1";
		$stmt = $database->prepare($query);
		$results = $stmt->execute(array(":trackerID" => $trackerID));
		$asset = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($asset) { // if asset exists
			if ($asset['trackerID'] == $trackerID) {
			   	// update user data
				$query = "UPDATE asset SET longitude=:Longitude, latitude=:Latitude WHERE trackerID =:trackerID";
				    
				$stmt = $database->prepare($query);

				$stmt->bindValue(":trackerID", $trackerID, PDO::PARAM_STR);
				$stmt->bindValue(":Longitude", $Longitude, PDO::PARAM_STR);
				$stmt->bindValue(":Latitude", $Latitude, PDO::PARAM_STR);

				try {
					$stmt->execute();
				}
				catch (PDOException $e) {
				    echo $e->getMessage();
				}
				// all the data is handled succesfully send user to assets.php.
				header('location: assets.php?GpsUpdated=1');
			}
		}else{
			// add gps tracker to database but do not connect it to user account
			$query = "INSERT INTO asset (trackerID, name, latitude, longitude, activatiecode, info, user_ID, seconden) VALUES (?,?,?,?,?,?,?,?)";
			$insert = $database->prepare($query);
			$data = array("$trackerID","$gpsName","$Latitude", "$Longitude", "$PhoneNumber", "" ,"", "");
			var_dump($data);

			try {
				$insert->execute($data);
			}
			catch (PDOException $e) {
				echo $e->getMessage();
			}
			// header('location: assets.php?GpsUpdated=1');
		}	
	}
}
?>