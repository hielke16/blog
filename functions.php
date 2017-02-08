<?php
ob_start('ob_gzhandler');
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . '/lib/php');

include_once dirname(__FILE__) . '/app/Bootstrap.php';
$bootstrap = new Theme\Bootstrap;
$bootstrap->init();


include_once dirname(__FILE__) . '/app/Admin/Bootstrap.php';
$adminBootstrap = new Theme\Admin\Bootstrap;
add_action('admin_menu', array(&$adminBootstrap, 'init'), 0);
