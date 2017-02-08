<?php

namespace Webwijs\Module\Config;

/**
 * Config
 *
 * Contains the configuration data for a single module
 *
 * @author Leo Flapper <leo@webwijs.nu>
 * @author Chris Harris <chris@webwijs.nu>
 * @version 1.1.0
 * @since 1.1.0
 */
class Config 
{	
	/**
	 * The name of the module
	 * @var string $name the name of the module
	 */
	private $name;

	/**
	 * The version of the module
	 * @var string $version the version of the module
	 */
	private $version;

	/**
	 * If the module is active or not active
	 * @var boolean $active active or not active
	 */
	private $active;

	/**
	 * The constructor sets the configuration values of the desired modules
	 * @param object $config the configuration data object
	 */
	public function __construct($config)
	{
		if (!is_object($config)) {
	        throw new \InvalidArgumentException(sprintf(
	            '%s: expects a object argument; received "%s"',
	            __METHOD__,
	            (is_object($config) ? get_class($config) : gettype($config))
	        ));
	    }

		if(isset($config->name)){
			$this->setName($config->name);
		} else {
			throw new \Exception('Module name must be provided');
		}
		
		if(isset($config->version)){
			$this->setVersion($config->version);	
		} else {
			throw new \Exception('Module version must be provided');
		}

		if(isset($config->active)){
			$this->setActive($config->active);	
		} else {
			throw new \Exception('Module active state must be provided');
		}
		
	}

	/**
	 * Returns the name of the module
	 * @return string $name the name of the module
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Sets the name of the module
	 * @param string $name the name of the module
	 */
	private function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * Returns the version number of the module
	 * @return string $version the version number of the module
	 */
	public function getVersion()
	{
		return $this->version;
	}

	/**
	 * Sets the version of the module
	 * @param string $version the version of the module
	 */
	private function setVersion($version)
	{
		$this->version = $version;
	}

	/**
	 * Returns if the module is active or not
	 * @return boolean $active true if active, false if not
	 */
	public function isActive()
	{
		return $this->active;
	}

	/**
	 * Sets the active state of the module.
	 * True will be set to true, otherwise it will be false
	 * @param string $active if the module is active
	 */
	private function setActive($active)
	{
		if($active == 'true'){
			$this->active = true;
		} else {
			$this->active = false;
		}
		
	}
}