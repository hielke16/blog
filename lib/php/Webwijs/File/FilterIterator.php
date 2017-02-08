<?php

namespace Webwijs\File;

use RecursiveFilterIterator;
use RecursiveIterator;
use SplFileInfo;

/**
 * File Filter Iterator
 *
 * Applies a filter to determine which files are acceptable
 *
 * @author Chris Harris
 * @version 1.1.0
 * @since 1.0.0
 */ 
class FilterIterator extends RecursiveFilterIterator
{
    /**
     * A filter to determine which files are acceptable.
     *
     * @var FilterInterface
     */
    private $filter;

    /**
     * Construct a new FilterIterator
     *
     * @param RecursiveIterator $iterator the recursive iterator to be filtered.
     * @param FilterInterface $filter the file to determine which files are acceptable.
     */
    public function __construct(RecursiveIterator $iterator, FilterInterface $filter)
    {
        parent::__construct($iterator);
        $this->filter = $filter;
    }

    /**
     * Checks if the file is of type SplFileInfo and applies the filter to determine if the file is acceptable
     * @return boolean true if acceptable, false if not acceptable
     */
    public function accept() 
    {
        $accept = true;
        if ($this->current() instanceof SplFileInfo) {
            $accept = $this->filter->accept($this->current());
        }
        return $this->hasChildren() || $accept;
    }

    /**
     * Applies the filters rules to determine if the children can be accepted
     * @return  FilterIterator the filter iterator
     */
    public function getChildren()
    {
        return new self($this->getInnerIterator()->getChildren(), $this->filter);
    }
}
