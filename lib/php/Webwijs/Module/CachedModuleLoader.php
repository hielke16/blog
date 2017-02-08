<?php

namespace Webwijs\Module;

use Doctrine\Common\Cache\CacheProvider;
use Webwijs\File\FileFinder;
use Webwijs\Module\ModuleLoaderInterface;

/**
 * Cached Module Loader
 *
 * Loads modules from cache if available.
 *
 * @author Leo Flapper <leo@webwijs.nu>
 * @author Chris Harris <chris@webwijs.nu>
 * @version 1.1.0
 * @since 1.1.0
 */ 
class CachedModuleLoader implements ModuleLoaderInterface
{
    /**
     * The module loader to decorate.
     * 
     * @var ModuleLoaderInterface
     */
    private $loader;
    
    /**
     * A Doctrine cache provider.
     *
     * @var Cache
     */
    private $cache;

    /**
     * The cache key to use for retrieving the modules from cache
     * @var string $cacheKey the cache key
     */
    private $cacheKey;

    /**
     * The cache salt to use for retrieving the modules cache 
     * @var string
     */
    private static $cacheSalt = '@[Modules]';


    /**
     * Construct the CachedModuleLoader by defining the loader, cache and cache key
     *
     * @param ModuleLoaderInterface $loader the module loader to decorate.
     * @param Cache $cache A Doctrine cache provider.
     */
    public function __construct(ModuleLoaderInterface $loader, CacheProvider $cache, $cacheKey)
    {
        $this->loader = $loader;
        $this->cache  = $cache;
        $this->setCacheKey($cacheKey);
    }

    /**
     * {@inheritDoc}
     */
    public function load(FileFinder $paths)
    {
        $modules = array();
        if ($this->isWpDebug()) {
            $modules = $this->loader->load($paths);
            if($this->fetchFromeCache($this->getCacheKey())){
                $this->deleteFromCache($this->getCacheKey());
            }
        } else {
            if (!$modules = $this->fetchFromeCache($this->getCacheKey())) {
                $modules = $this->loader->load($paths);
                $this->saveToCache($this->getCacheKey(), $modules);
            }
        }
        return $modules;
    }

    /**
     * Saves a value to the cache.
     *
     * @param string $rawCacheKey The cache key.
     * @param mixed  $value       The value.
     *
     * @return boolean true if saved, false if not
     */
    private function saveToCache($rawCacheKey, $value)
    {
        if (!is_string($rawCacheKey)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($rawCacheKey) ? get_class($rawCacheKey) : gettype($rawCacheKey))
            ));
        } 

        $cacheKey = $rawCacheKey . self::$cacheSalt;
        if($this->cache->save('[T]'.$cacheKey, time())){
            return $this->cache->save($cacheKey, $value);
        }
        return false;
    }

    /**
     * Fetches a value from cache
     * @param  string $rawCacheKey the key to retrieve the cache
     * @return mixed The cached value or false when the value is not in cache
     */
    private function fetchFromeCache($rawCacheKey)
    {
        if (!is_string($rawCacheKey)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($rawCacheKey) ? get_class($rawCacheKey) : gettype($rawCacheKey))
            ));
        } 

        $cacheKey = $rawCacheKey . self::$cacheSalt;
        return $this->cache->fetch($cacheKey);
    }

    /**
     * Deletes a value from cache
     * @param  string $rawCacheKey the key to delete the cache
     * @return mixed true if deleted, false if not
     */
    private function deleteFromCache($rawCacheKey)
    {
        if (!is_string($rawCacheKey)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($rawCacheKey) ? get_class($rawCacheKey) : gettype($rawCacheKey))
            ));
        } 

        $cacheKey = $rawCacheKey . self::$cacheSalt;
        if($this->cache->delete('[T]'.$cacheKey)){
            return $this->cache->delete($cacheKey);
        }
        return false;
        
    }

    /**
     * Checks if the cache is fresh.
     *
     * @param string $rawCacheKey the key to retrieve the cache modification time
     * @param integer $modificationTime the modification time to check by
     *
     * @return boolean
     */
    public function isCacheFresh($rawCacheKey, $modificationTime)
    {
        if (!is_string($rawCacheKey)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($rawCacheKey) ? get_class($rawCacheKey) : gettype($rawCacheKey))
            ));
        } 
        
        if (!is_int($modificationTime)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an integer; received "%s"',
                __METHOD__,
                (is_object($modificationTime) ? get_class($modificationTime) : gettype($modificationTime))
            ));
        }    

        $cacheKey = $rawCacheKey . self::$cacheSalt;
        return $this->cache->fetch('[T]'.$cacheKey) >= $modificationTime;
    }

    /**
     * Returns the cache key
     * @return string $cacheKey the cache key
     */
    private function getCacheKey()
    {
        return $this->cacheKey;
    }

    /**
     * Sets the cache key
     * @param string $cacheKey the cache key
     */
    private function setCacheKey($cacheKey)
    {
        if (!is_string($cacheKey)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($cacheKey) ? get_class($cacheKey) : gettype($cacheKey))
            ));
        } 

        $this->cacheKey = $cacheKey;
    }

    /**
     * Returns if Wordpress is in debug mode
     * @return boolean true if in debug, false if not
     */
    private function isWpDebug()
    {
        return WP_DEBUG;
    }
}