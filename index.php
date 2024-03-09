<?php 
require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/header.php");

if(isset($_GET["v"])) {
	header("Location: /watch.php?v=" . $_GET["v"]);
}

//logged in welcome box
if(isset($_SESSION["username"])) {
	
	$stmt = $conn->prepare("SELECT * FROM users WHERE username=:t0");
	$stmt->bindParam(":t0", $_SESSION["username"]);
	$stmt->execute();
	foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $user);
	
	// grab all video counts
	$stmt = $conn->prepare("SELECT * FROM videos WHERE uploaded_by=:t0");
	$stmt->bindParam(":t0", $_SESSION["username"]);
	$stmt->execute();
	$public_videos = $stmt->rowCount();

	$stmt = $conn->prepare("SELECT * FROM favorites WHERE added_by=:t0");
	$stmt->bindParam(":t0", $_SESSION["username"]);
	$stmt->execute();
	$favorite_videos = $stmt->rowCount();

	$stmt = $conn->prepare("SELECT * FROM views WHERE viewed_by=:t0");
	$stmt->bindParam(":t0", $_SESSION["username"]);
	$stmt->execute();
	$videos_watched = $stmt->rowCount();
	
	$stmt = $conn->prepare("SELECT * FROM friends WHERE sent_by=:t0 OR sent_to=:t0 AND pending=0");
	$stmt->bindParam(":t0", $_SESSION["username"]);
	$stmt->execute();
	$friends = $stmt->rowCount();

	$stmt = $conn->prepare("SELECT * FROM friends WHERE sent_by=:t0 OR sent_to=:t0 AND pending=1");
	$stmt->bindParam(":t0", $_GET["user"]);
	$stmt->execute();
	$pending_friends = $stmt->rowCount();

	echo '<table width="790" align="center" cellpadding="0" cellspacing="0" border="0">
	<tr valign="top">
		<td style="padding-right: 15px;">
		
		<table width="595" align="center" cellpadding="0" cellspacing="0" border="0" bgcolor="#E5ECF9">
			<tr>
				<td><img src="img/box_login_tl.gif" width="5" height="5"></td>
				<td width="100%"><img src="img/pixel.gif" width="1" height="5"></td>
				<td><img src="img/box_login_tr.gif" width="5" height="5"></td>
			</tr>
			<tr>
				<td><img src="img/pixel.gif" width="5" height="1"></td>
				<td style="padding: 5px 0px 5px 0px;">
				
				<table width="100%" cellpadding="0" cellspacing="0" border="0">
					<tr valign="top">
					<td style="border-right: 1px dashed #369; padding: 0px 10px 10px 10px; color: #444;">
					<div style="color:#506A85;font-size: 16px; font-weight: bold; margin-bottom: 5px;">My Account Overview</div>
					<b>User Name:</b> <a href="profile.php?user=' . $_SESSION["username"] . '">' . $_SESSION["username"] . '</a><br>
					<b>Email:</b> ' . $user->email . '<br>
					<b>Videos watched:</b> ' . $videos_watched . '<br>
					
					<div style="margin-top: 5px; background-color: #FFFFFF; padding-top: 5px; padding-bottom: 5px; margin-bottom: 5px; text-align: center">
						<table width="100%">
							<tr>
								<td width="100" align="center"><a href="/my_videos.php" style="font-size: 14px">Videos: ' . $public_videos . '</a></td>
								<td width="100" align="center"><a href="/my_favorites.php" style="font-size: 14px">Favorites: ' . $favorite_videos . '</a></td>
								<td width="100" align="center"><a href="/my_friends.php" style="font-size: 14px">Friends: ' . $friends . '</a><br></td>
						</table>
					</div>					
					</td>
					<td style="padding: 0px 10px 10px 10px; color: #444;">
						<img src="/img/mail.gif"> You have <a href="/my_messages.php">0 new messages</a>.
					</td>
					</tr>
				</table>

									
				</td>
				<td><img src="img/pixel.gif" width="5" height="1"></td>
			</tr>
			<tr>
				<td><img src="img/box_login_bl.gif" width="5" height="5"></td>
				<td><img src="img/pixel.gif" width="1" height="5"></td>
				<td><img src="img/box_login_br.gif" width="5" height="5"></td>
			</tr>
		</table><br>';
} else {
	//logged out welcome box
	echo '<table width="790" align="center" cellpadding="0" cellspacing="0" border="0">
	<tr valign="top">
		<td style="padding-right: 15px;">
		
		<table width="595" align="center" cellpadding="0" cellspacing="0" border="0" bgcolor="#E5ECF9">
			<tr>
				<td><img src="img/box_login_tl.gif" width="5" height="5"></td>
				<td width="100%"><img src="img/pixel.gif" width="1" height="5"></td>
				<td><img src="img/box_login_tr.gif" width="5" height="5"></td>
			</tr>
			<tr>
				<td><img src="img/pixel.gif" width="5" height="1"></td>
				<td style="padding: 5px 0px 10px 0px;">
				
				<table width="100%" cellpadding="0" cellspacing="0" border="0">
					<tr valign="top">
					<td width="33%" style="border-right: 1px dashed #369; padding: 0px 10px 10px 10px; color: #444;">
					<div style="font-size: 16px; font-weight: bold; margin-bottom: 5px;"><a href="my_videos_upload.php">Upload</a></div>
					Quickly upload and tag videos in almost any video format.
					</td>
					<td width="33%" style="border-right: 1px dashed #369; padding: 0px 10px 10px 10px; color: #444;">
					<div style="font-size: 16px; font-weight: bold; margin-bottom: 5px;"><a href="browse.php">Watch</a></div>
					Instantly find and watch 1000\'s of fast streaming videos.
					</td>
					<td width="33%" style="padding: 0px 10px 10px 10px; color: #444;">
					<div style="font-size: 16px; font-weight: bold; margin-bottom: 5px;"><a href="my_friends_invite.php">Share</a></div>
					Easily share your videos with family, friends, or co-workers.
					</td>
					</tr>
				</table>
				
				</td>
				<td><img src="img/pixel.gif" width="5" height="1"></td>
			</tr>
			<tr>
				<td><img src="img/box_login_bl.gif" width="5" height="5"></td>
				<td><img src="img/pixel.gif" width="1" height="5"></td>
				<td><img src="img/box_login_br.gif" width="5" height="5"></td>
			</tr>
		</table><br>';
}
?>
		<table width="595" align="center" cellpadding="0" cellspacing="0" border="0" bgcolor="#CCCCCC">
			<tr>
				<td><img src="img/box_login_tl.gif" width="5" height="5"></td>
				<td width="100%"><img src="img/pixel.gif" width="1" height="5"></td>
				<td><img src="img/box_login_tr.gif" width="5" height="5"></td>
			</tr>
			<tr>
				<td><img src="img/pixel.gif" width="5" height="1"></td>
				<td width="585">
				<div class="moduleTitleBar">
				<div class="moduleTitle"><div style="float: right; padding: 1px 5px 0px 0px; font-size: 12px;"><a href="browse.php">See More Videos</a></div>
				Today's Featured Videos
				</div>
				</div>
				
<?php
//featured videos
$stmt = $conn->prepare("SELECT * from videos WHERE isFeatured=1 AND isPrivate=0 ORDER BY uploaded_on DESC LIMIT 5");
$stmt->execute();
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
	echo "\n					<div class=\"moduleEntry\">
					<table width=\"565\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
						<tr valign=\"top\">
							<td><a href=\"index.php?v=" . $video->id . "\"><img src=\"get_still.php?video_id=" . $video->id . "\" class=\"moduleEntryThumb\" width=\"120\" height=\"90\"></a></td>
							<td width=\"100%\"><div class=\"moduleEntryTitle\"><a href=\"index.php?v=" . $video->id . "\">" . htmlspecialchars($video->title) . "</a></div>
							<div class=\"moduleEntryDescription\">" . nl2br(htmlspecialchars($video->description)) . "</div>
							<div class=\"moduleEntryTags\">Tags // ";
							$thetags = [];
							$thetags = array_merge($thetags, explode(" ", $video->tags));
							$thetags = array_unique($thetags);
							foreach($thetags as $tag) {
							echo "<a href=\"results.php?search=" . htmlspecialchars($tag) . "\">" . htmlspecialchars($tag) . "</a> : ";
							}
							echo "</div>
							<div class=\"moduleEntryDetails\">Added: " . $Now->format('F') . " " . $Now->format('d') . ", " . $Now->format('Y') . " by <a href=\"profile.php?user=" . $video->uploaded_by . "\">" . $video->uploaded_by . "</a></div>
							<div class=\"moduleEntryDetails\">Views: $view_count | Comments: $comment_count </div>
							</td>
						</tr>
					</table>
					</div>
\n";
}
?>
				</td>
				<td><img src="img/pixel.gif" width="5" height="1"></td>
			</tr>
			<tr>
				<td><img src="img/box_login_bl.gif" width="5" height="5"></td>
				<td><img src="img/pixel.gif" width="1" height="5"></td>
				<td><img src="img/box_login_br.gif" width="5" height="5"></td>
			</tr>
		</table>

		
		</td>
		<td width="180">
		
		<table width="180" align="center" cellpadding="0" cellspacing="0" border="0" bgcolor="#FFEEBB">
			<tr>
				<td><img src="img/box_login_tl.gif" width="5" height="5"></td>
				<td><img src="img/pixel.gif" width="1" height="5"></td>
				<td><img src="img/box_login_tr.gif" width="5" height="5"></td>
			</tr>
			<tr>
				<td><img src="img/pixel.gif" width="5" height="1"></td>
				<td width="170">
		
				
<?php
if(isset($_SESSION["username"])) {
	echo "				<div style=\"font-size: 16px; font-weight: bold; text-align: center; padding: 5px 5px 10px 5px;\"><a href=\"#\">Invite your friends to join " . $website["instance_name"] . "!</a></div>";
} else echo "				<div style=\"font-size: 16px; font-weight: bold; text-align: center; padding: 5px 5px 10px 5px;\"><a href=\"signup.php\">Sign up for your free account!</a></div>";
?>				
				
				
								
				</td>
				<td><img src="img/pixel.gif" width="5" height="1"></td>
			</tr>
			<tr>
				<td><img src="img/box_login_bl.gif" width="5" height="5"></td>
				<td><img src="img/pixel.gif" width="1" height="5"></td>
				<td><img src="img/box_login_br.gif" width="5" height="5"></td>
			</tr>
		</table>

		<div style="margin: 10px 0px 5px 0px; font-size: 12px; font-weight: bold; color: #333;">Recent Tags:</div>
		<div style="font-size: 13px; color: #333333;">
		<?php 
		$stmt = $conn->query("SELECT tags FROM videos WHERE isPrivate=0 ORDER BY uploaded_on DESC LIMIT 50");
		$related_tags = [];
		foreach($stmt as $result) $related_tags = array_merge($related_tags, explode(" ", $result['tags']));
		$related_tags = array_unique($related_tags);
		foreach($related_tags as $tag) {
			echo '<a style="font-size: 12px;" href="results.php?search=' . htmlspecialchars($tag) . '">' . htmlspecialchars($tag) . '</a> : ';
		}
		?>
		</div>
		
					
		<div style="font-size: 14px; font-weight: bold; margin-top: 10px;"><a href="tags.php">See More Tags</a></div>
		
		</div>
<?php require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/footer.php"); ?>