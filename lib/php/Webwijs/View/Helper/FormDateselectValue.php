<?php

namespace Webwijs\View\Helper;

class FormDateselectValue extends FormElement
{
    public function formDateselectValue($name, $value, $attribs, $options)
    {
        return $value['day'] . '-' . $value['month'] . '-' . $value['year'];
    }
}
