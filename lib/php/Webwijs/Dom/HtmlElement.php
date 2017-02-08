<?php

namespace Webwijs\Dom;

/**
 * The HtmlElement represents an HTML element inside a web page.
 *
 * <code>
 *     $div = new HtmlElement('div');
 *     $div->addAttributes(array(
 *         'class'           => 'my-class',
 *         'contenteditable' => 'true',
 *     ));
 *     $div->addChild(new TextElement('FooBar'));
 *
 *     echo $div;
 * </code>      
 *
 * @author Chris Harris <chris@webwijs.nu>
 * @version 1.0.0
 * @since 1.1.0
 */
class HtmlElement extends AbstractCompositeElement
{
    /**
     * The HTML tag name.
     *
     * @var string
     */
    private $tagName = '';
    
    /**
     * Construct a new HtmlElement.
     *
     * @return string the tag name.
     * @throws InvalidArgumentException if the specified argument is not a string.
     */
    public function __construct($tag)
    {
        $this->setTagName($tag);
    }
        
    /**
     * {@inheritDoc}
     */
    public function getInnerText()
    {
        return strip_tags($this->toHtml());
    }
    
    /**
     * {@inheritDoc}
     */
    public function toHtml()
    {
        $tagName = $this->getTagName();
        $attribs = array_map(function($name, $value) {
            return sprintf('%s="%s"', $name, $value);
        }, array_keys($this->getAttributes()), $this->getAttributes());
        
        $html = '';
        foreach ($this->getChildren() as $child) {
            $html .= $child->toHtml();
        }
                
        return sprintf('<%1$s %2$s>%3$s</%1$s>', $tagName, join(' ', $attribs), $html);
    }

    /**
     * Returns the HTML tag name.
     *
     * @return string the tag name.
     */
    public function getTagName()
    {
        return $this->tagName;
    }
    
    /**
     * Set the HTML tag name.
     *
     * @return string $tagName the tag name.
     * @throws InvalidArgumentException if the specified argument is not a string.
     */
    private function setTagName($tagName)
    {
        if (!is_string($tagName)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects name to be a string argument; received "%s" instead.',
                __METHOD__,
                (is_object($tagName)) ? get_class($tagName) : gettype($tagName)
            ));
        }
        
        $this->tagName = $tagName;
    }
    
    /**
     * Returns the html associated with this element.
     *
     * @return string the HTML representation of this element.
     * @see ElementInterface::toHtml();
     */
    public function __toString()
    {
        return $this->toHtml();
    }
}
