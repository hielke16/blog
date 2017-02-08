<?php

namespace Webwijs\Loader;

use Webwijs\Autoloader as OldAutoloader;

class ClassLoaderAdapter implements \Iterator
{
    /**
     * array containing of autoloader options.
     *
     * @var array
     */
    private $options = array();

    /**
     * A flag indicating if the end of the array has been reached.
     *
     * @var bool
     */
    private $valid = false;

    /**
     * Create a new class loader adapter.
     *
     * @param OldAutoloader $autoloader the autoloader to adapt.
     * @throws \InvalidArgumentException if the given argument is not of type Autoloader.
     */
    public function __construct(OldAutoloader $autoloader) 
    {
        $this->options = $this->toArray($autoloader);
    }

    /**
     * Returns the current element.
     *
     * @return mixed the current element.
     */
    public function current()
    {
        return current($this->options);
    }
    
    /**
     * Returns the key of the current element.
     *
     * @return scalar the key of the element.
     */
    public function key()
    {
        return key($this->options);
    }
    
    /**
     * Move forward to the next element.
     *
     * @return void
     */
    public function next()
    {
        $this->valid = (false !== next($this->options));
    }
    
    /**
     * Rewind the iterator to the first element.
     *
     * @return void.
     */
    public function rewind()
    {
        $this->valid = (false !== reset($this->options));
    }

    /**
     * Checks if the current position is valid.
     *
     * @return bool true if position is valid, false otherwise.
     */
    public function valid()
    {
        return $this->valid;
    }
    
    /**
     * Adapts the given autoloader to an array that the {@link ClassLoaderAdapter} can
     * use to build a stack of resources.
     *
     * @param OldAutoloader the autoloader that will be adapted.
     * @return array associative array of options.
     * @see StandardAutoloader::setOptions($options)
     * @throws \InvalidArgumentException if the given argument is not of type OldAutoloader.
     */
    private function toArray(OldAutoloader $autoloader) 
    { 
        if (null === $autoloader) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an "Autoloader" as argument; received "%s"',
                __METHOD__,
                (is_object($autoloader) ? get_class($autoloader) : gettype($autoloader))
            ));
        }
    
        $resources = array();
        if (is_array($autoloader->resourceTypes)) {
            foreach ($autoloader->resourceTypes as $name => $option) {
                if (isset($option['namespace'])) {
                    // construct a prefix from multiple parts.
                    $prefix = $autoloader->namespace . $option['namespace'];
                    $resources[$name] = $prefix; 
                }
            }
        }
        
        return $resources;
    }
}
