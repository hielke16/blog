<?php

namespace Module\Employee\Admin;

use Webwijs\AbstractBootstrap;
use Webwijs\Admin\Metabox\PageLayout;
use Webwijs\Admin\Metabox\Excerpt;
use Webwijs\Admin\Metabox\Multibox;
use Theme\Admin\Controller\SettingsController;
use Theme\Admin\Controller\SidebarsController;

use Module\Employee\Admin\Metabox\Data;

/**
 * The admin bootstrap of the Employee module
 *
 * @author Leo Flapper
 * @version 1.0.0
 */
class Bootstrap extends AbstractBootstrap
{

    /**
     * Initializes the metaboxes for the Employee module
     * @return void
     */
    protected function _initMetaboxes()
    {
    	$dataMetabox = new Data();
        $dataMetabox->register();
    }

}
