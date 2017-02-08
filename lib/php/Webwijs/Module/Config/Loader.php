<?php

namespace Webwijs\Module\Config;

use Webwijs\Util\Strings;
use Webwijs\Module\Config\Config;

/**
 * Config loader
 *
 * Loads the config data for the modules
 *
 * @author Leo Flapper <leo@webwijs.nu>
 * @author Chris Harris <chris@webwijs.nu>
 * @version 1.1.0
 * @since 1.1.0
 */
class Loader 
{	
	/**
	 * Array containing the configuration of the different modules
	 * @var array $configs array containing configurations of the different modules
	 */
	private $configs = array();

	/**
	 * The extension of the configuration files to be loaded
	 * @var string the extension of the configuration file
	 */
	private $extension;


	/**
	 * Sets the default extension and loads the configuration files
	 * @param string $extension extension of the configuration files
	 */
	public function __construct($extension = 'xml')
	{
		$this->setExtension($extension);
	}

	/**
	 * Loads the configuration files of the modules
	 * @param  array $modules array containing the modules
	 * @return array $configs array containing the configurations of the modules
	 */
	public function loadConfigFiles($modules)
	{
		foreach($modules as $name => $module){
			$config = $this->loadConfig($module['name'], $module['path']);
			$this->setConfig($name, $config);	
		}
		return $this->getConfigs();
	}

	/**
	 * Loads the configuration file
	 * @param  string $name the name of the configuration file
	 * @param  string $path the path of the configuration file
	 * @return SimpleXML Object containing the configuration data
	 */
	public function loadConfig($name, $path)
	{
		if (!is_string($path)) {
	        throw new \InvalidArgumentException(sprintf(
	            '%s: expects a string argument; received "%s"',
	            __METHOD__,
	            (is_object($path) ? get_class($path) : gettype($path))
	        ));
	    }

	    if (!is_string($name)) {
	        throw new \InvalidArgumentException(sprintf(
	            '%s: expects a string argument; received "%s"',
	            __METHOD__,
	            (is_object($name) ? get_class($name) : gettype($name))
	        ));
	    }

		$configPath = Strings::addTrailing($path, '/').$name.'.'.$this->getExtension();
		if (file_exists($configPath)) {
			return simplexml_load_file($configPath);
		} else {
			throw new \Exception(sprintf('Config %s for module %s at path "%s" does not exist', $this->getExtension(), $name, $configPath));
		}
	}

	/**
	 * Returns the configuration by module name
	 * @param  string $name the module name
	 * @return Config|null the configuration data or null on failure
	 */
	public function getConfig($name)
	{
		if (!is_string($name)) {
	        throw new \InvalidArgumentException(sprintf(
	            '%s: expects a string argument; received "%s"',
	            __METHOD__,
	            (is_object($name) ? get_class($name) : gettype($name))
	        ));
	    }

	    $lookup = strtolower($name);
	    $config = null;
		if(isset($this->configs[$lookup])){
			$config = $this->configs[$lookup];
		}
		return $config;
	}

	/**
	 * Returns the modules configurations
	 * @return array containing the configuration data
	 */
	public function getConfigs()
	{
		return $this->configs;
	}

	/**
	 * Sets the configuration data, where the key is the module name
	 * @param string $name   the module name
	 * @param object $config the configuration data
	 */
	private function setConfig($name, $config)
	{
		if (!is_string($name)) {
	        throw new \InvalidArgumentException(sprintf(
	            '%s: expects a string argument; received "%s"',
	            __METHOD__,
	            (is_object($name) ? get_class($name) : gettype($name))
	        ));
	    }

	    if (!is_object($config)) {
	        throw new \InvalidArgumentException(sprintf(
	            '%s: expects a object argument; received "%s"',
	            __METHOD__,
	            (is_object($config) ? get_class($config) : gettype($config))
	        ));
	    }

	    $lookup = strtolower($name);
		$this->configs[$lookup] = new Config($config);
	}

	/**
	 * Returns the file extension used for the config files
	 * @return string $extension the configuration file extension
	 */
	private function getExtension()
	{
		return $this->extension;
	}

	/**
	 * Sets the configuration file extension
	 * @param string $extension the configuration file extension
	 */
	private function setExtension($extension)
	{
		if (!is_string($extension)) {
	        throw new \InvalidArgumentException(sprintf(
	            '%s: expects a string argument; received "%s"',
	            __METHOD__,
	            (is_object($extension) ? get_class($extension) : gettype($extension))
	        ));
	    }

		$this->extension = $extension;
	}

}