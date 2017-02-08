<?php
/**
 * Template Name: Loginpage
 * Layouts: login
 */
?>
<?php the_post(); ?>

<!--
	kijken of er al is ingelogd
-->

<?php if(!isset($_SESSION['id'])): ?>
	<div id="login">
	<form method="POST">
		<div>login naam: <input type="text" name="name" /></div>
		<div>password: <input type="password" name="password" /></div>
		<div><input type="submit" value="login" /></div>
	</form>
	</div>

<?php else: ?>


<?php endif ?>