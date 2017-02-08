<?php

namespace Webwijs\View\Helper;

class FormFile extends FormElement
{
    public function formFile($name, $value, $attribs, $options)
    {
        $attribs['type'] = 'file';
        $attribs['name'] = $name;
        !isset($attribs['id']) && $attribs['id'] = $name . '-input';
        $output = '<input' . $this->_renderAttribs($attribs) . '/>';
        if (!empty($value)) {
            $output = '<p class="file-preview">Huidig bestand: ' . $this->view->escape($value)
                . '<input type="hidden" name="' . $name . '" value="' . $this->view->escape($value) . '" />'
                . '</p>'
                . '<div>' . $output . '</div>';
        }
        return $output;
    }
}
