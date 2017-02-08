<?php

namespace Webwijs\Form\Decorator;

use Webwijs\Form\Decorator;

class ViewHelper extends Decorator
{
    public function render($content, $view)
    {
        if (!empty($this->element->helper)) {
            $helperMethod = 'form' . ucfirst($this->element->helper);
            $content .= $this->view->$helperMethod($this->element->name, $this->element->value, $this->element->attribs, $this->element->options);
        }
        return $content;
    }
}
