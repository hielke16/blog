<?php

namespace Webwijs\Dom;

/**
 * The AbstractCompositeElement provides a skeleton implementation of the {@link CompositeElementInterface} interaface
 * and minimizes the effort required to implement this interface.
 *
 * @author Chris Harris <chris@webwijs.nu>
 * @version 1.0.0
 * @since 1.1.0
 */
abstract class AbstractCompositeElement extends AbstractElement implements CompositeElementInterface
{
    /**
     * A collection of elements contained by this element.
     *
     * @var array
     */
    private $elements = array();
    
    /**
     * Add a child element.
     *
     * @param ElementInterface $element the child element to add.
     */
    public function addChild(ElementInterface $element)
    {
        $this->elements[] = $element;
    }
    
    /**
     * {@inheritDoc}
     *
     * @throws InvalidArgumentException if the specified argument is not an array or Traversable object.
     */
    public function addChildren($elements)
    {
        if (!is_array($elements) && !($elements instanceof \Traversable)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an array or instance of the Traversable; received "%s"',
                __METHOD__,
                (is_object($elements) ? get_class($elements) : gettype($elements))
            ));
        }
        
        foreach ($elements as $element) {
            $this->addChild($element);
        }
    }
    
    /**
     * {@inheritDoc}
     */
    public function getChildren()
    {
        return $this->elements;
    }
    
    /**
     * Remove all child elements from this element.
     *
     * @return void
     */
    public function clearChildren()
    {
        $this->elements = array();
    }
    
    /**
     * Returns true if this element has children.
     *
     * @return bool true if this element has children, otherwise false.
     */
    public function hasChildren()
    {
        return (count($this->elements) > 0);
    }
    
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
        
        // remove all child elements.
        $this->clearChildren();
        
        $this->text = $text;
    }
}
