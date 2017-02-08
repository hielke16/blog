<?php

namespace Webwijs\View\Helper;

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
    public function escape($text)
    {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}
