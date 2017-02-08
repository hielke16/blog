<?php

namespace Webwijs\Loader;

use Webwijs\Loader\StandardAutoloader as Autoloader;
use Webwijs\Util\Strings;

class ClassLoader implements ResourceLocator
{
    /**
     * global resources to seed all loaders with.
     *
     * @var array
     */
    protected static $staticResources = array();

    /**
     * the stack containing resources for this loader.
     *
     * @var array
     */
    protected $resources = array();
    
    /**
     * Create a new resource loader.
     *
     * @param array|\Traversable|null $resources an optional array or Traversable object consisting of resources.
     * @throws \InvalidArgumentException if an argument is provided but it is not an array or instance of Traversable.
     */
    public function __construct($resources = null) {
        // register global resources with loader.
        if (!empty(static::$staticResources)) {
            $this->registerResources(static::$staticResources);
        }
        
        // register resources from the argument list.
        if (null !== $resources) {
            $this->registerResources($resources);
        }
    }
    
    /**
     * Register one or more resources with the loader.
     *
     * @param array|\Traversable $resources one or more resources to register.
     * @throws \InvalidArgumentException if the provided argument is not an array or instance of Traversable.
     */
    public function registerResources($resources)
    {    
        if (!is_array($resources) && !($resources instanceof \Traversable)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an array or instance of the Traversable; received "%s"',
                __METHOD__,
                (is_object($resources) ? get_class($resources) : gettype($resources))
            ));
        }

        foreach ($resources as $shortName => $namespace) {
            $this->registerResource($shortName, $namespace);
        }
    }
    
    /**
     * Determine if a resource is registered.
     *
     * @param string $shortName the (short) name associated with a resource.
     * @return bool returns true if a resource was found, false otherwise.
     * @throws \InvalidArgumentException if the given argument is not of type string.
     */
    public function isRegisteredResource($shortName)
    {
        if (!is_string($shortName)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($shortName) ? get_class($shortName) : gettype($shortName))
            ));
        }
        
        $lookup = strtolower($shortName);
        return (isset($this->resources[$lookup]));
    }
    
    /**
     * {@inheritDoc}
     */
    public function registerResource($shortName, $namespace)
    {
        if (!is_string($shortName)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($shortName) ? get_class($shortName) : gettype($shortName))
            ));
        }

        $lookup = strtolower($shortName);
        if (isset($this->resources[$lookup])) {
            // create array of resource(s).
            $resource = (is_array($namespace)) ? $namespace : array($namespace);
            // stack namespaces as LIFO (last in, first out).
            $this->resources[$lookup] = array_merge_recursive($resource, $this->resources[$lookup]);
        } else {
            $this->resources[$lookup] = (is_array($namespace)) ? $namespace : array($namespace);
        }
    }
    
    /**
     * {@inheritDoc}
     */
    public function unregisterResource($shortName)
    {
        $unregistered = false;
        if (is_string($shortName)) {
            $lookup = strtolower($shortName);
            if (isset($this->resources[$lookup])) {
                unset($this->resources[$lookup]);
                $unregistered = true;
            }
        }
        return $unregistered;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getRegisteredResources()
    {
        $resources = array();
        foreach ($this->resources as $resource) {
            $resources = array_merge($resources, (array) $resource);
        }
        return $resources;
    }
    
    /**
     * {@inheritDoc}                
     */
    public function load($type, $name)
    {    
        if (!is_string($type)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($type) ? get_class($type) : gettype($type))
            ));
        } else if (!is_string($name)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($name) ? get_class($name) : gettype($name))
            ));
        }
    
        $lookup = strtolower($type);
        if (isset($this->resources[$lookup])) {
            $resources = $this->resources[$lookup];
            foreach ($resources as $namespace) {
                // find a defined class by it's name.
                if ($class = self::getClassByName($name, $namespace)) {
                    return $class;
                }
            }
        }
        return null;
    }
    
    /**
     * Register one or more global resources.
     *
     * @param array|\Traversable $resources one or more resources to register.
     * @throws \InvalidArgumentException if the provided argument is not an array or instance of Traversable.
     */
    public static function addStaticResources($resources)
    {        
        if (!is_array($resources) && !($resources instanceof \Traversable)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an array or instance of the Traversable; received "%s"',
                __METHOD__,
                (is_object($resources) ? get_class($resources) : gettype($resources))
            ));
        }
        
        foreach ($resources as $shortName => $namespace) {
            static::addStaticResource($shortName, $namespace);
        }
    }
    
    /**
     * Register a new global resource.
     *
     * @param string $shortName a (short) name to identify the resource.
     * @param string $namespace a namespace or prefix that is registered by an autoloader.
     * @throws \InvalidArgumentException if either one of the arguments is not of type string.
     */
    public static function addStaticResource($shortName, $namespace)
    {
        if (!is_string($shortName)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($shortName) ? get_class($shortName) : gettype($shortName))
            ));
        } else if (!is_string($namespace)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($namespace) ? get_class($namespace) : gettype($namespace))
            ));
        }
        
        $lookup = strtolower($shortName);
        if (isset(static::$staticResources[$lookup])) {
            // create array of resource(s).
            $resource = (is_array($namespace)) ? $namespace : array($namespace);
            // stack namespaces as LIFO (last in, first out).
            static::$staticResources[$lookup] = array_merge_recursive($resource, static::$staticResources[$lookup]);
        } else {
            static::$staticResources[$lookup] = (is_array($namespace)) ? $namespace : array($namespace);
        }
    }
    
    /**
     * Removes all global resources. The array containing these resources will be empty
     * after this call returns.
     *
     * @return void
     */
    public static function clearStaticResources()
    {
        static::$staticResources = array();
    }
    
    /**
     * Removes a global resource lookup.
     *
     * @param string $shortName the (short) name associated with a resource.
     * @throws \InvalidArgumentException if the given argument is not of type string.
     */
    public static function removeStaticResource($shortName)
    {
        if (!is_string($shortName)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($shortName) ? get_class($shortName) : gettype($shortName))
            ));
        }
    
        $lookup = strtolower($shortName);
        if (isset(static::$staticResources[$lookup])) {
            unset(static::$staticResources[$lookup]);
        }
    }
    
    /**
     * Returns the first class that matches with a registered global resource, or null if no class was found.
     *
     * Although this method is similar to the {@link ClassLoader::load($type, $name)} method it will only search
     * for a match using global resources. Global resources are added through the use of a static method and are
     * automatically to each new instance of the ClassLoader.
     *
     * @param string $type the type of resource to search in.
     * @param string $name the name of a class to find in the given resource.
     * @return string|null returns a class that matches with the given resource type, or null if no resource was found.
     * @throws \InvalidArgumentException if either one of the arguments is not of type string.                   
     */
    public static function loadStatic($type, $name)
    {    
        if (!is_string($type)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($type) ? get_class($type) : gettype($type))
            ));
        } else if (!is_string($name)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($name) ? get_class($name) : gettype($name))
            ));
        }
    
        $lookup = strtolower($type);
        if (isset(static::$staticResources[$lookup])) {
            $resources = static::$staticResources[$lookup];
            foreach ($resources as $namespace) {
                // find a defined class by it's name.
                if ($class = self::getClassByName($name, $namespace)) {
                    return $class;
                }
            }
        }
        return null;
    }
    
    /**
     * Returns a class with the given class name and namespace, or null if
     * no class exists.
     *
     * @param string $className the name of class to find.
     * @param string|null $namespace an optional namespace that is prepended to the class name.
     * @return string|null the class name if the class exists, otherwise null.
     */
    protected static function getClassByName($className, $namespace = null)
    {        
        $class = $className;
        if (is_string($namespace)) {
            if (false !== strpos($namespace, Autoloader::NS_SEPARATOR)) {
                $class = Strings::addTrailing($namespace, Autoloader::NS_SEPARATOR) . $className;
            } else if (false !== strpos($namespace, Autoloader::PREFIX_SEPARATOR)) {
                $class = Strings::addTrailing($namespace, Autoloader::PREFIX_SEPARATOR) . $className;
            }
        }
        return (class_exists($class)) ? $class : null;
    }
}
