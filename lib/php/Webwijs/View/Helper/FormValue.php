<?php

namespace Webwijs\View\Helper;

class FormValue
{
    public function formValue($name, $value, $attribs, $options)
    {
        if (is_array($value)) {
            return implode(', ', $value);
        }
        return $value;
    }
}
