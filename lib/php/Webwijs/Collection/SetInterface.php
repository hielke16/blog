<?php

namespace Webwijs\Collection;

/**
 * A collection that contains no duplicates. More formally, sets contain no pair of elements
 * $e1 and $e2 such that ($e1 == $e2). This behavior of a set cannot be specified by this interface,
 * meaning that some set will allow null values while others might prohibit it.
 *
 * @author Chris Harris
 * @version 1.1.0
 * @since 1.0.0
 */
interface SetInterface extends \Iterator, \Countable
{
    /**
     * Add the specified element to this set if it not already present.
     *
     * @param mixed $element the element to add to this set.
     * @return bool true if this set did no already contain the specified element.
     */
    public function add($element);
    
    /**
     * Add to this set all of the elements that are contained in the specified collection.
     *
     * @param array|\Traversable $elements collection containing elements to add to this set.
     * @return bool true if the set has changed, false otherwise.
     * @throws \InvalidArgumentException if the given argument is not an array of instance of Traversable.
     * @see SetInterface::add($element)
     */
    public function addAll($elements);
    
    /**
     * Removes all elements from this set. The set will be empty after this call returns.
     *
     * @return void
     */
    public function clear();
    
    /**
     * Returns true if this set is considered to be empty.
     *
     * @return bool true is this set contains no elements, false otherwise.
     */
    public function isEmpty();
    
    /**
     * Returns true if this set contains the specified element. More formally returns true only if this set
     * contains an element $e such that ($e == $element).
     *
     * @param mixed $element the element whose presence will be tested.
     * @return bool true if this set contains the specified element, false otherwise.
     */
    public function contains($element);
    
    /**
     * Returns true if this set contains all elements contained in the specified collection.
     *
     * @param array|\Traversable $elements collection elements whose presence will be tested.
     * @return bool true if this set contains all elements in the specified collection, false otherwise.
     * @see SetInterface::contains($element)
     */
    public function containsAll($elements);
    
    /**
     * Removes the specified element from this set if it is present. More formally removes an element $e
     * such that ($e == $element), if this set contains such an element.
     *
     * @param array|\Traversable $element the element to remove from this set.
     * @return bool if the element is contained by this set.
     */
    public function remove($element);
    
    /**
     * Removes from this set all of the elements that are contained in the specified collection.
     *
     * This implementation determines which if this set of the given collection is smaller, by invoking
     * the {@link count()} method on each. If this set has fewer elements, then the implementation iterates
     * over this set, checking each element to see if it is contained in the specified collection. If the
     * specified collection has fewer elements, then the implementation iterates of the specified collection,
     * removing from this set each element contained by the specified collection.
     *
     * @param array|\Traversable $elements collection containing elements to remove from this set.
     * @return bool true if the set has changed, false otherwise.
     * @throws \InvalidArgumentException if the given argument is not an array of instance of Traversable.
     * @see SetInterface::remove($element)
     */
    public function removeAll($elements);
    
    /**
     * Returns an array containing all elements in this set. The caller is free to modify the returned
     * array since it has no reference to the actual elements contained by this set.
     *
     * @return array an array containing all elements from this set.
     */
    public function toArray();
}
