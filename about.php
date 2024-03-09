<?php require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/header.php"); ?>
<div class="tableSubTitle">About Us</div>

<span class="highlight">What is <?php echo $website["instance_name"]; ?>?</span>

<br><br>

<?php echo $website["instance_name"]; ?> is a way to get your videos to the people who matter to you. With <?php echo $website["instance_name"]; ?> you can:

<ul>
<li> Show off your favorite videos to the world
<li> Take videos of your dogs, cats, and other pets
<li> Blog the videos you take with your digital camera or cell phone
<li> Securely and privately show videos to your friends and family around the world
<li> ... and much, much more!
</ul>

<?php if(!isset($_SESSION["username"])) { echo '<br><span class="highlight"><a href="signup.php">Sign up now</a> and open a free account.</span>'; } ?>

<br><br><br>

To learn more about our service, please see our <a href="help.php">Help</a> section.<br><br>

Please feel free to <a href="contact.php">contact us</a>.
<?php require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/footer.php"); ?>