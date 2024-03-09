<?php require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/header.php"); ?>
<div class="tableSubTitle">Welcome to <?php echo $website["instance_name"]; ?>, <?php echo $_SESSION["username"]; ?></div>
<p>We hope you enjoy your experience. Write anytime to let us know how we can serve you better.<br>- <i>The <?php echo $website["instance_name"]; ?> Team</i> </p>

<table width="790" align="center" cellpadding="0" cellspacing="0" border="0">
	<tr valign="top">
		<td style="padding-right: 15px;">
		
		<table width="770" align="center" cellpadding="0" cellspacing="0" border="0" bgcolor="#E5ECF9">
			<tr>
				<td><img src="img/box_login_tl.gif" width="5" height="5"></td>
				<td width="100%"><img src="img/pixel.gif" width="1" height="5"></td>
				<td><img src="img/box_login_tr.gif" width="5" height="5"></td>
			</tr>
			<tr>
				<td><img src="img/pixel.gif" width="5" height="1"></td>
				<td style="padding: 5px 0px 10px 5px;"><div style="font-size: 14px; font-weight: bold; margin-bottom: 5px;">What would you like to do next?</div>
				
				<table width="100%" cellpadding="0" cellspacing="0" border="0">
					<tr valign="top">
					<td width="33%" style="border-right: 1px dashed #369; padding: 0px 10px 10px 10px; color: #444;">
					<div style="font-size: 13px; font-weight: bold; margin-bottom: 5px;">&bull; <a href="my_profile.php">Complete your profile page</a></div>
					The <?php echo $website["instance_name"]; ?> community wants to know about you.
					</td>
					<td width="33%" style="padding: 0px 10px 10px 10px; color: #444;">
					<div style="font-size: 13px; font-weight: bold; margin-bottom: 5px;">&bull; <a href="browse.php">Start watching videos</a></div>
					Search and browse 1000's of streaming videos.
					</td>
					</tr>
					<tr valign="top">
					<td width="33%" style="border-right: 1px dashed #369; padding: 0px 10px 10px 10px; color: #444;">
					<div style="font-size: 13px; font-weight: bold; margin-bottom: 5px;">&bull; <a href="my_videos_upload.php">Upload your videos</a></div>
					Share your experiences with the world.
					</td>
					</tr>
				</table>
				
				</td>
				<td><img src="img/pixel.gif" width="5" height="1"></td>
			</tr>
			<tr>
				<td><img src="img/box_login_bl.gif" width="5" height="5"></td>
				<td><img src="img/pixel.gif" width="1" height="5"></td>
				<td><img src="img/box_login_br.gif" width="5" height="5"></td>
			</tr>
		</table><br>

<?php require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/footer.php"); ?>