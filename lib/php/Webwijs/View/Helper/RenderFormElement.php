<?php

namespace Webwijs\View\Helper;

use Webwijs\Loader\ClassLoader;
use Webwijs\Form\Element;

class RenderFormElement
{
    public function renderFormElement($type, $name, $value, $options)
    {
        $elementClass = ClassLoader::loadStatic('formelement', ucfirst($type));
        if ($elementClass) {
            $element = new $elementClass($name);
        }
        else {
            $element = new Element($name);
            $element->helper = $elementType;
        }
        $options['decorators'] = array('ViewHelper');
        $element->setOptions($options);
        $element->setValue($value);
        return $element->render($this->view);
    }
}
