<?php

namespace Webwijs\Io;

/**
 * A FileSystem class can be used to perform operations on an operating system (OS) on which PHP
 * is running, or it can be used to retrieve information only available to a specific OS.
 *
 * @author Chris Harris
 * @version 0.0.9
 */
interface FileSystemInterface
{
    /**
     * Returns true if the given pathname is absolute.
     *
     * @param string $path the path to be tested.
     * @return bool true if the given pathname is absolute, false otherwise
     * @throws InvalidArgumentException if the given argument is not of type 'string'.
     */
    public function isAbsolute($path);
    
    /**
     * Returns the length of the given pathname's prefix, or zero if it has no prefix.
     *
     * @param string $path the path whose prefix length will be determined.
     * @return int the length of the pathname's prefix.
     * @throws InvalidArgumentException if the given argument is not of type 'string'.
     */
    public function getPrefixLength($path);
}
