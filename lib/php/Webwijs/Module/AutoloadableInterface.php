<?php

namespace Webwijs\Module;

/**
 *
 * Autoloadable Interface
 *
 * If the module has auto loader configuration, it will be used for the AutoloaderConfig
 * 
 * @author Leo Flapper <leo@webwijs.nu>
 * @author Chris Harris <chris@webwijs.nu>
 * @version 1.1.0
 * @since 1.1.0
 */
interface AutoloadableInterface
{	

	/**
	 * Returns the AutoLoaderConfig configuration array
	 * @return array $autoloaderconfig multidimensional array containig AutoloaderConfig configuration
	 */
	public function getAutoloaderConfig();

}