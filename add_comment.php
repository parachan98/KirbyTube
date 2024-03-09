<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/config.php");

if(isset($_SESSION["username"])) {
	if(!isset($_POST["comment"])) { die(); }
	if(!isset($_POST["video_id"])) { die(); }
	if(empty($_POST["field_reference_video"])) { $_POST["field_reference_video"] = NULL; }
	
	$stmt = $conn->prepare("SELECT * from videos where id=:t0");
	$stmt->bindParam(":t0", $_POST["video_id"]);
	$stmt->execute();
	if($stmt->rowCount() == 0) { die(); }
	
	$stmt = $conn->prepare("INSERT INTO comments (posted_to, attached_video, content, posted_by) VALUES (:t0, :t1, :t2, :t3)");
	$stmt->bindParam(":t0", $_POST["video_id"]);
	$stmt->bindParam(":t1", $_POST["field_reference_video"]);
	$stmt->bindParam(":t2", $_POST["comment"]);
	$stmt->bindParam(":t3", $_SESSION["username"]);
	$stmt->execute();
}

?>