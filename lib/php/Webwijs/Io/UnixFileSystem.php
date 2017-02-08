<?php

namespace Webwijs\Io;

/**
 * This FileSystem implements the FileSystemInterface and is used for operating systems (OS) 
 * that implement the Unix architecture, or a closely related architecture such as Linux.
 *
 * @author Chris Harris
 * @version 0.0.9
 */
class UnixFileSystem extends AbstractFileSystem
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
    
        return ($this->getPrefixLength($path) === 1);
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
            return ($path[0] === DIRECTORY_SEPARATOR) ? 1 : 0;
        }
        return 0;
    }
}
