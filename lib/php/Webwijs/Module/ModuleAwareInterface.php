<?php

namespace Webwijs\Module;

/**
 *
 * Module Aware Interface
 *
 * A module doesn't have to comply to the bootstrap rules defined in the Webwijs theme.
 * If the module contains this interface, it has to implement the following functions
 * 
 * @author Leo Flapper <leo@webwijs.nu>
 * @author Chris Harris <chris@webwijs.nu>
 * @version 1.1.0
 * @since 1.1.0
 */
interface ModuleAwareInterface
{	

	/**
	 * This sets the module info for the module to use
	 * @param ModuleInfo $module the module info class
	 */
	public function setModule(ModuleInfo $module);

}