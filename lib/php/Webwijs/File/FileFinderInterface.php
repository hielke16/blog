<?php

namespace Webwijs\File;

use IteratorAggregate;

/**
 * File Finder Interface
 *
 * Contains the needed functions for finding acceptable files at different directory paths
 *
 * @author Chris Harris & Leo Flapper
 * @version 1.1.0
 * @since 1.0.0
 */ 
interface FileFinderInterface extends IteratorAggregate
{
    /**
     * Add to this file finder the specified path.
     *
     * @param string $path the path to add.
     * @return bool true if the specified path was added, false otherwise.
     * @throws InvalidArgumentException if the specified argument is not a string.
     */
    public function addPath($path);
    
    /**
     * Add to this file finder all the paths contained within the specified collection.
     *
     * @param array|Traversable $paths a collection of path that to add.
     * @return bool true if the underlying collection of paths has changed, false otherwise.
     * @throws InvalidArgumentException if the specified argument is not an array or Traversable object.
     */
    public function addPaths($paths);
    
    /**
     * Remove from this file finder the specified path.
     *
     * @param string $path the path to remove.
     * @return bool true if the file finder contained the specified path, false otherwise.
     * @throws InvalidArgumentException if the specified argument is not a string.
     */
    public function removePath($path);
    
    /**
     * Remove from this file finder all the paths contained within the specified collection.
     *
     * @param array|Traversable $paths a collection of path that will be removed.
     * @return bool true if the underlying collection of paths has changed, false otherwise.
     * @throws InvalidArgumentException if the specified argument is not an array or Traversable object.
     */
    public function removePaths($paths);
    
    /**
     * Retain from this file finder all the paths contained within the specified collection.
     *
     * @param array|Traversable $paths a collection of path that will be retained.
     * @return bool true if the underlying collection of paths has changed, false otherwise.
     * @throws InvalidArgumentException if the specified argument is not an array or Traversable object.
     */
    public function retainPaths($paths);
    
    /**
     * Returns true if the specified path is contained within this file finder.
     *
     * @param string $path the path whose presence will be tested.
     * @return bool true if this file finder contains the specified path, false otherwise.
     * @throws InvalidArgumentException if the specified argument is not a string.
     */
    public function hasPath($path);
    
    /**
     * Remove all paths from this file finder. The file finder will have no paths after this call returns.
     *
     * @return void
     */
    public function clearPaths();
    
    /**
     * Set a filter to remove files which do not belong to a directory.
     *
     * @param FilterInterface $filter the filter to remove unwanted directories.
     */
    public function setFilter(FilterInterface $filter);


    /**
     * Sets the max depth to look for files
     * @param int $maxDepth the max depth to look for files
     */
    public function setMaxDepth($maxDepth);
}
