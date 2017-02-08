<?php
namespace Webwijs\Module;

use Webwijs\Module\Config\Config;

/**
 * Module Info
 *
 * Contains the information of a single module
 *
 * @author Leo Flapper <leo@webwijs.nu>
 * @author Chris Harris <chris@webwijs.nu>
 * @version 1.1.0
 * @since 1.1.0
 */ 
class ModuleInfo
{
	/**
	 * The name of the module
	 * @var string $name the name of the module
	 */
	private $name;

	/**
	 * The path of the module
	 * @var string $path the path of the module
	 */
	private $path;

	/**
	 * The version of the module
	 * @var string $version the version of the module
	 */
	private $url;

	/**
	 * The constructor sets the configuration values of the desired modules
	 * @param object $config the configuration data object
	 */
	public function __construct($name, $path, $url, $config)
	{
		$this->setName($name);
		$this->setPath($path);
		$this->setUrl($url);
		$this->config = new Config($config);
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
	 * Returns the path of the module
	 * @return string $path the path of the module
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * Sets the path of the module
	 * @param string $path the path of the module
	 */
	private function setPath($path)
	{
		$this->path = $path;
	}

	/**
	 * Returns the url of the module
	 * @return string $url the url of the module
	 */
	public function getUrl()
	{
		return $this->url;
	}

	/**
	 * Sets the url of the module
	 * @param string $url the url of the module
	 */
	private function setUrl($url)
	{
		$this->url = $url;
	}

	/**
	 * Returns the module namespace
	 * @return string the module namespace
	 */
	public function getNamespace()
	{
		return '\Module\\'.$this->getName();
	}

	/**
	 * Returns the frontend name of the module
	 * @return string $name the frontend name of the module
	 */
	public function getFrontendName()
	{
		return $this->config->getName();
	}

	/**
	 * Returns the version number of the module
	 * @return string $version the version number of the module
	 */
	public function getVersion()
	{
		return $this->config->getVersion();
	}

	/**
	 * Returns if the module is active or not
	 * @return boolean $active true if active, false if not
	 */
	public function isActive()
	{
		return $this->config->isActive();
	}
}