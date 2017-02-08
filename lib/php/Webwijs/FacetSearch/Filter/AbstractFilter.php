<?php

namespace Webwijs\FacetSearch\Filter;

use Webwijs\FacetSearch\Factory;

abstract class AbstractFilter implements FilterInterface
{
    /**
     * The name of the filter.
     *
     * @var string
     */
    public $name;
    
    /**
     * A collection of filter options.
     *
     * @var array
     */
    public $filterOptions = array();
    /**
     * The value to filter.
     *
     * @var mixed
     */
    public $value;
    
    /**
     * A collection of error.
     *
     * @var array
     */
    public $errors = array();
    
    /**
     * Constructs a new filter.
     *
     * @param $name the name of the filter.
     * @param array array of options for this filter.
     */
    public function __construct($name, $options)
    {
        $this->name = $name;
        $this->filterOptions = $options;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getValue()
    {
        return $this->value;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }
    
    public function setValue($value)
    {
        if (empty($value) && !empty($this->filterOptions['default'])) {
            $value = $this->filterOptions['default'];        
        }
        $this->value = $value;
        return $this;
    }
    /**
     * {@inheritDoc}
     */
    public function getErrors()
    {
        if (!empty($this->errors)) {
            return $this->errors();
        }
    }
    
    public function setError($msg)
    {
        $this->errors[] = $msg;
    }
    
    public function getCaller()
    {
        return Factory::getInstance();
    }
}
