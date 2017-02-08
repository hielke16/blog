<?php

namespace Webwijs\View\Helper;

class FormMulticheckbox extends FormElement
{  
    public function formMulticheckbox($name, $value, $attribs, $options)
    {
        $output = '<span id="' . $name . '-input">';
        foreach ($this->convertOptions($options) as $optionValue => $optionLabel) {
            $optionAttribs = array();
            if(is_array($optionLabel)){
                $id = $optionLabel['id'];
                $optionLabel = $optionLabel['label'];
            }
            else{
                $id = false;
                $optionAttribs = $attribs;
            }          
            
            $optionAttribs['name'] = $name . '[]';
            $optionAttribs['value'] = $optionValue;
            $optionAttribs['type'] = 'checkbox';
            
            if(is_array($value) && in_array($optionValue, $value)){
                $optionAttribs['checked'] = 'checked';
            }
            $output .= '<label class="option-label" id="' . $id . '">'
                . '<input'. $this->_renderAttribs($optionAttribs) . ' />'
                . $this->escape($optionLabel) . '</label><br />';
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
