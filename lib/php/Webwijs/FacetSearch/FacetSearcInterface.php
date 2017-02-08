<?php

/**
 * A FacetSearch is capable of parsing a string into a collection of query variables.
 *
 * @author Chris harris
 * @version 1.1.0
 * @since 1.0.0
 */
interface FacetSearchInterface
{
    /**
     * Associate the specified filter with the specified name in this FacetSearch. Any filter that was previously mapped to the given name 
     * will be replaced by the new filter.
     *
     * @param string $name the name to associate with the filter.
     * @param FilterInterface the filter to associate with the given name.
     * @return FilterInterface|null the previous associated filter for the given name, or null if no filter was associated with the given name.                             
     */
    public function addFilter($name, FilterInterface $filter);
    
    /**
     * Returns true if a filter is mapped to the give name.
     *
     * @param string $name the name whose presence will be tested.
     * @return bool true if a filter is associated with the given name, false otherwise.
     */
    public function hasFilter($name);
    
    /**
     * Removes if present a filter that is associated with the given name.
     *
     * @param string $name the name whose filter will be removed.
     * @return FilterInterface|null the previously filter associated with the name, or null if no filter was found.
     */
    public function removeFilter($name);
    
    /**
     * Returns the filter associated with the given name, or null if the name doesn't have a filter associated with it.
     *
     * @param string the name for which to retrieve a filter.
     * @return FilterInterface|null the filter associated with the given name, or null if no filter was found.
     */
    public function getFilter($name);
    
    /**
     * Parses the given query string into an array consisting of key-value pairs.
     *
     * @param string $str the query string to parse.
     * @return array array consisting of key-value pairs.
     */
    public function parseQuery($str);
}
