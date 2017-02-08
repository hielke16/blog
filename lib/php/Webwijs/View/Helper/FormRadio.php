<?php

namespace Webwijs\View\Helper;

class FormRadio extends FormElement
{
    public function formRadio($name, $value, $attribs, $options)
    {
        $output = '<span id="' . $name . '-input">';
        foreach ($this->convertOptions($options) as $optionValue => $optionLabel) {
            $optionAttribs = $attribs;
            $optionAttribs['name'] = $name;
            $optionAttribs['value'] = $optionValue;
            $optionAttribs['type'] = 'radio';
            if ($optionValue == $value) {
                $optionAttribs['checked'] = 'checked';
            }
            $output .= '<label class="option-label">'
                . '<input'. $this->_renderAttribs($optionAttribs) . ' />'
                . $this->escape($optionLabel) . '</label>';
        }
        $output .= '</span>';
        return $output;
    }
    public function convertOptions($options) {
        if (!is_array($options)) {

            $strings = explode(',', $options);
            $options = array();

            foreach ($strings as $string) {
                $optionParts = explode('=', trim($string));
                $options[$optionParts[0]] = (count($optionParts) > 1)
                                          ? $optionParts[1]
                                          : $optionParts[0];
            }
        }
        return $options;
    }
}
