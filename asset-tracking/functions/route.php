<?php
include_once 'db.php';

$database = db_connect();

// if delete is pressed
function deleteRoute($database){
	// $_Get delete is the id of the asset the id is used to identify the correct asset and delete it
	// $_GEt TS is the timestamp of the asset lat lon points it will delete all the asset points of that timestamp.
	$route_id= $_GET['ID'];
    $query = "DELETE FROM point WHERE ASSET_ID='{$_GET['delete']}' AND CAST(point.TS AS DATE) = '{$_GET['TS']}' ";
    $delete = $database->prepare($query);
    $delete->execute();
    header('location: route.php?ID='.$route_id);
}
function getRoute($database){
	// select the traveled routes ordered by timestamp.
	$result_name = $database->prepare("SELECT * FROM `point` inner join asset on asset.trackerID = point.ASSET_ID WHERE point.ASSET_ID=:ASSET_ID");
	$result_name->execute(array(":ASSET_ID" => $_GET['ID']));
	$result = $result_name->fetch(PDO::FETCH_ASSOC);
	$assetName= "";
	// if there are no traveled routes show error message.
	if(!$result){
		$errorMessage = "Geen afgelegde routes gevonden";
		?>
		<div class="alert">
		   	<span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
		    <strong>Let op!</strong> <?php echo $errorMessage ?>
		</div><?php
	}else{
		return $assetName = $result['name'];
	}
}