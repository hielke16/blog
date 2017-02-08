<?php

namespace Webwijs\View\Helper;

class FormBoolean extends FormElement
{
    public function formBoolean($name, $value, $attribs, $options)
    {
        return '<label>' . $this->view->formCheckbox($name, $value, $attribs, $options) . ' Ja</label>';
    }
}
