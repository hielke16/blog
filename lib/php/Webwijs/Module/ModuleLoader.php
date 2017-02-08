<?php
namespace Webwijs\Module;

use Webwijs\Util\Strings;
use Webwijs\File\FileFinder;
use Webwijs\Module\ModuleLoaderInterface;

/**
 * Module Loaders
 *
 * Loads the modules by iterating through the given directories
 *
 * @author Leo Flapper <leo@webwijs.nu>
 * @author Chris Harris <chris@webwijs.nu>
 * @version 1.1.0
 * @since 1.1.0
 */ 
class ModuleLoader implements ModuleLoaderInterface
{

	/**
	 * The module container
	 * @var array $modules, array containing modules
	 */
	protected static $modules = array();

	/**
	 * Searches for modules by their .xml config and returns an array which will be used to load the modules
	 * @return array $modules associative array containing modules
	 */
	public function load(FileFinder $fileFinder)
	{
		foreach($fileFinder as $info){
			$name = $info->getBasename('.xml');
			$module = array(
				'name' => ucfirst($name),
				'path' => $info->getPath()
			);
			$this->setModule($name, $module);
		}
		
		return $this->getModules();	
	}

	/**
	 * Sets an array of modules
	 * @param array $modules the array containing the modules
	 * @return array $modules returns the modules container
	 */
	private function setModules($modules)
	{
		if (!is_array($modules) && !($modules instanceof \Traversable)) {
	        throw new \InvalidArgumentException(sprintf(
	            '%s: expects an array or instance of the Traversable; received "%s"',
	            __METHOD__,
	            (is_object($modules) ? get_class($modules) : gettype($modules))
	        ));
	    }

	    foreach($modules as $name => $module){
	    	$this->setModule($name, $module);
	    }

	    return $this->getModules();
	}

	/**
	 * Sets a single module.
	 * The name will be used as key for the module.
	 * @param string $name the name of the module which will be used as key
	 * @param array $module a single module containing it's data
	 * @return array $module the module.
	 */
	private function setModule($name, $module)
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
	 * Returns the modules
	 * @return array $modules the modules or empty array if there are no modules
	 */
	public function getModules()
	{
		$modules = array();
		if(static::$modules){
			$modules = static::$modules;
		}
		return $modules;
	}

	/**
	 * Returns a single module by name
	 * @param  string $name the name of the module
	 * @return array|null static::$modules[$lookup] the module if found, or null if not found
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
    	$lookup = strtolower($name);
    	$module = null;
		if(isset(static::$modules[$lookup])){
			$module = static::$modules[$lookup];
		}
		return $module;
	}
}