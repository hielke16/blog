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

namespace Webwijs\Cache\Loader;

/**
 * This class provides a partial implementation of the {@see LoaderInterface}
 * to minimize the effort required to implement this interface.
 *
 * @author Leo Flapper - <info@leoflapper.nl>
 * @version 1.1.0
 * @since 1.0.0
 */
abstract class AbstractLoader implements LoaderInterface
{
	/**
	 * The default server name constant which will be used as default for certain cache drivers
	 */
	const DEFAULT_SERVER_NAME = 'webwijsthema';

	/**
	 * The cache loader container
	 * @var null
	 */
	protected $loader = null;

	/**
     * {@inheritDoc}
     */
	public function nextLoader(LoaderInterface $loader)
	{
		$this->loader = $loader;
	}

	/**
	 * Checks if a given cache driver exists within the system.
	 * 
	 * @param  string $name the name of the system
	 * @return boolean true if exists, false if not
	 */
	protected function cacheDriverExists($name){
		if (!is_string($name)) {
            throw new \InvalidArgumentException(sprintf(
                '%s(): expects a string as argument; received "%d"',
                __METHOD__,
                (is_object($name) ? get_class($name) : gettype($name))
            ));
        }

		if(!class_exists($name)){
			if(!extension_loaded($name)){
				return false;
			}
		}
		return true;
	}

}