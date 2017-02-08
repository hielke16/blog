<?php

namespace Webwijs\View\Helper;

class FormDecimal extends FormElement
{
    public function formDecimal($name, $value, $attribs, $options)
    {
        $output = $this->view->formText($name . '[number]', $value['number'], array_merge(array('maxlength' => 5, 'class' => 'regular-text medium digits', 'id' => $name . '-input'), (array) $attribs), null);
        $output .= '<label for="' .  $name . 'decimal-input' . '">, </label>';
        $output .= $this->view->formText($name . '[decimal]', $value['decimal'], array('maxlength' => 2, 'class' => 'regular-text small digits', 'id' => $name . 'decimal-input'), null);
        return $output;
    }
}
