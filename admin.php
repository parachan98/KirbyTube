<?php 
require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/header.php"); 
require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/logincheck.php"); 

$stmt = $conn->prepare("SELECT isAdmin from users where username=:t0");
$stmt->bindParam(":t0", $_SESSION["username"]);
$stmt->execute();
foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $user);
if($user->isAdmin == 0) { die(header("Location: /")); }

if(isset($_POST["term"])) {
	$stmt = $conn->prepare("UPDATE users SET isBanned=1 WHERE username=:t0");
	$stmt->bindParam(":t0", $_POST["ban_user"]);
	$stmt->execute();
	
	// delete banned user's videos
	$stmt = $conn->prepare("SELECT * FROM videos WHERE `uploaded_by`=:t0");
	$stmt->bindParam(':t0', $_POST["ban_user"]);
	$stmt->execute();
	foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $video) {
		$conn->query("DELETE FROM videos WHERE uploaded_by='" . $video->id . "'");
		$conn->query("DELETE FROM favorites WHERE video_id='" . $video->id . "'");
		// remove files
		unlink($_SERVER["DOCUMENT_ROOT"] . "/content/video/" . $video->id . ".3gp");
		unlink($_SERVER["DOCUMENT_ROOT"] . "/content/video/" . $video->id . ".flv");
		unlink($_SERVER["DOCUMENT_ROOT"] . "/content/video/" . $video->id . ".mp4");
		unlink($_SERVER["DOCUMENT_ROOT"] . "/content/thumb/" . $video->id . "_1.jpg");
		unlink($_SERVER["DOCUMENT_ROOT"] . "/content/thumb/" . $video->id . "_2.jpg");
		unlink($_SERVER["DOCUMENT_ROOT"] . "/content/thumb/" . $video->id . "_3.jpg");
		echo "user banned";
	}
}

if(isset($_POST["unterm"])) {
	$stmt = $conn->prepare("UPDATE users SET isBanned=0 WHERE username=:t0");
	$stmt->bindParam(":t0", $_POST["unban_user"]);
	$stmt->execute();
	echo "user unbanned";
}

if(isset($_POST["delete_video"])) {
	$conn->query("DELETE FROM videos WHERE id='" . $_POST["delete_video_id"] . "'");
	// remove files
	unlink($_SERVER["DOCUMENT_ROOT"] . "/content/video/" . $_POST["delete_video_id"] . ".3gp");
	unlink($_SERVER["DOCUMENT_ROOT"] . "/content/video/" . $_POST["delete_video_id"] . ".flv");
	unlink($_SERVER["DOCUMENT_ROOT"] . "/content/video/" . $_POST["delete_video_id"] . ".mp4");
	unlink($_SERVER["DOCUMENT_ROOT"] . "/content/thumb/" . $_POST["delete_video_id"] . "_1.jpg");
	unlink($_SERVER["DOCUMENT_ROOT"] . "/content/thumb/" . $_POST["delete_video_id"] . "_2.jpg");
	unlink($_SERVER["DOCUMENT_ROOT"] . "/content/thumb/" . $_POST["delete_video_id"] . "_3.jpg");
	echo "video deleted";
}

if(isset($_POST["feature_video"])) {
	$stmt = $conn->prepare("UPDATE videos SET isFeatured=1 WHERE id=:t0");
	$stmt->bindParam(":t0", $_POST["feature_video_id"]);
	$stmt->execute();
	echo "video featured";
}

if(isset($_POST["blog_post_submit"])) {
	$stmt = $conn->prepare("INSERT INTO blog (content) VALUES (:t0)");
	$stmt->bindParam(":t0", $_POST["blog_post_text"]);
	$stmt->execute();
	echo "Added to blog!";
}

?>

<div class="tableSubTitle">Admin Panel</div>
<p>if you abuse this shit your familys going down motherfucker</p>

<form method="post">
	<input type="text" name="ban_user"><input type="submit" name="term" value="terminate user"><br>
	<input type="text" name="unban_user"><input type="submit" name="unterm" value="unban user"><br>
	<input type="text" name="delete_video_id"><input type="submit" name="delete_video" value="delete video"><br>
	<input type="text" name="feature_video_id"><input type="submit" name="feature_video" value="feature video"><br>
	<br>
	<div class="tableSubTitle">Post to Blog</div>
	<p>Newlines will have to be entered as breaking spaces.</p>
	<textarea name="blog_post_text" style="width:500px;height:400px"></textarea><br>
	<input type="submit" name="blog_post_submit" value="Add to Blog">
</form>




<?php require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/footer.php"); ?>