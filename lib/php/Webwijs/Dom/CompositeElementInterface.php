<?php

namespace Webwijs\Dom;

/**
 * The CompositeElementInterface represents an HTML element composed of zero or more HTML elements.
 *
 * @author Chris Harris <chris@webwijs.nu>
 * @version 1.0.0
 * @since 1.1.0
 */
interface CompositeElementInterface
{    
    /**
     * Add a collection of child elements.
     *
     * @param array|Traversable $elements a collection of child elements to add.
     */
    public function addChildren($elements);
    
    /**
     * Returns a list containing child elements.
     *
     * @return ListInterface a list containing child elements.
     */
    public function getChildren();
}
