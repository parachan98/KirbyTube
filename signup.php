<?php 
require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/header.php"); 
if(isset($_SESSION["username"])) { header("Location: /"); }

if(isset($_POST["submit"])) {
	$email = $_POST["field_signup_email"];
	$username = preg_replace("/[^a-zA-Z0-9]/", "", $_POST["field_signup_username"]);
	$password = $_POST["field_signup_password_1"];
	$c_password = $_POST["field_signup_password_2"];
	// now, check the length.
	if(strlen($email) > 60) { die(header("Location: /signup.php?err=1")); }
	if(strlen($username) > 20) { die(header("Location: /signup.php?err=2")); }
	if(strlen($password) > 40) { die(header("Location: /signup.php?err=3")); }
	if(strlen($username) < 2) { die(header("Location: /signup.php?err=4")); }
	// ----------------------
	if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		if(strlen($username) < 2) { die(header("Location: /signup.php?err=10")); }
	}
	// ----------------------
	if($password !== $c_password) { die(header("Location: /signup.php?err=5")); }
	if(empty($email)) { die(header("Location: /signup.php?err=6")); }
	if(empty($password)) { die(header("Location: /signup.php?err=7")); }
	// ----------------------
	$password = password_hash($password, PASSWORD_BCRYPT);
	// ----------------------
	// And now, the actual signup
	
	$result = $conn->query("SELECT email FROM users WHERE email='" . $_POST["field_signup_email"] . "'");
	if($result->rowCount() == 0) {
		$login_ok = true;
	} else die(header("Location: /signup.php?err=8"));
	
	$result = $conn->query("SELECT username FROM users WHERE username='" . $_POST["field_signup_username"] . "'");
	if($result->rowCount() == 0) {
		$login_ok = true;
	} else die(header("Location: /signup.php?err=9"));
	
	if($login_ok) {
		$stmt = $conn->prepare("INSERT INTO users (email, username, password) VALUES (:t0, :t1, :t2)");
		$stmt->bindParam(':t0', $email);
		$stmt->bindParam(':t1', $username);
		$stmt->bindParam(':t2', $password);
		$stmt->execute();
		
		//set sessid
		$_SESSION["username"] = $username;
		header("Location: /signup_invite.php");
	}
}

if(isset($_GET["err"])) {
	if($_GET["err"] == 1)  { error("Your email cannot be longer than 60 characters."); }
	if($_GET["err"] == 2)  { error("Your username cannot be longer than 20 characters"); }
	if($_GET["err"] == 3)  { error("Your password cannot be longer than 40 characters."); }
	if($_GET["err"] == 4)  { error("Your username cannot be shorter than 2 characters."); }
	if($_GET["err"] == 5)  { error("Passwords do not match!"); }
	if($_GET["err"] == 6)  { error("Your email cannot be empty."); }
	if($_GET["err"] == 7)  { error("Your password cannot be empty."); }
	if($_GET["err"] == 8)  { error("The email you entered is already in use."); }
	if($_GET["err"] == 9)  { error("The username you entered is already in use."); }
	if($_GET["err"] == 10) { error("The email you entered is invalid."); }
}

?>

<div class="tableSubTitle">Sign Up</div>

Please enter your account information below. All fields are required.<br><br>

<table width="100%" cellpadding="5" cellspacing="0" border="0">
<form method="post" action="signup.php">

<input type="hidden" name="field_command" value="signup_submit">

	<tr>
		<td width="200" align="right"><span class="label">Email Address:</span></td>
		<td><input type="text" size="30" maxlength="60" name="field_signup_email" value=""></td>
	</tr>
	<tr>
		<td align="right"><span class="label">User Name:</span></td>
		<td><input type="text" size="20" maxlength="20" name="field_signup_username" value=""></td>
	</tr>
	<tr>
		<td align="right"><span class="label">Password:</span></td>
		<td><input type="password" size="20" maxlength="20" name="field_signup_password_1" value=""></td>
	</tr>
	<tr>
		<td align="right"><span class="label">Retype Password:</span></td>
		<td><input type="password" size="20" maxlength="20" name="field_signup_password_2" value=""></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><br>- I certify I am over 13 years old.
		<br>- I agree to the <a href="terms.php" target="_blank">terms of use</a> and <a href="privacy.php" target="_blank">privacy policy</a>.</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="submit" value="Sign Up" name="submit"></td>
	</tr>
	</form>
	<tr>
		<td>&nbsp;</td>
		<td><br>Or, <a href="index.php">return to the homepage</a>.</td>
	</tr>
</table>

<?php require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/footer.php"); ?>