<?php

namespace Webwijs\View\Helper;

class FormTimeselect extends FormElement
{
    public function formTimeselect($name, $value, $attribs = array(), $options = array())
    {
        if (!is_array($value)) {
            if (!empty($value)) {
                $value = strtotime($value);
            }
            $value = array('hours' => date('H', $value), 'mins' => date('i', $value));
        }

        $selects = array();
        $selects['hours'] = array('' => '--');
        $selects['mins'] = array('' => '--');

        for ($i = 0; $i < 24; $i++) {
            $selects['hours'][sprintf('%02s', $i)] = sprintf('%02s', $i);
        }
        for ($i = 0; $i < 60; $i++) {
            $selects['mins'][sprintf('%02s', $i)] = sprintf('%02s', $i);
        }

        $output = '<span id="' . $name . '-input">';
        foreach ($selects as $key => $options) {
            $output .= $this->view->formSelect($name . '[' . $key . ']', @$value[$key], array('id' => $name . '-' . $key), $options);
        }
        $output .= '</span>';
        return $output;
    }
}
