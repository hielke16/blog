<?php

namespace Webwijs\Module;

use Webwijs\File\FileFinder;
use Webwijs\Module\Config\Loader as ConfigLoader;
use Webwijs\Module\ModuleOptions;
use Webwijs\Module\CachedModuleLoader;
use Webwijs\Module\ModuleLoader;
use Webwijs\Module\Module;
use Webwijs\Module\ModuleInfo;
use Webwijs\Module\ModuleTemplates;
use Webwijs\Module\Action\ModuleLoaded;

/**
 * Module Manager
 *
 * Handling the modules with it's data
 *
 * @author Leo Flapper <leo@webwijs.nu>
 * @author Chris Harris <chris@webwijs.nu>
 * @version 1.1.0
 * @since 1.1.0
 */
class ModuleManager
{
	/**
	 * The cache key to use for caching the routes
	 */
	const CACHE_KEY = 'modules';

	/**
	 * The module container
	 * @var array $modules, array containing modules
	 */
	protected static $modules = array();

	/**
	 * Contains the options for the module manager
	 * @var ModuleOptions $moduleOptions contains the module manager options
	 */
	private $moduleOptions;

	/**
	 * Sets the module manager options
	 * @param ModuleOptions $moduleOptions contains the module manager options
	 */
	public function __construct(ModuleOptions $moduleOptions)
	{
		$this->setModuleOptions($moduleOptions);
	}

	/**
	 * Loads the module config files
	 * @return array $configs array containing module configurations where the keys are the module names
	 */
	private function loadConfigs($modules)
	{
		if ($modules = self::getModules()){
			$this->setConfigs(new ConfigLoader($modules, 'xml'));
		}
		return $this->getConfigs();
	}

	/**
	 * Initiates the templates to use with the Wordpress templating system
	 * @return void
	 */
	private function initTemplates()
	{
		$templates = new ModuleTemplates($this->getModules());
		$templates->init();
	}

	/**
	 * Loads the modules from the given path.
	 * 
	 * @param  string $path the path to load the modules from
	 * @return strin       [description]
	 */
	public function loadModules($path)
	{
		$fileFinder = new FileFinder();
		$fileFinder->setFilter(new ModuleFilter());
		$fileFinder->setMaxDepth($this->getMaxDepth());
		$fileFinder->addPath($path);

		$loader = new CachedModuleLoader(new ModuleLoader(), $this->getCacheProvider(), self::CACHE_KEY);
		$moduleLoaded = new ModuleLoaded();

		if($modules = $loader->load($fileFinder)){
			$configLoader = new ConfigLoader();
			foreach($modules as $module){
				if(isset($module['name']) && $module['path']){
					$config = $configLoader->loadConfig($module['name'], $module['path']);
					$info   = new ModuleInfo($module['name'], $module['path'], $this->getModuleUri($module['path']), $config);
	    			$this->setModule($info->getName(), $info);
	    			
	    			do_action('module_loaded', $info, $this->loadBootstrap($info));
				}
			}
		}

		return $this->getModules();
	}

	/**
	 * Loads the module bootstrap
	 * @param  ModuleInfo $moduleInfo the module info
	 * @return Bootstrap the module bootstrap class
	 */
 	public function loadBootstrap(ModuleInfo $info)
    {
        if ($info->isActive() && $info->getPath() && $info->getName()) {
            $file = $info->getPath().'/Bootstrap.php';
            if (file_exists($file)) {
                include_once($file);
                $bootstrap = $info->getNamespace().'\Bootstrap';
                
                return new $bootstrap();
            }
            
            throw new \Exception(sprintf('Bootstrap not found for module %s at path %s', $info->getName(), $info->getPath()));
        }
    }

	/**
	 * Sets a single module.
	 * The name will be used as key for the module.
	 * @param string $name the name of the module which will be used as key
	 * @param array $module a single module containing it's data
	 * @return ModuleInfo $module the module info.
	 */
	private function setModule($name, ModuleInfo $module)
	{
		if (!is_string($name)) {
	        throw new \InvalidArgumentException(sprintf(
	            '%s: expects a string argument; received "%s"',
	            __METHOD__,
	            (is_object($name) ? get_class($name) : gettype($name))
	        ));
	    }

    	$lookup = strtolower($name);
    	   	
    	static::$modules[$lookup] = $module;
	}

	/**
	 * Returns a single module by name
	 * @param  string $name the name of the module
	 * @return Module|null the module if found, or null if not found
	 */
	private function getModule($name)
	{
		if (!is_string($name)) {
	        throw new \InvalidArgumentException(sprintf(
	            '%s: expects a string argument; received "%s"',
	            __METHOD__,
	            (is_object($name) ? get_class($name) : gettype($name))
	        ));
	    }

    	$lookup  = strtolower($name);
    	$modules = self::getModules();
    	
		return (isset($modules[$lookup])) ? $modules[$lookup] : null;
	}

	/**
	 * Sets the module config data
	 * @param ConfigLoader $configs array containing config data objects
	 */
	private function setConfigs(ConfigLoader $configs)
	{
		$this->configs = $configs;
	}

	/**
	 * Returns the config data array
	 * @return ConfigLoader $configs array containing config data objects
	 */
	private function getConfigs()
	{
		return $this->configs;
	}

	/**
	 * Returns the module uri by the directory given.
	 * 
	 * @param  string $dir the directory to generate the uri from
	 * @return string the generated module directory uri
	 */
	private function getModuleUri($dir)
	{
	    $themeDir = get_template_directory();
	    if (substr($dir, 0, strlen($themeDir)) == $themeDir) {
	        $path = substr($dir, strlen($themeDir));
	    } else {
	        $path = $dir;
	    }
	    
	    return get_template_directory_uri() . '/' . ltrim($path, '/');
	}

	/**
	 * Returns the cache provider
	 * @return CacheProvider returns the cache provider
	 */
	public function getCacheProvider()
	{
		return $this->moduleOptions->getCacheProvider();
	}

	/**
	 * Returns the max depth of the directories to search for modules
	 * @return integer $maxDepth the max depth of the directories to search for modules
	 */
	public function getMaxDepth()
	{
		return $this->moduleOptions->getMaxDepth();
	}

	/**
	 * Returns a collection of loaded modules.
	 * 
	 * @return array a collection of modules, or empty array if no modules have been loaded.
	 */
	public static function getModules()
	{
		return static::$modules;
	}

	/**
	 * Sets the module manager options class
	 * @param ModuleOptions $moduleOptions the module manager options class
	 */
	private function setModuleOptions(ModuleOptions $moduleOptions)
	{
		$this->moduleOptions = $moduleOptions;
	}
	
}
