<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/config.php");
header("Content-Type: text/xml");

// recently added
if(isset($_GET["recently_added"])) {
	echo '<?xml version="1.0" encoding="utf-8"?>
<rss version="2.0" xmlns:media="http://search.yahoo.com/mrss">
	<channel>
		<title>' . $website["instance_name"] . ' :: Recently Added Videos</title>
		<link>http://' . $_SERVER["HTTP_HOST"] . '/rss.php?recently_added</link>
		<description>Recently Added Videos</description>
		';
	// start outputting videos list
	$stmt = $conn->query("SELECT * FROM videos WHERE isPrivate=0 ORDER BY uploaded_on DESC LIMIT 15");
	foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $video) { 
		$Now = new DateTime($video->uploaded_on);
		echo '		
		<item>
			<author>' . $video->uploaded_by . '</author>
			<title>' . htmlentities($video->title) . '</title>
			<link>http://' . $_SERVER["HTTP_HOST"] . '/?v=' . $video->id . '</link>
			<description>
				<![CDATA[
				<img src="http://' . $_SERVER["HTTP_HOST"] . '/get_still.php?video_id=' . $video->id . '" align="right" border="0" width="120" height="90" vspace="4" hspace="4" />
				<p>
				' . nl2br(htmlentities($video->description)) . '
				</p>
				<p>
					Author: <a href="http://' . $_SERVER["HTTP_HOST"] . '/profile.php?user=' . $video->uploaded_by . '">' . $video->uploaded_by . '</a><br/>
					Keywords:  ';
					$thetags = [];
					$thetags = array_merge($thetags, explode(" ", $video->tags));
					$thetags = array_unique($thetags);
					foreach($thetags as $tag) {
						echo '<a href="http://' . $_SERVER["HTTP_HOST"] . '/results.php?search=' . htmlentities($tag) . '">' . htmlentities($tag) . '</a> ';
					}
					echo '<br/>
					Added: ' . $Now->format('F') . ' ' . $Now->format('d') . ', ' . $Now->format('Y') . '<br/>
				</p>
]]>
			</description>
			<guid isPermaLink="true">http://' . $_SERVER["HTTP_HOST"] . '/?v=' . $video->id . '</guid>
			<pubDate>' . $Now->format('r') . '</pubDate>

			<media:player url="http://' . $_SERVER["HTTP_HOST"] . '/?v=' . $video->id . '" />
			<media:thumbnail url="http://' . $_SERVER["HTTP_HOST"] . '/get_still.php?video_id=' . $video->id . '" width="120" height="90" />
			<media:title>' . htmlentities($video->title) . '</media:title>
			<media:category label="Tags">' . htmlentities($video->tags) . '</media:category>
			<media:credit>' . $video->uploaded_by . '</media:credit>
			<enclosure url="http://' . $_SERVER["HTTP_HOST"] . '/player.swf?video_id=' . $video->id . '&amp;l=' . $video->runtime . '" length="' . $video->runtime . '" type="application/x-shockwave-flash" />
		</item>
';
	}
}

// profile videos
if(isset($_GET["user"])) {
	echo '<?xml version="1.0" encoding="utf-8"?>
<rss version="2.0" xmlns:media="http://search.yahoo.com/mrss">
	<channel>
		<title>' . $website["instance_name"] . ' :: Videos by ' . $_GET["user"] . '</title>
		<link>http://' . $_SERVER["HTTP_HOST"] . '/rss.php?user=' . $_GET["user"] . '</link>
		<description>Videos uploaded by ' . $_GET["user"] . ' hosted at http://' . $_SERVER["HTTP_HOST"] . '.</description>
		';
	// start outputting videos list
	$stmt = $conn->query("SELECT * FROM videos WHERE isPrivate=0 AND uploaded_by='" . $_GET["user"] . "' ORDER BY uploaded_on DESC");
	foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $video) { 
		$Now = new DateTime($video->uploaded_on);
		echo '		
		<item>
			<author>' . $video->uploaded_by . '</author>
			<title>' . htmlentities($video->title) . '</title>
			<link>http://' . $_SERVER["HTTP_HOST"] . '/?v=' . $video->id . '</link>
			<description>
				<![CDATA[
				<img src="http://' . $_SERVER["HTTP_HOST"] . '/get_still.php?video_id=' . $video->id . '" align="right" border="0" width="120" height="90" vspace="4" hspace="4" />
				<p>
				' . nl2br(htmlentities($video->description)) . '
				</p>
				<p>
					Author: <a href="http://' . $_SERVER["HTTP_HOST"] . '/profile.php?user=' . $video->uploaded_by . '">' . $video->uploaded_by . '</a><br/>
					Keywords:  ';
					$thetags = [];
					$thetags = array_merge($thetags, explode(" ", $video->tags));
					$thetags = array_unique($thetags);
					foreach($thetags as $tag) {
						echo '<a href="http://' . $_SERVER["HTTP_HOST"] . '/results.php?search=' . htmlentities($tag) . '">' . htmlentities($tag) . '</a> ';
					}
					echo '<br/>
					Added: ' . $Now->format('F') . ' ' . $Now->format('d') . ', ' . $Now->format('Y') . '<br/>
				</p>
]]>
			</description>
			<guid isPermaLink="true">http://' . $_SERVER["HTTP_HOST"] . '/?v=' . $video->id . '</guid>
			<pubDate>' . $Now->format('r') . '</pubDate>

			<media:player url="http://' . $_SERVER["HTTP_HOST"] . '/?v=' . $video->id . '" />
			<media:thumbnail url="http://' . $_SERVER["HTTP_HOST"] . '/get_still.php?video_id=' . $video->id . '" width="120" height="90" />
			<media:title>' . htmlentities($video->title) . '</media:title>
			<media:category label="Tags">' . htmlentities($video->tags) . '</media:category>
			<media:credit>' . $video->uploaded_by . '</media:credit>
			<enclosure url="http://' . $_SERVER["HTTP_HOST"] . '/player.swf?video_id=' . $video->id . '&amp;l=' . $video->runtime . '" length="' . $video->runtime . '" type="application/x-shockwave-flash" />
		</item>
';
	}
}

//search
if(isset($_GET["tag"])) {
	echo '<?xml version="1.0" encoding="utf-8"?>
<rss version="2.0" xmlns:media="http://search.yahoo.com/mrss">
	<channel>
		<title>' . $website["instance_name"] . ' :: Tag // ' . $_GET["tag"] . '</title>
		<link>' . $_SERVER["HTTP_HOST"] . '/rss.php?tag=' . $_GET["tag"] . '</link>
		<description>Videos tagged with ' . $_GET["tag"] . '</description>
';

	$stmt = $conn->prepare("SELECT * FROM videos WHERE isPrivate=0 AND tags REGEXP :t0 OR uploaded_by=:t0 AND isPrivate=0 ORDER BY uploaded_on DESC LIMIT 15"); // Regex!
	$stmt->bindParam(":t0", $_GET["tag"]);
	$stmt->execute();
	
	foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $video) {
		$Now = new DateTime($video->uploaded_on);
		echo '		
		<item>
			<author>' . $video->uploaded_by . '</author>
			<title>' . htmlentities($video->title) . '</title>
			<link>http://' . $_SERVER["HTTP_HOST"] . '/?v=' . $video->id . '</link>
			<description>
				<![CDATA[
				<img src="http://' . $_SERVER["HTTP_HOST"] . '/get_still.php?video_id=' . $video->id . '" align="right" border="0" width="120" height="90" vspace="4" hspace="4" />
				<p>
				' . nl2br(htmlentities($video->description)) . '
				</p>
				<p>
					Author: <a href="http://' . $_SERVER["HTTP_HOST"] . '/profile.php?user=' . $video->uploaded_by . '">' . $video->uploaded_by . '</a><br/>
					Keywords:  ';
					$thetags = [];
					$thetags = array_merge($thetags, explode(" ", $video->tags));
					$thetags = array_unique($thetags);
					foreach($thetags as $tag) {
						echo '<a href="http://' . $_SERVER["HTTP_HOST"] . '/results.php?search=' . htmlentities($tag) . '">' . htmlentities($tag) . '</a> ';
					}
					echo '<br/>
					Added: ' . $Now->format('F') . ' ' . $Now->format('d') . ', ' . $Now->format('Y') . '<br/>
				</p>
]]>
			</description>
			<guid isPermaLink="true">http://' . $_SERVER["HTTP_HOST"] . '/?v=' . $video->id . '</guid>
			<pubDate>' . $Now->format('r') . '</pubDate>

			<media:player url="http://' . $_SERVER["HTTP_HOST"] . '/?v=' . $video->id . '" />
			<media:thumbnail url="http://' . $_SERVER["HTTP_HOST"] . '/get_still.php?video_id=' . $video->id . '" width="120" height="90" />
			<media:title>' . htmlentities($video->title) . '</media:title>
			<media:category label="Tags">' . htmlentities($video->tags) . '</media:category>
			<media:credit>' . $video->uploaded_by . '</media:credit>
			<enclosure url="http://' . $_SERVER["HTTP_HOST"] . '/player.swf?video_id=' . $video->id . '&amp;l=' . $video->runtime . '" length="' . $video->runtime . '" type="application/x-shockwave-flash" />
		</item>
';
	}

}

echo '</channel>
</rss>';

?>