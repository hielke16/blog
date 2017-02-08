<?php

namespace Webwijs\View\Helper;

class FormPlaintext extends FormElement
{
    public function formPlaintext($name, $value, $attribs, $options)
    {
        return $this->view->formHidden($name, $value, $attribs, $options) . $value;
    }
}
