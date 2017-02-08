<?php

namespace Webwijs\Dom;

/**
 * The TextElement represents plaint text inside a web page.
 *
 * <code>
 *     $text = new TextElement('FooBar');
 *     echo $text;
 * </code>   
 *
 * @author Chris Harris <chris@webwijs.nu>
 * @version 1.0.0
 * @since 1.1.0
 */
class TextElement extends AbstractElement
{    
    /**
     * Construct a new TextElement.
     *
     * @return string the text.
     */
    public function __construct($text)
    {
        $this->setInnerText($text);
    }
    
    /**
     * {@inheritDoc}
     */
    public function toHtml()
    {                
        return $this->getInnerText();
    }
    
    /**
     * Returns the inner text associated with this element.
     *
     * @return string the text of this element.
     * @see ElementInterface::toHtml();
     */
    public function __toString()
    {
        return $this->getInnerText();
    }
}
