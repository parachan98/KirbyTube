<?php require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/config.php"); ?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo $website["instance_name"] . " - " . $website["instance_slogan"]; ?></title>
<link rel="icon" href="<?php echo $website["favicon"]; ?>" type="image/x-icon">
<link rel="shortcut icon" href="<?php echo $website["favicon"]; ?>" type="image/x-icon">
<link href="styles.css" rel="stylesheet" type="text/css">
<link rel="alternate" type="application/rss+xml" title="<?php echo $website["instance_name"]; ?> "" Recently Added Videos [RSS]" href="/rss/global/recently_added.rss">
</head>

<body>

<div style="margin: auto; text-align:center; padding: 20px;">
			<img src="<?php echo $website["instance_banner"]; ?>" width="180" height="71" hspace="12" vspace="12" alt="<?php echo $website["instance_name"]; ?>">
			<div style="font-size:16px;">
			<br>You have reached <?php echo $website["instance_name"]; ?>, the premier digital video repository on the Internet. We are currently rolling out new changes to the site. After these messages, we'll be right back...
			</div>
</div>

</body>
</html>
