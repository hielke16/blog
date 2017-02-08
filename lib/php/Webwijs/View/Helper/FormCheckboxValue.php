<?php

namespace Webwijs\View\Helper;

class FormCheckboxValue extends FormElement
{
    public function formCheckboxValue($name, $value, $attribs, $options)
    {
        if (isset($attribs['label'])) {
            return '<strong>&#10004; ' . $attribs['label'] . '</strong>';
        }
    }
}
