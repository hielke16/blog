<?php

namespace Webwijs\View\Helper;

class FormSubmit extends FormElement
{
    public function formSubmit($name, $value, $attribs, $options)
    {
        $label = $attribs['label'];
        unset($attribs['label']);
        !isset($attribs['type']) && $attribs['type'] = 'submit';
        !isset($attribs['name']) && $attribs['name'] = $name;
        !isset($attribs['value']) && $attribs['value'] = 1;
        !isset($attribs['id']) && $attribs['id'] = $name . '-input';
        return '<button ' . $this->_renderAttribs($attribs) . '><span><span>' . $label . '</span></span></button>';
    }
}
