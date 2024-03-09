<?php 
require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/header.php");

if(isset($_GET["user"])) {
	$stmt = $conn->prepare("SELECT * from users where username=:t0");
	$stmt->bindParam(":t0", $_GET["user"]);
	$stmt->execute();
	if($stmt->rowCount() == 0) { header("Location: /index_down.php"); }
	foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $user);
} else {
	header("Location: /index_down.php");
}

if($user->isBanned == 1) { header("Location: /index_down.php"); }

// grab all video counts
$stmt = $conn->prepare("SELECT * FROM videos WHERE uploaded_by=:t0 AND isPrivate=0");
$stmt->bindParam(":t0", $_GET["user"]);
$stmt->execute();
$public_videos = $stmt->rowCount();

$stmt = $conn->prepare("SELECT * FROM videos WHERE uploaded_by=:t0 AND isPrivate=1");
$stmt->bindParam(":t0", $_GET["user"]);
$stmt->execute();
$private_videos = $stmt->rowCount();

$stmt = $conn->prepare("SELECT * FROM favorites WHERE added_by=:t0");
$stmt->bindParam(":t0", $_GET["user"]);
$stmt->execute();
$favorite_videos = $stmt->rowCount();

$stmt = $conn->prepare("SELECT * FROM friends WHERE sent_by=:t0 OR sent_to=:t0 AND pending=0");
$stmt->bindParam(":t0", $_GET["user"]);
$stmt->execute();
$friends = $stmt->rowCount();

?>


<table width="100%" align="center" cellpadding="0" cellspacing="0" border="0">
	<tr valign="top">
		<td width="180">
		
		<table width="100%" align="center" cellpadding="0" cellspacing="0" border="0" bgcolor="#E5ECF9">
			<tr>
				<td><img src="img/box_login_tl.gif" width="5" height="5"></td>
				<td width="100%"><img src="img/pixel.gif" width="1" height="5"></td>
				<td><img src="img/box_login_tr.gif" width="5" height="5"></td>
			</tr>
			<tr>
				<td><img src="img/pixel.gif" width="5" height="1"></td>
				<td align="center" style="padding: 5px;">
				
				
						<?php
						if(isset($_SESSION["username"])) {
							$stmt = $conn->prepare("SELECT * FROM friends WHERE sent_by=:t0 and sent_to=:t1");
							$stmt->bindParam(":t0", $_SESSION["username"]);
							$stmt->bindParam(":t1", $_GET["user"]);
							$stmt->execute();
							foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $friend_status);
							// not friends, so just output an add button
							if($stmt->rowCount() == 0) {
								echo '<div style="font-size: 14px; font-weight: bold; color:#003366; margin-bottom: 5px;">Do you know ' . $_GET["user"] . '?</div>
								<form method="post" action="my_friends_invite_user.php?user=' . $_GET["user"] . '">
				<input type="submit" value="Add ' . $_GET["user"] . ' as a Friend"><br>
				</form>';
							}
							if($stmt->rowCount() == 1 && $friend_status->pending == 0) {
								echo '<form method="post" action="my_friends.php' . $_GET["user"] . '">
				<input type="submit" value="Remove ' . $_GET["user"] . ' from Friends"><br>
				</form>';
							}
						} else echo '<a href="signup.php">Sign up</a> or <a href="login.php">log in</a> to add ' . $_GET["user"] . ' as friend.<br><br>';
						?>
					
									
				<div style="font-size: 14px; font-weight: bold; color:#003366; margin-bottom: 5px;">Question? Comment?</div>
				<form method="post" action="outbox.php?user=<?php echo $_GET["user"]; ?>">
				<input type="submit" value="Contact Me!"><br>
				</form>
		
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
		
		<td style="padding: 0px 10px 0px 10px;">
		
		<table width="100%" cellpadding="5" cellspacing="0" border="0">
			<tr>
				<td width="120" align="right"><span class="label">User Name:</span></td>
				<td><?php echo $_GET["user"]; ?></td>
			</tr>
			
			<!-- Personal Information: -->
			<?php
			if(isset($user->personal_name)) {
				echo '			<tr>
				<td align="right"><span class="label">Name:</span></td>
				<td>' . htmlspecialchars($user->personal_name) . '</td>
			</tr>';
			}
			
			if(isset($user->personal_age)) {
				echo '			<tr>
				<td align="right"><span class="label">Age:</span></td>
				<td>' . $user->personal_age . '</td>
			</tr>';
			}
			
			//so what do we display the gender as?
			switch($user->personal_gender) {
				case 0:
					$gender = NULL;
					break;
				case 1:
					$gender = "Male";
					break;
				case 2:
					$gender = "Female";
					break;
				case 3: //accurracy sucks in this case...  inclusivity = good
					$gender = "Other";
					break;
			}
			
			if(isset($gender)) {
				echo '			<tr>
				<td align="right"><span class="label">Gender:</span></td>
				<td>' . $gender . '</td>
			</tr>';
			}
			
			//so what do we display the relationship status as?
			switch($user->personal_relationship) {
				case 0:
					$relationship = NULL;
					break;
				case 1:
					$relationship = "Single";
					break;
				case 2:
					$relationship = "Taken";
					break;
				case 3:
					$relationship = "Open";
					break;
			}
			
			if(isset($relationship)) {
				echo '			<tr>
				<td align="right"><span class="label">Relationship Status:</span></td>
				<td>' . $relationship . '</td>
			</tr>';
			}
			
			if(isset($user->personal_about)) {
				echo '			<tr>
				<td align="right"><span class="label">About Me:</span></td>
				<td>' . nl2br(htmlspecialchars($user->personal_about)) . '</td>
			</tr>'; // did they have newlines at the time...?
			}
			
			if(isset($user->personal_website)) {
				echo '			<tr>
				<td align="right"><span class="label">Personal Website:</span></td>
				<td><a href="' . htmlspecialchars($user->personal_website) . '" target="_blank">' . htmlspecialchars($user->personal_website) . '</a></td>
			</tr>';
			}
			
			?>
			
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>

			
			
			
			<!-- Location Information -->
			<?php
			if(isset($user->location_hometown)) {
				echo '			<tr>
				<td align="right"><span class="label">Hometown:</span></td>
				<td>' . htmlspecialchars($user->location_hometown) . '</td>
			</tr>';
			}

			if(isset($user->location_city)) {
				echo '			<tr>
				<td align="right"><span class="label">Current City:</span></td>
				<td>' . htmlspecialchars($user->location_city) . '</td>
			</tr>';
			}

			if(isset($user->location_country)) {
				echo '			<tr>
				<td align="right"><span class="label">Current Country:</span></td>
				<td>' . htmlspecialchars($user->location_country) . '</td>
			</tr>';
			}
	
			?>
			
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			
			
			
			<!-- Random Information -->
			<?php 
			if(isset($user->location_occupations)) {
				echo '			<tr>
				<td align="right"><span class="label">Occupations:</span></td>
				<td>' . htmlspecialchars($user->location_occupations) . '</td>
			</tr>';
			}
			
			if(isset($user->location_companies)) {
				echo '			<tr>
				<td align="right"><span class="label">Companies:</span></td>
				<td>' . htmlspecialchars($user->location_companies) . '</td>
			</tr>';
			}
	
			if(isset($user->location_schools)) {
				echo '			<tr>
				<td align="right"><span class="label">Schools:</span></td>
				<td>' . htmlspecialchars($user->location_schools) . '</td>
			</tr>';
			}
			
			if(isset($user->random_interests)) {
				echo '			<tr>
				<td align="right"><span class="label">Interests &amp; Hobbies:</span></td>
				<td>' . htmlspecialchars($user->random_interests) . '</td>
			</tr>';
			}
			
			if(isset($user->random_movies)) {
				echo '			<tr>
				<td align="right"><span class="label">Favorite Movies &amp; Shows:</span></td>
				<td>' . htmlspecialchars($user->random_movies) . '</td>
			</tr>';
			}	

			if(isset($user->random_music)) {
				echo '			<tr>
				<td align="right"><span class="label">Favorite Music:</span></td>
				<td>' . htmlspecialchars($user->random_music) . '</td>
			</tr>';
			}	

			if(isset($user->random_books)) {
				echo '			<tr>
				<td align="right"><span class="label">Favorite Books:</span></td>
				<td>' . htmlspecialchars($user->random_books) . '</td>
			</tr>';
			}
			?>
			
			<tr>
				<td align="right"><span class="label">Last Login:</span></td>
				<td><?php echo timetostr($user->last_login); ?></td>
			</tr>
		</table>
		
		</td>
		
				
		<td width="180">
		<div style="font-size: 14px; font-weight: bold; margin-bottom: 10px; color: #444;">&#187; Profile</div>
		<div style="font-size: 14px; font-weight: bold; margin-bottom: 10px; color: #444;">&#187; <a href="profile_videos.php?user=<?php echo $_GET["user"]; ?>">Public Videos</a> (<?php echo $public_videos; ?>)</div>
		<!-- only show this link to friends in their network -->
		<div style="font-size: 14px; font-weight: bold; margin-bottom: 10px; color: #444;">&#187; <a href="profile_videos_private.php?user=<?php echo $_GET["user"]; ?>">Private Videos</a> (<?php echo $private_videos; ?>)</div>
		<!-- only show this link to friends in their network -->
		<div style="font-size: 14px; font-weight: bold; margin-bottom: 10px; color: #444;">&#187; <a href="profile_favorites.php?user=<?php echo $_GET["user"]; ?>">Favorites</a> (<?php echo $favorite_videos; ?>)</div>
		<div style="font-size: 14px; font-weight: bold; margin-bottom: 20px; color: #444;">&#187; <a href="profile_friends.php?user=<?php echo $_GET["user"]; ?>">Friends</a> (<?php echo $friends; ?>)</div>
		
		<?php
		//if public video count is over 0, fetch info
		if($public_videos > 0) {
				$stmt = $conn->prepare("SELECT * FROM videos WHERE uploaded_by=:t0 AND isPrivate=0 ORDER BY uploaded_on DESC LIMIT 1");
				$stmt->bindParam(":t0", $_GET["user"]);
				$stmt->execute();
				// start outputting video
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
				echo '		<table width="100%" align="center" cellpadding="0" cellspacing="0" border="0" bgcolor="#CCCCCC">
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
					<tr>
						<td><div class="moduleTitle">My Latest Video</div></td>
					</tr>
				</table>
				</div>
		
		<div class="moduleFeatured">
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td align="center">
				<a href="watch.php?v=' . $video->id . '"><img src="get_still.php?video_id=' . $video->id . '" class="moduleFeaturedThumb" width="120" height="90"></a>
				<div class="moduleFeaturedTitle"><a href="watch.php?v=' . $video->id . '">' . htmlspecialchars($video->title) . '</a></div>
				<div class="moduleFeaturedDetails">Added: ' . $Now->format('F') . " " . $Now->format('d') . ", " . $Now->format('Y') . '<br>by <a href="profile.php?user=' . $_GET["user"] . '">' . $_GET["user"] . '</a></div>
				<div class="moduleFeaturedDetails">Views: ' . $view_count . '</div>
				<div class="moduleFeaturedDetails">Comments: ' . $comment_count . '</div>
				</td>
			</tr>
		</table>
		</div>
		
				</td>
				<td><img src="img/pixel.gif" width="5" height="1"></td>
			</tr>
			<tr>
				<td><img src="img/box_login_bl.gif" width="5" height="5"></td>
				<td><img src="img/pixel.gif" width="1" height="5"></td>
				<td><img src="img/box_login_br.gif" width="5" height="5"></td>
			</tr>
		</table>';
			}
		}
		?>
		
		<div style="font-size: 12px; color: #444; margin: 10px 0px 0px 0px; text-align: center;"><strong>Like my videos?</strong><br>
		<a href="/rss.php?user=<?php echo $_GET["user"]; ?>">Subscribe to my RSS Feed.</a></div>
		
		</td>

			
	</tr>
</table>
<?php require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/footer.php"); ?>