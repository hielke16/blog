<?php
/*
 * Layouts: two-columns-right, two-columns-left, three-columns, one-column
 */
?>
<?php the_post(); ?>

<?php echo $this->breadcrumbs() ?>

<article class="singular page-<?php echo get_post_type() ?>" itemscope itemtype="http://schema.org/Article">
    <div class="<?php echo $this->layoutContainerClass() ?> col-container singular-container">
        <?php echo $this->sidebarArea('col-left') ?>
        <div class="col-content column">
            <?php echo $this->partial('partials/page/singular.phtml') ?>
        </div>
        <?php echo $this->sidebarArea('col-right') ?>
    </div>
</article>
