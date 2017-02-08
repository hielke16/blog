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

namespace Webwijs\Config;

/**
 * Saves config values for use within the application
 *
 * @author Leo Flapper - <info@leoflapper.nl>
 * @version 1.1.0
 * @since 1.0.0
 */
class Config implements ConfigInterface
{
    /**
     * global config to save all config data
     *
     * @var array
     */
    protected static $staticConfig = array();

    /**
     * the stack containing all config values.
     *
     * @var array
     */
    protected $config = array();
    
    /**
     * Creates a new config array and merges already existing config values with the new config array.
     *
     * @param array|\Traversable|null $config an optional array or Traversable object consisting of config values.
     */
    public function __construct($config = null)
    {
        // register global resources with loader.
        if (!empty(static::$staticConfig)) {
            $this->registerConfigs(static::$staticConfig);
        }
        
        // register resources from the argument list.
        if (null !== $config) {
            $this->registerConfigs($config);
        }
    }
    
    /**
     * {@inheritDoc}
     */
    public function registerConfigs($config)
    {    
        if (!is_array($config) && !($config instanceof \Traversable)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an array or instance of the Traversable; received "%s"',
                __METHOD__,
                (is_object($config) ? get_class($config) : gettype($config))
            ));
        }

        foreach ($config as $shortName => $configValue) {
            $this->registerConfig($shortName, $configValue);
        }
    }
    
    /**
     * {@inheritDoc}
     */
    public function isRegisteredConfig($shortName)
    {
        if (!is_string($shortName)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($shortName) ? get_class($shortName) : gettype($shortName))
            ));
        }
        
        $lookup = strtolower($shortName);
        return (isset($this->config[$lookup]));
    }
    
    /**
     * {@inheritDoc}
     */
    public function registerConfig($shortName, $configValue)
    {
        $lookup = strtolower($shortName);
        return $this->config[$lookup] = $configValue;
    }

    /**
     * {@inheritDoc}
     */
    public function getConfig($shortName, $default = null)
    {
    	if (!is_string($shortName)) {
          throw new \InvalidArgumentException(sprintf(
              '%s: expects a string argument; received "%s"',
              __METHOD__,
              (is_object($shortName) ? get_class($shortName) : gettype($shortName))
          ));
        }

        $lookup = strtolower($shortName);
        return (isset($this->config[$lookup])) ? $this->config[$lookup] : $default;

    }
    
    /**
     * {@inheritDoc}
     */
    public function unregisterConfig($shortName)
    {
    		if (!is_string($shortName)) {
	          throw new \InvalidArgumentException(sprintf(
	              '%s: expects a string argument; received "%s"',
	              __METHOD__,
	              (is_object($shortName) ? get_class($shortName) : gettype($shortName))
	          ));
	      }

        $unregistered = false;
        if (is_string($shortName)) {
            $lookup = strtolower($shortName);
            if (isset($this->config[$lookup])) {
                unset($this->config[$lookup]);
                $unregistered = true;
            }
        }
        return $unregistered;
    }
    
    
    /**
     * Register one or more config values.
     *
     * @param array|\Traversable $config one or more config values to register.
     * @throws \InvalidArgumentException if the provided argument is not an array or instance of Traversable.
     */
    public static function addStaticConfigs($config)
    {        
        if (!is_array($config) && !($config instanceof \Traversable)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an array or instance of the Traversable; received "%s"',
                __METHOD__,
                (is_object($config) ? get_class($config) : gettype($config))
            ));
        }
        
        foreach ($config as $shortName => $configValue) {
            static::addStaticConfig($shortName, $configValue);
        }
    }
    
    /**
     * Register one config value.
     *
     * @param string $shortName the key for the config value
     * @param string $configValue the config value
     * @throws \InvalidArgumentException if the given argument is not of type string.
     */
    public static function addStaticConfig($shortName, $configValue)
    {
    		if (!is_string($shortName)) {
	          throw new \InvalidArgumentException(sprintf(
	              '%s: expects a string argument; received "%s"',
	              __METHOD__,
	              (is_object($shortName) ? get_class($shortName) : gettype($shortName))
	          ));
	      }

        $lookup = strtolower($shortName);
        return static::$staticConfig[$lookup] = $configValue;
    }

    /**
     * Returns a config value by key
     *
     * @param string $shortName the (short) name associated with a config key.
     * @return mixed returns the config value if the config key was found, false otherwise.
     * @throws \InvalidArgumentException if the given argument is not of type string.
     */
    public static function getStaticConfig($shortName)
    {
    	if (!is_string($shortName)) {
          throw new \InvalidArgumentException(sprintf(
              '%s: expects a string argument; received "%s"',
              __METHOD__,
              (is_object($shortName) ? get_class($shortName) : gettype($shortName))
          ));
      }

      $lookup = strtolower($shortName);
      if (isset(static::$staticConfig[$lookup])) {
      	return static::$staticConfig[$lookup];
      } else {
      	return false;
      }

    }
    
    /**
     * Removes all global config values. The array containing these config values will be empty
     * after this call returns.
     *
     * @return void
     */
    public static function clearStaticConfig()
    {
        static::$staticConfig = array();
    }
    
    /**
     * Removes a global config value lookup.
     *
     * @param string $shortName the (short) name associated with a config key.
     * @throws \InvalidArgumentException if the given argument is not of type string.
     */
    public static function removeStaticConfig($shortName)
    {
        if (!is_string($shortName)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($shortName) ? get_class($shortName) : gettype($shortName))
            ));
        }
    
        $lookup = strtolower($shortName);
        if (isset(static::$config[$lookup])) {
            unset(static::$config[$lookup]);
        }
    }
    
    
}