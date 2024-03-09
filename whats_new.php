<?php 
require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/header.php"); 

$stmt = $conn->prepare("SELECT * from blog ORDER BY id DESC");
$stmt->execute();
foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $post) {
	$Now = new DateTime($post->posted_on);
	echo '<div class="tableSubTitle">' . $Now->format('F') . ' ' . $Now->format('d') . ', ' . $Now->format('Y') . '</div>
	
	' . $post->content . '
<br/>
<br/>
<br/>
';
}

require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/footer.php"); 
?>