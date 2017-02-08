<?php

namespace Webwijs\Module;

use Doctrine\Common\Cache\CacheProvider;
use Doctrine\Common\Cache\ArrayCache;

/**
 * 
 * Module Options
 *
 * Contains all the options for the module manager
 * 
 * @author Leo Flapper <leo@webwijs.nu>
 * @author Chris Harris <chris@webwijs.nu>
 * @version 1.1.0
 * @since 1.1.0
 */
class ModuleOptions
{

	/**
	 * The cache provider to use 
	 * @var CacheProvider $cacheProvider the cache provider to use
	 */
	private $cacheProvider = null;

	/**
	 * The cache driver to use
	 * @var object
	 */
	protected $cacheManager;

	/**
	 * The max depth to use
	 * @var int
	 */
	protected $maxDepth;

	/**
	 * Sets the cache provider to use
	 * @param CacheProvider $cacheProvider the cache provider to use
	 */
	public function setCacheProvider(CacheProvider $cacheProvider)
	{
		$this->cacheProvider = $cacheProvider;
	}

	/**
	 * Returns the cache provider.
	 * If no cache provider is set, it will return the ArrayCache 
	 * @return CacheProvider $cacheProvider the cache provider to use
	 */
	public function getCacheProvider()
	{
		if($this->cacheProvider === null){
			$this->cacheProvider = new ArrayCache();
		}
		return $this->cacheProvider;
	}

	/**
	 * Sets the max depth of the directories to search for modules
	 * @param integer $maxDepth the max depth of the directories to search for modules
	 */
	public function setMaxDepth($maxDepth)
	{
		if (!is_numeric($maxDepth)) {
		    throw new \InvalidArgumentException(sprintf(
		        '%s: expects an integer or numeric value; received "%s"',
		        __METHOD__,
		        (is_object($maxDepth) ? get_class($maxDepth) : gettype($maxDepth))
		    ));
		}

		$this->maxDepth = (int)$maxDepth;
	}

	/**
	 * Returns the max depth of the directories to search for modules
	 * @return integer $maxDepth the max depth of the directories to search for modules
	 */
	public function getMaxDepth()
	{
		if($this->maxDepth === null){
			$this->maxDepth = 2;
		}
		return $this->maxDepth;
	}


}