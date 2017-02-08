<?php

namespace Webwijs\View;

/**
 * Saves directories to use with the view object
 *
 * @author Leo Flapper
 * @version 1.1.0
 * @since 1.0.0
 */
class Directories
{
    /**
     * Global directories to save all directories
     *
     * @var array
     */
    protected static $staticDirectories = array();

    /**
     * The stack containing all directories.
     *
     * @var array
     */
    protected $directories = array();
    
    /**
     * Creates a new directories array and merges already existing directories with the new directories array.
     *
     * @param array|\Traversable|null $directories an optional array or Traversable object consisting of directories.
     */
    public function __construct($directories = null)
    {
        // register global resources with loader.
        if (!empty(static::$staticDirectories)) {
            $this->registerDirectories(static::$staticDirectories);
        }
        
        // register resources from the argument list.
        if (null !== $directories) {
            $this->registerDirectories($directories);
        }
    }
    
    /**
     * Register one or more directories.
     *
     * @param array|\Traversable $directories one or more directories to register.
     * @throws \InvalidArgumentException if the provided argument is not an array or instance of Traversable.
     */
    public function registerDirectories($directories)
    {    
        if (!is_array($directories) && !($directories instanceof \Traversable)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an array or instance of the Traversable; received "%s"',
                __METHOD__,
                (is_object($directories) ? get_class($directories) : gettype($directories))
            ));
        }

        foreach ($directories as $directory) {
            $this->registerDirectory($directory);
        }
    }
    
    /**
     * Register one directory.
     *
     * @param string $directory the directory
     */
    public function registerDirectory($directory)
    {
        return $this->directories[] = $directory;
    }

    /**
     * Returns the directories
     *
     * @return array returns the directories.
     * @throws \InvalidArgumentException if the given argument is not of type string.
     */
    public function getDirectories()
    {
        return $this->directories;

    }
    
    /**
     * Register one or more directories.
     *
     * @param array|\Traversable $directories one or more directories to register.
     * @throws \InvalidArgumentException if the provided argument is not an array or instance of Traversable.
     */
    public static function addStaticDirectories($directories)
    {        
        if (!is_array($directories) && !($directories instanceof \Traversable)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an array or instance of the Traversable; received "%s"',
                __METHOD__,
                (is_object($directories) ? get_class($directories) : gettype($directories))
            ));
        }
        
        foreach ($directories as $directory) {
            static::addStaticDirectory($directory);
        }
    }
    
    /**
     * Register one directory statically.
     *
     * @param string $directory the directory
     * @throws \InvalidArgumentException if the given argument is not of type string.
     */
    public static function addStaticDirectory($directory)
    {
    		if (!is_string($directory)) {
	          throw new \InvalidArgumentException(sprintf(
	              '%s: expects a string argument; received "%s"',
	              __METHOD__,
	              (is_object($directory) ? get_class($directory) : gettype($directory))
	          ));
	      }

        return static::$staticDirectories[] = $directory;
    }

    /**
     * Returns a directory by key
     *
     * @return mixed returns the directories array
     * @throws \InvalidArgumentException if the given argument is not of type string.
     */
    public static function getStaticDirectories()
    {
      	return static::$staticDirectories;
    }
    
    /**
     * Removes all global directories. The array containing these directories will be empty
     * after this call returns.
     *
     * @return void
     */
    public static function clearStaticDirectories()
    {
        static::$staticDirectories = array();
    }
    
}