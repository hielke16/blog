<?php

namespace Webwijs;

use Webwijs\Loader\ClassLoader;

/**
 * This loader is responsible for finding resources. A resource in this context refers to a PHP class that is
 * accessible by the loader.
 *
 * @see ClassLoader
 * @deprecated 1.1.0 Use the new ClassLoader instead.
 */
class ResourceLoader
{    
    /**
     * Returns the first class that matches with a registered resource, or null if no class was found.
     *
     * @param string $type the type of resource to search in.
     * @param string $name the name of a class to find in the given resource.
     * @return string|null returns a class that matches with the given resource type, or null if no resource was found.
     * @throws \InvalidArgumentException if either one of the arguments is not of type string.                   
     * @deprecated 1.1.0 Use the new ClassLoader instead.
     */
    public static function load($type, $name)
    {
        return ClassLoader::loadStatic($type, $name);
    }
}

