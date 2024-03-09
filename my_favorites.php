<?php 
require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/header.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/logincheck.php");

// video deletion
if(isset($_GET["remove"])) {
	$stmt = $conn->prepare("SELECT * FROM favorites WHERE video_id=:t0 AND added_by=:t1");
	$stmt->bindParam(":t0", $_GET["remove"]);
	$stmt->bindParam(":t1", $_SESSION["username"]);
	$stmt->execute();
	if($stmt->rowCount() !== 0) {
		$conn->query("DELETE FROM favorites WHERE video_id='" . $_GET["remove"] . "' AND added_by='" . $_SESSION["username"] . "'");
		die(header("Location: /my_favorites.php?err=1"));
	}
}

//pagination
$limit = 10;

if(!isset($_GET['page'])) {
	$page = 1;
} else {
	$page = intval($_GET['page']);
}

$stmt = $conn->prepare("SELECT * FROM favorites WHERE added_by=:t0 ORDER BY added_on DESC");
$stmt->bindParam(":t0", $_SESSION["username"]);
$stmt->execute();
$total_videos = $stmt->rowCount();

$start = ($page-1)*$limit;
$total_pages = ceil($total_videos/$limit);

$stmt = $conn->prepare('SELECT * FROM favorites WHERE added_by=:t0 ORDER BY added_on DESC LIMIT :t1, :t2');
$stmt->bindParam(":t0", $_SESSION["username"]);
$stmt->bindParam(":t1", $start, PDO::PARAM_INT);
$stmt->bindParam(":t2", $limit, PDO::PARAM_INT);
$stmt->execute();

if($page == 1) {
	$video_count = 1; 
} else {
	$video_count = $page + $total_videos;
}

if(isset($_GET["err"])) {
	if($_GET["err"] == 1) { error("Video removed from Favorites!"); }
}

$favorite_tags = [];

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
							<td><div class="watchTitle">My Favorites</div></td>
 							<td align="right"> 
								<div style="font-weight: bold; color: #444; margin-right: 5px;">
								<?php echo "Videos " . $page . "-" . $stmt->rowCount() + $page - 1 . " of " . $total_videos;?>
								</div>
							</td>
						</tr>
					</table>
				</div>
				<?php
				foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $favorite) {
					$stmt = $conn->prepare("SELECT * from videos WHERE id=:t0 AND isPrivate=0");
					$stmt->bindParam(":t0", $favorite->video_id);
					$stmt->execute();
					foreach($stmt->fetchall(PDO::FETCH_OBJ) as $video);
					
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
							<td>
								<a href="watch.php?v=' . $video->id . '"><img src="get_still.php?video_id=' . $video->id . '" class="moduleEntryThumb" width="120" height="90"></a>
								<div class="tableFavRemove">
									<form method="post" action="?remove=' . $video->id . '" style="margin: 0">
										<input type="submit" value="Remove Video" style="margin-left: 10px"><br>
									</form>
								</div>
							</td>
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
		
							<div class="moduleEntryDetails">Added: ' . $Now->format('F') . ' ' . $Now->format('d') . ', ' . $Now->format('Y') . ' by <a href="profile.php?user=' . $video->uploaded_by . '">' . $video->uploaded_by . '</a></div>
							<div class="moduleEntryDetails">Views: ' . $view_count . ' | Comments: ' . $comment_count . '</div>
							</td>
		
						</tr>
					</table>
				</div>';
				//add video tags into an array for favorite tags
				$favorite_tags = array_merge($favorite_tags, explode(" ", $video->tags));
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
		<div style="font-weight: bold; color: #333; margin: 10px 0px 5px 0px;">Favorite Tags:</div>
		<?php
		foreach($favorite_tags as $tag) {
			echo '<div style="padding: 0px 0px 5px 0px; color: #999;">&#187; <a href="results.php?search=' . htmlspecialchars($tag) . '">'. htmlspecialchars($tag) . '</a></div>';
		}
		?>
<?php require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/footer.php"); ?>