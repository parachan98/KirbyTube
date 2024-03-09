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

//pagination
$limit = 10;

if(!isset($_GET['page'])) {
	$page = 1;
} else {
	$page = intval($_GET['page']);
}

$start = ($page-1)*$limit;
$total_pages = ceil($public_videos/$limit);

$stmt = $conn->prepare('SELECT * FROM videos WHERE uploaded_by=:t0 AND isPrivate=0 ORDER BY uploaded_on DESC LIMIT :t1, :t2');
$stmt->bindParam(":t0", $_GET["user"]);
$stmt->bindParam(":t1", $start, PDO::PARAM_INT);
$stmt->bindParam(":t2", $limit, PDO::PARAM_INT);
$stmt->execute();

if($page == 1) { 
	$video_count = 1; 
} else {
	$video_count = $page + $public_videos;
}

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
							<td><div class="watchTitle">Public Videos // <span style="text-transform: capitalize;"><?php echo $_GET["user"]; ?></span></div></td>
 							<td align="right"> 
								<div style="font-weight: bold; color: #444; margin-right: 5px;">
								<?php echo "Videos " . $page . "-" . $stmt->rowCount() + $page - 1 . " of " . $public_videos;?>
								</div>
							</td>
						</tr>
					</table>
				</div>

				
				<?php
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
					// start outputting video list
					echo '<div class="moduleEntry"> 
					<table width="100%" cellpadding="0" cellspacing="0" border="0">
						<tr valign="top">
							<td><a href="watch.php?v=' . $video->id . '"><img src="get_still.php?video_id=' . $video->id . '" class="moduleEntryThumb" width="120" height="90"></a></td>
							<td width="100%"><div class="moduleEntryTitle"><a href="watch.php?v=' . $video->id . '">' . htmlspecialchars($video->title) . '</a></div>
							<div class="moduleEntryDescription">' . nl2br(htmlspecialchars($video->description)) . '</div>
							<div class="moduleEntryTags"> Tags // ';
							$thetags = [];
							$thetags = array_merge($thetags, explode(" ", $video->tags));
							$thetags = array_unique($thetags);
							foreach($thetags as $tag) {
							echo '<a href="results.php?search=' . htmlspecialchars($tag) . '">' . htmlspecialchars($tag) . '</a> : ';
							}
							echo '</div>
		
							<div class="moduleEntryDetails">Added: ' . $Now->format('F') . ' ' . $Now->format('d') . ', ' . $Now->format('Y') . ' by <a href="profile.php?user=' . $_GET["user"] . '">' . $_GET["user"] . '</a></div>
							<div class="moduleEntryDetails">Views: ' . $view_count . ' | Comments: ' . $comment_count . '</div>
							</td>
		
						</tr>
					</table>
				</div>';
				}
				?>

				<!-- begin paging -->
				<?php 
				if($total_pages > 1) { 
					echo '<div style="font-size: 13px; font-weight: bold; color: #444; text-align: right; padding: 5px 0px 5px 0px;">Result Page:'; 
					for($p=1; $p<=$total_pages; $p++) { ?>
					<span style="<?= $page == $p ? 'color: #444; background-color: #FFFFFF;' : 'color: #CCC;'; ?> padding: 1px 4px 1px 4px; border: 1px solid #999; margin-right: 5px;"><?= $page == $p ? $p . '</span>' : '<a href="profile_videos.php?user=' . $_GET["user"] . '&page=' . $p . '">' . $p . '</a></span>' ?>
				<?php }}?>
				</div>
				<!-- end paging -->
		
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
		<div style="font-size: 14px; font-weight: bold; margin-bottom: 10px; color: #444;">&#187; Public Videos</div>
		<div style="font-size: 14px; font-weight: bold; margin-bottom: 10px; color: #444;">&#187; <a href="profile_videos_private.php?user=<?php echo $_GET["user"]; ?>">Private Videos</a> (<?php echo $private_videos; ?>)</div>
		<!-- only show this link to friends in their network -->
		<div style="font-size: 14px; font-weight: bold; margin-bottom: 10px; color: #444;">&#187; <a href="profile_favorites.php?user=<?php echo $_GET["user"]; ?>">Favorites</a> (<?php echo $favorite_videos; ?>)</div>
		<div style="font-size: 14px; font-weight: bold; margin-bottom: 10px; color: #444;">&#187; <a href="profile_friends.php?user=<?php echo $_GET["user"]; ?>">Friends</a> (<?php echo $friends; ?>)</div>
		</td>
	</tr>
</table>	




<?php require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/footer.php"); ?>