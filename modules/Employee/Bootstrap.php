<?php
namespace Module\Employee;

use Webwijs\AbstractBootstrap;
use Webwijs\Module\ModuleInfo;
use Webwijs\Module\ModuleAwareInterface;
use Webwijs\Module\AutoloadableInterface;

use Module\Employee\Cpt\Employee as EmployeeCpt;
use Module\Employee\Admin\Bootstrap as AdminBootstrap;

use Webwijs\CSS\MainCompiler as CSSCompiler;

/**
 * The module Employees bootstrap
 *
 * @author Leo Flapper
 * @version 1.0.0
 */
class Bootstrap extends AbstractBootstrap implements ModuleAwareInterface, AutoloadableInterface
{

    /**
     * The module class
     * @var Module $module the module class
     */
    protected $module;

    /**
     * Adds the Employee directory to the SPLAutoloader object to retrieve classes within this namespace
     * @return void
     */
    public function getAutoloaderConfig()
    {	
        return array(
            'Webwijs\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    'Module\Employee' => __DIR__,
                ),
            ),
        );
    }

    /**
     * Sets the module class
     * @param Module $module the module class
     */
    public function setModule(ModuleInfo $module)
    {
        $this->module = $module;
    }

    /**
     * Initializes the Employee custom post type
     * @return void
     */
    public function _initCpt()
    {
        EmployeeCpt::register(); 
    }

    /**
     * Initializes the admin bootstrap for the backend.
     * @return void
     */
    public function _initAdmin()
    {
        $adminBootstrap = new AdminBootstrap();
        add_action('admin_menu', array(&$adminBootstrap, 'init'));    
    }

}
