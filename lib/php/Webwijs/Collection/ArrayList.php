<?php

namespace Webwijs\Collection;

/**
 * This class implements the {@see ListInterface}, and is backed by a native array.
 *
 * @author Chris Harris
 * @version 1.1.0
 * @since 1.0.0
 */
class ArrayList extends AbstractList
{
    /**
     * A native array to hold elements.
     *
     * @var array
     */
    private $data = array();

    /**
     * Indicate if the end of the array has been reached.
     *
     * @var bool
     */
    private $valid = false;

    /**
     * Construct a new ArrayList.
     *
     * @param mixed $data one or more elements to add to the set, or null.
     */
    public function __construct($data = null) 
    {
        if (null !== $data) {
            $elements = (!is_array($data) && !($data instanceof \Traversable)) ? array($data) : $data;
            $this->addAll($elements);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function add($element, $index = -1)
    {
        if (!is_int($index)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an integer argument; received "%s"',
                __METHOD__,
                (is_object($index) ? get_class($index) : gettype($index))
            ));
        } else if ($index >= 0 && $index >= $this->count()) {
            throw new \OutOfRangeException(sprintf(
                '%s: list size: %d; received index %s',
                __METHOD__, 
                $this->count(),
                $index
            ));
        }
    
        if ($index >= 0) {
            array_slice($this->data, $index, 0, (array) $element);
        } else {
            $this->data[] = $element;
        }

        return true;
    }
    
    /**
     * {@inheritDoc}
     */
    public function addAll($elements, $index = -1)
    {
        if (!is_int($index)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an integer argument; received "%s"',
                __METHOD__,
                (is_object($index) ? get_class($index) : gettype($index))
            ));
        } else if ($index >= 0 && $index >= $this->count()) {
            throw new \OutOfRangeException(sprintf(
                '%s: list size: %d; received index %s',
                __METHOD__, 
                $this->count(),
                $index
            ));
        } else if (!is_array($elements) && !($elements instanceof \Traversable)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an array or instance of the Traversable; received "%s"',
                __METHOD__,
                (is_object($elements) ? get_class($elements) : gettype($elements))
            ));
        }
    
        if ($elements instanceof \Traversable) {
            $elements = iterator_to_array($elements);
        }
    
        if ($index >= 0) {
            array_slice($this->data, $index, 0, $elements);
        } else {
            $this->data = array_merge($this->data, $elements);
        }
        
        return true;
    }
    
    /**
     * {@inheritDoc}
     */
    public function clear()
    {
        $this->data = array();
    }

    /**
     * {@inheritDoc}
     */
    public function contains($element)
    {
        return (in_array($element, $this->data));
    }
    
    /**
     * {@inheritDoc}
     */
    public function get($index)
    {
        if (!is_int($index)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an integer argument; received "%s"',
                __METHOD__,
                (is_object($fromIndex) ? get_class($fromIndex) : gettype($fromIndex))
            ));
        } else if ($index < 0 || $index >= $this->count()) {
            throw new \OutOfRangeException(sprintf(
                '%s: list size: %d; received index %s',
                __METHOD__, 
                $this->count(),
                $index
            ));
        }
        
        $retval = null;
        if (isset($this->data[$index])) {
            $retval = $this->data[$index];
        }
        return $retval;
    }
    
    /**
     * {@inheritDoc}
     */
    public function indexOf($element)
    {
        $firstIndex = array_search($element, $this->data);
        if ($firstIndex === false) {
            $firstIndex = -1;
        }
        
        return $firstIndex;
    }
    
    /**
     * {@inheritDoc}
     */
    public function lastIndexOf($element)
    {
        $lastIndex = -1;
        if ($indices = array_keys($this->data, $element)) {
            $lastIndex = end($indices); 
        }
        
        return $lastIndex;
    }
    
    /**
     * {@inheritDoc}
     */
    public function isEmpty()
    {
        return ($this->count() == 0);
    }
    
    /**
     * {@inheritDoc}
     */
    public function remove($element)
    {
        $retval = null;
        if (false !== ($index = array_search($element, $this->data))) {
            $retval = $this->removeByIndex($index);
        }
        return $retval;
    }
    
    /**
     * {@inheritDoc}
     */
    public function removeByIndex($index)
    {
        if (!is_int($index)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an integer argument; received "%s"',
                __METHOD__,
                (is_object($fromIndex) ? get_class($fromIndex) : gettype($fromIndex))
            ));
        } else if ($index < 0 || $index >= $this->count()) {
            throw new \OutOfRangeException(sprintf(
                '%s: list size: %d; received index %s',
                __METHOD__, 
                $this->count(),
                $index
            ));
        }
        
        $retval = null;
        if (isset($this->data[$index])) {
            $retval = $this->data[$index];
            // remove element.
            unset($this->data[$index]);
            // reset array keys.
            $this->data = array_values($this->data);
        }
        return $retval;
    }
    
    /**
     * {@inheritDoc}
     */
    public function set($index, $element)
    {
        if (!is_int($index)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an integer argument; received "%s"',
                __METHOD__,
                (is_object($index) ? get_class($index) : gettype($index))
            ));
        } else if ($index < 0 || $index >= $this->count()) {
            throw new \OutOfRangeException(sprintf(
                '%s: list size: %d; received index %s',
                __METHOD__, 
                $this->count(),
                $index
            ));
        }
        
        $oldElement = null;
        if (isset($this->data[$index])) {
            $oldElement = $this->data[$index];
        }
        
        $this->data[$index] = $element;
        
        return $oldElement;
    }
    
    /**
     * {@inheritDoc}
     */
    public function subList($fromIndex, $toIndex)
    {
        if (!is_int($fromIndex) && $fromIndex < 0) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a whole positive number; received "%s"',
                __METHOD__,
                $fromIndex
            ));
        } else if (!is_int($toIndex) && $toIndex < 0) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a whole positive number; received "%s"',
                __METHOD__,
                $toIndex
            ));
        } else if ($fromIndex > $toIndex) {
            throw new \LogicException(sprintf(
                '%s: $fromIndex(%s) > $toIndex(%s)',
                __METHOD__,
                $fromIndex,
                $toIndex
            ));
        } else if ($fromIndex < 0) {
            throw new \LogicException(sprintf(
                '%s: $fromIndex(%s) cannot be smaller than 0',
                __METHOD__,
                $fromIndex
            ));
        } else if ($toIndex > $this->count()) {
            throw new \LogicException(sprintf(
                '%s: $toIndex(%s) cannot be larger than %d',
                __METHOD__,
                $toIndex,
                $this->count()
            ));
        }
        
        $offset = $fromIndex;
        $length = $toIndex - $fromIndex;
        
        $list = new ArrayList();
        if ($elements = array_slice($this->toArray(), $offset, $length)) {
            $list->addAll($elements);
        }
        return $list;
    }
    
    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        return $this->data;
    }
    
    /**
     * Returns the number of elements contained by this list.
     *
     * @return int the number of elements contained by this list.
     */
    public function count()
    {
        return (count($this->data));
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
}
