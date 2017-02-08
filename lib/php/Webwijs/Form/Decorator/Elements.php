<?php

namespace Webwijs\Form\Decorator;

use Webwijs\Form\Decorator;

class Elements extends Decorator
{
    public $placement = 'append';
    public function render($content, $view)
    {
        $output = '';
        foreach ($this->element->elements as $element) {
            $output .= $element->render($view);
        }
        if (!empty($this->tag)) {
            $output = '<' . $this->tag . '>' . $output . '</' . $this->tag . '>';
        }
        $output = $this->_place($output, $content);
        return $output;
    }
}
