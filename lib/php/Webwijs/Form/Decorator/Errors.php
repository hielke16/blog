<?php

namespace Webwijs\Form\Decorator;

use Webwijs\Form\Decorator;

class Errors extends Decorator
{
    public $tag = 'ul';
    public $itemTag = 'li';
    public $attribs = array('class' => 'errors');
    public $itemAttribs = array();
    public $placement = 'append';
    public function render($content, $view)
    {
        $output = '';
        if (!empty($this->element->errors)) {
            $items = array();
            foreach ($this->element->errors as $error) {
                if ($this->itemTag) {
                    $items[] = '<' . $this->itemTag . $this->_renderAttribs($this->itemAttribs) . '>';
                }
                $items[] = $error;
                if ($this->itemTag) {
                    $items[] =  '</' . $this->itemTag . '>';
                }
            }
            
            $output = implode($items);
            if ($this->tag) {
                $output = '<'. $this->tag . $this->_renderAttribs($this->attribs) . '>' . $output . '</' . $this-> tag . '>';
            }
        }
        $output = $this->_place($output, $content);
        return $output;
    }
}
