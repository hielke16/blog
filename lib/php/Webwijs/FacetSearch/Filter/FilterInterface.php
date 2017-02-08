<?php

namespace Webwijs\FacetSearch\Filter;

/**
 * A filter is capable of creating a collection of query variables from a value.
 *
 * Usually a filter is used in conjunction with one or more filters and the resulting collection of a filter
 * only makes up for a small part of the whole query.
 *
 * @author Chris Harris
 * @version 1.1.0
 * @since 1.0.0
 */
interface FilterInterface
{
    /**
     * Apply filter to the value.
     *
     * @return array a collection of key-value pairs to be used in a query.
     */
    public function apply();

    /**
     * Returns if presenst one or more errors.
     *
     * @return array a collection of errors.
     */
    public function getErrors();

    /**
     * Returns the name of the filter.
     *
     * @return string the name of the filter
     */
    public function getName();
    
    /**
     * Returns the value to filter.
     *
     * @return mixed the value.
     */
    public function getValue();
}
