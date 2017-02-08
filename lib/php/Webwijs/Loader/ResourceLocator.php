<?php

namespace Webwijs\Loader;

/**
 * This locator is responsible for loading resources. A resource in this context refers to a PHP class that is
 * accessible by the locator. The locator will traverse all registered namespaces in reverse order, also known
 * as LIFO (last in, first out), and applies each namespace with the given class name until an existing class 
 * is found.
 *
 * @author Chris Harris
 * @version 1.1.0
 * @since 1.0.0
 */
interface ResourceLocator
{
    /**
     * Register a new resource.
     *
     * @param string $shortName a (short) name to identify the resource.
     * @param string $namespace a namespace or prefix that is registered by an autoloader.
     * @throws \InvalidArgumentException if either one of the arguments is not of type string.
     */
    public function registerResource($shortName, $namespace);
    
    /**
     * Unregister a resource lookup.
     *
     * @param string $shortName the (short) name associated with a resource.
     * @return bool returns true if a resource lookup was unregistered, false otherwise.
     */
    public function unregisterResource($shortName);
    
    /**
     * Returns an indexed array containing a namespace for each registered resource.
     *
     * @return array the namespaces associated with all (short) names.
     */
    public function getRegisteredResources();
    
    /**
     * Returns the first class that matches with a registered resource, or null if no class was found.
     *
     * @param string $type the type of resource to search in.
     * @param string $name the name of a class to find in the given resource.
     * @return string|null returns a class that matches with the given resource type, or null if no resource was found.
     * @throws \InvalidArgumentException if either one of the arguments is not of type string.                   
     */
    public function load($type, $name);
}
