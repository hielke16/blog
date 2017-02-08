<?php

namespace Webwijs\View\Helper;

class FormText extends FormElement
{
    public function formText($name, $value, $attribs = array(), $options = array())
    {
        $attribs['type'] = 'text';
        $attribs['value'] = $this->escape($value);
        $attribs['name'] = $name;
        !isset($attribs['id']) && $attribs['id'] = $name . '-input';
        return '<input' . $this->_renderAttribs($attribs) . '/>';
    }
}
