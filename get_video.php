<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/config.php");

if(!isset($_GET["video_id"])) { die(); }
if(!isset($_GET["format"])) { $_GET["format"] = "flv"; }

$stmt = $conn->prepare("SELECT * from videos where id=:t0");
$stmt->bindParam(":t0", $_GET["video_id"]);
$stmt->execute();
if($stmt->rowCount() == 0) { die(); }
foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $video);

$stmt = $conn->prepare("SELECT * from servers where id=:t0");
$stmt->bindParam(":t0", $video->server);
$stmt->execute();
if($stmt->rowCount() == 0) { die(); }
foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $server);

header("Location: " . $server->server . $_GET["video_id"] . "." . $_GET["format"]);

?>