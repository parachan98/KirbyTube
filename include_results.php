<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/config.php");

if(!isset($_GET["v"])) { die(); }

$stmt = $conn->prepare("SELECT * from videos where id=:t0");
$stmt->bindParam(":t0", $_GET["v"]);
$stmt->execute();
if($stmt->rowCount() == 0) { die(); }

$video_id = $_GET["v"];

$search = str_replace("+", "|", trim($_GET["search"]));
$stmt = $conn->prepare("SELECT * FROM videos WHERE isPrivate=0 AND tags REGEXP :t0 AND isPrivate=0 ORDER BY uploaded_on DESC"); // Regex!
$stmt->bindParam(":t0", $search);
$stmt->execute();

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>' . $website["instance_name"] . ' - ' . $website["instance_slogan"] . '</title>
<link href="styles.css" rel="stylesheet" type="text/css">
</head>
<body style="background-color:#DDDDDD">';

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
	echo '
	<div class="'; if($video->id == $_GET['v']){ echo "moduleEntrySelected\"><a name=\"selected\"></a>"; }else echo "moduleEntry\">"; echo '
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr valign="top">
			<td><a href="watch.php?v=' . $video->id . '" class="bold" target="_parent"><img src="get_still.php?video_id=' . $video->id . '" class="moduleEntryThumb" width="80" height="60"></a></td>
			<td width="100%">
				<div class="moduleFrameTitle"><a href="watch.php?v=' . $video->id . '" target="_parent">' . htmlspecialchars($video->title) . '</a></div>
				<div class="moduleFrameDetails">Added: ' . $Now->format('F') . ' ' . $Now->format('d') . ', ' . $Now->format('Y') . ' <br>by  <a href="profile.php?user=' . $video->uploaded_by . '" target="_parent">' . $video->uploaded_by . '</a></div>
				<div class="moduleFrameDetails">Views: ' . $view_count . '<br>Comments: ' . $comment_count . '</div><br>
			</td>
		</tr>
	</table>
</div>
	';
}
?>
</body>
</html>