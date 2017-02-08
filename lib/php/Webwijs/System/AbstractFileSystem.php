<?php

namespace Webwijs\System;

/**
 * The AbstractFileSystem is capable of instantiating a FileSystem for the operating system
 * on which PHP is running. It also provides convenient methods to retrieve more information
 * about the operating system, most FileSystem classes inherit from this abstract class so
 * these methods are also available to a FileSystem object.
 *
 * @author Chris Harris
 * @version 0.0.9
 */
abstract class AbstractFileSystem implements FileSystemInterface
{
    /**
     * A FileSystem is only instantiated once.
     *
     * @var FileSystemInterface
     */
    private static $fileSystem;

    /**
     * Returns a FileSystem object for the operating system on which PHP is running.
     *
     * @return FileSystemInterface a FileSystem object.
     */
    public static function getFileSystem()
    {
        if (self::$fileSystem === null) {
            $os = self::getOperatingSystemName();
            if (strtoupper(substr($os, 0, 3)) === 'WIN') {
                self::$fileSystem = new WinFileSystem(); 
            } else {
                self::$fileSystem = new UnixFileSystem();
            }
        }
        
        return self::$fileSystem;
    }
    
    /**
     * Returns the name of the operating system.
     *
     * @return string the name of the operating system.
     */
    public static function getOperatingSystemName()
    {
        $os = self::getOperatingSystem('s');
        return (is_string($os)) ? $os : \PHP_OS;
    }
    
    /**
     * Returns information about the operating system PHP is running on.
     *
     * @param string $mode a single character that defines what information is returned.
     * @return string|null detailed information about the operation system, or null if php_uname is disabled.
     * @link http://php.net/manual/en/function.php-uname.php
     */
    public static function getOperatingSystem($mode = 'a')
    {
        return (function_exists('php_uname')) ? php_uname($mode) : null;
    }
}
