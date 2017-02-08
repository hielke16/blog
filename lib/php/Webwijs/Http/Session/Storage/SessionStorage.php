<?php

namespace Webwijs\Http\Session\Storage;

use Webwijs\Util\Arrays;

/**
 * The SessionStorage allows mapping of keys to values. It cannot contain duplicate keys; and each key
 * can map to at most one value. 
 *
 * This class was specifically designed to replace the $_SESSION superglobal to give a more granular 
 * control of how session data is accessed and updated. 
 *
 * @author Chris Harris
 * @version 1.1.0
 * @since 1.0.0
 */
class SessionStorage implements StorageInterface,
\Iterator
{
    /**
     * A flag indicating if the end of the array has been reached.
     *
     * @var bool
     */
    private $valid = false;
    
    /**
     * A native PHP array to iterate over.
     *
     * @var array
     */
    private $sessionData = array();
    
    /**
     * A Unix timestamp when the storage was last used.
     *
     * @var int
     */
    private $lastUsed;
    
    /**
     * Create a new SessionStorage.
     *
     * @param mixed $input data to store for this session.
     */
    public function __construct($input = null)
    {
        $resetSession = true;
        if ((null === $input) && isset($_SESSION)) {
            $input = $_SESSION;
            if (is_object($input) && $_SESSION instanceof \ArrayAccess) {
                $resetSession = false;
            } else if (is_object($input) && !($_SESSION instanceof \ArrayAccess)) {
                $input = (array) $input;
            }
        } else if (null === $input) {
            $input = array();
        }

        $this->sessionData = $input;
        
        // create (or update) metadata.
        if (null !== $this->getMetadata('created')) {
            // set last used equal to previously 'updated' time stamp.
            $this->setLastUsed($this->getMetadata('updated'));
            $this->setMetadata('updated', current_time('timestamp'));
        } else {
            $timeStamp = current_time('timestamp');
            $this->setMetadata('created', $timeStamp);
            $this->setMetadata('updated', $timeStamp);
            $this->setMetadata('lifetime', ini_get('session.cookie_lifetime'));
            
            // set last used equal to 'created' time stamp.
            $this->setLastUsed($timeStamp);
        }
        
        if ($resetSession) {
            $_SESSION = $this;
        }
    }
    
    /**
     * Whether or not an offset exists.
     *
     * @param mixed $offset an offset to check for.
     * @return bool true if the offset exists, false otherwise.
     */ 
    public function offsetExists($offset)
    {
        return (isset($this->sessionData[$offset]));
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
            $this->sessionData[] = $value;
        } else {
            $this->sessionData[$offset] = $value;
        }
    }
    
    /**
     * Unsets an offset.
     *
     * @param mixed $offset the offset to unset.
     */
    public function offsetUnset($offset)
    {
        unset($this->sessionData[$offset]);
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
            $value = $this->sessionData[$offset];
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
        return current($this->sessionData);
    }
    
    /**
     * Returns the key for the current element.
     *
     * @return scalar the key of the current element.
     */
    public function key()
    {
        return key($this->sessionData);
    }
    
    /**
     * Move forward to the next element.
     *
     * @return void
     */
    public function next()
    {
        $this->valid = (false !== next($this->sessionData)); 
    }
    
    /**
     * Rewind the iterator to the first element.
     *
     * @return void.
     */
    public function rewind()
    {
        $this->valid = (false !== reset($this->sessionData));
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
     * Returns the number of elements stored by this map.
     *
     * @return int the number of elements.
     */
    public function count()
    {
        return count($this->sessionData);
    }
    
    /**
     * Removes all values from this map.
     *
     * @return void
     */
    public function clear()
    {
        $this->fromArray(array());
    }
    
    /**
     * Returns true if this map contains the specified value. 
     *
     * @param mixed $value value whose presence in this map is to be tested.
     * @return bool true if this map contains the specified value, false otherwise.
     */
    public function contains($value)
    {
        return in_array($value, $this->sessionData);
    }
    
    /**
     * Returns true if this map contains no values.
     *
     * @return bool true if this map contains no values, false otherwise.
     */
    public function isEmpty()
    {
        return ($this->count() == 0);
    }
    
    /**
     * Returns the index of the first occurence of the specified value in this map,
     * or -1 if this map does not contain the value.
     *
     * @param mixed $value the value to search for.
     * @return int|string the index of first occurence of the specified value in this
     *                    object, or -1 if the object does not contain the value.
     */
    public function indexOf($value)
    {
        $firstIndex = array_search($value, $this->sessionData);
        if ($firstIndex === false) {
            $firstIndex = -1;
        }
        
        return $firstIndex;   
    }
    
    /**
     * Returns the index of the last occurence of the specified value in this map,
     * or -1 if this map does not contain the value.
     *
     * @param mixed $value the value to search for.
     * @return int|string the index of last occurence of the specified value in this
     *                    object, or -1 if the object does not contain the value.
     */
    public function lastIndexOf($value)
    {
        $lastIndex = -1;
        if ($indices = array_keys($this->sessionData, $value)) {
            $lastIndex = end($indices); 
        }
        
        return $lastIndex;
    }
    
    /**
     * {@inheritdoc}
     */
    public function fromArray(array $array)
    {
        $this->sessionData = $array;
        
        // create (or update) metadata.
        if (null !== $this->getMetadata('created')) {
            // set last used equal to previously 'updated' time stamp.
            $this->setLastUsed($this->getMetadata('updated'));
            $this->setMetadata('updated', current_time('timestamp'));
        } else {
            $timeStamp = current_time('timestamp');
            $this->setMetadata('created', $timeStamp);
            $this->setMetadata('updated', $timeStamp);
            $this->setMetadata('lifetime', ini_get('session.cookie_lifetime'));
            
            // set last used equal to 'created' time stamp.
            $this->setLastUsed($timeStamp);
        }

        if ($_SESSION !== $this) {
            $_SESSION = $this;
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function toArray($includeMetaData = false)
    {
        $data = $this->sessionData;
        if (is_object($data) && $data instanceof \Traversable) {
            if (version_compare(PHP_VERSION, '5.1.0', '>=')) {
                $data = iterator_to_array($data);
            } else {
                $data = Arrays::iteratorToArray($data);
            }
        } else {
            $data = (array) $data;
        }
        
        // remove metadata from array.
        if (!((bool) $includeMetaData) && isset($data['_webwijs_meta'])) {
            unset($data['_webwijs_meta']);
        }
        return $data;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setMetadata($key, $value, $overwriteArray = false)
    {
        $storageKey = '_webwijs_meta';
        if (!$this->offsetExists($storageKey)) {
            // create array to hold metadata.
            $this->offsetSet($storageKey, array());
        }

        $metadata = $this->offsetGet($storageKey);
        if (isset($metadatap[$key]) && is_array($value)) {
            if ((bool) $overwriteArray) {
                $metadata[$key] = $value;
            } else {
                $metadata[$key] = array_replace_recursive($metadata[$key], $value);
            }
        } else {
            if ((null === $value) && isset($metadata[$key])) {
                unset($metadata[$key]);
            } else if (null !== $value) {
                $metadata[$key] = $value;
            }
        }
        
        $this->offsetSet($storageKey, $metadata);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata($key = null)
    {    
        $storageKey = '_webwijs_meta';
        if ($this->offsetExists($storageKey)) {
            $metadata = $this->offsetGet($storageKey);
            if (null === $key) {
                return $metadata;
            }
            
            if (is_array($metadata) && array_key_exists($key, $metadata)) {
                return $metadata[$key];
            }
        }
        return null;
    }
    
    /**
     * Store a Unix timestamp containing the date and time when this storage was last accessed.
     *
     * @param int $timestamp a Unix timestamp.
     */
    protected function setLastUsed($timestamp)
    {
        if (!is_numeric($timestamp)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a numeric argument; received "%s"',
                __METHOD__,
                (is_object($timestamp) ? get_class($timestamp) : gettype($timestamp))
            ));
        }
        
        $this->lastUsed = (int) $timestamp;
    }
    
    /**
     * Returns a Unix timestamp containing the date and time when this storage was last accessed.
     *
     * @return int a Unix timestamp.
     */
    public function getLastUsed()
    {
        return $this->lastUsed;
    }
}
