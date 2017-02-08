<?php

namespace Webwijs\Form\Decorator;

use Webwijs\Form\Decorator;

class Header extends Decorator
{
    public $attr = 'label';
    public $tag;
    public $placement = 'prepend';
    public $notEmpty = true;
    public function render($content, $view)
    {
        $output = '';
        if (!$this->notEmpty || (trim($content) != '')) {

            $attr = $this->attr;
            $output = $this->element->$attr;
            if (!empty($output) && !empty($this->tag)) {
                $output = '<' . $this->tag . $this->_renderAttribs() . '>' . $output . '</' . $this->tag . '>';
            }
        }
        $output = $this->_place($output, $content);
        return $output;
    }
}
