<?php 
require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/config.php");

// ban check
if(isset($_SESSION["username"])) {
	$stmt = $conn->prepare("SELECT isBanned from users where username=:t0");
	$stmt->bindParam(":t0", $_SESSION["username"]);
	$stmt->execute();
	foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $user);
	if($user->isBanned == 1) { die("get the fuck off my website!!! you are BANNED you dumb mother fucker!!!!"); }
} else { $_SESSION["username"] = NULL; }

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo $website["instance_name"] . " - " . $website["instance_slogan"]; ?></title>
<?php if($_SERVER["PHP_SELF"] == "/watch.php") { '<script type="text/javascript" src="flashobject.js"></script>'; } ?>
<link rel="icon" href="<?php echo $website["favicon"]; ?>" type="image/x-icon">
<link rel="shortcut icon" href="<?php echo $website["favicon"]; ?>" type="image/x-icon">
<link href="styles.css" rel="stylesheet" type="text/css">
<link rel="alternate" type="application/rss+xml" title="<?php echo $website["instance_name"]; ?> "" Recently Added Videos [RSS]" href="/rss.php?recently_added">
</head>


<body>

<table width="800" cellpadding="0" cellspacing="0" border="0" align="center">
	<tr>
		<td bgcolor="#FFFFFF" style="padding-bottom: 25px;">
		

<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr valign="top">
		<td width="130" rowspan="2" style="padding: 0px 5px 5px 5px;"><a href="index.php"><img src="<?php echo $website["instance_banner"]; ?>" width="120" height="48" alt="<?php echo $website["instance_name"]; ?>" border="0"></a></td>
		<td valign="top">
		
		<table width="670" cellpadding="0" cellspacing="0" border="0">
			<tr valign="top">
				<td style="padding: 0px 5px 0px 5px; font-style: italic;"><?php echo $website["instance_tagline"]; ?></td>
				<td align="right">
				<table cellpadding="0" cellspacing="0" border="0">
					<tr>
<?php
if(isset($_SESSION["username"])) {
	echo "
				<td>Hello, <a href=\"profile.php?user=" . $_SESSION["username"] . "\">" . $_SESSION["username"] . "</a>!&nbsp;<img src=\"/img/mail.gif\" border=\"0\" valign=\"top\">&nbsp;(<a href=\"/my_messages.php\">0</a>)</td>
				<td>&nbsp;&nbsp;|&nbsp;&nbsp;</td>
				<td><a href=\"logout.php\">Log Out</a></td>
				<td>&nbsp;&nbsp;|&nbsp;&nbsp;</td>
				<td style=\"padding-right: 5px;\"><a href=\"help.php\">Help</a></td>";
} else echo "
				<td><a href=\"signup.php\" class=\"bold\">Sign Up</a></td>
				<td>&nbsp;&nbsp;|&nbsp;&nbsp;</td>
				<td><a href=\"login.php\">Log In</a></td>
				<td>&nbsp;&nbsp;|&nbsp;&nbsp;</td>
				<td style=\"padding-right: 5px;\"><a href=\"help.php\">Help</a></td>";
?>	


			</tr>
		</table>
		</td>
		</tr>
		</table>
		</td>
	</tr>

		<tr>
		<td width="100%">
		
		<div style="font-size: 12px; font-weight: bold; float: right; padding: 10px 5px 0px 5px;"><a href="my_videos_upload.php">Upload</a> &nbsp;//&nbsp; <a href="browse.php">Browse</a> &nbsp;//&nbsp; <a href="my_friends_invite.php">Invite</a></div>
		<?php if($_SERVER["PHP_SELF"] !== "/signup.php")  {
			echo '
		<table cellpadding="2" cellspacing="0" border="0">
			<tr>
				<form method="GET" action="results.php">
				<td>
					<input type="text" value="'; if(isset($_GET["search"])){echo htmlentities($_GET["search"]);} echo '" name="search" size="30" maxlength="128" style="color:#146412; font-size: 14px; padding: 2px;">
				</td>
				<td>
					<input type="submit" value="Search Videos">
				</td>
				</form>
			</tr>
		</table>
		
		</td>
	</tr>';
		}
	?>

			
</table>

<table align="center" width="100%" bgcolor="#D5E5F5" cellpadding="0" cellspacing="0" border="0" style="margin: 5px 0px 10px 0px;">
	<tr>
		<td><img src="img/box_login_tl.gif" width="5" height="5"></td>
		<td><img src="img/pixel.gif" width="1" height="5"></td>
		<td><img src="img/box_login_tr.gif" width="5" height="5"></td>
	</tr>
	<tr>
		<td><img src="img/pixel.gif" width="5" height="1"></td>
		
		<td width="100%">

		<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td>
											<table cellpadding="2" cellspacing="0" border="0">
						<tr>
<?php
if($_SERVER["PHP_SELF"] == "/index.php") {
	echo "							<td>&nbsp;<a href=\"index.php\" class=\"bold\">Home</a></td>\n";
	} else {
		echo "							<td>&nbsp;<a href=\"index.php\">Home</a></td>\n";
	}

echo "							<td>&nbsp;|&nbsp;</td>\n";
							
if($_SERVER["PHP_SELF"] == "/my_videos.php") {
	echo "							<td>&nbsp;<a href=\"my_videos.php\" class=\"bold\">My Videos</a></td>\n";
	} else {
		echo "							<td>&nbsp;<a href=\"my_videos.php\">My Videos</a></td>\n";
	}

echo "							<td>&nbsp;|&nbsp;</td>\n";
							
if($_SERVER["PHP_SELF"] == "/my_favorites.php") {
	echo "							<td>&nbsp;<a href=\"my_favorites.php\" class=\"bold\">My Favorites</a></td>\n";
} else {
	echo "							<td>&nbsp;<a href=\"my_favorites.php\">My Favorites</a></td>\n";
}

echo "							<td>&nbsp;|&nbsp;</td>\n";

if($_SERVER["PHP_SELF"] == "/my_friends.php") {
	echo "							<td>&nbsp;<a href=\"my_friends.php\" class=\"bold\">My Friends</a></td>\n";
	} else {
		echo "							<td>&nbsp;<a href=\"my_friends.php\">My Friends</a></td>\n";
	}

echo "							<td>&nbsp;|&nbsp;</td>\n";
							
if($_SERVER["PHP_SELF"] == "/my_profile.php") {
	echo "							<td>&nbsp;<a href=\"my_profile.php\" class=\"bold\">My Profile</a></td>";
} else {
		echo "							<td>&nbsp;<a href=\"my_profile.php\">My Profile</a></td>";
	}	
?>
							
						</tr>
						</table>
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
</table>

<div style="padding: 0px 5px 0px 5px;">

