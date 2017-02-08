<?php
/**
 * Template Name: Homepage
 * Layouts: home
 */
?>
<?php the_post(); ?>

<?php echo $this->listPosts( array('post_type' =>'blog' )); ?>

<?php echo $this->sidebarArea('home-blocks'); ?>
