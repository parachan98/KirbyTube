<?php 
require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/header.php"); 
if(isset($_SESSION["username"])) { header("Location: /"); }

if(isset($_POST["submit"])) {
	if(empty($_POST["field_login_username"])) { die("you did not input an username"); }
	if(empty($_POST["field_login_password"])) { die("you did not input a password"); }
	
	$username = $_POST["field_login_username"];
	
	$sql = "SELECT * FROM users WHERE username='$username'";
	$result = $conn->query($sql);
	if ($result->rowCount() > 0) {
		while($row = $result->fetch(PDO::FETCH_ASSOC)) { 
			$id = $row["id"]; 
			$password_db = $row["password"]; 
			$isBanned = $row["isBanned"];
		}
		
		$password_ok = false;

		if(password_verify($_POST["field_login_password"], $password_db)) {
			$password_ok = true;
		}
		
		if($password_ok) {
			if($isBanned == 1) {
				die(header("Location: /login.php?err=1"));
			}
			$_SESSION["username"] = $username;
			// Update last login date
			$lastLogin = date('Y-m-d H:i:s');
			$stmt = $conn->prepare("UPDATE users SET last_login=:last_login WHERE username=:t0");
			$stmt->bindParam(":last_login", $lastLogin);
			$stmt->bindParam(":t0", $username);
			$stmt->execute();
			header("Location: /");
		} else {
			die(header("Location: /login.php?err=2"));
		}
	} else die(header("Location: /login.php?err=3"));
}

if(isset($_GET["err"])) {
	if($_GET["err"] == 1)  { error("This account has been suspended."); }
	if($_GET["err"] == 2)  { error("Login crdentials do not match."); }
	if($_GET["err"] == 3)  { error("The user you requested does not exist."); }
}

?>

<div class="tableSubTitle">Log In</div>

<table width="795" align="center" cellpadding="0" cellspacing="0" border="0">
	<tr valign="top">
		<td style="padding-right: 15px;" width="515">
		
		
		<span class="highlight">What is <?php echo $website["instance_name"]; ?>?</span>

		<?php echo $website["instance_name"]; ?> is a way to get your videos to the people who matter to you. With <?php echo $website["instance_name"]; ?> you can:
		
		<ul>
		<li> Show off your favorite videos to the world
		<li> Blog the videos you take with your digital camera or cell phone
		<li> Securely and privately show videos to your friends and family around the world
		<li> ... and much, much more!
		</ul>

		<br><span class="highlight"><a href="signup.php">Sign up now</a> and open a free account.</span>
		<br><br><br>
		
		To learn more about our service, please see our <a href="help.php">Help</a> section.<br><br><br>
		</td>
		<td width="280">
		
		<table width="280" align="center" cellpadding="0" cellspacing="0" border="0" bgcolor="#E5ECF9">
			<tr>
				<td><img src="img/box_login_tl.gif" width="5" height="5"></td>
				<td width="100%"><img src="img/pixel.gif" width="1" height="5"></td>
				<td><img src="img/box_login_tr.gif" width="5" height="5"></td>
			</tr>
			<tr>
				<td><img src="img/pixel.gif" width="5" height="1"></td>
				<td align="center">
		<table width="100%" cellpadding="5" cellspacing="0" border="0">
			<form method="post" action="login.php">

			<input type="hidden" name="field_command" value="login_submit">
				<tr>
					<td align="center" colspan="2"><div style="font-size: 14px; font-weight: bold; color:#003366; margin-bottom: 5px;"><?php echo $website["instance_name"]; ?> Log In</div></td>
				</tr>
				<tr>
					<td align="right"><span class="label">User Name:</span></td>
					<td><input type="text" size="20" name="field_login_username" value=""></td>
				</tr>
				<tr>
					<td align="right"><span class="label">Password:</span></td>
					<td><input type="password" size="20" name="field_login_password"></td>
				</tr>
				<tr>
					<td align="right"><span class="label">&nbsp;</span></td>
					<td><input type="submit" value="Log In" name="submit"></td>
				</tr>
				<tr>
					<td align="center" colspan="2"><a href="forgot.php">Forgot your password?</a><br><br></td>
				</tr>
			</form>
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
			
		</td>
	</tr>
</table>


<?php require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/footer.php"); ?>