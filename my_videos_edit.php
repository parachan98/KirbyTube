<?php 
require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/header.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/logincheck.php");

if(isset($_GET["v"])) {
	$stmt = $conn->prepare("SELECT * FROM videos WHERE id=:t0");
	$stmt->bindParam(":t0", $_GET["v"]);
	$stmt->execute();
	if($stmt->rowCount() !== 0) { foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $video); }
}

if($video->uploaded_by !== $_SESSION["username"]) { die(header("Location: /my_videos.php")); }

if(isset($_POST["submit"])) {
	//check duped tags
	$taglist = explode(" ", $_POST["video_tags"]);
	if(array_has_dupes($taglist)) { die("please remove the duplicate tags"); } 

	$stmt = $conn->prepare("UPDATE videos SET title=:t1, description=:t2, tags=:t3, isPrivate=:t4 WHERE id=:t0");
	$stmt->bindParam(":t0", $_GET["v"]);
	$stmt->bindParam(":t1", $_POST["video_title"]);
	$stmt->bindParam(":t2", $_POST["video_description"]);
	$stmt->bindParam(":t3", $_POST["video_tags"]);
	$stmt->bindParam(":t4", $_POST["private"]);
	$stmt->execute();
	echo '<table width="100%" align="center" bgcolor="#666666" cellpadding="6" cellspacing="3" border="0">
		<tbody><tr>
			<td align="center" bgcolor="#FFFFFF"><span class="success">Video has been updated!</span></td>
		</tr>
	</tbody></table><br>';
}

?>

<div class="tableSubTitle">Video Details <span style="float:right;font-size: 12px; font-weight: normal;"><a href="/my_videos.php">Back to "My Videos"</a></span></div>

<table width="100%" cellpadding="5" cellspacing="0" border="0">

<form method="post">
	<tr>
		<td width="200" align="right"><span class="label">Title:</span></td>
		<td><input type="text" size="30" maxlength="60" name="video_title" value="<?php echo htmlentities($video->title); ?>"></td>
	</tr>
	<tr>
		<td align="right" valign="top"><span class="label">Description:</span></td>
		<td><textarea name="video_description" maxlength="500" style="width:295px;resize:none" rows="3"><?php echo $video->description; ?></textarea></td>
	</tr>
	<tr>
		<td align="right"><span class="label">Tags:</span></td>
		<td><input type="text" size="30" name="video_tags" value="<?php echo htmlentities($video->tags); ?>">
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><span class="formFieldInfo"><b>Enter three or more keywords, separated by spaces, describing your video.</b></span>
		<br><span class="formFieldInfo">It helps to use relevant keywords so that others can find your video!</span></td>
	</tr>
</table>

<div class="pageTable">
	<div class="tableSubTitle">Sharing</div>
	<table width="100%" cellpadding="5" cellspacing="0" border="0">
		<tr>
			<td width="195" align="right"><span class="label">Video URL:</span></td>
			<td><input name="video_link" type="text" onClick="javascript:document.linkForm.video_link.focus();document.linkForm.video_link.select();" value="http://<?php echo $_SERVER["HTTP_HOST"] . '/?v=' . $_GET["v"]; ?>" size="50" readonly="true" style="font-size: 10px;"></td>
		</tr>
		<tr>
			<td width="195" align="right"><span class="label">Broadcast:</span></td>
			<td>
				<select name="private" tabindex="5">
					<option value="0">Public</option>
					<option value="1">Private</option>
				</select>
			</td>
		</tr>
		<tr>
			<td align="right">&nbsp;</td>
			<td><input type="submit" value="Update Video" name="submit"></td>
		</tr>
	</table>
</div>

</form>

<?php require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/footer.php"); ?>