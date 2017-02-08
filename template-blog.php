<?php
/**
 * Template Name: Blogpage
 * Layouts: blog
 */
?>
<?php the_post(); ?>

<?php if((isset($_GET['id']))&&($_GET['id'] > 0)): $id = (int)$_GET['id']; ?>
<?php 	echo $this->listPosts(array('post_type' => 'blog','include' => $id,'posts_per_page' => 1,'partial' => 'blog')); ?>
<?php endif ?>
<?php echo $this->sidebarArea('home-blocks'); ?>
