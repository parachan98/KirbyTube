<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/config.php");

if(isset($_SESSION["username"])) {
	if(!isset($_GET["video_id"])) { die(); }
	
	// check if video even exists
	$stmt = $conn->prepare("SELECT * from videos where id=:t0");
	$stmt->bindParam(":t0", $_GET["video_id"]);
	$stmt->execute();
	if($stmt->rowCount() == 0) { die(); }
	
	foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $video);
	if($video->uploaded_by == $_SESSION["username"]) { die(); }
	
	// check if video has been added to favorites
	$stmt = $conn->prepare("SELECT * from favorites where video_id=:t0 AND added_by=:t1");
	$stmt->bindParam(":t0", $_GET["video_id"]);
	$stmt->bindParam(":t1", $_SESSION["username"]);
	$stmt->execute();
	if($stmt->rowCount() == 1) { die(); }
	
	$stmt = $conn->prepare("INSERT INTO favorites (video_id, added_by) VALUES (:t0, :t1)");
	$stmt->bindParam(":t0", $_GET["video_id"]);
	$stmt->bindParam(":t1", $_SESSION["username"]);
	$stmt->execute();
}

?>