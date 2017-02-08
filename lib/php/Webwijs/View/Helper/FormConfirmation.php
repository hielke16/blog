<?php

namespace Webwijs\View\Helper;

class FormConfirmation extends FormElement
{
    public function formConfirmation($name, $value, $attribs = null, $options = null)
    {
        return '<div class="confirmation">' . $value . '</div>';
    }
}
