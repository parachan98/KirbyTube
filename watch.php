<?php 
require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/header.php"); 

if(isset($_GET["v"])) {
	$stmt = $conn->prepare("SELECT * from videos where id=:t0");
	$stmt->bindParam(":t0", $_GET["v"]);
	$stmt->execute();
	if($stmt->rowCount() == 0) { header("Location: /index.php"); }
	foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $video);
} else {
	header("Location: /index.php");
}

if($video->isPrivate == 1) {
	if(!isset($_SESSION["username"])) { die(header("Location: /")); }
	if($_SESSION["username"] !== $video->uploaded_by) {
		//this video is private, are we Friends?
		$stmt = $conn->prepare("SELECT * FROM friends WHERE sent_by=:t0 or sent_to=:t1");
		$stmt->bindParam(":t0", $_SESSION["username"]);
		$stmt->bindParam(":t1", $_GET["user"]);
		$stmt->execute();
		if($stmt->rowCount() == 0) { die(header("Location: /")); }
	}
}

$Now = new DateTime($video->uploaded_on);

// grab all video counts
$stmt = $conn->prepare("SELECT * FROM videos WHERE uploaded_by=:t0");
$stmt->bindParam(":t0", $video->uploaded_by);
$stmt->execute();
$uploader_videos = $stmt->rowCount();

$stmt = $conn->prepare("SELECT * FROM favorites WHERE added_by=:t0");
$stmt->bindParam(":t0", $video->uploaded_by);
$stmt->execute();
$uploader_favorites = $stmt->rowCount();

$stmt = $conn->prepare("SELECT * FROM friends WHERE sent_by=:t0 OR sent_to=:t0 AND pending=0");
$stmt->bindParam(":t0", $video->uploaded_by);
$stmt->execute();
$uploader_friends = $stmt->rowCount();

$stmt = $conn->prepare("SELECT * FROM views WHERE viewed_video=:t0");
$stmt->bindParam(":t0", $video->id);
$stmt->execute();
$views = $stmt->rowCount();

//delete comment
if(isset($_GET["delcomm"])) {
	$_GET["delcomm"] = intval($_GET['delcomm']);
	$stmt = $conn->prepare("SELECT * FROM comments WHERE id=:t0");
	$stmt->bindParam(':t0', $_GET["delcomm"]);
	$stmt->execute();
	foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $comment) {
		if($_SESSION["username"] == $comment->posted_by) {
			$stmt = $conn->prepare("DELETE FROM comments WHERE id=:t0");
			$stmt->bindParam(':t0', $_GET["delcomm"]);
			$stmt->execute();	
		}
	}
}

$stmt = $conn->prepare("SELECT * FROM comments WHERE posted_to=:t0");
$stmt->bindParam(":t0", $video->id);
$stmt->execute();
$comments = $stmt->rowCount();

$url = "http://" . $_SERVER["HTTP_HOST"];

// add view to video
$view_count = 0;
$add_view = true;
$current_ip = getIP();

$stmt = $conn->prepare("SELECT * FROM views WHERE viewed_video=:t0");
$stmt->bindParam(':t0', $_GET['v']);
$stmt->execute();
foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $video_view_ip) {
	if ($video_view_ip->viewed_from == $current_ip) {
		$add_view = false;	
	}
	$view_count++;
}

if ($add_view) {
	$stmt = $conn->prepare("INSERT INTO views (viewed_video, viewed_from, viewed_by) VALUES (:id, :ip, :user)");
	$stmt->bindParam(":id", $_GET['v']);
	$stmt->bindParam(":ip", $current_ip);
	$stmt->bindParam(":user", $_SESSION["username"]);
	$stmt->execute();
	$view_count++;
}

?>

<iframe id="invisible" name="invisible" src="" scrolling="yes" width="0" height="0" frameborder="0" marginheight="0" marginwidth="0"></iframe>   

<script>

function CheckLogin()
{
	
		<?php 
		if(!isset($_SESSION["username"])) { 
		echo 'alert("You must be logged in to to perform this action!");
		return false;'; 
		} else echo 'return true;'; ?>

		
	return true;
}

function FavoritesHandler()
{
	if (CheckLogin() == false)
		return false;

	alert("Video has been added to Favorites!");
	return true;
}

function CommentHandler()
{
	if (CheckLogin() == false)
		return false;

	var comment = document.comment_form.comment;
	var comment_button = document.comment_form.comment_button;

	if (comment.value.length == 0 || comment.value == null)
	{
		alert("You must enter a comment!");
		comment.focus();
		return false;
	}

	if (comment.value.length > 500)
	{
		alert("Your comment must be shorter than 500 characters!");
		comment.focus();
		return false;
	}

	comment_button.disabled='true';
	comment_button.value='Thanks for your comment!';

	return true;
}
</script>

<table width="795" align="center" cellpadding="0" cellspacing="0" border="0">
	<tr valign="top">
		<td width="515" style="padding-right: 15px;">
		
		<div class="tableSubTitle"><?php echo htmlspecialchars($video->title); ?></div>
		<div style="font-size: 13px; font-weight: bold; text-align:center;">
		<a href="mailto:?subject=<?php echo htmlspecialchars($video->title); ?>&body=<?php echo $url; ?>/?v=<?php echo $_GET["v"]; ?>">Share</a>
		// <a href="#comment">Comment</a>
		// <a href="add_favorites.php?video_id=<?php echo $_GET["v"]; ?>" target="invisible" onClick="return FavoritesHandler();">Add to Favorites</a>
		// <a href="outbox.php?user=<?php echo $video->uploaded_by; ?>&subject=Re: <?php echo htmlspecialchars(htmlentities($video->title)); ?>">Contact Me</a>
		<?php
		//video owner options
		if($_SESSION["username"] == $video->uploaded_by) {
			echo '<p>Video Owner Options: <a href="my_videos_edit.php?v=' . $_GET["v"] . '">Edit Your Video Here</a></p>';
		}
		?>
		</div>

		<div style="text-align: center; padding-bottom: 10px;">
		<div id="flashcontent">
		<?php
		if(!isset($_COOKIE["flash"])) {
			echo '<link rel="stylesheet" href="/img/player.css">
			<!-- player HTML begins here -->
        <div class="player" id="playerBox">
            <div class="mainContainer">
                <div class="playerScreen">
                    <div class="playbackArea">
                        <div class="videoContainer">
                            <video class="videoObject" id="video">
                                <source src="/content/video/' . $_GET["v"] . '.mp4"> 
                             </video>
                        </div>
                    </div>
                </div>
                <div class="controlBackground">
                    <div class="controlContainer">
                        <div class="lBtnContainer">
                            <div class="button" id="playButton">
                                <img src="/img/resource/play.png" id="playIcon">
                                <img src="/img/resource/pause.png" class="hidden" id="pauseIcon">
                            </div>
                        </div>
                        <div class="centerContainer">
                            <div class="seekbarElementContainer">
                                <progress class="seekProgress" id="seekProgress" value="0" min="0"></progress>
                            </div>
                            <div class="seekbarElementContainer">
                                <input class="seekHandle" id="seekHandle" value="0" min="0" step="1" type="range">
                            </div>
                        </div>
                        <div class="rBtnContainer">
                            <div class="button" id="muteButton">
                                <img src="/img/resource/mute.png" id="muteIcon">
                                <img src="/img/resource/unmute.png" class="hidden" id="unmuteIcon">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="aboutBox hidden" id="aboutBox">
                <div class="aboutBoxContent">
                    <div class="aboutHeader">Viewfinder 2005</div>
                    <div class="aboutBody">
                        <div>Version 1.0<br>
                        <br>
                        Early 2005 YouTube<br>
                        player HTML5 replica<br>
                        <br>
                        by purpleblaze<br>
                    </div>
                <button id="aboutCloseBtn">Close</button>
                </div>
            </div>
            <div class="contextMenu hidden" id="playerContextMenu">
                <div class="contextItem" id="contextMute">
                    <span>Mute</span>
                    <div id="muteTick" class="tick hidden">    
                    </div>
                </div>
                <div class="contextItem" id="contextLoop">
                    <span>Loop</span>
                    <div id="loopTick" class="tick hidden">
                    </div>
                </div>
                <div class="contextSeparator"></div>
                <div class="contextItem" id="contextAbout">About</div>
            </div>
        </div>
		</div>
        <script src="/img/player.js"></script>
        <!-- here lies purple -->';
		} else echo '<embed src="player.swf?video_id=' . $_GET["v"] . "&l=" . $video->runtime . '" width="425" height="350">';
		?>
		</div>
		</div>

		<table width="425" cellpadding="0" cellspacing="0" border="0" align="center">
			<tr>
				<td>
					<div class="watchDescription"><?php echo nl2br(htmlspecialchars($video->description)); ?>					<div class="watchAdded" style="margin-top: 5px;">
										</div>
					</div>
					<div class="watchTags">Tags //
					<?php
					$thetags = [];
					$thetags = array_merge($thetags, explode(" ", $video->tags));
					$thetags = array_unique($thetags);
					foreach($thetags as $tag) {
						echo "<a href=\"results.php?search=" . htmlspecialchars($tag) . "\">" . htmlspecialchars($tag) . "</a> : ";
					}
					?>
					</div>
					
					<?php
					echo '<div class="watchAdded">';
					echo 'Added: ' . $Now->format('F') . " " . $Now->format('d') . ", " . $Now->format('Y');
					echo ' by <a href="profile.php?user=' . $video->uploaded_by . '">' . $video->uploaded_by . '</a> // ';
					echo '<a href="profile_videos.php?user=' . $video->uploaded_by . '">Videos</a> (' . $uploader_videos . ') | ';
					echo '<a href="profile_favorites.php?user=' . $video->uploaded_by . '">Favorites</a> (' . $uploader_favorites . ') | ';
					echo '<a href="profile_friends.php?user=' . $video->uploaded_by . '">Friends</a> (' . $uploader_friends . ')';
					echo '					</div>
			
					<div class="watchDetails">';
					echo 'Views: ' . $views . ' | <a href="#comment">Comments</a>: ' . $comments . '</div>';
					?>

				</td>
			</tr>
		</table>
		
		<!-- watchTable -->
		
		<div style="padding: 15px 0px 10px 0px;">
		<table width="100%" align="center" cellpadding="0" cellspacing="0" border="0" bgcolor="#E5ECF9">
			<tr>
				<td><img src="img/box_login_tl.gif" width="5" height="5"></td>
				<td width="100%"><img src="img/pixel.gif" width="1" height="5"></td>
				<td><img src="img/box_login_tr.gif" width="5" height="5"></td>
			</tr>
			<tr>
				<form name="linkForm" id="linkForm">
				<td><img src="img/pixel.gif" width="5" height="1"></td>
				<td align="center">
		
				<div style="font-size: 11px; font-weight: bold; color: #CC6600; padding: 5px 0px 5px 0px;">Share this video! Copy and paste this link:</div>
				<div style="font-size: 11px; padding-bottom: 15px;">
				<input name="video_link" type="text" onClick="javascript:document.linkForm.video_link.focus();document.linkForm.video_link.select();" value="<?php echo $url . '/?v=' . $_GET["v"]; ?>" size="50" readonly="true" style="font-size: 10px; text-align: center;">
				</div>
				
				</td>
				<td><img src="img/pixel.gif" width="5" height="1"></td>
				</form>
			</tr>
			<tr>
				<td><img src="img/box_login_bl.gif" width="5" height="5"></td>
				<td><img src="img/pixel.gif" width="1" height="5"></td>
				<td><img src="img/box_login_br.gif" width="5" height="5"></td>
			</tr>
		</table>
		</div>
		
		<a name="comment"></a>
		<div style="padding-bottom: 5px; font-weight: bold; color: #444;">Comment on this video:</div>

		<form name="comment_form" id="comment_form" method="post" action="add_comment.php" target="invisible" onSubmit="return CommentHandler();">
		<input type="hidden" name="video_id" value="<?php echo $_GET["v"]; ?>">

		<textarea name="comment" cols="55" rows="3"></textarea>
		<br>
		<input type="submit" name="comment_button" value="Add Comment">
		<?php if(isset($_SESSION["username"])) {
			echo '
		Attach a video: <select name="field_reference_video">
				<option value="">- Your Videos -</option>';
				// get the users videos as to allow for spam and self promotion
				$stmt = $conn->prepare("SELECT * FROM videos WHERE uploaded_by=:t0 AND isPrivate=0 ORDER BY uploaded_on DESC");
				$stmt->bindParam(":t0", $_SESSION["username"]);
				$stmt->execute();
				foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $attach_video) {
					echo '<option value="' . $attach_video->id . '">' . htmlspecialchars($attach_video->title) . '</option>';
				}
				echo '
				<option value="">- Your Favorite Videos -</option>';
				// get the users favorite videos
				$stmt = $conn->prepare("SELECT * FROM favorites WHERE added_by=:t0 ORDER BY id DESC");
				$stmt->bindParam(":t0", $_SESSION["username"]);
				$stmt->execute();
				foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $favorite) {
					// start querying video table for information
					$stmt = $conn->prepare("SELECT * FROM videos WHERE id=:t0 ORDER BY uploaded_on DESC");
					$stmt->bindParam(":t0", $favorite->video_id);
					$stmt->execute();
					foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $attach_video) {
						if($video->isPrivate == 0) { echo '<option value="' . $attach_video->id . '">' . htmlspecialchars($attach_video->title) . '</option>'; }
					}
				}
				echo '
			</select>';
		}
		?>
		
		</form>
		<br>

		<div class="commentsTitle">Comments (<?php echo $comments; ?>):</div>
		<?php
		// fetch all comments
		$stmt = $conn->prepare("SELECT * FROM comments WHERE posted_to=:t0 ORDER BY id DESC");
		$stmt->bindParam(":t0", $_GET["v"]);
		$stmt->execute();
		foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $comment) {
			$Now = new DateTime($comment->posted_at);
			// fetch commenter information
			$stmt = $conn->prepare("SELECT * FROM videos WHERE uploaded_by=:t0");
			$stmt->bindParam(":t0", $comment->posted_by);
			$stmt->execute();
			$public_videos = $stmt->rowCount();

			$stmt = $conn->prepare("SELECT * FROM favorites WHERE added_by=:t0");
			$stmt->bindParam(":t0", $comment->posted_by);
			$stmt->execute();
			$favorite_videos = $stmt->rowCount();
			
			$stmt = $conn->prepare("SELECT * FROM friends WHERE sent_by=:t0 OR sent_to=:t0 AND pending=0");
			$stmt->bindParam(":t0", $comment->posted_by);
			$stmt->execute();
			$friends = $stmt->rowCount();
			
			//HACKY! comment deletion
			if($_SESSION["username"] == $video->uploaded_by) { 
				$delete_comment_html = '(<a href="watch.php?v=' . $_GET["v"] . '&delcomm=' . $comment->id . '">Remove Comment</a>)'; 
			} else $delete_comment_html = NULL;
	
			// check for attached video
			if(isset($comment->attached_video)) {
				echo ' <div class="commentsEntry">
					<table cellpadding="0" cellspacing="0" border="0">
					<tr valign="top">
					<td width="80">
					<div style="float: left;"><a href="watch.php?v=' . $comment->attached_video . '"><img src="get_still.php?video_id=' . $comment->attached_video . '" class="commentsThumb" width="60" height="45"></a></div>
					<div style="font-size: 10px; text-align: center;"><a href="watch.php?v=' . $comment->attached_video . '">Related Video</a></div>
					</td><td style="font-size: 11px;">
					"' . nl2br(htmlspecialchars($comment->content)) . '"<br>
 - <a href="profile.php?user=' . $comment->posted_by . '">' . $comment->posted_by . '</a> // <a href="profile_videos.php?user=' . $comment->posted_by . '">Videos</a> (' . $public_videos . '</a>) | <a href="profile_favorites.php?user=' . $comment->posted_by . '">Favorites</a> (' . $favorite_videos . ') | <a href="profile_friends.php?user=' . $comment->posted_by . '">Friends</a> (' . $friends . ') - ' . $delete_comment_html . ' (' . commentTimeAgo($comment->posted_at) . ') 			</td></tr>
					</table>

					</div>';
			} else {
				echo '<div class="commentsEntry">"' . nl2br(htmlspecialchars($comment->content)) . '"<br>
 - <a href="profile.php?user=' . $comment->posted_by . '">' . $comment->posted_by . '</a> // <a href="profile_videos.php?user=' . $comment->posted_by . '">Videos</a> (' . $public_videos . '</a>) | <a href="profile_favorites.php?user=' . $comment->posted_by . '">Favorites</a> (' . $favorite_videos . ') | <a href="profile_friends.php?user=' . $comment->posted_by . '">Friends</a> (' . $friends . ') - ' . $delete_comment_html . ' (' . commentTimeAgo($comment->posted_at) . ')	</div>';
			}
		}
		?>
		</td>
		<td width="280">
		
		<table width="280" align="center" cellpadding="0" cellspacing="0" border="0" bgcolor="#CCCCCC">
			<tr>
				<td><img src="img/box_login_tl.gif" width="5" height="5"></td>
				<td><img src="img/pixel.gif" width="1" height="5"></td>
				<td><img src="img/box_login_tr.gif" width="5" height="5"></td>
			</tr>
			<tr>
				<td><img src="img/pixel.gif" width="5" height="1"></td>
				<td width="270">
				<div class="moduleTitleBar">
				<table width="270" cellpadding="0" cellspacing="0" border="0">
					<tr valign="top">
						<td><div class="moduleFrameBarTitle">Tag // <?php echo htmlspecialchars($video->tags); ?></div></td>
						<td align="right"><div style="font-size: 11px; margin-right: 5px;"><a href="results.php?&search=<?php echo str_replace(" ", "+", htmlentities($video->tags)); ?>" target="_parent">See more Results</a></div></td>
					</tr>
				</table>
				</div>

				<iframe id="side_results" name="side_results" src="include_results.php?v=<?php echo $_GET["v"] . '&search=' . str_replace(" ", "+", htmlentities($video->tags)); ?>#selected" scrolling="auto" 
				 width="270" height="400" frameborder="0" marginheight="0" marginwidth="0">
				 [Content for browsers that don't support iframes goes here]
				</iframe>
				</td>
				<td><img src="img/pixel.gif" width="5" height="1"></td>
			</tr>
			<tr>
				<td><img src="img/box_login_bl.gif" width="5" height="5"></td>
				<td><img src="img/pixel.gif" width="1" height="5"></td>
				<td><img src="img/box_login_br.gif" width="5" height="5"></td>
			</tr>
		</table><br>

<div style="font-weight: bold; color: #333; margin: 10px 0px 5px 0px;">Related tags:</div>
<?php 
$stmt = $conn->query("SELECT tags FROM videos WHERE tags REGEXP \"" . str_replace(" ", "|", $video->tags) . "\" AND isPrivate=0 ORDER BY uploaded_on DESC"); // Regex!
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

<?php require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/footer.php"); ?>