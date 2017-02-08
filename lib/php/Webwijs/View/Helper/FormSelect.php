<?php

namespace Webwijs\View\Helper;

class FormSelect extends FormElement
{
    public function formSelect($name, $value, $attribs, $options)
    {
        $value = array_map('strval', (array) $value);
        $attribs['name'] = $name;
        !isset($attribs['id']) && $attribs['id'] = $name . '-input';
        $output = '<select' . $this->_renderAttribs($attribs) . '">';

        $list = array();
        foreach ($this->convertOptions($options) as $optionValue => $optionLabel) {
            if (is_array($optionLabel)) {
                $list[] = '<optgroup label="' . $optionValue . '">';
                foreach ($optionLabel as $val => $lab) {
                    $list[] = $this->_build($val, $lab, $value);
                }
                $list[] = '</optgroup>';
            }
            else {
                $list[] = $this->_build($optionValue, $optionLabel, $value);
            }
        }
        $output .= implode(' ',  $list);
        $output .= '</select>';
        return $output;
    }
    protected function _build($value, $label, $selected)
    {
        $opt = '<option value="' . $value . '" label="' . $label . '"';

        if (in_array((string) $value, $selected)) {
            $opt .= ' selected="selected"';
        }

        $opt .= '>' . $label . "</option>";

        return $opt;
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
