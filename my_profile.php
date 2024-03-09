<?php 
require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/header.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/logincheck.php");

$stmt = $conn->query("SELECT * FROM users WHERE username='" . $_SESSION["username"] . "'");
foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $user);

$fields = array("personal_name", "personal_gender", "personal_relationship", "personal_about", "personal_website", "location_hometown", "location_city", "location_country", "location_occupations", "location_companies", "location_schools", "random_interests", "random_movies", "random_music" , "random_books");

if(isset($_POST["update"])) {
	foreach($fields as $field) {
		if(empty($_POST[$field])) {
			$_POST[$field] = NULL;
		}
	}
	
	//check if site url is valid
	if(isset($_POST["personal_website"])) {
		if(filter_var($_POST["personal_website"], FILTER_VALIDATE_URL) == false) {
			die(header("Location: /my_profile.php?err=1"));
		}
	}

	$stmt = $conn->prepare("UPDATE users SET personal_name=:t0, personal_gender=:t1, personal_relationship=:t2, personal_about=:t3, personal_website=:t4, location_hometown=:t5, location_city=:t6, location_country=:t7, location_occupations=:t8, location_companies=:t9, location_schools=:t10, random_interests=:t11, random_movies=:t12, random_music=:t13, random_books=:t14 WHERE username=:username");
	$stmt->bindParam(":t0", $_POST["personal_name"]);
	$stmt->bindParam(":t1", $_POST["personal_gender"]);
	$stmt->bindParam(":t2", $_POST["personal_relationship"]);
	$stmt->bindParam(":t3", $_POST["personal_about"]);
	$stmt->bindParam(":t4", $_POST["personal_website"]);
	$stmt->bindParam(":t5", $_POST["location_hometown"]);
	$stmt->bindParam(":t6", $_POST["location_city"]);
	$stmt->bindParam(":t7", $_POST["location_country"]);
	$stmt->bindParam(":t8", $_POST["location_occupations"]);
	$stmt->bindParam(":t9", $_POST["location_companies"]);
	$stmt->bindParam(":t10", $_POST["location_schools"]);
	$stmt->bindParam(":t11", $_POST["random_interests"]);
	$stmt->bindParam(":t12", $_POST["random_movies"]);
	$stmt->bindParam(":t13", $_POST["random_music"]);
	$stmt->bindParam(":t14", $_POST["random_books"]);
	$stmt->bindParam(":username", $_SESSION["username"]);
	$stmt->execute();
	
	header("Location: /my_profile.php?success");
}

if(isset($_GET["err"])) { if($_GET["err"] == 1) { error("The URL you entered is invalid."); } }
if(isset($_GET["success"])) { error("Your profile has been updated!"); }

if($user->personal_gender == NULL) { $gender = 0; }
if($user->personal_relationship == NULL) { $relationship = 0; }

?>

<div class="tableSubTitle">Personal Information</div>

<form method="post">

<table width="100%" cellpadding="5" cellspacing="0" border="0">

	<tr>
		<td width="200" align="right"><span class="label">Name:</span></td>
		<td><input type="text" size="30" maxlength="60" name="personal_name" value="<?php echo htmlentities($user->personal_name); ?>"></td>
	</tr>

	<tr>
		<td width="200" align="right"><span class="label">Gender:</span></td>
		<td>
			<select name="personal_gender" tabindex="5">
				<option value="0" <?php if($user->personal_gender == 0 || null) { echo 'selected'; } ?>>Prefer not to say</option>
				<option value="1" <?php if($user->personal_gender == 1) { echo 'selected'; } ?>>Male</option>
				<option value="2" <?php if($user->personal_gender == 2) { echo 'selected'; } ?>>Female</option>
				<option value="3" <?php if($user->personal_gender == 3) { echo 'selected'; } ?>>Other</option>
			</select>
		</td>
	</tr>

	<tr>
		<td width="200" align="right"><span class="label">Relationship Status:</span></td>
		<td>
			<select name="personal_relationship" tabindex="5">
				<option value="0" <?php if($user->personal_gender == 0 || null) { echo 'selected'; } ?>>Prefer not to say</option>
				<option value="1" <?php if($user->personal_gender == 1) { echo 'selected'; } ?>>Single</option>
				<option value="2" <?php if($user->personal_gender == 2) { echo 'selected'; } ?>>Taken</option>
				<option value="3" <?php if($user->personal_gender == 3) { echo 'selected'; } ?>>Open</option>
			</select>
		</td>
	</tr>

	<tr>
		<td align="right" valign="top"><span class="label">About Me:<br>(describe yourself)</span></td>
		<td><textarea name="personal_about" maxlength="500" style="width:295px;resize:none" rows="3"><?php echo $user->personal_about; ?></textarea></td>
	</tr>
	
	<tr>
		<td width="200" align="right"><span class="label">Personal Website:</span></td>
		<td><input type="text" size="30" maxlength="60" name="personal_website" value="<?php echo htmlentities($user->personal_website); ?>"></td>
	</tr>
	
</table>
<br>

<div class="tableSubTitle">Location Information</div>

<table width="100%" cellpadding="5" cellspacing="0" border="0">

	<tr>
		<td width="200" align="right"><span class="label">Hometown:</span></td>
		<td><input type="text" size="30" maxlength="60" name="location_hometown" value="<?php echo htmlentities($user->location_hometown); ?>"></td>
	</tr>
	
	<tr>
		<td width="200" align="right"><span class="label">Current City:</span></td>
		<td><input type="text" size="30" maxlength="60" name="location_city" value="<?php echo htmlentities($user->location_city); ?>"></td>
	</tr>

	<tr>
		<td width="200" align="right"><span class="label">Current Country:</span></td>
		<td><input type="text" size="30" maxlength="60" name="location_country" value="<?php echo htmlentities($user->location_country); ?>"></td>
	</tr>

</table>
<br>

<div class="tableSubTitle">Random Information</div>

<p>(separate items by commas)</p>

<table width="100%" cellpadding="5" cellspacing="0" border="0">

	<tr>
		<td width="200" align="right"><span class="label">Occupations:</span></td>
		<td><input type="text" size="30" maxlength="60" name="location_occupations" value="<?php echo htmlentities($user->location_occupations); ?>"></td>
	</tr>
	
	<tr>
		<td width="200" align="right"><span class="label">Companies:</span></td>
		<td><input type="text" size="30" maxlength="60" name="location_companies" value="<?php echo htmlentities($user->location_companies); ?>"></td>
	</tr>

	<tr>
		<td width="200" align="right"><span class="label">Schools:</span></td>
		<td><input type="text" size="30" maxlength="60" name="location_schools" value="<?php echo htmlentities($user->location_schools); ?>"></td>
	</tr>

	<tr>
		<td width="200" align="right"><span class="label">Interests &amp; Hobbies:</span></td>
		<td><input type="text" size="30" maxlength="60" name="random_interests" value="<?php echo htmlentities($user->random_interests); ?>"></td>
	</tr>

	<tr>
		<td width="200" align="right"><span class="label">Favorite Movies &amp; Shows:</span></td>
		<td><input type="text" size="30" maxlength="60" name="random_movies" value="<?php echo htmlentities($user->random_movies); ?>"></td>
	</tr>

	<tr>
		<td width="200" align="right"><span class="label">Favorite Music:</span></td>
		<td><input type="text" size="30" maxlength="60" name="random_music" value="<?php echo htmlentities($user->random_music); ?>"></td>
	</tr>

	<tr>
		<td width="200" align="right"><span class="label">Favorite Books:</span></td>
		<td><input type="text" size="30" maxlength="60" name="random_books" value="<?php echo htmlentities($user->random_books); ?>"></td>
	</tr>

	<tr>
		<td width="200" align="right">&nbsp;</td>
		<td><input type="submit" value="Update Profile" name="update"></td>
	</tr>

</table>

</form>

<?php require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/footer.php"); ?>