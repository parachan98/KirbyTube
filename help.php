<?php 
require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/header.php"); 
$url = "http://" . $_SERVER["HTTP_HOST"];

if(isset($_GET["flash"])) {
	if($_GET["flash"] == 1) {
		setcookie("flash", true, 2147483647);
	} else if($_GET["flash"] == 0) {
		setcookie("flash", "", time()-3600);
	}
}

?>
<div class="tableSubTitle">Help</div>

<span class="highlight">Q: What kind of videos can I upload?</span>

<br><br>A: You may upload any kind of personal video that you'd like to share with the world. We don't allow any nudity and your video must be appropriate for all audiences. 
<br/>
<br/>
However, this still leaves a lot of room for creativity!! Do you own a <a href="results.php?search=dog">dog</a> or a <a href="results.php?search=cat">cat</a>? Have you gone on vacationing in <a href="results.php?search=mexico">Mexico</a>? Do you live in <a href="results.php?search=netherlands">The Netherlands</a>?
<br/>
<br/>
These are just some examples of the videos that our users are uploading. In the end, you know yourself best. What would <i>you</i> like to capture on video?

<br><br><span class="highlight">Q: How long can my video be?</span>

<br><br>A: There is no time limit on your video, but the video file you upload must be less than 100 MB in size.

<br><br><span class="highlight">Q: What video file formats can I upload?</span>

<br><br>A: <?php echo $website["instance_name"]; ?> accepts video files from most digital cameras and from cell phones in the .AVI, .MOV, and .MPG file formats.

<br><br><span class="highlight">Q: How can I improve my videos?</span>

<br><br>A: We encourage you to edit your videos with software such as <a href="http://www.microsoft.com/windowsxp/using/moviemaker/default.mspx" target="_blank">Windows MovieMaker</a> (included with every Windows installation), or <a href="http://www.apple.com/ilife/imovie/" target="_blank">Apple iMovie</a>. Using these programs you can easily edit
your videos, add soundtracks, etc.

<br><br><span class="highlight">Q: How do I link to my <?php echo $website["instance_name"]; ?> videos from my homepage?</span>

<br><br>A: Any video you upload to <?php echo $website["instance_name"]; ?> is still <b>your</b> video. We want to enable <?php echo $website["instance_name"]; ?> users to link to their videos in every way possible. By placing a small snippet of HTML code in your webpage, you can pull up a list of all your <?php echo $website["instance_name"]; ?> videos in a neat, little window. Take a look at the example below, on the left is the HTML snippet you would copy+paste into your webpage. As a result, a small box with your videos will be rendered as shown on the right.
<br>
<br>

	<table width="100%">
	<tr>
		<td valign="top" align="center">
			<span class="highlight">HTML Snippet (iframe version)</span>
			<br/>
			<br/>
			<textarea cols="65" rows="8" id="snippet_iframe" wrap="soft"><iframe id="videos_list" name="videos_list" src="<?php echo $url; ?>/videos_list.php?user=<?php if(isset($_SESSION["username"])) { echo $_SESSION["username"]; } else echo "YOUR_USERNAME"; ?>" scrolling="auto" width="265" height="400" frameborder="0" marginheight="0" marginwidth="0"></iframe></textarea>
			<br/>
			<br/>
			<span class="highlight">HTML Snippet (embed version)</span>
			<br/>
			<br/>
			<textarea cols="65" rows="8" id="snippet_embed" wrap="soft"><embed src="<?php echo $url; ?>/myclips.swf?u=<?php if(isset($_SESSION["username"])) { echo $_SESSION["username"]; } else echo "YOUR_USERNAME"; ?>" quality="high" width="425" height="350" name="myclips" align="middle" allowscriptaccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer"/></textarea>
			<br/>
			
		</td>
		<td valign="top" align="center">
			<span class="highlight">What Shows Up</span>
			<br/>
			<br/>
			<iframe id="videos_list" name="videos_list" src="videos_list.php?user=<?php echo $website["videos_list"]; ?>" scrolling="auto" width="265" height="400" frameborder="0" marginheight="0" marginwidth="0"></iframe>
		</td>
	</tr>
</table>

<br><br><span class="highlight">Q: I have an old computer, how can I play <?php echo $website["instance_name"]; ?> videos?</span>
<?php
if(isset($_COOKIE["flash"])) {
	echo '<br><br>A: Click <a href="?flash=0">here</a> to toggle the Flash video player.';
} else echo '<br><br>A: Click <a href="?flash=1">here</a> to toggle the Flash video player.';
?>

<br><br><br><span class="highlight">Contact <?php echo $website["instance_name"]; ?></span>
<br><br>If you have any account or video issues, please contact us <a href="contact.php">here</a>.
Also, if you have any ideas or suggestions to make our service better, please don't hesitate to drop us a line.
<?php require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/footer.php"); ?>