<?php 
require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/header.php");

if(isset($_GET["user"])) {
	$stmt = $conn->prepare("SELECT * from users where username=:t0");
	$stmt->bindParam(":t0", $_GET["user"]);
	$stmt->execute();
	if($stmt->rowCount() == 0) { header("Location: /index_down.php"); }
	foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $user);
} else {
	header("Location: /index_down.php");
}

if($user->isBanned == 1) { header("Location: /index_down.php"); }

// grab all video counts
$stmt = $conn->prepare("SELECT * FROM videos WHERE uploaded_by=:t0 AND isPrivate=0");
$stmt->bindParam(":t0", $_GET["user"]);
$stmt->execute();
$public_videos = $stmt->rowCount();

$stmt = $conn->prepare("SELECT * FROM videos WHERE uploaded_by=:t0 AND isPrivate=1");
$stmt->bindParam(":t0", $_GET["user"]);
$stmt->execute();
$private_videos = $stmt->rowCount();

$stmt = $conn->prepare("SELECT * FROM favorites WHERE added_by=:t0");
$stmt->bindParam(":t0", $_GET["user"]);
$stmt->execute();
$favorite_videos = $stmt->rowCount();

$stmt = $conn->prepare("SELECT * FROM friends WHERE sent_by=:t0 OR sent_to=:t0 AND pending=0");
$stmt->bindParam(":t0", $_GET["user"]);
$stmt->execute();
$friends = $stmt->rowCount();


?>

<table width="100%" align="center" cellpadding="0" cellspacing="0" border="0">
	<tr valign="top">
		<td style="padding-right: 15px;">

		<table width="100%" align="center" cellpadding="0" cellspacing="0" border="0" bgcolor="#CCCCCC">
			<tr>
				<td><img src="img/box_login_tl.gif" width="5" height="5"></td>
				<td width="100%"><img src="img/pixel.gif" width="1" height="5"></td>
				<td><img src="img/box_login_tr.gif" width="5" height="5"></td>
			</tr>
			<tr>
				<td><img src="img/pixel.gif" width="5" height="1"></td>
				<td>
				
				<div class="watchTitleBar">
					<table width="100%" cellpadding="0" cellspacing="0" border="0">
						<tr valign="top">
							<td><div class="watchTitle">Friends // <span style="text-transform: capitalize;"><?php echo $_GET["user"]; ?></span></div></td>
 							<td align="right"> 
								<div style="font-weight: bold; color: #444; margin-right: 5px;">
								</div>
							</td>
						</tr>
					</table>
				</div>
				<?php
				foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $friend) {
					// what user do we fetch the info for?
					if($friend->sent_by == $_GET["user"]) { $f_name = $friend->sent_to; }
					if($friend->sent_to == $_GET["user"]) { $f_name = $friend->sent_by; }
					
					// grab all video counts
					$stmt = $conn->prepare("SELECT * FROM videos WHERE uploaded_by=:t0 AND isPrivate=0");
					$stmt->bindParam(":t0", $f_name);
					$stmt->execute();
					$f_videos = $stmt->rowCount();

					$stmt = $conn->prepare("SELECT * FROM favorites WHERE added_by=:t0");
					$stmt->bindParam(":t0", $f_name);
					$stmt->execute();
					$f_favorites = $stmt->rowCount();

					$stmt = $conn->prepare("SELECT * FROM friends WHERE sent_by=:t0 OR sent_to=:t0 AND pending=0");
					$stmt->bindParam(":t0", $f_name);
					$stmt->execute();
					$f_friends = $stmt->rowCount();

					// get latest video
					$stmt = $conn->prepare("SELECT * FROM videos WHERE uploaded_by=:t0 AND isPrivate=0 ORDER BY uploaded_on DESC LIMIT 1");
					$stmt->bindParam(":t0", $f_name);
					$stmt->execute();
					echo '				<div class="moduleEntry">
				<table width="565" cellpadding="0" cellspacing="0" border="0">
					<tr valign="top">
						<td align="center"><a href="">
						';
						foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $video) {
							echo '							<a href="watch.php?v=' . $video->id . '"><img src="get_still.php?video_id=' . $video->id . '" class="moduleEntryThumb" width="120" height="90"></a>
							<div class="moduleFeaturedTitle"><a href="watch.php?v=' . $video->id .'">' . htmlspecialchars($video->title) . '</a></div>';
						}
						echo '
						<td width="100%">
						<div class="moduleEntryTitle" style="margin-bottom: 5px;">

						<a href="profile.php?user=' . $f_name . '">' . $f_name . '</a>

						</div>
						<div class="moduleEntryDescription"><a href="profile_videos.php?user=' . $f_name . '">Videos</a> (' . $f_videos . ') | <a href="profile_favorites.php?user=' . $f_name . '">Favorites</a> (' . $f_favorites . ') | <a href="profile_friends.php?user=' . $f_name . '">Friends</a> (' . $f_friends . ')</div>
						<div class="moduleEntryDetails"></div>
						<div class="moduleEntryDetails"></div>
						</td>
					</tr>
				</table>
				</div>';

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
		<div style="font-size: 14px; font-weight: bold; margin-bottom: 10px; color: #444;">&#187; <a href="profile.php?user=<?php echo $_GET["user"]; ?>">Profile</a></div>
		<div style="font-size: 14px; font-weight: bold; margin-bottom: 10px; color: #444;">&#187; <a href="profile_videos.php?user=<?php echo $_GET["user"]; ?>">Private Videos</a> (<?php echo $public_videos; ?>)</div>
		<div style="font-size: 14px; font-weight: bold; margin-bottom: 10px; color: #444;">&#187; <a href="profile_videos_private.php?user=<?php echo $_GET["user"]; ?>">Private Videos</a> (<?php echo $private_videos; ?>)</div>
		<!-- only show this link to friends in their network -->
		<div style="font-size: 14px; font-weight: bold; margin-bottom: 10px; color: #444;">&#187; <a href="profile_favorites.php?user=<?php echo $_GET["user"]; ?>">Favorites</a> (<?php echo $favorite_videos; ?>)</div>
		<div style="font-size: 14px; font-weight: bold; margin-bottom: 10px; color: #444;">&#187; Friends</div>
		</td>
	</tr>
</table>	


<?php require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/footer.php"); ?>