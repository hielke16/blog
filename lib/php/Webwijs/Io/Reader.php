<?php

namespace Webwijs\Io;

/**
 * An abstract class for reading characters from a string. Concrete subclasses 
 * are required to implement the read($amount) method.
 * 
 * @author Chris Harris
 * @version 0.0.1
 */
abstract class Reader
{
    /**
     * Reader is unmarked.
     *
     * @var int
     */
    const UNMARKED = -1;

    /**
     * Read the given amount of the characters from the reader.
     * 
     * @param int $amount the number of characters to read.
     * @return string|null a string containing the given amount of characters.
     */ 
    public function readChar($amount = 1)
    {
        return $this->read(0, $amount);
    }
    
    /**
     * Reads the given amount of the characters from the reader starting 
     * from the given offset. 
     *
     * @param int $offset the offset at which to start storing characters.
     * @param int $length the maximum number of characters to read.
     * @return string a string consisting of the characters read.
     * @throws \InvalidArgumentException if the given arguments are not integer types.
     */
    public abstract function read($offset, $length);    
    
    /**
     * Mark the current position of the reader. Calling reset() will
     * reset the reader postion to the marked position.
     *
     */
    public abstract function mark();
    
    /**
     * Reset the reader to the most recent mark.
     *
     * @throws \LogicException if no has mark has been set.
     */
    public abstract function reset();
    
    /**
     * Skip the given number of characters.
     *
     * @param int $amount the number of characters to skip.
     */
    public abstract function skip($amount = 1);
}
