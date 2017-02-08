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

<?php echo $this->partial("partials/login/login.phtml");