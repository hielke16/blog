<?php

namespace Webwijs\Dom;

/**
 * The AbstractElement provides a skeleton implementation of the {@link ElementInterface} interaface and minimizes
 * the effort required to implement this interface.
 *
 * @author Chris Harris <chris@webwijs.nu>
 * @version 1.0.0
 * @since 1.1.0
 */
abstract class AbstractElement implements ElementInterface
{
    /**
     * The inner text of the element.
     * 
     * @var string
     */
    private $text = '';
    
    /**
     * A collection of attributes.
     *
     * @var array
     */
    private $attributes = array();
    
    /**
     * A collection of arbitrary data.
     *
     * @var array
     */
    private $data = array();
        
    /**
     * {@inheritDoc}
     *
     * @throws InvalidArgumentException if the specified argument is not a string.
     */
    public function setInnerText($text)
    {
        if (!is_string($text)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s" instead.',
                __METHOD__,
                (is_object($text)) ? get_class($text) : gettype($text)
            ));
        }
        
        $this->text = $text;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getInnerText()
    {
        return $this->text;
    }
        
    /**
     * Add a new attribute.
     *
     * @param string $name the attribute name.
     * @param mixed $value the attribute value.
     * @throws InvalidArgumentException if the specified argument is not a string.
     */
    public function addAttribute($name, $value)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects name to be a string argument; received "%s" instead.',
                __METHOD__,
                (is_object($name)) ? get_class($name) : gettype($name)
            ));
        }
    
        $this->attributes[$name] = $value;
    }
    
    /**
     * {@inheritDoc}
     *
     * @throws InvalidArgumentException if the specified argument is not an array or Traversable object.
     */
    public function addAttributes($attributes)
    {
        if (!is_array($attributes) && !($attributes instanceof \Traversable)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an array or instance of the Traversable; received "%s"',
                __METHOD__,
                (is_object($attributes) ? get_class($attributes) : gettype($attributes))
            ));
        }
        
        foreach ($attributes as $name => $value) {
            $this->addAttribute($name, $value);
        }
    }
    
    /**
     * {@inheritDoc}
     */
    public function hasAttribute($name)
    {
        return isset($this->attributes[$name]);
    }
    
    /**  
     * {@inheritDoc}
     *
     * @throws InvalidArgumentException if the specified argument is not a string.
     */
    public function removeAttribute($name)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects name to be a string argument; received "%s" instead.',
                __METHOD__,
                (is_object($name)) ? get_class($name) : gettype($name)
            ));
        }
    
        $value = null;
        if ($this->hasAttribute($name)) {
            $value = $this->getAttribute($name);
            unset($this->attributes[$name]);
        }
        
        return $value;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getAttribute($name, $default = null)
    {
        return ($this->hasAttribute($name)) ? $this->attributes[$name] : $default;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
    
    /**
     * Remove all attributes from this element.
     *
     * @return void
     */
    public function clearAttributes()
    {
        $this->attributes = array(); 
    }
    
    /**
     * {@inheritDoc}
     *
     * @throws InvalidArgumentException if the first argument is not a string.
     */
    public function addData($name, $value)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects name to be a string argument; received "%s" instead.',
                __METHOD__,
                (is_object($name)) ? get_class($name) : gettype($name)
            ));
        }
    
        $this->data[$name] = $value;
    }
    
    /**
     * {@inheritDoc}
     * 
     * @throw InvalidArgumentException if the specified argument is not a string.
     */
    public function removeData($name)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects name to be a string argument; received "%s" instead.',
                __METHOD__,
                (is_object($name)) ? get_class($name) : gettype($name)
            ));
        }
        
        $value = null;
        if (isset($this->data[$name])) {
            $value = $this->data[$name];
            unset($this->data[$name]);
        }
        
        return $value;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getData()
    {    
        return $this->data;
    }
    
    /**
     * Remove all arbitrary data associated with this element.
     *
     * @return void
     */
    public function clearData()
    {
        $this->data = array();
    }
}
