<?php

namespace Webwijs\Form\Decorator;

use Webwijs\Form\Decorator;

class ElementValue extends Decorator
{
    public function render($content, $view)
    {
        $helperName = 'form' . ucfirst($this->element->helper) . 'Value';
        $helper = $view->getHelper($helperName);

        if (!$helper) {
            $helperName = 'formValue';
        }

        return $view->$helperName($this->element->name, $this->element->value, $this->element->attribs, $this->element->options);
    }
}
