<?php

namespace Webwijs\FacetSearch\Filter;

/**
 * A MultiFilterInterface is capable of filtering multiple values. Possible implementations of a multifilter include but are not 
 * limited to categories, tags or related posts. Categories, tags or related posts usually consist of zero or more values; also 
 * known as options which could be displayed in a form elements such as a multicheckbox.
 *
 * So in short a multifilter returns the possible options to display through the {@link MultiFilterInterface::getOptions()} method
 * and will filter the values to return based on the selected options.
 *
 * @author Chris Harris 
 * @version 0.0.1
 */
interface MultiFilterInterface extends FilterInterface
{
    /**
     * Returns a collection of key-value pairs that this filter operates on.
     *
     * A possible return value for this method is as following:
     *
     * array(
     *    'home'     => 'Homepage',
     *    'products' => 'Our products',
     *    'contact'  => 'Contact us'
     * );
     *
     * @return array an array of options to display.
     */
    public function getOptions();
}
