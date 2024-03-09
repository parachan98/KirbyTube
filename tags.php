<?php require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/header.php"); ?>

<div class="formTitle">Tags</div>

<div class="pageTable">

<div style="font-size: 14px; font-weight: bold; color: #666666; margin-bottom: 10px;">Lastest Tags //</div>
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

<div style="font-size: 16px; font-weight: bold; color: #666666; margin-bottom: 10px;">Most Popular Tags //</div>
<?php 
$stmt = $conn->query("SELECT * FROM views LEFT JOIN videos ON views.viewed_video = videos.id WHERE videos.isPrivate=0 GROUP BY views.viewed_video ORDER BY COUNT(views.view_id) DESC LIMIT 50");
$related_tags = [];
foreach($stmt as $result) $related_tags = array_merge($related_tags, explode(" ", $result['tags']));
$related_tags = array_unique($related_tags);
foreach($related_tags as $tag) {
	echo '<a style="font-size: 12px;" href="results.php?search=' . htmlspecialchars($tag) . '">' . htmlspecialchars($tag) . '</a> : ';
}
?>
</div>

<?php require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/footer.php"); ?>