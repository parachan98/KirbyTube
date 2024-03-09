<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/header.php");

// sorting
if(!isset($_GET['s'])) { $_GET['s'] = "mr"; }

//get public videos
$stmt = $conn->prepare("SELECT * FROM videos WHERE isPrivate=0");
$stmt->execute();
$public_videos = $stmt->rowCount();

//pagination
$limit = 20;

if(!isset($_GET['page'])) {
	$page = 1;
} else {
	$page = intval($_GET['page']);
}

$start = ($page-1)*$limit;
$total_pages = ceil($public_videos/$limit);
if($public_videos >= 100) { $public_videos = 100; }
if($page >= 5) { $total_pages = 5; }

if($_GET['s'] == 'mr') { $sql = 'SELECT * FROM videos WHERE isPrivate=0 ORDER BY uploaded_on DESC LIMIT :t0, :t1'; }
if($_GET['s'] == 'mp') { $sql = 'SELECT * FROM views LEFT JOIN videos ON views.viewed_video = videos.id WHERE videos.isPrivate=0 GROUP BY views.viewed_video ORDER BY COUNT(views.view_id) DESC LIMIT :t0, :t1'; }
if($_GET['s'] == 'md') { $sql = 'SELECT * FROM comments LEFT JOIN videos ON videos.id = comments.posted_to WHERE videos.isPrivate=0  GROUP BY comments.posted_to ORDER BY COUNT(comments.id) DESC LIMIT :t0, :t1'; }
if($_GET['s'] == 'mf') { $sql = 'SELECT * FROM favorites LEFT JOIN videos ON videos.id = favorites.video_id WHERE videos.isPrivate=0 GROUP BY favorites.video_id ORDER BY COUNT(favorites.id) DESC LIMIT :t0, :t1'; }
if($_GET['s'] == 'r')  { $sql = 'SELECT * FROM videos WHERE isPrivate=0 ORDER BY rand() DESC LIMIT :t0, :t1'; }
$stmt = $conn->prepare($sql);
$stmt->bindParam(":t0", $start, PDO::PARAM_INT);
$stmt->bindParam(":t1", $limit, PDO::PARAM_INT);
$stmt->execute();

if($page == 1) { 
	$video_count = 1; 
} else {
	$video_count = $page + $public_videos;
}

?>

<table align="center" cellpadding="5" cellspacing="0" border="0">
	<tr>
		<?php
		if($_GET['s'] == "mr") { echo '<td class="bold">Most Recent</td>'; } else echo '<td><a href="browse.php?s=mr">Most Recent</a></td>';
		echo '<td>|</td>';
		if($_GET['s'] == "mp") { echo '<td class="bold">Most Popular</td>'; } else echo '<td><a href="browse.php?s=mp">Most Popular</a></td>';
		echo '<td>|</td>';
		if($_GET['s'] == "md") { echo '<td class="bold">Most Discussed</td>'; } else echo '<td><a href="browse.php?s=md">Most Discussed</a></td>';
		echo '<td>|</td>';
		if($_GET['s'] == "mf") { echo '<td class="bold">Most Added to Favorites</td>'; } else echo '<td><a href="browse.php?s=mf">Most Added to Favorites</a></td>';
		echo '<td>|</td>';
		if($_GET['s'] == "r") { echo '<td class="bold">Random</td>'; } else echo '<td><a href="browse.php?s=r">Random</a></td>';
		?>
		</tr>
</table>

<br>

<table width="795" align="center" cellpadding="0" cellspacing="0" border="0" bgcolor="#CCCCCC">
	<tr>
		<td><img src="img/box_login_tl.gif" width="5" height="5"></td>
		<td><img src="img/pixel.gif" width="1" height="5"></td>
		<td><img src="img/box_login_tr.gif" width="5" height="5"></td>
	</tr>
	<tr>
		<td><img src="img/pixel.gif" width="5" height="1"></td>
		<td width="785">
		<div class="moduleTitleBar">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr valign="top">
					<td>
						<div class="moduleTitle">
							<?php
							if($_GET['s'] == "mr") { echo 'Most Recent'; }
							if($_GET['s'] == "mp") { echo 'Most Popular'; }
							if($_GET['s'] == "md") { echo 'Most Discussed'; }
							if($_GET['s'] == "mf") { echo 'Most Added to Favorites'; }
							if($_GET['s'] == "r") { echo 'Random'; }
							?></div>
					</td>
					<td align="right">
						<div style="font-weight: bold; color: #444; margin-right: 5px;"><?php echo "Videos " . $page . "-" . $stmt->rowCount() + $page - 1 . " of " . $public_videos;?></div>
					</td>
				</tr>
			</table>
		</div>
		<div class="moduleFeatured">
			<table width="770" cellpadding="0" cellspacing="0" border="0">
				<tr valign="top">
					<?php
					$count = 0;
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
						//td stuff
						if($count == 0) {
							echo "<tr valign=\"top\">";
						}
						echo '					<td width="20%" align="center">
						<a href="watch.php?v=' . $video->id . '"><img src="get_still.php?video_id=' . $video->id . '" width="120" height="90" class="moduleFeaturedThumb"></a>
						<div class="moduleFeaturedTitle"><a href="watch.php?v=' . $video->id . '">' . htmlspecialchars($video->title) . '</a></div>
						<div class="moduleFeaturedDetails">Added: ' . $Now->format('F') . ' ' . $Now->format('d') . ', ' . $Now->format('Y') . '<br>by <a href="profile.php?user=' . $video->uploaded_by . '">' . $video->uploaded_by . '</a></div>
						<div class="moduleFeaturedDetails" style="padding-bottom: 5px;">Views: ' . $view_count . ' | Comments: ' . $comment_count . '</div>
						</td>';
						$count++;
						if($count == 5) {
							echo "</tr>";
						$count = 0;
						}
					}
					?>
			</table>
		</div>
				<!-- begin paging -->
				<?php  
					echo '<div style="font-size: 13px; font-weight: bold; color: #444; text-align: right; padding: 5px 0px 5px 0px;">Browse Pages:'; 
					for($p=1; $p<=$total_pages; $p++) { ?>
					<span style="<?= $page == $p ? 'color: #444; background-color: #FFFFFF;' : 'color: #CCC;'; ?> padding: 1px 4px 1px 4px; border: 1px solid #999; margin-right: 5px;"><?= $page == $p ? $p . '</span>' : '<a href="browse.php?page=' . $p . '">' . $p . '</a></span>' ?>
				<?php }?>
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

		</div>
		</td>
	</tr>
</table>

<?php require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/footer.php"); ?>