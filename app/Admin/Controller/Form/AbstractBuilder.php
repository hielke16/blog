<?php

namespace Theme\Admin\Controller\Form;

use Webwijs\Form\Element;
use Webwijs\Loader\ClassLoader;

/**
 * The AbstractBuilder implements methods which a builder can use to create form elements with.
 *
 * @author Chris Harris <chris@webwijs.nu>
 * @version 1.0.0
 * @since 1.0.1
 */
abstract class AbstractBuilder
{
    /**
     * {@inheritDoc}
     *
     * @throws InvalidArgumentException if the specified element name or element type are not strings.
     */
    public function createElement($name, $type = 'text', array $options = array())
    {
	    if (!is_string($name) || !is_string($type)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects name and type to be string arguments.',
                __METHOD__
            ));
	    }
	    
        $class = ClassLoader::loadStatic('formelement', ucfirst($type));
        if ($class !== null) {
            $element = new $class($name);
            $element->setOptions($options);
        } else {
            $element = new Element($name);
            $element->setOptions($options);
            $element->helper = $type;
        }
        
        return $element;
    }    
}
