<?php 
require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/header.php");

if(empty($_GET["search"])) { $_GET["search"] = ""; } //not sure how yt handled empty search...

$search = str_replace("+", "|", $_GET['search']);

$stmt = $conn->prepare("SELECT * FROM videos WHERE isPrivate=0 AND tags REGEXP :t0 OR uploaded_by=:t0 AND isPrivate=0 ORDER BY uploaded_on DESC"); // Regex!
$stmt->bindParam(":t0", $search);
$stmt->execute();
$video_count = $stmt->rowCount();

//pagination
$limit = 10;

if(!isset($_GET['page'])) {
	$page = 1;
} else {
	$page = intval($_GET['page']);
}

$start = ($page-1)*$limit;
$total_pages = ceil($video_count/$limit);

$stmt = $conn->prepare("SELECT * FROM videos WHERE isPrivate=0 AND tags REGEXP :t0 OR uploaded_by=:t0 AND isPrivate=0 ORDER BY uploaded_on DESC LIMIT :t1, :t2"); // Regex!
$stmt->bindParam(":t0", $search);
$stmt->bindParam(":t1", $start, PDO::PARAM_INT);
$stmt->bindParam(":t2", $limit, PDO::PARAM_INT);
$stmt->execute();

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

				<div class="moduleTitleBar">
				<table width="100%" cellpadding="0" cellspacing="0" border="0">
					<tr valign="top">
						<td><div class="moduleTitle">Tag // <?php echo htmlspecialchars($_GET["search"]); ?></div></td>
						<td align="right">
						<div style="font-weight: bold; color: #444; margin-right: 5px;">
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
					echo "\n					<div class=\"moduleEntry\">
					<table width=\"565\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
						<tr valign=\"top\">
							<td>
								<td><a href=\"watch.php?v=" . $video->id . "&search=" . htmlentities($_GET["search"]) . "\"><img src=\"get_still.php?video_id=" . $video->id . "&still_id=1\" class=\"moduleEntryThumb\" width=\"100\" height=\"75\"></a></td>
								<td><a href=\"watch.php?v=" . $video->id . "&search=" . htmlentities($_GET["search"]) . "\"><img src=\"get_still.php?video_id=" . $video->id . "&still_id=2\" class=\"moduleEntryThumb\" width=\"100\" height=\"75\"></a></td>
								<td><a href=\"watch.php?v=" . $video->id . "&search=" . htmlentities($_GET["search"]) . "\"><img src=\"get_still.php?video_id=" . $video->id . "&still_id=3\" class=\"moduleEntryThumb\" width=\"100\" height=\"75\"></a></td>
								</td>
							<td width=\"100%\"><div class=\"moduleEntryTitle\"><a href=\"watch.php?v=" . $video->id . "&search=" . htmlentities($_GET["search"]) . "\">" . htmlspecialchars($video->title) . "</a></div>
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
				<!-- begin paging -->
				<?php 
				if($total_pages > 1) { 
					echo '<div style="font-size: 13px; font-weight: bold; color: #444; text-align: right; padding: 5px 0px 5px 0px;">Result Page:'; 
					for($p=1; $p<=$total_pages; $p++) { ?>
					<span style="<?= $page == $p ? 'color: #444; background-color: #FFFFFF;' : 'color: #CCC;'; ?> padding: 1px 4px 1px 4px; border: 1px solid #999; margin-right: 5px;"><?= $page == $p ? $p . '</span>' : '<a href="results.php?search=' . $search . '&page=' . $p . '">' . $p . '</a></span>' ?>
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
		<a href="/rss.php?tag=<?php echo $_GET["search"]; ?>"><img src="img/rss.gif" width="36" height="14" border="0" style="vertical-align: text-top;"></a> 
		<span style="font-size: 11px; margin-right: 3px;"><a href="/rss.php?tag=<?php echo $_GET["search"]; ?>">Feed For Tag // <?php echo $_GET["search"]; ?></a></span>
		
		<div style="font-weight: bold; color: #333; margin: 10px 0px 5px 0px;">Related Tags:</div>
		<?php 
		$stmt = $conn->prepare("SELECT * FROM videos WHERE isPrivate=0 AND tags REGEXP :t0 OR uploaded_by=:t0 AND isPrivate=0 ORDER BY uploaded_on DESC LIMIT :t1, :t2"); // Regex!
		$stmt->bindParam(":t0", $search);
		$stmt->bindParam(":t1", $start, PDO::PARAM_INT);
		$stmt->bindParam(":t2", $limit, PDO::PARAM_INT);
		$stmt->execute();
		$related_tags = [];
		foreach($stmt as $result) $related_tags = array_merge($related_tags, explode(" ", $result['tags']));
		$related_tags = array_unique($related_tags);
		foreach($related_tags as $tag) {
			echo '<div style="padding: 0px 0px 5px 0px; color: #999;">&#187; <a href="results.php?search=' . htmlspecialchars($tag) . '">'. htmlspecialchars($tag) . '</a></div>';
		}
		?>
		</td>
		
	</tr>
</table>

	<?php
	if($video_count == 0) {
		echo '	<br>
	<div class="moduleTitle">
		Found no videos matching "'. $_GET["search"]. '". Do you have one? <a href="my_videos_upload.php">Upload</a> it!
	</div>';
	}
	?>

<?php require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/footer.php"); ?>