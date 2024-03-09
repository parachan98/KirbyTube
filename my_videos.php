<?php 
require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/header.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/logincheck.php");

// video deletion
if(isset($_GET["delete"])) {
	$stmt = $conn->prepare("SELECT uploaded_by FROM videos WHERE id=:t0");
	$stmt->bindParam(":t0", $_GET["delete"]);
	$stmt->execute();
	if($stmt->rowCount() !== 0) {
		foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $video_to_delete) {
			if($video_to_delete->uploaded_by == $_SESSION["username"]) {
				$conn->query("DELETE FROM videos WHERE id='" . $_GET["delete"] . "'");
				$conn->query("DELETE FROM favorites WHERE video_id='" . $_GET["delete"] . "'");
				// remove files
				unlink($_SERVER["DOCUMENT_ROOT"] . "/content/video/" . $_GET["delete"] . ".3gp");
				unlink($_SERVER["DOCUMENT_ROOT"] . "/content/video/" . $_GET["delete"] . ".flv");
				unlink($_SERVER["DOCUMENT_ROOT"] . "/content/video/" . $_GET["delete"] . ".mp4");
				unlink($_SERVER["DOCUMENT_ROOT"] . "/content/thumb/" . $_GET["delete"] . "_1.jpg");
				unlink($_SERVER["DOCUMENT_ROOT"] . "/content/thumb/" . $_GET["delete"] . "_2.jpg");
				unlink($_SERVER["DOCUMENT_ROOT"] . "/content/thumb/" . $_GET["delete"] . "_3.jpg");
				die(header("Location: /my_videos.php?err=1"));
			}
		}
	}
}

//pagination
$limit = 10;

if(!isset($_GET['page'])) {
	$page = 1;
} else {
	$page = intval($_GET['page']);
}

$stmt = $conn->prepare("SELECT * FROM videos WHERE uploaded_by=:t0");
$stmt->bindParam(":t0", $_SESSION["username"]);
$stmt->execute();
$total_videos = $stmt->rowCount();

$start = ($page-1)*$limit;
$total_pages = ceil($total_videos/$limit);

$stmt = $conn->prepare('SELECT * FROM videos WHERE uploaded_by=:t0 ORDER BY uploaded_on DESC LIMIT :t1, :t2');
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
	if($_GET["err"] == 1) { error("Video deleted!"); }
}

?>

<table align="center" cellpadding="5" cellspacing="0" border="0">
	<tr>
		<td class="bold">Overview</td>
		<td>|</td>
		<td><a href="help.php">Share</a></td>
		<td>|</td>
		<td><a href="my_videos_upload.php">Upload</a></td>
		</tr>
</table>
<br>
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
							<td><div class="watchTitle">My Videos</div></td>
 							<td align="right"> 
								<div style="font-weight: bold; color: #444; margin-right: 5px;">
								<?php echo "Videos " . $page . "-" . $stmt->rowCount() + $page - 1 . " of " . $total_videos;?>
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
					// fans!
					$stmt = $conn->prepare("SELECT * FROM favorites WHERE video_id=:t0");
					$stmt->bindParam(":t0", $video->id);
					$stmt->execute();
					$fans= $stmt->rowCount();
					// start outputting video list
					echo '<div class="moduleEntry"> 
					<table width="100%" cellpadding="0" cellspacing="0" border="0">
						<tr valign="top">
							<td>
								<a href="watch.php?v=' . $video->id . '"><img src="get_still.php?video_id=' . $video->id . '" class="moduleEntryThumb" width="120" height="90"></a>
								<div class="tableFavRemove">
									<form method="post" action="my_videos_edit.php?v=' . $video->id . '" style="margin: 0">
										<input type="submit" value="Edit Video" style="margin-left: 20px; margin-bottom: 5px"><br>
									</form>
									<form method="post" action="?delete=' . $video->id . '" style="margin: 0">
										<input type="submit" value="Remove Video" style="margin-left: 10px"><br>
									</form>
								</div>
							</td>
							<td width="100%"><div class="moduleEntryTitle"><a href="watch.php?v=' . $video->id . '">' . htmlspecialchars($video->title) . '</a></div>
							<div class="moduleEntryDescription">' . nl2br(htmlspecialchars($video->description)) . '</div>
							<div class="moduleEntryTags"> Tags // ' . htmlspecialchars($video->tags) . '
							</div>
		
							<div class="moduleEntryDetails">Added: ' . $Now->format('F') . ' ' . $Now->format('d') . ', ' . $Now->format('Y') . " " . $Now->format('h') . ":" . $Now->format('i') . strtolower($Now->format('A')) . ' </a></div>
							<div class="moduleEntryDetails">Views: ' . $view_count . ' | Comments: ' . $comment_count . ' | Fans: ' . $fans  . '</div>
							<hr style="border: 0; border-bottom: 1px dashed #999999; margin: 1em 0;">
							<div class="moduleEntryDetails">File: ' . $video->filename . '
							<div class="moduleEntryDetails">Broadcast: ';
							if($video->isPrivate == 0) { echo '<span style="color:#24692A;font-weight:bold">Public Video</span>'; }
							if($video->isPrivate == 1) { echo '<span style="color:#8C172A;font-weight:bold">Private Video</span>'; }
							echo '</div>
							<div class="moduleEntryDetails">Status: Live!</div>
							<input name="video_link" type="text" onClick="javascript:document.linkForm.video_link.focus();document.linkForm.video_link.select();" value="http://' . $_SERVER["HTTP_HOST"] . '/?v=' . $video->id . '" size="50" readonly="true" style="font-size: 10px; text-align: center;">
							<div class="formFieldInfo">Share this video with friends! Copy and paste the link above to an email or website.</div>
							</div>
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

		<table width="180" align="center" cellpadding="0" cellspacing="0" border="0" bgcolor="#FFEEBB">
			<tr>
				<td><img src="img/box_login_tl.gif" width="5" height="5"></td>
				<td><img src="img/pixel.gif" width="1" height="5"></td>
				<td><img src="img/box_login_tr.gif" width="5" height="5"></td>
			</tr>
			<tr>
				<td><img src="img/pixel.gif" width="5" height="1"></td>
				<td width="170">
					<div style="font-size: 16px; font-weight: bold; text-align: center; padding: 5px 5px 10px 5px;"><a href="help.php">Share your videos with friends!</a></div>
				</td>
				<td><img src="img/pixel.gif" width="5" height="1"></td>
			</tr>
			<tr>
				<td><img src="img/box_login_bl.gif" width="5" height="5"></td>
				<td><img src="img/pixel.gif" width="1" height="5"></td>
				<td><img src="img/box_login_br.gif" width="5" height="5"></td>
			</tr>
		</table>
		<div style="font-weight: bold; color: #333; margin: 10px 0px 5px 0px;">My Tags:</div>
		<?php 
		$stmt = $conn->query("SELECT tags FROM videos WHERE uploaded_by='" . $_SESSION["username"] . "' ORDER BY uploaded_on DESC");
		$related_tags = [];
		foreach($stmt as $result) $related_tags = array_merge($related_tags, explode(" ", $result['tags']));
		$related_tags = array_unique($related_tags);
		foreach($related_tags as $tag) {
			echo '<div style="padding: 0px 0px 5px 0px; color: #999;">&#187; <a href="results.php?search=' . htmlspecialchars($tag) . '">'. htmlspecialchars($tag) . '</a></div>';
		}
		?>
<?php require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/footer.php"); ?>