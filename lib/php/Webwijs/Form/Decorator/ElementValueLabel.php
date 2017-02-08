<?php

namespace Webwijs\Form\Decorator;

use Webwijs\Form\Decorator;

class ElementValueLabel extends Decorator
{
    public $tag;
    public $placement = 'prepend';
    public function render($content, $view)
    {
        $output = '';
        if($this->element->ignore){
            return $output;
        }
        if (!empty($content)) {
            $output = $view->escape($this->element->label);
            if (!empty($output) && !empty($this->tag)) {
                $output = '<' . $this->tag . $this->_renderAttribs() . '><strong>' . $output . '</strong></' . $this->tag . '>';
            }
        }
        $output = $this->_place($output, $content);
        return $output;
    }
}
