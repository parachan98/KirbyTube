<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/config.php");
header("Content-Type: image/jpeg");

if(!isset($_GET["video_id"])) { die(); }
if(!isset($_GET["still_id"])) { $_GET["still_id"] = 2; }

$stmt = $conn->prepare("SELECT * from videos where id=:t0");
$stmt->bindParam(":t0", $_GET["video_id"]);
$stmt->execute();
if($stmt->rowCount() == 0) { die(); }

if(isset($_GET["still_id"])) { 
	if(!file_exists($_SERVER["DOCUMENT_ROOT"] . "/content/thumb/" . $_GET["video_id"] . "_" . $_GET["still_id"] . ".jpg")) { 
		echo file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/img/comingsoon.jpg"); 
	} else {
		echo file_get_contents($_SERVER["DOCUMENT_ROOT"]. "/content/thumb/" . $_GET["video_id"] . "_" . $_GET["still_id"] . ".jpg");
	}
}

?>