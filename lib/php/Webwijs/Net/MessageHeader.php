<?php

namespace Webwijs\Net;

/**
 * The MessageHeader is a collection class that stores request properties; it's methods 
 * include but are not limited to fetching, storing and removing of the underlying data.
 *
 * @author Chris Harris
 * @version 0.0.1
 */
class MessageHeader implements 
\ArrayAccess,
\Countable,
\Iterator
{
    /**
     * A native PHP array to iterate over.
     *
     * @var array
     */
    private $data = array();

    /**
     * Whether or not an offset exists.
     *
     * @param mixed $offset an offset to check for.
     * @return bool true if the offset exists, false otherwise.
     */ 
    public function offsetExists($offset)
    {
        return (isset($this->data[$offset]));
    }
    
    /**
     * Assigns a value to the specified offset.
     *
     * @param mixed $offset the offset to assign the value to.
     * @param mixed $value the value to set.
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }
    
    /**
     * Unsets an offset.
     *
     * @param mixed $offset the offset to unset.
     */
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    /**
     * Returns the value at the specified offset.
     *
     * @return mixed the value for the given offset.
     */
    public function offsetGet($offset)
    {
        $value = null;
        if ($this->offsetExists($offset)) {
            $value = $this->data[$offset];
        }
        return $value;
    }

    /**
     * Returns the current element.
     *
     * @return mixed the current element.
     */
    public function current()
    {
        return current($this->data);
    }
    
    /**
     * Returns the key for the current element.
     *
     * @return scalar the key of the current element.
     */
    public function key()
    {
        return key($this->data);
    }
    
    /**
     * Move forward to the next element.
     *
     * @return void
     */
    public function next()
    {
        $this->valid = (false !== next($this->data)); 
    }
    
    /**
     * Rewind the iterator to the first element.
     *
     * @return void.
     */
    public function rewind()
    {
        $this->valid = (false !== reset($this->data));
    }
    
    /**
     * Checks if the current position is valid.
     *
     * @return bool true if the current position is valid, false otherwise.
     */
    public function valid()
    {
        return $this->valid;
    }
    
    /**
     * Returns the number of elements stored by the collection.
     *
     * @return int the number of elements.
     */
    public function count()
    {
        return count($this->data);
    }
    
    /**
     * Removes all data from this collection.
     *
     * @return void
     */
    public function clear()
    {
        $this->data = array();
    }
    
    /**
     * Returns a query string containing key-value pairs from this collection.
     *
     * @return string a query string.
     */
    public function toQueryString()
    {
        // array to hold key-value pairs.
        $result = array();
        // create a query string.
        foreach ($this as $key => $value) {
            if (is_array($value) || $value instanceof \Traversable) {
                // support duplicate keys in query string.
                foreach ($value as $v) {
                    $result[] = sprintf('%s=%s', urlencode($key), urlencode($v));
                }
            } else {
                // create an encoded key-value pair for each part.
                $result[] = sprintf('%s=%s', urlencode($key), urlencode($value));
            }
        }
        
        // return query string.
        return implode('&', $result);
    }
}
