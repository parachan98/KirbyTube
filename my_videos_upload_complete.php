<?php 
require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/header.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/logincheck.php");
?>

<div class="tableSubTitle">Thank You</div>
<h3>Your video was successfully added!</h3>
<p>Your video is currently being processed and will be available to view in a few minutes.</p>

<table width="500" align="center" cellpadding="0" cellspacing="0" border="0" bgcolor="#E5ECF9">
			<tbody><tr>
				<td><img src="img/box_login_tl.gif" width="5" height="5"></td>
				<td width="100%"><img src="img/pixel.gif" width="1" height="5"></td>
				<td><img src="img/box_login_tr.gif" width="5" height="5"></td>
			</tr>
			<tr>
				<form name="linkForm" id="linkForm"></form>
				<td><img src="img/pixel.gif" width="5" height="1"></td>
				<td align="center">
		
				<div style="font-size: 11px; font-weight: bold; color: #CC6600; padding: 5px 0px 5px 0px;">Share your video! Copy and paste this link:</div>
				<div style="font-size: 11px; padding-bottom: 15px;">
				<input name="video_link" type="text" onclick="javascript:document.linkForm.video_link.focus();document.linkForm.video_link.select();" value="http://<?php echo $_SERVER["HTTP_HOST"]; ?>/?v=<?php echo $_GET["video_id"]; ?>" size="50" readonly="true" style="font-size: 10px; text-align: center;">
				</div>
				
				</td>
				<td><img src="img/pixel.gif" width="5" height="1"></td>
				
			</tr>
			<tr>
				<td><img src="img/box_login_bl.gif" width="5" height="5"></td>
				<td><img src="img/pixel.gif" width="1" height="5"></td>
				<td><img src="img/box_login_br.gif" width="5" height="5"></td>
			</tr>
		</tbody></table>
<br>

<b>What would you like to do next?</b>
<br><br>
<table width="100%" align="center" cellpadding="0" cellspacing="0" border="0">
			<tbody><tr>
				<td><img src="img/box_login_tl.gif" width="5" height="5"></td>
				<td width="100%"><img src="img/pixel.gif" width="1" height="5"></td>
				<td><img src="img/box_login_tr.gif" width="5" height="5"></td>
			</tr>
			<tr>
				<td><img src="img/pixel.gif" width="5" height="1"></td>
				<td style="padding: 5px 0px 10px 0px;">
				
				<table width="100%" cellpadding="0" cellspacing="0" border="0">
					<tbody><tr valign="top">
					<td width="33%" style="border-right: 1px dashed #369; padding: 0px 10px 10px 10px; color: #444;">
					<div style="font-size: 14px; font-weight: bold; margin-bottom: 5px;">&bull;&nbsp;<a href="browse.php">Watch some videos</a></div>
					<span style="margin-left: 10">Search and browse 1000's of videos.</span>
					</td>
					<td width="33%" style="padding: 0px 10px 10px 10px; color: #444;">
					<div style="font-size: 14px; font-weight: bold; margin-bottom: 5px;">&bull;&nbsp;<a href="my_videos_upload.php">Upload more videos</a></div>
					<span style="margin-left: 10">Start building your video collection.</span>
					</td>
					</tr>
				</tbody></table>
				
				<table width="100%" cellpadding="0" cellspacing="0" border="0">
					<tbody><tr valign="top">
					<td width="33%" style="border-right: 1px dashed #369; padding: 0px 10px 10px 10px; color: #444;">
					<div style="font-size: 14px; font-weight: bold; margin-bottom: 5px;">&bull;&nbsp;<a href="my_friends_invite.php">Invite your friends</a></div>
					<span style="margin-left: 10">Invite your friends to watch your videos</span>
					</td>
					<td width="33%" style="padding: 0px 10px 10px 10px; color: #444;">
					<div style="font-size: 14px; font-weight: bold; margin-bottom: 5px;">&bull;&nbsp;<a href="my_videos_edit.php?v=<?php echo $_GET["video_id"]; ?>">Edit your video details</a></div>
					<span style="margin-left: 10">Add or change your video information or options.</span>
					</td>
					</tr>
				</tbody></table>
				
				</td>
				<td><img src="img/pixel.gif" width="5" height="1"></td>
			</tr>
			<tr>
				<td><img src="img/box_login_bl.gif" width="5" height="5"></td>
				<td><img src="img/pixel.gif" width="1" height="5"></td>
				<td><img src="img/box_login_br.gif" width="5" height="5"></td>
			</tr>
		</tbody></table>
<br><br><br>
<br><br><br>
<?php require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/footer.php"); ?>