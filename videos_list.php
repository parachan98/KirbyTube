<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/config.php");

if(!isset($_GET["user"])) { die(); }

$stmt = $conn->prepare("SELECT * from users where username=:t0");
$stmt->bindParam(":t0", $_GET["user"]);
$stmt->execute();
if($stmt->rowCount() == 0) { die(); }

$stmt = $conn->prepare("SELECT * FROM videos WHERE uploaded_by=:t0 AND isPrivate=0 ORDER BY uploaded_on DESC LIMIT 50"); //safe to assume limit was 50...
$stmt->bindParam(":t0", $_GET["user"]);
$stmt->execute();

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>' . $website["instance_name"] . ' - ' . $website["instance_slogan"] . '</title>
<link href="styles.css" rel="stylesheet" type="text/css">
</head>
<body style="background-color:#DDDDDD">
<div style="font-weight: bold; padding-left: 5px; padding: 3px; padding-right: 5px; border-bottom: 1px dashed #999;">
<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td>My Videos // <span style="text-transform: capitalize;">' . $_GET["user"] . '</span> (' . $stmt->rowCount() . ')</td>
		<td align="right"><div style="vertical-align: text-bottom; text-align: right; padding: 2px;">
		<a href="http://' . $_SERVER["HTTP_HOST"] . '" target="_blank"><img src="' . $website["instance_banner"] . '" width="38" height="15" border="0"></a>
		</div></td>
	</tr>
</table>
</div>
';

foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $video) {
	$Now = new DateTime($video->uploaded_on);
	// get view count
	$stmt = $conn->prepare("SELECT * FROM views WHERE viewed_video=:t0");
	$stmt->bindParam(":t0", $video->id);
	$stmt->execute();
	$view_count = $stmt->rowCount();
	//get comment count
	$stmt = $conn->prepare("SELECT * FROM comments WHERE posted_to=:t0");
	$stmt->bindParam(":t0", $video->id);
	$stmt->execute();
	$comment_count = $stmt->rowCount();
	echo '<div class="moduleEntry">
<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr valign="top">
		<td><a href="watch.php?v=' . $video->id . '" class="bold" target="_parent"><img src="get_still.php?video_id=' . $video->id . '" class="moduleEntryThumb" width="80" height="60"></a></td>
		<td width="100%"><div class="moduleFrameTitle"><a href="watch.php?v=' . $video->id . '" target="_parent">' . htmlspecialchars($video->title) . '</a></div>
		<div class="moduleFrameDetails">Added: ' . $Now->format('F') . ' ' . $Now->format('d') . ', ' . $Now->format('Y') . '<br>by <a href="/profile.php?user=' . $video->uploaded_by . '" target="_parent">' . $video->uploaded_by . '</a></div>
		<div class="moduleFrameDetails">Views: ' . $view_count . ' <br> Comments: ' . $comment_count . '</div>
		
		</td>
	</tr>
</table>
</div>';
}

?>

