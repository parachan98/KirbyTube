<?php
// ----------------------------------------------------------
//   This is specifically for the Flash video list embed.
// This might branch off into an XML rest API implementation.
//    The only available request is sequence_overview.
// ----------------------------------------------------------
require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/config.php");
header("Content-Type: text/xml");
if(!isset($_GET['user'])) { die(); }

$stmt = $conn->prepare('SELECT * FROM videos WHERE uploaded_by=:t0 AND isPrivate=0 ORDER BY uploaded_on DESC');
$stmt->bindParam(":t0", $_GET['user']);
$stmt->execute();
$public_videos = $stmt->rowCount();

echo '<?xml version="1.0" encoding="utf-8"?>
 <ut_response>
  <response_type>sequence_response</response_type>
  <response>
      <sequence_overview>
          <title>' . $_GET["user"] . '</title>
          <length>' . $public_videos . '</length>
          <md5_sum>' . md5($_GET["user"]) . '</md5_sum>
      </sequence_overview>
	  <sequence_items>
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
		  $stmt->bindParam(":t0", $video->id);		$stmt->execute();
		  $comment_count = $stmt->rowCount();	
		  echo '
          <sequence_item>
              <id>' . $video->id . '</id>
              <author>' . $video->uploaded_by . '</author>
              <title>' . htmlentities($video->title) . '</title>
              <keywords>' . htmlentities($video->tags) . '</keywords>
              <description>' . htmlentities($video->description) . '</description>
              <date_uploaded>' . $Now->format('F') . ' ' . $Now->format('d') . ', ' . $Now->format('Y') .' </date_uploaded>
              <view_count>' . $view_count . '</view_count>
              <comment_count>' . $comment_count . '</comment_count>
          </sequence_item>
';
	  }
      
echo '</sequence_items>
  </response>
 </ut_response>';

?>