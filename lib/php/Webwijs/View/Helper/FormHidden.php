<?php

namespace Webwijs\View\Helper;

class FormHidden extends FormElement
{
    public function formHidden($name, $value, $attribs, $options)
    {
        $attribs['type'] = 'hidden';
        $attribs['value'] = $this->escape($value);
        $attribs['name'] = $name;
        !isset($attribs['id']) && $attribs['id'] = $name . '-input';
        return '<input' . $this->_renderAttribs($attribs) . '/>';
    }
}
