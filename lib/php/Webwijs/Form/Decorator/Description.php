<?php

namespace Webwijs\Form\Decorator;

use Webwijs\Form\Decorator;

class Description extends Decorator
{
    public $placement = 'append';
    public $tag = 'p';
    public $attribs = array('class' => 'description');
    public function render($content, $view)
    {
        $output = '';
        if (!empty($this->element->description)) {
            $output = '<' . $this->tag . $this->_renderAttribs() . '>' . $this->element->description . '</' . $this->tag . '>';
        }
        $output = $this->_place($output, $content);
        return $output;
    }
}
