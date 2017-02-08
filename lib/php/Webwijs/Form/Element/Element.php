<?php

namespace Webwijs\Form\Element;

use Webwijs\Form\Element;

class FormElement
{
    protected function _renderAttribs($attribs)
    {
        $parts = array();
        if (is_array($attribs)) {
            foreach ($attribs as $name => $value) {
                $parts[] = $name . '="' . $value . '"';
            }
        }
        if (!empty($parts)) {
            return ' ' . implode(' ', $parts);
        }
    }
    public function render($name, $value, $attribs, $options)
    {

    }
    public function escape($text)
    {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}
