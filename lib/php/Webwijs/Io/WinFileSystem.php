<?php

namespace Webwijs\Io;

/**
 * This FileSystem implements the FileSystemInterface and is used for operating systems (OS)  
 * that follow the Microsoft architecture.
 *
 * @author Chris Harris
 * @version 0.0.9
 */
class WinFileSystem extends AbstractFileSystem
{
    /**
     * {@inheritDoc}
     */
    public function isAbsolute($path)
    {
        if (!is_string($path)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($str) ? get_class($str) : gettype($str))
            ));
        } 
    
        $length = $this->getPrefixLength($path);
        return ($length === 3 || ($length == 2 && $path[0] === DIRECTORY_SEPARATOR));
    }
    
    /**
     * {@inheritDoc}
     */
    public function getPrefixLength($path)
    {
        if (!is_string($path)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($str) ? get_class($str) : gettype($str))
            ));
        }

        if (strlen($path) > 0) {
            $firstChar  = $path[0];
            $secondChar = (strlen($path) > 1) ? $path[1] : '';
            if ($firstChar === DIRECTORY_SEPARATOR) {
                if ($secondChar === DIRECTORY_SEPARATOR) {
                    // path is Microsoft UNC "\\foo"
                    return 2;
                } else {
                    // path is drive-relative "\foo"
                    return 1;
                }
            }
            if (ctype_alpha($firstChar) && $secondChar === ':') {
                if (strlen($path) > 2 && $path[2] === DIRECTORY_SEPARATOR) {
                    // path is an absolute local pathname "c:\foo"
                    return 3;
                } else {
                    // path is directory-relative "c:foo"
                    return 2;
                }
            }
            // path is relative "foo".
            return 0;
        }
    }
}
