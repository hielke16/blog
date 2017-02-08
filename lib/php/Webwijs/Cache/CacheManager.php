<?php
/**
 * Copyright (c) 2016, Leo Flapper.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of the copyright holder nor the names of its 
 *     contributors may be used to endorse or promote products derived 
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @author     Leo Flapper <info@leoflapper.nl>
 * @copyright  Copyright (c) 2016 Leo Flapper
 * @license    http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 */

namespace Webwijs\Cache;

use Doctrine\Common\Cache\XcacheCache;
use Webwijs\Config\ConfigInterface;
use Webwijs\Config\Config;
use Webwijs\Cache\Loader\LoaderInterface;
use Webwijs\Cache\Loader\ArrayCacheLoader;

/**
 * Cache Manager
 * Class for loading the desired Cache driver
 *
 * @author Leo Flapper - <info@leoflapper.nl>
 * @version 1.1.0
 * @since 1.1.0
 */ 
class CacheManager
{
	
	/**
	 * Holds the desired cache driver loader
	 * 
	 * @var LoaderInterface $loader the desired cache loader
	 */
	protected $loader;

	/**
	 * Sets the default cache provider
	 */
	public function __construct()
	{
		$this->loader = new ArrayCacheLoader();
	}

	/**
	 * Returns the cache provider to use
	 * @return CacheProvider the cache provider to use
	 */
	public function getCacheProvider(ConfigInterface $config = null)
	{
		$loader = $this->getLoader();

		if($config === null){
			$config = new Config();
		}

		return $loader->load($config);
	}

	/**
	 * Adds multiple loaders
	 * @param array $loaders array containing multiple loaders
	 */
	public function addLoaders($loaders)
	{
		if (!is_array($loaders)) {
		    throw new \InvalidArgumentException(sprintf(
		        '%s: expects an array argument; received "%s"',
		        __METHOD__,
		        (is_object($loaders) ? get_class($loaders) : gettype($loaders))
		    ));
		}
		
		foreach($loaders as $loader){
			$this->addLoader($loader);
		}

	}

	/**
	 * Adds a loader. 
	 * If the loader not the same as the previous it will be added to the loader chain.
	 * @param LoaderInterface $loader the desired cache loader
	 */
	public function addLoader(LoaderInterface $loader)
	{
		if($this->loader !== null && $this->loader !== $loader){
			$loader->nextLoader($this->loader);
		}	
		$this->loader = $loader;
	}

	/**
	 * Returns the loader
	 * @return LoaderInterface $loader the desired cache loader
	 */
	public function getLoader()
	{
		return $this->loader;
	}

}