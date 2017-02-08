<?php

namespace Webwijs\View\Helper;

class FormTextarea extends FormElement
{
    public function formTextarea($name, $value, $attribs = array(), $options = array())
    {
        $attribs['name'] = $name;
        !isset($attribs['id']) && $attribs['id'] = $name . '-input';
        return '<textarea' . $this->_renderAttribs($attribs) . '>' . $this->escape($value) . '</textarea>';
    }
}
