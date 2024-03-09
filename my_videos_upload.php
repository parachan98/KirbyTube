<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/header.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/logincheck.php");

if(isset($_GET["err"])) {
	if($_GET["err"] == 1) { error("Please remove the duplicate tags from your video"); }
	if($_GET["err"] == 2) { error("Your video title is too long."); }
	if($_GET["err"] == 3) { error("Video title cannot be empty!"); }
	if($_GET["err"] == 4) { error("Your video description is too long."); }
	if($_GET["err"] == 5) { error("Video description cannot be empty!"); }
	if($_GET["err"] == 6) { error("You cannot input less than three tags!"); }
	if($_GET["err"] == 7) { error("You have entered too many tags!"); }
}

?>

<div class="tableSubTitle">Video Upload (Step 1 of 2)</div>

<table width="100%" cellpadding="5" cellspacing="0" border="0">

<form method="post" action="my_videos_upload_2.php">
	<tr>
		<td width="200" align="right"><span class="label">Title:</span></td>
		<td><input type="text" size="30" maxlength="60" name="video_title" value=""></td>
	</tr>
	<tr>
		<td align="right" valign="top"><span class="label">Description:</span></td>
		<td><textarea name="video_description" maxlength="500" style="width:295px;resize:none" rows="3"></textarea></td>
	</tr>
	<tr>
		<td align="right"><span class="label">Tags:</span></td>
		<td><input type="text" size="30" name="video_tags">
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><span class="formFieldInfo"><b>Enter three or more keywords, separated by spaces, describing your video.</b></span>
		<br><span class="formFieldInfo">It helps to use relevant keywords so that others can find your video!</span></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="submit" value="Continue ->"><br></td>
	</tr>
</table>


<?php require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/footer.php"); ?>