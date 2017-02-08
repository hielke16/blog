<?php

namespace Webwijs\Form\Decorator;

use Webwijs\Form\Decorator;

class Form extends Decorator
{
    public function render($content, $view)
    {
        if (empty($this->element->attribs['id'])) {
            $this->element->attribs['id'] = $this->element->getId();
        }
        if (empty($this->element->attribs['action'])) {
            $this->element->attribs['action'] = get_permalink();
        }
        return '<form' . $this->_renderAttribs($this->element->attribs) . '>' . $content . '</form>';
    }
}
