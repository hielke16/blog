<?php

namespace Webwijs\View\Helper;

class FormCheckbox extends FormElement
{
    public function formCheckbox($name, $value, $attribs = array(), $options = array())
    {
        $attribs['type'] = 'checkbox';
        $attribs['value'] = '1';
        $attribs['name'] = $name;
        !isset($attribs['id']) && $attribs['id'] = $name . '-input';
        if (!empty($value)) {
            $attribs['checked'] = 'checked';
        }

        $label = null;
        if (isset($attribs['label'])) {
            $label = $attribs['label'];
            unset($attribs['label']);
        }
        $output = "<input {$this->_renderAttribs($attribs)} />";
        if (!empty($label)) {
            $output .= "<label for=\"{$attribs['id']}\">{$label}</label>";
        }
        return $output;
    }
}
