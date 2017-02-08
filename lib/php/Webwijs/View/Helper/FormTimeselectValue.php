<?php

namespace Webwijs\View\Helper;

class FormTimeselectValue extends FormElement
{
    public function formTimeselectValue($name, $value, $attribs, $options)
    {
        return $value['hours'] . ':' . $value['mins'];
    }
}
