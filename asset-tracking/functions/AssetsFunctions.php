<?php
/*In this file all the functions used on the page assets.php are listed.
All these functions have there own specific job see comments to find out what */

// include db file to setup database connection
include_once 'db.php';
//give var database the connection info from db.php
$database = db_connect();

// function getTS is used to get the last timestamp from database.
// The timestamp is used to make historylocation call with javascript
function getTS($database){
	$query = "SELECT * FROM `point` ORDER BY TS DESC LIMIT 1";
	$stmt = $database->prepare($query);
	$results = $stmt->execute();
	$point = $stmt->fetch(PDO::FETCH_ASSOC);
	$startTime= $point['TS'];
	return $startTime;
}
// function deleteAsset is used to delete the asset. the function is activated when delete is pressed
function deleteAsset($database){
	// $_Get delete is the id of the asset the id is used to identify the correct asset and delete it
	$query = "DELETE FROM asset WHERE ID={$_GET['delete']}";
	$insert = $database->prepare($query);
	$insert->execute();
	?>
	<script>window.location.href = "assets.php";</script>
	<?php
}
// Function submitAsset is used to submit assets to database. and connect asset to user account. by asset I mean gps tracker. The function is triggerd when add asset form is filled in and submitted.
function submitAsset($database, $User_ID){
	// set error to 0 if a if statement is not succes the error var will increase by one. There will also be a specific errormessage assigned to the error.
	// in the end there will be a check if error is 0 if not show error message.
	$error = 0;
	// check in database if the activationkey is already in use
	$activatiecode = htmlspecialchars($_POST['activatiecode']);
	$query = "SELECT * FROM asset WHERE activatiecode= :activatiecode AND user_ID = 0 LIMIT 1";
	$stmt = $database->prepare($query);
	$results = $stmt->execute(array(":activatiecode" => $activatiecode));
	$asset = $stmt->fetch(PDO::FETCH_ASSOC);
	$trackerID = $asset['trackerID'];
	if ($asset) { // if asset exists
	    if ($asset['activatiecode'] == $activatiecode) {
	    	if (!empty($_POST['name'])){ //if asset name is not empty
	    		$name = htmlspecialchars($_POST['name']);
			}else{
			    $error++;
			    $errorMessage = "Naam is leeg";
			}
			if (!empty($_POST['activatiecode'])){ // if activatiecode is not empty
			    $activatiecode = htmlspecialchars($_POST['activatiecode']);
			}else{
			    $error++;
			    $errorMessage = "Activatiecode is empty";
			}
			if (!empty($_POST['info'])){ //if info is not empty
			    $info = htmlspecialchars($_POST['info']);

			}else{
			    $error++;
			    $errorMessage = "Info is leeg";
			}
			if(!empty($_POST['seconden'])){ //if seconden is not empty
				$seconden = htmlspecialchars($_POST['seconden']);
			}else{
			    $error++;
			    $errorMessage = "Seconden is leeg";
			}
		}else{
			$error++;
			$errorMessage= "De gps tracker moet nog geactiveerd worden.";
		}
		if ($error == 0) { //if error is 0 proceed
			// insert asset into database
			$query = "UPDATE asset SET name=:gpsName, user_ID=:user_ID, info=:info WHERE trackerID =:trackerID";
			$stmt = $database->prepare($query);

			$stmt->bindValue(":trackerID", $trackerID, PDO::PARAM_STR);
			$stmt->bindValue(":gpsName", $name, PDO::PARAM_STR);
			$stmt->bindValue(":info", $info, PDO::PARAM_STR);
			$stmt->bindValue(":user_ID", $User_ID, PDO::PARAM_STR);

			try {
			    $stmt->execute();
			}
			catch (PDOException $e) {
			    echo $e->getMessage();
			}
			// all the data is handled succesfully send user to assets.php.
			header('location: assets.php');	

		}else{?>
			<!-- error was not 0 so there was a error -->
			<!-- show a html box that will contain the specified erromessage -->
			<div class="alert">
			    <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
			    <strong>Let op!</strong> <?php echo $errorMessage ?>
			</div><?php
		}
	}	
}
