<?php 
require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/header.php"); 
require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/logincheck.php"); 

if(!isset($_GET["user"])) { header("Location: /"); }
if($_GET["user"] == $_SESSION["username"]) { header("Location: /"); }

//so are we friends already?
$stmt = $conn->prepare("SELECT * FROM friends WHERE sent_by=:t0 and sent_to=:t1");
$stmt->bindParam(":t0", $_SESSION["username"]);
$stmt->bindParam(":t1", $_GET["user"]);
$stmt->execute();
if($stmt->rowCount() == 1) { die(header("Location: /profile.php?user=" . $_GET["user"])); }

//okay then, lets be friends!
if(isset($_POST["submit"])) {
	$stmt = $conn->prepare("INSERT INTO friends (sent_by, sent_to) VALUES (:t0, :t1)");
	$stmt->bindParam(":t0", $_SESSION["username"]);
	$stmt->bindParam(":t1", $_GET["user"]);
	$stmt->execute();
	die(header("Location: /profile.php?user=" . $_GET["user"]));
}

?>
<div class="tableSubTitle">Friend Invitation</div>
<p>Send an invitation if you know this user wish to share private videos with each other</p>


<table width="100%" cellpadding="5" cellspacing="0" border="0">
<form method="post">
	<tr>
		<td width="200" align="right"><span class="label">User Name:</span></td>
		<td><a href="/profile.php?user=<?php echo $_GET["user"]; ?>"><?php echo $_GET["user"]; ?></a></td>
	</tr>

	<tr>
		<td>&nbsp;</td>
		<td><span class="formFieldInfo">They will be able to see the private videos you share <!-- with these groups --> in addition to your public videos.</span></td>
	</tr>
	
	<tr>
		<td>&nbsp;</td>
		<td><input type="submit" name="submit" value="Send Invite"></td>
	</tr>	
</form>
</table>

<?php require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/footer.php"); ?>