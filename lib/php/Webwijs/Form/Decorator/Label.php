<?php

namespace Webwijs\Form\Decorator;

use Webwijs\Form\Decorator;

class Label extends Decorator
{
    public $placement = 'prepend';
    public function render($content, $view)
    {
        $output = '';
        if (!empty($this->element->label)) {
            $attribs = isset($this->attribs) ? $this->attribs : array();
            $attribs['for'] = !empty($this->element->attribs['id']) ? $this->element->attribs['id'] : $this->element->name . '-input';
            
            $output = '<label' . $this->_renderAttribs($attribs) . '>' . $this->element->label;
            if (!empty($this->element->required) && ($this->element->label != '&nbsp;')) {
                $output .= '<span class="requiredAsterix">*</span>';
            }
            $output .= '</label>';
        }
        if (!empty($this->tag)) {
           $output = '<' . $this->tag . '>' . $output . '</' . $this->tag . '>';
        }
        $output = $this->_place($output, $content);
        return $output;
    }
}
