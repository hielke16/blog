<?php

namespace Webwijs\Dom;

/**
 * The ElementInterface represents an HTML element.
 *
 * @author Chris Harris <chris@webwijs.nu>
 * @version 1.0.0
 * @since 1.1.0
 */
interface ElementInterface extends RenderableInterface
{
    /**
     * Set the inner text of this element.
     *
     * @param string $content the content.
     */
    public function setInnerText($content);
    
    /**
     * Returns the plain text contained by this element.
     *
     * @return string
     */
    public function getInnerText();
    
    /**
     * Add a collection of attributes.
     *
     * @param array|Traversable $attributes a collection of attributes to add.
     */
    public function addAttributes($attributes);
    
    /**  
     * Remove the specified attribute from this element.
     *
     * @param string $name the attribute to remove.
     * @return mixed|null the value associated with the removed attribute, or null on failure.
     */
    public function removeAttribute($name);
    
    /**
     * Returns if present the value associated with the specified attribute.
     *
     * @param string $name the name of an attribute whose value to return.
     * @param mixed $default (optional) the value to return if the attribute does not exist.
     * @return mixed the value associated with the specified attribute, or default value on failure.
     */
    public function getAttribute($name, $default = null);
    
    /**
     * Returns a collection of attributes for this element.
     *
     * @return array a collection of attributes.
     */
    public function getAttributes();
    
    /**
     * Returns true if the specified attribute exists.
     *
     * @param string $name the attribute whose presence will be tested.
     * @return bool true if the specified attribute exists, otherwise false.
     */
    public function hasAttribute($name);
    
    /**
     * Add a piece of arbitrary data to this element.
     *
     * @param string $name the name by which the data is identified.
     * @param mixed $value the value to associate with the specified name.
     */
    public function addData($name, $value);
    
    /**
     * Remove a pieve of arbitrary data from this element.
     *
     * @param string $name the name of the data to remove.
     * @return mixed|null the value associated with the remove data, or null on failure.
     */
    public function removeData($name);
    
    /**
     * Returns a collection of arbitrary data associated for this element.
     *
     * @return array a collection of arbitrary data.
     */
    public function getData();
}
