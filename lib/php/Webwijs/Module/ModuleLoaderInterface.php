<?php

namespace Webwijs\Module;

use Webwijs\File\FileFinder;

/**
 * Module Loader Interface
 *
 * Interface to use when retrieving modules
 *
 * @author Leo Flapper <leo@webwijs.nu>
 * @author Chris Harris <chris@webwijs.nu>
 * @version 1.1.0
 * @since 1.1.0
 */ 
interface ModuleLoaderInterface
{
    /**
     * Load modules from the specified file paths.
     *
     * @param FileFinder $paths a collection of file paths.
     */
    public function load(FileFinder $paths);
}