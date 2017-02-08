<!DOCTYPE html>
<!--[if lt IE 7 ]> <html class="ie ie6 no-js" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 7 ]>    <html class="ie ie7 no-js" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8 ]>    <html class="ie ie8 no-js" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 9 ]>    <html class="ie ie9 no-js" <?php language_attributes(); ?>> <![endif]-->
<!--[if gt IE 9]><!--><html class="no-js" <?php language_attributes(); ?>><!--<![endif]-->
<head>
    <title><?php wp_title('') ?></title>
    <meta charset="<?php bloginfo('charset' ); ?>">
    <meta name="viewport" content="width=device-width">
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0">
    <link rel="shortcut icon" href="<?php bloginfo('stylesheet_directory'); ?>/assets/img/favicon.ico" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    
    <?php //wp_enqueue_style('foundation', get_bloginfo('stylesheet_directory') . '/assets/lib/foundation/css/foundation.css', 'main') ?>
    <?php wp_enqueue_style('reset', get_bloginfo('stylesheet_directory') . '/assets/css/reset.css') ?>
    <?php wp_enqueue_style('text', get_bloginfo('stylesheet_directory') . '/assets/css/text.css') ?>
    <?php wp_enqueue_style('forms', get_bloginfo('stylesheet_directory') . '/assets/css/forms.css') ?>
    <?php wp_enqueue_style('main', get_bloginfo('stylesheet_directory') . '/assets/css/main.css') ?>
    <?php wp_enqueue_style('print', get_bloginfo('stylesheet_directory') . '/assets/css/print.css', array(), false, 'print') ?>

    <?php wp_enqueue_style('responsive', get_bloginfo('stylesheet_directory') . '/assets/css/responsive.css', 'main') ?>

    <?php wp_enqueue_script('jquery') ?>
    <?php //wp_enqueue_script('foundation', get_bloginfo('stylesheet_directory') . '/assets/lib/foundation/js/foundation.min.js', 'jquery','',true) ?>
    <?php wp_enqueue_script('modernizr', get_bloginfo('stylesheet_directory') . '/assets/lib/js/modernizr.js') ?>
    <?php wp_head() ?>
</head>

<body <?php body_class() ?> itemscope itemtype="http://schema.org/WebPage">
    
    <?php echo $this->partial('partials/layout/header.phtml') ?>

    <section class="main-container" role="main">
        <?php echo $this->content ?>
    </section>
    
    <?php echo $this->partial('partials/layout/footer.phtml'); ?>

    <?php wp_footer() ?>
</body>
</html>