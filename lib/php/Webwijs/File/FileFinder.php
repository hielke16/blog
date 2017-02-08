<?php

namespace Webwijs\File;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use AppendIterator;
use FilesystemIterator;

use Webwijs\Collection\ArrayList;

/**
 * File Finder
 *
 * Searches for acceptable files at different paths
 *
 * @author Leo Flapper
 * @version 1.1.0
 * @since 1.0.0
 */ 
class FileFinder implements FileFinderInterface
{

	/**
	 * Contains file paths
	 * @var ArrayList $paths array list containing the paths of the files
	 */
	private $paths;

	/**
	 * The filter which will be applied to determine if the file path is acceptable
	 * @var object $filter the filter containing the rules
	 */
	private $filter;

	/**
	 * The max depth to find the acceptable files
	 * @var integer $maxDepth the max depth to find acceptable files
	 */
	private $maxDepth = 1;

	/**
	 * Sets a new Array List for the paths
	 */
	public function __construct()
	{
		$this->paths = new ArrayList();
	}

	/**
	 * Searches for files in the designated directory and returns the paths in an array
	 * @return array $paths an array of found files, an empty array if there are no files in the designated directory
	 */
	public function find()
	{
		$paths = array();
		foreach($this as $file){
			$paths[] = $file->getRealPath();
		}
		return $paths;
	}

	/**
	 * Returns all paths in array format
	 * @return array $paths the paths in an associative array
	 */
	public function getPaths()
	{
		return $this->paths->toArray();
	}

	/**
	 * Returns a single path, by path
	 * @param  string $path the path
	 * @return string|null the $path or null if path doesn't exist
	 * @throws InvalidArgumentException if the specified argument is not a string.
	 */
	public function getPath($path)
	{
		if (!is_string($path)) {
	        throw new \InvalidArgumentException(sprintf(
	            '%s: expects an string argument; received "%s"',
	            __METHOD__,
	            (is_object($path) ? get_class($path) : gettype($path))
	        ));
	    }

		return $this->paths->get($path);
	}

	/**
     * Adds a directory path to the file finder
     *
     * @param string $path the path to add.
     * @return bool true if the specified path was added, false otherwise.
     * @throws InvalidArgumentException if the specified argument is not a string.
     */
    public function addPath($path)
    {
    	if (!is_string($path)) {
	        throw new \InvalidArgumentException(sprintf(
	            '%s: expects an string argument; received "%s"',
	            __METHOD__,
	            (is_object($path) ? get_class($path) : gettype($path))
	        ));
	    }

	    $this->paths->add($path);
    }
    
    /**
     * Adds multiple directory paths to the file finder
     *
     * @param array|Traversable $paths a collection of path that to add.
     * @return bool true if the underlying collection of paths has changed, false otherwise.
     * @throws InvalidArgumentException if the specified argument is not an array or Traversable object.
     */
    public function addPaths($paths)
    {
    	if (!is_array($paths) && !($paths instanceof \Traversable)) {
	        throw new \InvalidArgumentException(sprintf(
	            '%s: expects an array argument; received "%s"',
	            __METHOD__,
	            (is_object($paths) ? get_class($paths) : gettype($paths))
	        ));
	    }

	    $this->paths->addAll($paths);
    }
    
    /**
     * Remove from this finder finder the specified path.
     *
     * @param string $path the path to remove.
     * @return bool true if the file finder contained the specified path, false otherwise.
     * @throws InvalidArgumentException if the specified argument is not a string.
     */
    public function removePath($path)
    {	
    	if (!is_string($path)) {
	        throw new \InvalidArgumentException(sprintf(
	            '%s: expects an string argument; received "%s"',
	            __METHOD__,
	            (is_object($path) ? get_class($path) : gettype($path))
	        ));
	    }

    	return $this->paths->remove($path);
    }
    
    /**
     * Remove from this file finder all the paths contained within the specified collection.
     *
     * @param array|Traversable $paths a collection of path that will be removed.
     * @return bool true if the underlying collection of paths has changed, false otherwise.
     * @throws InvalidArgumentException if the specified argument is not an array or Traversable object.
     */
    public function removePaths($paths)
    {
    	if (!is_array($paths) && !($paths instanceof \Traversable)) {
	        throw new \InvalidArgumentException(sprintf(
	            '%s: expects an array or instance of the Traversable; received "%s"',
	            __METHOD__,
	            (is_object($paths) ? get_class($paths) : gettype($paths))
	        ));
	    }

    	return $this->paths->removeAll($paths);
    	
    }
    
    /**
     * Retain from this file finder all the paths contained within the specified collection.
     *
     * @param array|Traversable $paths a collection of path that will be retained.
     * @return bool true if the underlying collection of paths has changed, false otherwise.
     * @throws InvalidArgumentException if the specified argument is not an array or Traversable object.
     */
    public function retainPaths($paths)
    {
    	if (!is_array($paths) && !($paths instanceof \Traversable)) {
	        throw new \InvalidArgumentException(sprintf(
	            '%s: expects an array or instance of the Traversable; received "%s"',
	            __METHOD__,
	            (is_object($paths) ? get_class($paths) : gettype($paths))
	        ));
	    }

	    return $this->paths->retainAll($paths);
    }
    
    /**
     * Returns true if the specified path is contained within this file finder.
     *
     * @param string $path the path whose presence will be tested.
     * @return bool true if this file finder contains the specified path, false otherwise.
     * @throws InvalidArgumentException if the specified argument is not a string.
     */
    public function hasPath($path)
    {	
    	if (!is_string($path)) {
	        throw new \InvalidArgumentException(sprintf(
	            '%s: expects an string argument; received "%s"',
	            __METHOD__,
	            (is_object($path) ? get_class($path) : gettype($path))
	        ));
	    }

    	return $this->paths->contains($path);
    }
    
    /**
     * Remove all paths from this file finder. The file finder will have no paths after this call returns.
     *
     * @return void
     */
    public function clearPaths()
    {
    	$this->paths->clear();
    }
    
    /**
     * Returns the filter which will be used to determine if a path is acceptable
     * If the filter is not set it will add a default filter
     * @return object $filter the filter which will be used to determine if a path is acceptable
     */
    public function getFilter()
    {
    	if($this->filter === null){
    		$this->filter = new DefaultFilter();
    	}
    	return $this->filter;
    }

    /**
     * Set a filter to remove directories which do not belong to a directory.
     *
     * @param FilterInterface $filter the filter to remove unwanted directories.
     */
    public function setFilter(FilterInterface $filter)
    {
    	$this->filter = $filter;
    }

    /**
     * Returns the max depth to find the acceptable files
     * @return integer the max depth to find acceptable files
     */
    public function getMaxDepth()
    {
    	return $this->maxDepth;
    }

    /**
     * Sets the max depth to find the acceptable files
     * @param integer $maxDepth the max depth to find acceptable files
     */
    public function setMaxDepth($maxDepth)
    {
    	if (!is_numeric($maxDepth)) {
	        throw new \InvalidArgumentException(sprintf(
	            '%s: expects an integer; received "%s"',
	            __METHOD__,
	            (is_object($maxDepth) ? get_class($maxDepth) : gettype($maxDepth))
	        ));
	    }

    	$this->maxDepth = (int)$maxDepth;
    }

    /**
     * Returns the Append Iterator which contains all acceptable file paths
     * @return AppendIterator $it the Append Iterator which contains all acceptable file paths
     */
    public function getIterator()
    {
    	$it = new AppendIterator();
    	foreach($this->getPaths() as $path){
    		$directoryIt = new RecursiveDirectoryIterator($path);
			$recursiveIt = new RecursiveIteratorIterator(new FilterIterator($directoryIt, $this->getFilter()));
			$recursiveIt->setMaxDepth($this->getMaxDepth());
			$it->append($recursiveIt);
    	}
		
        return $it;
    }

}