<?php

namespace Webwijs\Module\Action;

use Webwijs\Loader\AutoloaderFactory;
use Webwijs\File\FileFinder;
use Webwijs\Loader\ClassLoader;
use Webwijs\View\Directories as ViewDirectories;
use Webwijs\Template\PageTemplates;
use Webwijs\Module\ModuleAwareInterface;
use Webwijs\Module\AutoloadableInterface;
use Webwijs\Module\ModuleInfo;

/**
 * Module Loaded
 *
 * Contains the methods which are called after a module is loaded
 *
 * @author Leo Flapper <leo@webwijs.nu>
 * @author Chris Harris <chris@webwijs.nu>
 * @version 1.1.0
 * @since 1.1.0
 */
class ModuleLoaded
{	

    /**
     * Adds the module helpers to the helper resources and the views to the view directories.
     * The view object can use these to display the right helper and view.
     * @param ModuleInfo $moduleInfo the module info
     */
	public static function addResources(ModuleInfo $moduleInfo)
	{
		ClassLoader::addStaticResources(array(
            'viewhelper' => $moduleInfo->getNamespace().'\Helper'
        ));

		ViewDirectories::addStaticDirectory($moduleInfo->getPath());
	}

    /**
     * Sets the module info class in the bootstrap if the bootstrap contains
     * the Module Aware Interface.
     * @param ModuleInfo $moduleInfo the module info
     * @param Bootstrap $bootstrap the module it's bootstrap class
     * @return void
     */
    public static function setModule(ModuleInfo $moduleInfo, $bootstrap)
    {
        if($bootstrap instanceof ModuleAwareInterface) { 
            $bootstrap->setModule($moduleInfo);
        }   
    }

    /**
     * Initializes the autoloader of the module bootstrap if the
     * Bootstrap class contains the Autoloadable Interface.
     * @param ModuleInfo $moduleInfo the module info
     * @param Bootstrap $bootstrap the module it's bootstrap class
     * @return void
     */
    public static function autoloadModule(ModuleInfo $moduleInfo, $bootstrap)
    {
        if($bootstrap instanceof AutoloadableInterface) {
            AutoloaderFactory::factory($bootstrap->getAutoloaderConfig());
        }  
    }

    /**
     * Initializes the module bootstrap
     * 
     * @param ModuleInfo $moduleInfo the module info
     * @param Bootstrap $bootstrap the module it's bootstrap class
     * @return void
     */
    public static function initModule(ModuleInfo $moduleInfo, $bootstrap)
    {
        $bootstrap->init();
    }

    /**
     * Loads the page templates of the module
     * 
     * @param ModuleInfo $moduleInfo the module info
     * @return void
     */
    public static function loadModulePageTemplates(ModuleInfo $moduleInfo)
    {
        $fileFinder = new FileFinder();
        $fileFinder->addPath($moduleInfo->getPath());

        foreach($fileFinder as $info){
            if(substr($info->getBasename('.php'), 0, 9) === 'template-'){
                PageTemplates::setPageTemplate($info, sprintf('Module %s - ', $moduleInfo->getName()));
            }
        }
    }  

}