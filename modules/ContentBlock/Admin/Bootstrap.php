<?php

namespace Module\ContentBlock\Admin;

use Webwijs\AbstractBootstrap;
use Theme\Admin\Controller\SettingsController;
use Module\GoogleMap\Bootstrap as ModuleBootstrap;


/**
 * The admin bootstrap of the GoogleMaps module
 *
 * @author Joren de Graaf <jorendegraaf@gmail.com>
 * @version 1.0.0
 */
class Bootstrap extends AbstractBootstrap
{
    protected function _initSettings(){
      $builder = SettingsController::getBuilder();
      $builder->group('advanced')->add('theme_advanced_enable_slick', 'checkbox', array('label' => 'Schakel Slick slider in'));
    }
}
