<?php

namespace Webwijs\View\Helper;

class FormDatetimeselect extends FormElement
{
    public function formDatetimeselect($name, $value, $attribs, $options)
    {
        return $this->view->formDateselect($name, $value, $attribs, $options)
             . ' '
             . $this->view->formTimeselect($name, $value, $attribs, $options);
    }
}
