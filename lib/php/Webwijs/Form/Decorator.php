<?php

namespace Webwijs\Form;

class Decorator
{
    public $element;
    public $placement;
    public function __construct($element, $options = null)
    {
        $this->element = $element;
        $this->setOptions($options);
    }
    public function setOptions($options)
    {
        if (is_array($options)) {
            foreach ($options as $key => $option) {
                $this->$key = $option;
            }
        }
    }
    protected function _renderAttribs($attribs = null)
    {
        $parts = array();
        is_null($attribs) && isset($this->attribs) && $attribs = $this->attribs;

        if (is_array($attribs)) {
            foreach ($attribs as $name => $value) {
                $parts[] = $name . '="' . $value . '"';
            }
        }
        if (!empty($parts)) {
            return ' ' . implode(' ', $parts);
        }
    }
    protected function _place($output, $contents, $default = 'prepend')
    {
        $placement = $this->placement;
        if (!in_array($placement, array('prepend', 'append'))) {
            $placement = $default;
        }
        switch ($placement) {
            case 'append':
                $output = $contents . $output;
                break;
            case 'prepend':
                $output = $output . $contents;
                break;
        }
        return $output;
    }
    public function render($content, $view)
    {
        return $content;
    }
}
