<?php 
require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/header.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/logincheck.php");
if(!$_SERVER["REQUEST_METHOD"] == "POST") { die(""); }

// set main vars
$title = $_POST["video_title"];
$description = $_POST["video_description"];
$tags = $_POST["video_tags"];

//check duped tags
$taglist = explode(" ", $_POST["video_tags"]);
if(array_has_dupes($taglist)) { die(header("Location: /my_videos_upload.php?err=1")); } 

//input validation
if(mb_strlen($title, 'utf8') > 60) { die(header("Location: /my_videos_upload.php?err=2")); }
if(mb_strlen($title, 'utf8') < 1) { die(header("Location: /my_videos_upload.php?err=3")); }
if(mb_strlen($description, 'utf8') > 500) { die(header("Location: /my_videos_upload.php?err=4")); }
if(mb_strlen($description, 'utf8') < 1) { die(header("Location: /my_videos_upload.php?err=5")); }
if(substr_count($tags, ' ') < 2) { die(header("Location: /my_videos_upload.php?err=6")); }
if(substr_count($tags, ' ') > 20) { die(header("Location: /my_videos_upload.php?err=7")); }

if(isset($_POST["upload"])) {
	$_POST["video_title"] = html_entity_decode($_POST["video_title"]);
	$_POST["video_description"] = html_entity_decode($_POST["video_description"]);
	$_POST["video_tags"] = html_entity_decode($_POST["video_tags"]);
	
	if(!isset($_FILES["fileToUpload"])) { die("no file"); }
	if($_POST["private"] == 0) { $isPrivate = 0; } else { $isPrivate = 1; }
	
	// set uploader vars
	$video_id			= randstr(11);
	$upload_extension   =  strtolower(pathinfo($_FILES["fileToUpload"]["name"], PATHINFO_EXTENSION));
	$upload_tgt_preload = "content/video/preload/" . $video_id . "." . $upload_extension;
	$input 				= $upload_tgt_preload;
	$name      			= $_FILES['fileToUpload']['name']; 
	$temp_name  		= $_FILES['fileToUpload']['tmp_name'];
	
	if(move_uploaded_file($temp_name, $upload_tgt_preload)) {
		if(!in_array(strtolower($upload_extension), $website["allowed_filetypes"])) {
			unlink($upload_tgt_preload);
			die("sorry, that filetype is not allowed.");
		}
	}
	
	//create ffmpeg-php
	$ffprobe = FFMpeg\FFProbe::create();
	if(!($ffprobe->isValid($upload_tgt_preload))) { die("Invalid file"); }
	
	$ffmpeg = FFMpeg\FFMpeg::create();
	$video = $ffmpeg->open($input);
	
	//thumbnail generator
	$framecount = round((int)exec($website["ffprobe"] . " -v error -select_streams v:0 -count_packets -show_entries stream=nb_read_packets -of csv=p=0 " . $upload_tgt_preload));
	$thumb1 = $framecount / 3;
	$thumb2 = $framecount / 2;
	$thumb3 = $framecount / 1.2;
	exec($website["ffmpeg"] . " -i $input -vf \"select=gte(n\,$thumb1)\" -vframes 1 -s 120x90 content/thumb/" . $video_id . "_1.jpg");
	exec($website["ffmpeg"] . " -i $input -vf \"select=gte(n\,$thumb2)\" -vframes 1 -s 120x90 content/thumb/" . $video_id . "_2.jpg");
	exec($website["ffmpeg"] . " -i $input -vf \"select=gte(n\,$thumb3)\" -vframes 1 -s 120x90 content/thumb/" . $video_id . "_3.jpg");
	
	
	$framerate = new \FFMpeg\Coordinate\FrameRate(30);
	$v_fps = 15;
	
	//MP4 converter
	$format = new FFMpeg\Format\Video\X264();
	$format
		->setAudioCodec("libmp3lame")
		->setKiloBitrate(0)
		->setAudioChannels(1)
		->setAudioKiloBitrate(80);
	$video
		->filters()
		->framerate($framerate, $v_fps)
		->resize(new FFMpeg\Coordinate\Dimension(320, 240))
		->resample(22050);
	$video->save($format, 'content/video/' . $video_id . ".mp4");
	
	//FLV converter
	$format = new CustomFLVFormat();
	$format
		->setAudioCodec("mp3")
		->setKiloBitrate(200)
		->setAudioChannels(1)
		->setAudioKiloBitrate(80);
	$video
		->filters()
		->framerate($framerate, $v_fps)
		->resize(new FFMpeg\Coordinate\Dimension(320, 240))
		->resample(22050);
	$video->save($format, 'content/video/' . $video_id . ".flv");
	
	//3GP converter
	$framerate = new \FFMpeg\Coordinate\FrameRate(10);
	$format = new Custom3GPFormat();
	$format
		->setAudioCodec("aac")
		->setKiloBitrate(0)
		->setAudioChannels(1);
	$video
		->filters()
		->resize(new FFMpeg\Coordinate\Dimension(176, 144))
		->framerate($framerate, $v_fps)
		->resample(22050);
	$video->save($format, 'content/video/' . $video_id . ".3gp");
	
	// grab length
	$length = round((int)exec($website["ffprobe"] . " -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 ".$upload_tgt_preload));
	unlink($upload_tgt_preload);
	
	// add all the stuff to db
	$stmt = $conn->prepare("INSERT INTO videos (id, isPrivate, filename, title, description, tags, runtime, uploaded_by) VALUES (:t0, :t1, :t2, :t3, :t4, :t5, :t6, :t7)");
	$stmt->bindParam(':t0', $video_id);
	$stmt->bindParam(':t1', $isPrivate);
	$stmt->bindParam(':t2', $name);
	$stmt->bindParam(':t3', $_POST["video_title"]);
	$stmt->bindParam(':t4', $_POST["video_description"]);
	$stmt->bindParam(':t5', $_POST["video_tags"]);
	$stmt->bindParam(':t6', $length);
	$stmt->bindParam(':t7', $_SESSION["username"]);
	$stmt->execute();
	header("Location: /my_videos_upload_complete.php?video_id=" . $video_id);
}


?>

<table width="100%" cellpadding="5" cellspacing="0" border="0">
<form method="post" enctype="multipart/form-data">
<div style="display: none">
<input type="text" name="video_title" value="<?php echo htmlentities($_POST["video_title"]); ?>">
<input type="text" name="video_description" value="<?php echo htmlentities($_POST["video_description"]); ?>">
<input type="text" name="video_tags" value="<?php echo htmlentities($_POST["video_tags"]); ?>">
</div>
	<tr>
		<td width="200" align="right" valign="top"><span class="label">File:</span></td>
		<td>
		<div width="595" height="20" cellpadding="0" border="0" bgcolor="#E5ECF9" class="formHighlight">
			<input type="file" style="margin-bottom: 3px" id="fileToUpload" name="fileToUpload"><br>
			<span class="formHighlightText"><b>Max file size: 100 MB. No copyrighted or obscene material.</b></span><br>
			<span class="formHighlightText">After uploading, you can edit or remove this video at anytime under the "My Videos" link on top of the page.</span>
		</div>

	<tr>
		<td width="200" align="right"><span class="label">Broadcast:</span></td>
		<td>
			<select name="private" tabindex="5">
				<option value="0">Public</option>
				<option value="1">Private</option>
		</td>
</table>
<br>
<div style="margin-left: 220px">
	<b>PLEASE BE PATIENT, THIS MAY TAKE SEVERAL MINUTES. <br> ONCE COMPLETED, YOU WILL SEE A CONFIRMATION MESSAGE.</b>
	<br><br>
	<input type="submit" value="Upload Video" name="upload" id="upload">
</div>
<br><br>
</form>
<?php require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/footer.php"); ?>