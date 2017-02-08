<?php

namespace Webwijs\View\Helper;

class FormPassword extends FormElement
{
    public function formPassword($name, $value, $attribs, $options)
    {
        $attribs['type'] = 'password';
        $attribs['value'] = $this->escape($value);
        $attribs['name'] = $name;
        !isset($attribs['id']) && $attribs['id'] = $name . '-input';
        return '<input' . $this->_renderAttribs($attribs) . '/>';

    }
}
